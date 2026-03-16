<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateN8nToken extends Command
{
    protected $signature = 'n8n:create-token';
    protected $description = 'Buat user service account + API token untuk N8N';

    public function handle(): void
    {
        // Buat atau ambil user service khusus N8N
        $user = User::firstOrCreate(
            ['email' => 'n8n-service@bpkh.internal'],
            [
                'name'     => 'N8N Service',
                'password' => Hash::make(\Illuminate\Support\Str::random(32)),
            ]
        );

        // Hapus token lama
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('n8n-notification-service', ['notifications:read', 'notifications:write']);

        $this->info('');
        $this->info('═══════════════════════════════════════════════');
        $this->info('  N8N API Token berhasil dibuat!');
        $this->info('═══════════════════════════════════════════════');
        $this->info('');
        $this->line('  Token (simpan ini di N8N):');
        $this->newLine();
        $this->warn("  {$token->plainTextToken}");
        $this->newLine();
        $this->info('  Base URL: ' . config('app.url') . '/api/v1/notifications');
        $this->info('');
        $this->info('  Endpoints:');
        $this->line('  GET  /api/v1/notifications/pending?channel=email');
        $this->line('  GET  /api/v1/notifications/pending?channel=whatsapp');
        $this->line('  PATCH /api/v1/notifications/{id}/mark-sent');
        $this->line('  PATCH /api/v1/notifications/{id}/mark-failed');
        $this->line('  PATCH /api/v1/notifications/mark-sent-batch');
        $this->line('  GET  /api/v1/notifications/stats');
        $this->info('═══════════════════════════════════════════════');
    }
}
