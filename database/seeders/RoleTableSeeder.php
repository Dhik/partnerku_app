<?php

namespace Database\Seeders;

use App\Domain\User\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::updateOrcreate(['name' => RoleEnum::SuperAdmin]);
        Role::updateOrcreate(['name' => RoleEnum::Client1]);
        Role::updateOrcreate(['name' => RoleEnum::Client2]);
        Role::updateOrcreate(['name' => RoleEnum::TimInternal]);
        Role::updateOrcreate(['name' => RoleEnum::TimAds]);
    }
}
