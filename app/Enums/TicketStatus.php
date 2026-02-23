<?php

namespace App\Enums;

enum TicketStatus: string
{
    case SENT = 'sent';
    case VERIFIED = 'verified';
    case APPROVED = 'approved';
    case ASSIGNED = 'assigned';
    case READY = 'ready';
    case UNDER_REVIEW_PPKH = 'under_review_ppkh';
    case UNDER_REVIEW_BPKH = 'under_review_bpkh';
    case REVISION = 'revision';
    case FINAL_APPROVED = 'final_approved';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::SENT => 'Dikirim',
            self::VERIFIED => 'Diverifikasi Admin TU',
            self::APPROVED => 'Disetujui Pimpinan',
            self::ASSIGNED => 'Ditugaskan',
            self::READY => 'Data Siap',
            self::UNDER_REVIEW_PPKH => 'Dalam Peninjauan PPKH',
            self::UNDER_REVIEW_BPKH => 'Dalam Peninjauan BPKH',
            self::REVISION => 'Revisi',
            self::FINAL_APPROVED => 'Disetujui Final',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::REJECTED]);
    }

    public static function visibleForRole(string $role): array
    {
        return match ($role) {
            'admin_tu' => [
                self::SENT,
                self::VERIFIED,
                self::FINAL_APPROVED,
                self::COMPLETED,
                self::REJECTED,
            ],
            'pimpinan_bpkh' => [
                self::VERIFIED,
                self::APPROVED,
                self::REJECTED,
                self::UNDER_REVIEW_BPKH,
                self::FINAL_APPROVED,
            ],
            'pimpinan_ppkh' => [
                self::APPROVED,
                self::ASSIGNED,
                self::UNDER_REVIEW_PPKH,
                self::REVISION,
            ],
            'seksi' => [
                self::ASSIGNED,
                self::READY,
                self::REVISION,
            ],
            default => [],
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::SENT               => [self::VERIFIED, self::REJECTED],
            self::VERIFIED           => [self::APPROVED, self::REJECTED],
            self::APPROVED           => [self::ASSIGNED, self::UNDER_REVIEW_PPKH, self::REJECTED],
            self::ASSIGNED           => [self::READY, self::REJECTED],
            self::READY              => [self::UNDER_REVIEW_PPKH],
            self::UNDER_REVIEW_PPKH  => [self::UNDER_REVIEW_BPKH, self::REVISION],
            self::UNDER_REVIEW_BPKH  => [self::FINAL_APPROVED, self::REVISION],
            self::REVISION           => [self::READY],
            self::FINAL_APPROVED     => [self::COMPLETED],
            self::COMPLETED          => [],
            self::REJECTED           => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }
}
