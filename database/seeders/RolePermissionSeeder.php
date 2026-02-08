<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'ticket.create',
            'ticket.track',
            'ticket.verify',
            'ticket.assign',
            'ticket.approve',
            'ticket.reject',
            'ticket.finalize',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $pemohon = Role::firstOrCreate(['name' => 'pemohon']);
        $adminTu = Role::firstOrCreate(['name' => 'admin_tu']);
        $bpkh    = Role::firstOrCreate(['name' => 'pimpinan_bpkh']);
        $ppkh    = Role::firstOrCreate(['name' => 'pimpinan_ppkh']);
        $seksi   = Role::firstOrCreate(['name' => 'seksi']);

        // Pemohon
        $pemohon->givePermissionTo([
            'ticket.create',
            'ticket.track',
        ]);

        // Admin TU
        $adminTu->givePermissionTo([
            'ticket.verify',
            'ticket.assign',
            'ticket.reject',
        ]);

        // Pimpinan BPKH
        $bpkh->givePermissionTo([
            'ticket.assign',
            'ticket.approve',
            'ticket.reject',
        ]);

        // Pimpinan PPKH
        $ppkh->givePermissionTo([
            'ticket.assign',
            'ticket.approve',
            'ticket.reject',
        ]);

        // Seksi
        $seksi->givePermissionTo([
            'ticket.assign',
            'ticket.finalize',
        ]);
    }
}
