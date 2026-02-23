<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TicketWorkflowController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RequestController;

// ─── Landing Page ───────────────────────────────────
Route::get('/', fn() => Inertia::render('LandingPage/Home'));
Route::get('/formulir', [RequestController::class, 'showRequest'])->name('landing.form');
Route::post('/mengirim', [RequestController::class, 'postRequest'])->name('landing.post');
Route::get('/lacak', fn() => Inertia::render('LandingPage/TrackRequest'));
Route::post('/track-ticket', [RequestController::class, 'trackTicket'])->name('ticket.track');
Route::get('/bantuan', fn() => Inertia::render('LandingPage/FAQ'));

// ─── Admin (Authenticated) ──────────────────────────
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->name('admin.')
    ->group(function () {

        // Beranda
        Route::get('/admin/beranda', [TicketController::class, 'showBeranda'])->name('beranda');

        // Data Permohonan
        Route::get('/data-permohonan', [TicketController::class, 'dataPermohonan'])->name('tickets.index');

        // Detail Tiket
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

        // Download Lampiran
        Route::get('/attachments/{attachment}/download', [TicketController::class, 'download'])->name('attachments.download');

        // ─── Workflow Routes ────────────────────────
        Route::prefix('tickets/{ticket}')->name('tickets.')->group(function () {
            // Admin TU: verifikasi
            Route::post('/verify', [TicketWorkflowController::class, 'verify'])->name('verify');

            // Pimpinan BPKH: setujui
            Route::post('/approve', [TicketWorkflowController::class, 'approve'])->name('approve');

            // Pimpinan PPKH: disposisi ke Seksi
            Route::post('/assign', [TicketWorkflowController::class, 'assign'])->name('assign');

            // Seksi: data siap
            Route::post('/ready', [TicketWorkflowController::class, 'markReady'])->name('markReady');

            // Pimpinan PPKH: review
            Route::post('/review-ppkh', [TicketWorkflowController::class, 'reviewPpkh'])->name('reviewPpkh');

            // Pimpinan PPKH: teruskan ke BPKH
            Route::post('/forward-bpkh', [TicketWorkflowController::class, 'forwardToBpkh'])->name('forwardToBpkh');

            // Pimpinan PPKH/BPKH: minta revisi
            Route::post('/revision', [TicketWorkflowController::class, 'requestRevision'])->name('requestRevision');

            // Pimpinan BPKH: final approve
            Route::post('/final-approve', [TicketWorkflowController::class, 'finalApprove'])->name('finalApprove');

            // Admin TU: selesaikan
            Route::post('/finalize', [TicketWorkflowController::class, 'finalize'])->name('finalize');

            // Tolak
            Route::post('/reject', [TicketWorkflowController::class, 'reject'])->name('reject');
        });
    });



// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
