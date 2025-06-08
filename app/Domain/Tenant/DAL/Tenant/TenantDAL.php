<?php

namespace App\Domain\Tenant\DAL\Tenant;

use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use App\DomainUtils\BaseDAL\BaseDAL;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TenantDAL extends BaseDAL implements TenantDALInterface
{
    public function __construct(
        protected Tenant $tenant,
        protected User $user,
    ) {
    }

    /**
     * Return tenant for DataTable
     */
    public function getTenantDataTable(): Builder
    {
        return $this->tenant->query();
    }

    /**
     * Return all tenant
     */
    public function getAllTenants(): Collection
    {
        return $this->tenant->all();
    }

    /**
     * Change active tenant user
     */
    public function changeTenantUser(int $tenantId): void
    {
        Auth::user()->update(['current_tenant_id' => $tenantId]);
    }

    /**
     * Create a new tenant
     */
    public function storeTenant(array $data): Tenant
    {
        return $this->tenant->create($data);
    }

    /**
     * Update tenant
     */
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);
        return $tenant->fresh();
    }

    /**
     * Delete Tenant
     */
    public function deleteTenant(Tenant $tenant): void
    {
        $tenant->delete();
    }

    /**
     * Check if tenant is used by user
     */
    public function checkCurrentTenantUser(int $tenantId): ?User
    {
        return $this->user->where('current_tenant_id', $tenantId)->first();
    }
    
    /**
     * Check if tenant is used by visit
     */
    public function checkVisitTenant(int $tenantId): ?Visit
    {
        return $this->visit->where('tenant_id', $tenantId)->first();
    }

    /**
     * Check if tenant is used by marketing
     */
    public function checkMarketingTenant(int $tenantId): ?Marketing
    {
        return $this->marketing->where('tenant_id', $tenantId)->first();
    }

    /**
     * Check if tenant is used by Ad spent marketplace
     */
    public function checkAdSpentMPTenant(int $tenantId): ?AdSpentMarketPlace
    {
        return $this->adSpentMarketPlace->where('tenant_id', $tenantId)->first();
    }

    /**
     * Check if tenant is used by Ad spent social media
     */
    public function checkAdSpentSMTenant(int $tenantId): ?AdSpentSocialMedia
    {
        return $this->adSpentSocialMedia->where('tenant_id', $tenantId)->first();
    }
}