<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminHrSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@hr.com'
            ],
            [
                'name' => 'Admin HR',
                'password' => '12345678',
                'role' => 'hr',
                'phone' => '0500000000',
                // 'department' => 'HR',
                'position' => 'HR Manager',
                'base_salary' => 10000,
                'status' => 'active',
            ]
        );
    }
}
