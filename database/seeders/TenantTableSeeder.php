<?php

namespace Database\Seeders;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantTableSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::updateOrCreate(['name' => 'Microsoft']);
        Tenant::updateOrCreate(['name' => 'Google']);
    }
}
