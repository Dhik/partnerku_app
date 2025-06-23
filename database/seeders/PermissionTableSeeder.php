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

        $userPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreateUser]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateUser]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewUser]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteUser]),
        ];

        $payrollPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreatePayroll]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdatePayroll]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewPayroll]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeletePayroll]),
        ];

        $incomePermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreateIncome]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateIncome]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewIncome]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteIncome]),
        ];

        $otherSpentPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::CreateOtherSpent]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateOtherSpent]),
            Permission::updateOrCreate(['name' => PermissionEnum::ViewOtherSpent]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteOtherSpent]),
        ];

        $profile = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewProfile]),
            Permission::updateOrCreate(['name' => PermissionEnum::ChangeOwnPassword]),
        ];

        $tenant = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteTenant]),
            Permission::updateOrCreate(['name' => PermissionEnum::AssignTenantUser]),
        ];

        $resultPermissions = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewResult]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateResult]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateResult]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteResult]),
        ];

        $campaign = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewCampaign]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateCampaign]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateCampaign]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteCampaign])
        ];

        $campaignContent = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewCampaignContent]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateCampaignContent]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateCampaignContent]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteCampaignContent])
        ];

        $kol = [
            Permission::updateOrCreate(['name' => PermissionEnum::ViewKOL]),
            Permission::updateOrCreate(['name' => PermissionEnum::CreateKOL]),
            Permission::updateOrCreate(['name' => PermissionEnum::UpdateKOL]),
            Permission::updateOrCreate(['name' => PermissionEnum::DeleteKOL]),
        ];

        $superadmin = Role::findByName(RoleEnum::SuperAdmin);
        $superadmin->givePermissionTo($userPermissions);
        $superadmin->givePermissionTo($payrollPermissions);
        $superadmin->givePermissionTo($incomePermissions);
        $superadmin->givePermissionTo($otherSpentPermissions);
        $superadmin->givePermissionTo($profile);
        $superadmin->givePermissionTo($tenant);
        $superadmin->givePermissionTo($resultPermissions);
        $superadmin->givePermissionTo($campaign);
        $superadmin->givePermissionTo($campaignContent);
        $superadmin->givePermissionTo($kol);

        $client1 = Role::findByName(RoleEnum::Client1);
        $client1->givePermissionTo($kol);
        $client1->givePermissionTo($campaign);
        $client1->givePermissionTo($campaignContent);
        $superadmin->givePermissionTo($profile);

        $client2 = Role::findByName(RoleEnum::Client2);
        $client2->givePermissionTo($campaign);
        $client2->givePermissionTo($campaignContent);
        $superadmin->givePermissionTo($profile);

        $tim_internal = Role::findByName(RoleEnum::TimInternal);
        $tim_internal->givePermissionTo($kol);
        $superadmin->givePermissionTo($profile);

        $tim_ads = Role::findByName(RoleEnum::TimAds);
        $tim_ads->givePermissionTo($campaign);
        $tim_ads->givePermissionTo($campaignContent);
        $superadmin->givePermissionTo($profile);
    }
}
