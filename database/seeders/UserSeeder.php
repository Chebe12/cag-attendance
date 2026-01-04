<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        DB::table('users')->insert([
            [
                'employee_no' => 'CAG001',
                'firstname' => 'CA',
                'middlename' => null,
                'lastname' => 'Global',
                'email' => 'admin@caglobal.com',
                'email_verified_at' => now(),
                'password' => Hash::make('CAGLOBAL@26'),
                'phone' => '+234 8169135148',
                'avatar' => null,
                'department' => 'Administration',
                'position' => 'System Administrator',
                'user_type' => 'admin',
                'status' => 'active',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
