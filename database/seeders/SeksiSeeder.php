<?php

namespace Database\Seeders;

use App\Models\Seksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SeksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $seksiList = [
            [
                'name'        => 'Seksi pengukuhan dan perencanaan kawasan hutan',
                'description' => 'Seksi yang bertanggung jawab untuk pengukuhan dan perencanaan kawasan hutan.',
                'petugas'     => [
                    ['name' => 'Andi Petugas A1', 'email' => 'andi.a1@bpkh.go.id'],
                    ['name' => 'Budi Petugas A2', 'email' => 'budi.a2@bpkh.go.id'],
                ],
            ],
            [
                'name'        => 'Seksi penggunaan dan perubahan kawasan hutan',
                'description' => 'Seksi yang bertanggung jawab untuk penggunaan dan perubahan kawasan hutan.',
                'petugas'     => [
                    ['name' => 'Citra Petugas B1', 'email' => 'citra.b1@bpkh.go.id'],
                    ['name' => 'Dina Petugas B2', 'email' => 'dina.b2@bpkh.go.id'],
                ],
            ],
            [
                'name'        => 'Seksi data dan informasi',
                'description' => 'Seksi yang bertanggung jawab untuk pengelolaan data dan informasi.',
                'petugas'     => [
                    ['name' => 'Eko Petugas C1', 'email' => 'eko.c1@bpkh.go.id'],
                ],
            ],
        ];

        foreach ($seksiList as $data) {
            $seksi = Seksi::firstOrCreate(
                ['name' => $data['name']],
                ['description' => $data['description']],
            );

            // Buat user petugas seksi
            foreach ($data['petugas'] as $petugas) {
                $user = User::firstOrCreate(
                    ['email' => $petugas['email']],
                    [
                        'name'     => $petugas['name'],
                        'password' => Hash::make('password'),
                        'seksi_id' => $seksi->id,
                    ],
                );

                // Pastikan punya role seksi
                if (!$user->hasRole('seksi')) {
                    $user->assignRole('seksi');
                }

                // Pastikan seksi_id terupdate jika user sudah ada
                if (!$user->wasRecentlyCreated && $user->seksi_id !== $seksi->id) {
                    $user->update(['seksi_id' => $seksi->id]);
                }
            }
        }

        $this->command->info('✅ Seksi & petugas seeder selesai.');
    }
}
