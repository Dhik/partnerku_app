<?php

namespace Database\Seeders;

use App\Domain\User\Enums\PermissionEnum;
use App\Domain\User\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // User permissions
        $userPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreateUser]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateUser]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewUser]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteUser]),
        ];

        // Payroll permissions
        $payrollPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreatePayroll]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdatePayroll]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewPayroll]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeletePayroll]),
        ];

        // Income permissions
        $incomePermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreateIncome]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateIncome]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewIncome]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteIncome]),
        ];

        // Other Spent permissions
        $otherSpentPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreateOtherSpent]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateOtherSpent]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewOtherSpent]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteOtherSpent]),
        ];

        // Profile permissions
        $profilePermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewProfile]),
            Permission::updateOrCreate(['name' => PermissionEnum::ChangeOwnPassword]),
        ];

        // Tenant permissions
        $tenantPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::AssignTenantUser]),
        ];

        // Result permissions
        $resultPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewResult]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateResult]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateResult]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteResult]),
        ];

        // Campaign permissions
        $campaignPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewCampaign]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateCampaign]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateCampaign]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteCampaign])
        ];

        // Campaign Content permissions
        $campaignContentPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewCampaignContent]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateCampaignContent]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateCampaignContent]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteCampaignContent])
        ];

        // KOL permissions
        $kolPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewKOL]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateKOL]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateKOL]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteKOL]),
        ];

        // Assign permissions to roles

        // SuperAdmin - Has access to all features
        $superadmin = Role::findByName(RoleEnum::SuperAdmin);
        $superadmin->givePermissionTo($userPermissions);
        $superadmin->givePermissionTo($payrollPermissions);
        $superadmin->givePermissionTo($incomePermissions);
        $superadmin->givePermissionTo($otherSpentPermissions);
        $superadmin->givePermissionTo($profilePermissions);
        $superadmin->givePermissionTo($tenantPermissions);
        $superadmin->givePermissionTo($resultPermissions);
        $superadmin->givePermissionTo($campaignPermissions);
        $superadmin->givePermissionTo($campaignContentPermissions);
        $superadmin->givePermissionTo($kolPermissions);

        // Client1 - Can CRUD campaigns, campaign_contents, and KOLs for their brand, monitoring, input budget
        $client1 = Role::findByName(RoleEnum::Client1);
        $client1->givePermissionTo($profilePermissions);
        $client1->givePermissionTo($campaignPermissions); // Full CRUD for campaigns
        $client1->givePermissionTo($campaignContentPermissions); // Full CRUD for campaign contents
        $client1->givePermissionTo($kolPermissions); // Full CRUD for KOLs

        // Client2 - Monitor only (views, comment, likes, engagement rate, campaigns, campaign_contents, KOLs)
        $client2 = Role::findByName(RoleEnum::Client2);
        $client2->givePermissionTo($profilePermissions);
        $client2->givePermissionTo([
            Permission::findByName(PermissionEnum::ViewCampaign),
            Permission::findByName(PermissionEnum::ViewCampaignContent),
            Permission::findByName(PermissionEnum::ViewKOL) // Only view for monitoring
        ]);

        // TimInternal - Create and Read KOLs, view campaigns and campaign_contents (no edit/delete)
        $timInternal = Role::findByName(RoleEnum::TimInternal);
        $timInternal->givePermissionTo($profilePermissions);
        $timInternal->givePermissionTo([
            Permission::findByName(PermissionEnum::ViewCampaign),
            Permission::findByName(PermissionEnum::ViewCampaignContent),
            Permission::findByName(PermissionEnum::CreateKOL), // Can create KOLs
            Permission::findByName(PermissionEnum::ViewKOL) // Can view KOLs
        ]);

        // TimAds - Full KOL management, view campaigns and campaign_contents
        $timAds = Role::findByName(RoleEnum::TimAds);
        $timAds->givePermissionTo($profilePermissions);
        $timAds->givePermissionTo($kolPermissions); // Full CRUD for KOL
        $timAds->givePermissionTo([
            Permission::findByName(PermissionEnum::ViewCampaign),
            Permission::findByName(PermissionEnum::ViewCampaignContent)
        ]);
    }
}