<?php

namespace App\Domain\Tenant\BLL\Tenant;

use App\Domain\Tenant\DAL\Tenant\TenantDALInterface;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\Tenant\Requests\TenantRequest;
use App\Domain\User\Enums\RoleEnum;
use App\DomainUtils\BaseBLL\BaseBLL;
use App\DomainUtils\BaseBLL\BaseBLLFileUtils;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * @property TenantDALInterface DAL
 */
class TenantBLL extends BaseBLL implements TenantBLLInterface
{
    use BaseBLLFileUtils;

    public function __construct(protected TenantDALInterface $tenantDAL)
    {
    }

    /**
     * Return tenant for DataTable
     */
    public function getTenantDataTable(): Builder
    {
        return $this->tenantDAL->getTenantDataTable();
    }

    /**
     * Return all tenants - Fixed to return proper format for dropdowns
     */
    public function getAllTenants(): Collection
    {
        if (Auth::user()->hasRole(RoleEnum::SuperAdmin)) {
            return $this->tenantDAL->getAllTenants()->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                ];
            });
        }

        return Auth::user()->tenants()->get()->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->name,
            ];
        });
    }

    /**
     * Change active tenant user
     */
    public function changeTenant(int $tenantId): void
    {
        // check if user have role other than super admin
        if (! Auth::user()->hasRole(RoleEnum::SuperAdmin)) {
            Auth::user()->tenants()->findOrFail($tenantId);
        }

        $this->tenantDAL->changeTenantUser($tenantId);
    }

    /**
     * Change default tenant user
     */
    public function setDefaultTenant(): void
    {
        if (is_null(Auth()->user()->current_tenant_id)) {

            // check if user have role other than super admin
            if (Auth::user()->hasRole(RoleEnum::SuperAdmin)) {
                $tenant = $this->tenantDAL->getAllTenants()->first();
            } else {
                $tenant = Auth::user()->tenants()->first();
            }

            if (! is_null($tenant)) {
                $this->tenantDAL->changeTenantUser($tenant->id);
            }
        }
    }

    /**
     * Create new tenant
     */
    public function storeTenant(TenantRequest $request): Tenant
    {
        $data = $request->only('name');
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->handleLogoUpload($request->file('logo'));
        }

        return $this->tenantDAL->storeTenant($data);
    }

    /**
     * Update tenant
     */
    public function updateTenant(Tenant $tenant, TenantRequest $request): Tenant
    {
        $data = $request->only('name');
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo) {
                $this->deleteLogoFile($tenant->logo);
            }
            
            $data['logo'] = $this->handleLogoUpload($request->file('logo'));
        }

        return $this->tenantDAL->updateTenant($tenant, $data);
    }

    /**
     * Delete tenant
     */
    public function deleteTenant(Tenant $tenant): bool
    {
        $checkCurrentTenantUser = $this->tenantDAL->checkCurrentTenantUser($tenant->id);

        if (! empty($checkCurrentTenantUser)) {
            return false;
        }

        $checkVisitTenant = $this->tenantDAL->checkVisitTenant($tenant->id);

        if (! empty($checkVisitTenant)) {
            return false;
        }

        $checkMarketingTenant = $this->tenantDAL->checkMarketingTenant($tenant->id);

        if (! empty($checkMarketingTenant)) {
            return false;
        }

        $checkAdSpentMPTenant = $this->tenantDAL->checkAdSpentMPTenant($tenant->id);

        if (! empty($checkAdSpentMPTenant)) {
            return false;
        }

        $checkAdSpentSMTenant = $this->tenantDAL->checkAdSpentSMTenant($tenant->id);

        if (! empty($checkAdSpentSMTenant)) {
            return false;
        }

        // Delete logo file if exists
        if ($tenant->logo) {
            $this->deleteLogoFile($tenant->logo);
        }

        $this->tenantDAL->deleteTenant($tenant);

        return true;
    }

    /**
     * Handle logo file upload
     */
    private function handleLogoUpload(UploadedFile $file): string
    {
        // Create directory if it doesn't exist
        if (!Storage::disk('public')->exists('tenant-logos')) {
            Storage::disk('public')->makeDirectory('tenant-logos');
        }

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store the file
        $file->storeAs('tenant-logos', $filename, 'public');
        
        return $filename;
    }

    /**
     * Delete logo file
     */
    private function deleteLogoFile(string $filename): void
    {
        $path = 'tenant-logos/' . $filename;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}