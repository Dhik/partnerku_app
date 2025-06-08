<?php

namespace Database\Seeders;

use App\Domain\User\Enums\RoleEnum;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        $usersCsvPath = database_path('seeders/data/users.csv');
        $users = array_map('str_getcsv', file($usersCsvPath));
        $header = array_shift($users);

        foreach ($users as $row) {
            $row = array_combine($header, $row);
            $email = $row['Email User'];
            
            // Check if user already exists
            $existingUser = DB::table('users')->where('email', $email)->first();
            if ($existingUser) {
                continue; // Skip if user already exists
            }

            // Create user
            $user = DB::table('users')->insertGetId([
                'name' => $row['Full Name'],
                'email' => $email,
                'password' => Hash::make($row['Password']),
                'phone_number' => $row['Phone Number'],
                'position' => $row['Position'],
                'current_tenant_id' => 1, 
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Assign role to user
            $userModel = User::find($user);
            $userModel->syncRoles($row['Roles']);
        }
    }
}
