<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\TicketDetail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;


class FeedbackController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search'));

        $baseQuery = Feedback::query()
            ->with(['ticketDetail:id,ticket_code']);

        if ($search !== '') {
            $baseQuery->whereHas('ticketDetail', function ($query) use ($search) {
                $query->where('ticket_code', 'like', "%{$search}%");
            });
        }
        $allFeedbacks = (clone $baseQuery)->latest()->get();

        $feedbacks = (clone $baseQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function (Feedback $feedback) {
                return [
                    'id' => $feedback->id,
                    'ticket_code' => $feedback->ticketDetail?->ticket_code ?? '-',
                    'service_usability' => $this->formatUsability($feedback->service_usability),
                    'service_usability_raw' => $feedback->service_usability,
                    'service_satisfaction' => $this->formatSatisfaction($feedback->service_satisfaction),
                    'service_satisfaction_raw' => $feedback->service_satisfaction,
                    'illegal_fee_indication' => $this->formatIllegalFee($feedback->illegal_fee_indication),
                    'illegal_fee_indication_raw' => $feedback->illegal_fee_indication,
                    'suggestions' => $feedback->suggestions,
                    'created_at' => $feedback->created_at?->format('d M Y H:i'),
                ];
            });

        $totalFeedbacks = $allFeedbacks->count();

        $summary = [
            'total_feedback' => $totalFeedbacks,
            'positive_satisfaction' => $allFeedbacks
                ->whereIn('service_satisfaction', ['sangat_puas', 'puas'])
                ->count(),
            'illegal_fee_reports' => $allFeedbacks
                ->where('illegal_fee_indication', 'ada')
                ->count(),
            'with_suggestions' => $allFeedbacks
                ->filter(fn($item) => filled($item->suggestions))
                ->count(),
        ];

        $charts = [
            'service_usability' => $this->buildDistribution($allFeedbacks, 'service_usability', [
                'sangat_mudah' => 'Sangat mudah',
                'mudah' => 'Mudah',
                'cukup' => 'Cukup',
                'sulit' => 'Sulit',
            ]),
            'service_satisfaction' => $this->buildDistribution($allFeedbacks, 'service_satisfaction', [
                'sangat_puas' => 'Sangat puas',
                'puas' => 'Puas',
                'cukup_puas' => 'Cukup puas',
                'tidak_puas' => 'Tidak puas',
            ]),
            'illegal_fee_indication' => $this->buildDistribution($allFeedbacks, 'illegal_fee_indication', [
                'tidak_ada' => 'Tidak ada',
                'ada' => 'Ada',
                'tidak_tahu' => 'Tidak tahu',
            ]),
        ];

        return Inertia::render('Feedback/Index', [
            'filters' => [
                'search' => $search,
            ],
            'feedbacks' => $feedbacks,
            'summary' => $summary,
            'charts' => $charts,
        ]);
    }
    public function showFeedbackForm(Request $request)
    {
        return Inertia::render('Feedback/Form', [
            'ticket_code' => $request->string('ticket_code')->toString(),
        ]);
    }

    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'ticket_code'             => 'required|string',
            'service_usability'       => 'required|string|max:100',
            'service_satisfaction'    => 'required|string|max:100',
            'illegal_fee_indication'  => 'required|string|max:100',
            'suggestions'             => 'nullable|string|max:1000',
        ]);

        $detail = TicketDetail::where('ticket_code', $validated['ticket_code'])->first();

        if (! $detail) {
            throw ValidationException::withMessages([
                'ticket_code' => 'Kode tiket tidak ditemukan. Silakan periksa kembali.',
            ]);
        }

        Feedback::create([
            'ticket_detail_id'       => $detail->id,
            'service_usability'      => $validated['service_usability'],
            'service_satisfaction'   => $validated['service_satisfaction'],
            'illegal_fee_indication' => $validated['illegal_fee_indication'],
            'suggestions'            => $validated['suggestions'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Terima kasih. Feedback Anda berhasil dikirim.');
    }

    private function buildDistribution($feedbacks, string $field, array $labels): array
    {
        $total = $feedbacks->count();

        return collect($labels)->map(function ($label, $value) use ($feedbacks, $field, $total) {
            $count = $feedbacks->where($field, $value)->count();

            return [
                'key' => $value,
                'label' => $label,
                'value' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        })->values()->all();
    }

    private function formatUsability(?string $value): string
    {
        return match ($value) {
            'sangat_mudah' => 'Sangat mudah',
            'mudah' => 'Mudah',
            'cukup' => 'Cukup',
            'sulit' => 'Sulit',
            default => '-',
        };
    }

    private function formatSatisfaction(?string $value): string
    {
        return match ($value) {
            'sangat_puas' => 'Sangat puas',
            'puas' => 'Puas',
            'cukup_puas' => 'Cukup puas',
            'tidak_puas' => 'Tidak puas',
            default => '-',
        };
    }

    private function formatIllegalFee(?string $value): string
    {
        return match ($value) {
            'tidak_ada' => 'Tidak ada',
            'ada' => 'Ada',
            'tidak_tahu' => 'Tidak tahu',
            default => '-',
        };
    }
}
