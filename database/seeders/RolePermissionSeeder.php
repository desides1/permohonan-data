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
            'ticket.approve',
            'ticket.assign',
            'ticket.reject',
            'ticket.mark_ready',
            'ticket.review_ppkh',
            'ticket.forward_to_bpkh',
            'ticket.request_revision',
            'ticket.final_approve',
            'ticket.finalize',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─── Roles ──────────────────────────────────
        $pemohon = Role::firstOrCreate(['name' => 'pemohon']);
        $adminTu = Role::firstOrCreate(['name' => 'admin_tu']);
        $bpkh    = Role::firstOrCreate(['name' => 'pimpinan_bpkh']);
        $ppkh    = Role::firstOrCreate(['name' => 'pimpinan_ppkh']);
        $seksi   = Role::firstOrCreate(['name' => 'seksi']);

        // Pemohon
        $pemohon->syncPermissions([
            'ticket.create',
            'ticket.track',
        ]);

        // Admin TU
        $adminTu->syncPermissions([
            'ticket.verify',
            'ticket.reject',
            'ticket.finalize',
        ]);

        // Pimpinan BPKH
        $bpkh->syncPermissions([
            'ticket.approve',
            'ticket.reject',
            'ticket.final_approve',
            'ticket.request_revision',
        ]);

        // Pimpinan PPKH
        $ppkh->syncPermissions([
            'ticket.assign',
            'ticket.reject',
            'ticket.review_ppkh',
            'ticket.forward_to_bpkh',
            'ticket.request_revision',
        ]);

        // Seksi
        $seksi->syncPermissions([
            'ticket.mark_ready',
        ]);
    }
}
