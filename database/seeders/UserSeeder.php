<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Pemohon Umum',
                'email' => 'pemohon@ticket.test',
                'role' => 'pemohon',
            ],
            [
                'name' => 'Admin Tata Usaha',
                'email' => 'admin.tu@ticket.test',
                'role' => 'admin_tu',
            ],
            [
                'name' => 'Pimpinan BPKH',
                'email' => 'pimpinan.bpkh@ticket.test',
                'role' => 'pimpinan_bpkh',
            ],
            [
                'name' => 'Pimpinan PPKH',
                'email' => 'pimpinan.ppkh@ticket.test',
                'role' => 'pimpinan_ppkh',
            ],
            [
                'name' => 'Seksi 1',
                'email' => 'seksi1@ticket.test',
                'role' => 'seksi',
            ],
            [
                'name' => 'Seksi 2',
                'email' => 'seksi2@ticket.test',
                'role' => 'seksi',
            ],
        ];

        foreach ($users as $user) {
            $created = User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $created->syncRoles([$user['role']]);
        }
    }
}
