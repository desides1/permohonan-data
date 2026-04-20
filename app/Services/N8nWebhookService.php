<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nWebhookService
{
    private string $webhookUrl;
    private int $timeout;

    public function __construct()
    {
        $this->webhookUrl = config('services.n8n.webhook_url', '');
        $this->timeout = config('services.n8n.timeout', 10);
    }

    /**
     * Kirim notifikasi ke n8n webhook secara real-time.
     * Jika gagal, NotificationLog tetap tersimpan di DB sebagai fallback.
     */
    public function dispatch(NotificationLog $notification): void
    {
        if (empty($this->webhookUrl)) {
            Log::warning('N8N webhook URL not configured. Notification saved to DB only.', [
                'notification_id' => $notification->id,
            ]);
            return;
        }

        try {
            $payload = $this->buildPayload($notification);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Secret' => config('services.n8n.webhook_secret', ''),
                ])
                ->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('N8N webhook dispatched successfully.', [
                    'notification_id' => $notification->id,
                    'type' => $notification->type,
                ]);
            } else {
                Log::error('N8N webhook returned error.', [
                    'notification_id' => $notification->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            // Gagal kirim webhook bukan masalah fatal.
            // NotificationLog tetap ada di DB — n8n fallback schedule bisa ambil nanti.
            Log::error('N8N webhook dispatch failed.', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kirim batch notifikasi (untuk assignment ke banyak petugas).
     */
    public function dispatchBatch(array $notifications): void
    {
        if (empty($notifications)) return;

        if (empty($this->webhookUrl)) {
            Log::warning('N8N webhook URL not configured. Batch notifications saved to DB only.', [
                'count' => count($notifications),
            ]);
            return;
        }

        try {
            $payload = [
                'notifications' => array_map(
                    fn(NotificationLog $notification) => $this->buildPayload($notification),
                    $notifications
                ),
                'count' => count($notifications),
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Secret' => config('services.n8n.webhook_secret', ''),
                ])
                ->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('N8N batch webhook dispatched successfully.', [
                    'count' => count($notifications),
                ]);
            } else {
                Log::error('N8N batch webhook returned error.', [
                    // 'count' => count($notifications),
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('N8N batch webhook dispatch failed.', [
                'count' => count($notifications),
                'error' => $e->getMessage(),
            ]);
        }
        // foreach ($notifications as $notification) {
        //     $this->dispatch($notification);
        // }
    }

    private function buildPayload(NotificationLog $notification): array
    {
        return [
            'notification_id' => $notification->id,
            'type'            => $notification->type,
            'channel'         => $notification->channel,
            'recipient' => [
                'user_id' => $notification->recipient_user_id,
                'email'   => $notification->recipient_email,
                'phone'   => $notification->recipient_phone,
                'name'    => $notification->payload['name']
                    ?? $notification->recipientUser?->name
                    ?? 'Pemohon',
            ],
            'ticket' => [
                'id'          => $notification->ticket_detail_id,
                'ticket_code' => $notification->payload['ticket_code'] ?? null,
            ],
            'payload'    => $notification->payload,
            'created_at' => $notification->created_at->toIso8601String(),

            // Callback URLs agar n8n tahu harus PATCH ke mana
            'callbacks' => [
                'mark_sent'   => url("/api/v1/notifications/{$notification->id}/mark-sent"),
                'mark_failed' => url("/api/v1/notifications/{$notification->id}/mark-failed"),
            ],
        ];
    }
}
