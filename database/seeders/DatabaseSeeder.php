<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === CREATE ROLES ===
        $rolePemohon = Role::firstOrCreate([
            'name' => 'Pemohon',
            'guard_name' => 'web',
        ]);

        $roleAdminTU = Role::firstOrCreate([
            'name' => 'Admin TU',
            'guard_name' => 'web',
        ]);

        $roleSeksi1 = Role::firstOrCreate([
            'name' => 'Seksi 1',
            'guard_name' => 'web',
        ]);

        $roleSeksi2 = Role::firstOrCreate([
            'name' => 'Seksi 2',
            'guard_name' => 'web',
        ]);

        $rolePimpinan = Role::firstOrCreate([
            'name' => 'Pimpinan',
            'guard_name' => 'web',
        ]);

        // === CREATE USERS ===
        $pemohon = User::firstOrCreate(
            ['email' => 'pemohon@example.com'],
            [
                'name' => 'Pemohon',
                'password' => Hash::make('pemohon123'),
            ]
        );
        $pemohon->assignRole($rolePemohon);

        $adminTU = User::firstOrCreate(
            ['email' => 'admintu@example.com'],
            [
                'name' => 'Admin TU',
                'password' => Hash::make('admintu123'),
            ]
        );
        $adminTU->assignRole($roleAdminTU);

        $seksi1 = User::firstOrCreate(
            ['email' => 'seksi1@example.com'],
            [
                'name' => 'Seksi 1',
                'password' => Hash::make('seksi1123'),
            ]
        );
        $seksi1->assignRole($roleSeksi1);

        $seksi2 = User::firstOrCreate(
            ['email' => 'seksi2@example.com'],
            [
                'name' => 'Seksi 2',
                'password' => Hash::make('seksi2123'),
            ]
        );
        $seksi2->assignRole($roleSeksi2);

        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@example.com'],
            [
                'name' => 'Pimpinan',
                'password' => Hash::make('pimpinan123'),
            ]
        );
        $pimpinan->assignRole($rolePimpinan);
    }
}
