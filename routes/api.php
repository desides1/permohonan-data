<?php

use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\TicketWorkflowApiController;
use App\Http\Controllers\Api\AssignmentNotificationApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.v1.')->group(function () {

    // ─── Ticket Data ────────────────────────────────
    Route::get('/tickets', [TicketApiController::class, 'myTickets'])->name('tickets.index');
    Route::get('/tickets/unread-count', [TicketApiController::class, 'unreadCount'])->name('tickets.unreadCount');
    Route::get('/tickets/{ticket}', [TicketApiController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/activity-log', [TicketApiController::class, 'activityLog'])->name('tickets.activityLog');

    // ─── Workflow Actions ───────────────────────────
    Route::prefix('tickets/{ticket}')->name('tickets.')->group(function () {
        Route::post('/verify', [TicketWorkflowApiController::class, 'verify'])->name('verify');
        Route::post('/approve', [TicketWorkflowApiController::class, 'approve'])->name('approve');
        Route::post('/assign', [TicketWorkflowApiController::class, 'assign'])->name('assign');
        Route::post('/mark-ready', [TicketWorkflowApiController::class, 'markReady'])->name('markReady');
        Route::post('/review-ppkh', [TicketWorkflowApiController::class, 'reviewPpkh'])->name('reviewPpkh');
        Route::post('/forward-bpkh', [TicketWorkflowApiController::class, 'forwardToBpkh'])->name('forwardToBpkh');
        Route::post('/requestrevision', [TicketWorkflowApiController::class, 'requestRevision'])->name('requestRevision');
        Route::post('/final-approve', [TicketWorkflowApiController::class, 'finalApprove'])->name('finalApprove');
        Route::post('/finalize', [TicketWorkflowApiController::class, 'finalize'])->name('finalize');
        Route::post('/reject', [TicketWorkflowApiController::class, 'reject'])->name('reject');
    });

    Route::prefix('assignments')->group(function () {
        Route::get('/', [AssignmentNotificationApiController::class, 'myAssignments'])->name('assignments.my');
        Route::get('/unread-count', [AssignmentNotificationApiController::class, 'unreadAssignmentCount'])->name('assignments.unreadCount');
        Route::get('/by-role', [AssignmentNotificationApiController::class, 'assignmentsByRole'])->name('assignments.byRole');
        Route::patch('/{assignment}/mark-read', [AssignmentNotificationApiController::class, 'markAsRead'])->name('assignments.markRead');
        Route::patch('/mark-all-read', [AssignmentNotificationApiController::class, 'markAllAsRead'])->name('assignments.markAllRead');
    });
});
