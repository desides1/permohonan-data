<?php

namespace App\Services;

use App\Mail\AccountCredentialMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PemohonAutoRegisterService
{
    /**
     * Cari user berdasarkan email, atau buat baru dengan role 'pemohon'.
     * Return: [User, string|null $plainPassword]
     * - $plainPassword !== null berarti user baru dibuat (untuk dikirim via notifikasi).
     */
    public function findOrRegister(array $data): array
    {
        $existing = User::where('email', $data['email'])->first();

        if ($existing) {
            // Pastikan punya role pemohon
            if (!$existing->hasRole('pemohon')) {
                $existing->assignRole('pemohon');
            }
            return [$existing, null];
        }

        // Generate password random
        $plainPassword = Str::random(10);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($plainPassword),
        ]);

        $user->assignRole('pemohon');

        return [$user, $plainPassword];
    }
}
