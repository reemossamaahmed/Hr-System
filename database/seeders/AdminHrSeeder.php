<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminHrSeeder extends Seeder
{
    public function run(): void
    {
         $user = User::updateOrCreate(
            [
                'email' => 'admin@hr.com'
            ],
            [
                'name' => 'Admin HR',
                // 'password' => '12345678',
                'password' => Hash::make('12345678'),
                'phone' => '0500000000',
                'position' => 'HR Manager',
                'base_salary' => 10000,
                'status' => 'active',
            ]
        );
        $user->assignRole('HR');
    }
}
