<?php

namespace App\Domain\Tenant\Controllers;

use App\Domain\Tenant\BLL\Tenant\TenantBLLInterface;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\Tenant\Requests\TenantRequest;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View as ViewAlias;
use Illuminate\Foundation\Application as ApplicationAlias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TenantController extends Controller
{
    public function __construct(protected TenantBLLInterface $tenantBLL)
    {
    }

    /**
     * Change tenant
     */
    public function changeTenant(int $tenantId): RedirectResponse
    {
        $this->tenantBLL->changeTenant($tenantId);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * @throws Exception
     */
    public function get(): JsonResponse
    {
        $this->authorize('viewTenant', Tenant::class);

        $tenantQuery = $this->tenantBLL->getTenantDataTable();

        return DataTables::of($tenantQuery)
            ->addColumn('logo', function ($tenant) {
                if ($tenant->logo) {
                    // Check if file exists before showing
                    if (Storage::disk('public')->exists('tenant-logos/' . $tenant->logo)) {
                        $logoUrl = Storage::url('tenant-logos/' . $tenant->logo);
                        return '<img src="' . $logoUrl . '" alt="Logo" style="max-width: 120px; max-height: 80px; width: auto; height: auto;" class="img-thumbnail">';
                    }
                }
                return '<span class="text-muted">No Logo</span>';
            })
            ->addColumn('logo_url', function ($tenant) {
                if ($tenant->logo && Storage::disk('public')->exists('tenant-logos/' . $tenant->logo)) {
                    return Storage::url('tenant-logos/' . $tenant->logo);
                }
                return null;
            })
            ->addColumn(
                'actions',
                '<button class="btn btn-primary btn-xs updateButton">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-danger btn-xs deleteButton">
                            <i class="fas fa-trash-alt"></i>
                        </button>'
            )
            ->rawColumns(['logo', 'actions'])
            ->make();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ViewAlias|ApplicationAlias|Factory|View|Application
    {
        $this->authorize('viewTenant', Tenant::class);

        return view('admin.tenant.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantRequest $request): JsonResponse
    {
        $this->authorize('createTenant', Tenant::class);

        $this->tenantBLL->storeTenant($request);

        return response()->json(['message' => 'Tenant created successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Tenant $tenant, TenantRequest $request)
    {
        $this->authorize('updateTenant', Tenant::class);

        $this->tenantBLL->updateTenant($tenant, $request);

        return response()->json(['message' => 'Tenant updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $this->authorize('deleteTenant', Tenant::class);

        $result = $this->tenantBLL->deleteTenant($tenant);

        if (! $result) {
            return response()->json(['message' => trans('messages.tenant_failed_delete')], 422);
        }

        return response()->json(['message' => trans('messages.success_delete')]);
    }
}