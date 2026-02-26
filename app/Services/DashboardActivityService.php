<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\TicketDetail;
use Spatie\Activitylog\Models\Activity;
use App\Models\TicketProgress;

class DashboardActivityService
{
    /**
     * Ambil riwayat aktivitas terakhir berdasarkan role user.
     *
     * Hanya menampilkan aktivitas pada tiket yang status-nya
     * terlihat oleh role tersebut.
     *
     * @param  string  $role
     * @param  int     $limit
     * @return array<int, array{
     *     id: int,
     *     ticket_code: string,
     *     action: string|null,
     *     description: string,
     *     from_status: string|null,
     *     from_status_label: string|null,
     *     from_status_color: string|null,
     *     to_status: string|null,
     *     to_status_label: string|null,
     *     to_status_color: string|null,
     *     causer: string|null,
     *     created_at: string,
     *     time_ago: string
     * }>
     */
    public function recentActivities(string $role, int $limit = 10): array
    {
        $visibleStatuses = TicketStatus::visibleForRole($role);

        if (empty($visibleStatuses)) {
            return [];
        }

        // ID ticket_progress yang statusnya terlihat oleh role ini
        $visibleProgressIds = TicketProgress::query()
            ->whereIn('status', array_map(fn($s) => $s->value, $visibleStatuses))
            ->pluck('id');

        if ($visibleProgressIds->isEmpty()) {
            return [];
        }

        return Activity::query()
            ->where('log_name', 'ticket-workflow')
            ->where('subject_type', TicketProgress::class)
            ->whereIn('subject_id', $visibleProgressIds)
            ->with('causer:id,name')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Activity $activity) {
                $fromStatus = $this->resolveStatus($activity->properties['from_status'] ?? null);
                $toStatus   = $this->resolveStatus($activity->properties['to_status'] ?? null);

                return [
                    'id'                => $activity->id,
                    'ticket_code'       => $activity->properties['ticket_code'] ?? '-',
                    'action'            => $activity->properties['action'] ?? null,
                    'description'       => $activity->description,
                    'from_status'       => $fromStatus?->value,
                    'from_status_label' => $fromStatus?->label(),
                    'from_status_color' => $fromStatus?->color(),
                    'to_status'         => $toStatus?->value,
                    'to_status_label'   => $toStatus?->label(),
                    'to_status_color'   => $toStatus?->color(),
                    'causer'            => $activity->causer?->name ?? 'Sistem',
                    'created_at'        => $activity->created_at->format('d M Y H:i'),
                    'time_ago'          => $activity->created_at->diffForHumans(),
                ];
            })
            ->all();
    }

    /**
     * Resolve string status menjadi enum TicketStatus.
     */
    private function resolveStatus(?string $value): ?TicketStatus
    {
        if ($value === null) {
            return null;
        }

        return TicketStatus::tryFrom($value);
    }
}
