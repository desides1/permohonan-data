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

    public function color(): string
    {
        return match ($this) {
            self::SENT               => '#3b82f6',  // blue-500
            self::VERIFIED           => '#facc15',  // yellow-500
            self::APPROVED           => '#8b5cf6',  // violet-500
            self::ASSIGNED           => '#f59e0b',  // amber-500
            self::READY              => '#06b6d4',  // cyan-500
            self::UNDER_REVIEW_PPKH  => '#f97316',  // orange-500
            self::UNDER_REVIEW_BPKH  => '#a855f7',  // purple-500
            self::REVISION           => '#ef4444',  // red-500
            self::FINAL_APPROVED     => '#10b981',  // emerald-500
            self::COMPLETED          => '#22c55e',  // green-500
            self::REJECTED           => '#dc2626',  // red-600
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SENT               => 'Send',
            self::VERIFIED           => 'ShieldCheck',
            self::APPROVED           => 'ThumbsUp',
            self::ASSIGNED           => 'UserCheck',
            self::READY              => 'PackageCheck',
            self::UNDER_REVIEW_PPKH  => 'Search',
            self::UNDER_REVIEW_BPKH  => 'ScanSearch',
            self::REVISION           => 'RotateCcw',
            self::FINAL_APPROVED     => 'BadgeCheck',
            self::COMPLETED          => 'CircleCheckBig',
            self::REJECTED           => 'CircleX',
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'color' => $this->color(),
            'icon'  => $this->icon(),
        ];
    }

    public static function allToArray(): array
    {
        return array_map(fn($status) => $status->toArray(), self::cases());
    }

    public function metaByLabel(): array
    {
        $map = [];
        foreach (self::cases() as $status) {
            $map[$status->label()] = [
                'value' => $status->value,
                'color' => $status->color(),
                'icon'  => $status->icon(),
            ];
        }
        return $map;
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
