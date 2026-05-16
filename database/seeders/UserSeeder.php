<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@scrs.test', 'role' => 'super_admin', 'phone' => '081234567890'],
            ['name' => 'Admin Desa', 'email' => 'admin@scrs.test', 'role' => 'admin_desa', 'phone' => '081234567891'],
            ['name' => 'Petugas Lapangan', 'email' => 'petugas@scrs.test', 'role' => 'petugas', 'phone' => '081234567892'],
            ['name' => 'Budi Santoso', 'email' => 'budi@scrs.test', 'role' => 'warga', 'phone' => '081234567893'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@scrs.test', 'role' => 'warga', 'phone' => '081234567894'],
        ];

        foreach ($users as $user) {
            User::create([
                ...$user,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
