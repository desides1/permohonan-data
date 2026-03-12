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
    public function reviewByPpkh(TicketProgress $ticket, User $user): void
    {
        DB::transaction(function () use ($ticket, $user) {
            $fromStatus = $ticket->status;
            $ticket->update(['status' => TicketStatus::UNDER_REVIEW_PPKH]);

            $this->logger->log($ticket, $user, 'review_ppkh', [
                'action' => 'review_ppkh',
                'from_status' => $fromStatus->value,
                'to_status' => TicketStatus::UNDER_REVIEW_PPKH->value,
            ]);
        });
    }

    /**
     * Pimpinan PPKH meneruskan ke BPKH → UNDER_REVIEW_BPKH.
     */
    public function forwardToBpkh(TicketProgress $ticket, User $user): void
    {
        DB::transaction(function () use ($ticket, $user) {
            $fromStatus = $ticket->status;
            $ticket->update([
                'status' => TicketStatus::UNDER_REVIEW_BPKH,
            ]);
            // $ticket->assignments()->create([
            //     'assignment'  => TicketAssignment::PIMPINAN_BPKH,
            //     'assigned_by' => $user->id,
            //     'notes'       => 'Data telah direview PPKH, diteruskan ke Pimpinan BPKH.',
            // ]);


            $this->logger->log($ticket, $user, 'forward_to_bpkh', [
                'action' => 'forward_to_bpkh',
                'from_status' => $fromStatus->value,
                'to_status' => TicketStatus::UNDER_REVIEW_BPKH->value,
            ]);
        });
    }

    /**
     * Pimpinan PPKH/BPKH minta revisi → REVISION, dikembalikan ke Seksi.
     */
    public function requestRevision(TicketProgress $ticket, User $user, string $reason): void
    {
        DB::transaction(function () use ($ticket, $user, $reason) {
            $fromStatus = $ticket->status;
            $ticket->update(['status' => TicketStatus::REVISION]);

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::SEKSI,
                'assigned_by' => $user->id,
                'notes'       => "Diminta revisi: {$reason}",
            ]);

            $this->logger->log($ticket, $user, 'request_revision', [
                'action' => 'request_revision',
                'from_status' => $fromStatus->value,
                'to_status' => TicketStatus::REVISION->value,
                'reason' => $reason,
            ]);
        });
    }

    /**
     * Pimpinan BPKH final approve → FINAL_APPROVED.
     */
    public function finalApprove(TicketProgress $ticket, User $user): void
    {
        DB::transaction(function () use ($ticket, $user) {
            $fromStatus = $ticket->status;
            $ticket->update(['status' => TicketStatus::FINAL_APPROVED]);

            $ticket->assignments()->create([
                'assignment'  => TicketAssignment::ADMIN_TU,
                'assigned_by' => $user->id,
                'notes'       => 'Disetujui final, dikembalikan ke Admin TU untuk penyerahan.',
            ]);

            $this->logger->log($ticket, $user, 'final_approve', [
                'action' => 'final_approve',
                'from_status' => $fromStatus->value,
                'to_status' => TicketStatus::FINAL_APPROVED->value,
            ]);
        });
    }

    /**
     * Admin TU menyelesaikan → COMPLETED.
     */
    public function finalize(TicketProgress $ticket, User $user): void
    {
        DB::transaction(function () use ($ticket, $user) {
            $fromStatus = $ticket->status;
            $ticket->update(['status' => TicketStatus::COMPLETED]);

            $this->logger->log($ticket, $user, 'finalize', [
                'action' => 'finalize',
                'from_status' => $fromStatus->value,
                'to_status' => TicketStatus::COMPLETED->value,
            ]);
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
