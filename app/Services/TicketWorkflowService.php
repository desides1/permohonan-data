<?php

namespace App\Services;

use App\Enums\TicketAssignment;
use App\Enums\TicketStatus;
use App\Models\TicketProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketWorkflowService
{
    public function __construct(
        private readonly TicketActivityLogger $logger,
    ) {}

    /**
     * Admin TU verifikasi → VERIFIED, diteruskan ke Pimpinan BPKH.
     */
    public function verify(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::VERIFIED, 'Diverifikasi oleh Admin TU.');

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::PIMPINAN_BPKH,
                'assigned_by' => $performer->id,
                'notes'       => 'Tiket diverifikasi dan diteruskan ke Pimpinan BPKH.',
            ]);

            $this->logger->log($ticket, 'verify', "Tiket #{$ticket->ticketDetails->ticket_code} diverifikasi oleh {$performer->name}.");
        });
    }

    /**
     * Pimpinan BPKH approve → APPROVED, diteruskan ke Pimpinan PPKH.
     */
    public function approve(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::APPROVED, 'Disetujui oleh Pimpinan BPKH.');

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::PIMPINAN_PPKH,
                'assigned_by' => $performer->id,
                'notes'       => 'Tiket disetujui dan diteruskan ke Pimpinan PPKH.',
            ]);

            $this->logger->log($ticket, 'approve', "Tiket #{$ticket->ticketDetails->ticket_code} disetujui oleh {$performer->name}.");
        });
    }

    /**
     * Pimpinan PPKH disposisi ke Seksi → ASSIGNED.
     */
    public function assignToSeksi(TicketProgress $ticket, User $performer, User $petugasSeksi, ?string $notes = null): void
    {
        DB::transaction(function () use ($ticket, $performer, $petugasSeksi, $notes) {
            $SeksiName = $petugasSeksi->seksi ? $petugasSeksi->seksi->name : 'Seksi';

            $ticket->transitionTo(TicketStatus::ASSIGNED, $notes ?? 'Ditugaskan ke Seksi.');

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::SEKSI,
                'assigned_to_user_id' => $petugasSeksi->id,
                'assigned_by' => $performer->id,
                'seksi_id'   => $petugasSeksi->seksi_id,
                'notes'       => $notes ?? 'Disposisi ke Seksi untuk penyiapan data.',
            ]);

            $this->logger->log($ticket, 'assign', "Tiket #{$ticket->ticketDetails->ticket_code} ditugaskan ke {$petugasSeksi->name} ({$SeksiName}) oleh {$performer->name}.", [
                'assigned_to' => TicketAssignment::SEKSI->label(),
                'assigned_to_user_id' => $petugasSeksi->id,
                'assigned_to_user_name' => $petugasSeksi->name,
                'seksi_id' => $petugasSeksi->seksi_id,
                'seksi_name' => $SeksiName,
            ]);
        });
    }

    /**
     * Seksi menandai data siap → READY.
     */
    public function markReady(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::READY, 'Data telah disiapkan oleh Seksi.');

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::PIMPINAN_PPKH,
                'assigned_by' => $performer->id,
                'notes'       => 'Data permohonan siap, dikembalikan ke Pimpinan PPKH untuk review.',
            ]);

            $this->logger->log($ticket, 'mark_ready', "Data tiket #{$ticket->ticketDetails->ticket_code} ditandai siap oleh {$performer->name}.");
        });
    }

    /**
     * Pimpinan PPKH mulai review → UNDER_REVIEW_PPKH.
     */
    public function reviewByPpkh(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::UNDER_REVIEW_PPKH, 'Sedang ditinjau oleh Pimpinan PPKH.');

            $this->logger->log($ticket, 'review_ppkh', "Tiket #{$ticket->ticketDetails->ticket_code} sedang ditinjau oleh Pimpinan PPKH ({$performer->name}).");
        });
    }

    /**
     * Pimpinan PPKH meneruskan ke BPKH → UNDER_REVIEW_BPKH.
     */
    public function forwardToBpkh(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::UNDER_REVIEW_BPKH, 'Diteruskan ke Pimpinan BPKH untuk final review.');

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::PIMPINAN_BPKH,
                'assigned_by' => $performer->id,
                'notes'       => 'Data telah direview PPKH, diteruskan ke Pimpinan BPKH.',
            ]);

            $this->logger->log($ticket, 'forward_to_bpkh', "Tiket #{$ticket->ticketDetails->ticket_code} diteruskan ke Pimpinan BPKH oleh {$performer->name}.");
        });
    }

    /**
     * Pimpinan PPKH/BPKH minta revisi → REVISION, dikembalikan ke Seksi.
     */
    public function requestRevision(TicketProgress $ticket, User $performer, string $reason): void
    {
        DB::transaction(function () use ($ticket, $performer, $reason) {
            $ticket->transitionTo(TicketStatus::REVISION, "Revisi: {$reason}");

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::SEKSI,
                'assigned_by' => $performer->id,
                'notes'       => "Diminta revisi: {$reason}",
            ]);

            $this->logger->log($ticket, 'request_revision', "Revisi diminta untuk tiket #{$ticket->ticketDetails->ticket_code} oleh {$performer->name}.", [
                'reason' => $reason,
            ]);
        });
    }

    /**
     * Pimpinan BPKH final approve → FINAL_APPROVED.
     */
    public function finalApprove(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::FINAL_APPROVED, 'Disetujui final oleh Pimpinan BPKH.');

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::ADMIN_TU,
                'assigned_by' => $performer->id,
                'notes'       => 'Disetujui final, dikembalikan ke Admin TU untuk penyerahan.',
            ]);

            $this->logger->log($ticket, 'final_approve', "Tiket #{$ticket->ticketDetails->ticket_code} disetujui final oleh {$performer->name}.");
        });
    }

    /**
     * Admin TU menyelesaikan → COMPLETED.
     */
    public function finalize(TicketProgress $ticket, User $performer): void
    {
        DB::transaction(function () use ($ticket, $performer) {
            $ticket->transitionTo(TicketStatus::COMPLETED, 'Permohonan diselesaikan oleh Admin TU.');

            $this->logger->log($ticket, 'finalize', "Tiket #{$ticket->ticketDetails->ticket_code} diselesaikan oleh {$performer->name}.");
        });
    }

    /**
     * Penolakan.
     */
    public function reject(TicketProgress $ticket, User $performer, string $reason): void
    {
        DB::transaction(function () use ($ticket, $performer, $reason) {
            $ticket->transitionTo(TicketStatus::REJECTED, "Ditolak: {$reason}");

            $this->logger->log($ticket, 'reject', "Tiket #{$ticket->ticketDetails->ticket_code} ditolak oleh {$performer->name}.", [
                'reason' => $reason,
            ]);
        });
    }
}
