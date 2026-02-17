<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TicketWorkflowController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RequestController;

// Landing Page Routes
Route::get('/', fn() => Inertia::render('LandingPage/Home'));
Route::get('/formulir', [RequestController::class, 'showRequest'])->name('landing.form');
Route::post('/mengirim', [RequestController::class, 'postRequest'])->name('landing.post');
Route::get('/lacak', fn() => Inertia::render('LandingPage/TrackRequest'));
Route::post('/track-ticket', [RequestController::class, 'trackTicket'])->name('ticket.track');
Route::get('/bantuan', fn() => Inertia::render('LandingPage/FAQ'));

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->name('admin.')->group(function () {
    Route::get('/admin/beranda', function () {
        return Inertia::render('Admin/Beranda/Beranda');
    })->name('beranda');
    Route::get('/data-permohonan', [TicketController::class, 'dataPermohonan'])
        ->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    Route::post('/tickets/{ticket}/verify', [TicketWorkflowController::class, 'verify'])
        ->name('tickets.verify');

    Route::post('/tickets/{ticket}/approve', [TicketWorkflowController::class, 'approve'])
        ->name('tickets.approve');
    Route::post('/tickets/{ticket}/reject', [TicketWorkflowController::class, 'reject'])
        ->name('tickets.reject');

    Route::post('/tickets/{ticket}/assign', [TicketWorkflowController::class, 'assign'])
        ->name('tickets.assign');

    Route::post('/tickets/{ticket}/ready', [TicketWorkflowController::class, 'markReady'])
        ->name('tickets.markReady');

    Route::post('/tickets/{ticket}/finalize', [TicketWorkflowController::class, 'finalize'])
        ->name('tickets.finalize');

    Route::get('/attachments/{attachment}/download', [TicketController::class, 'download'])
        ->name('attachments.download');
});



// Ticket Workflow Routes
Route::prefix('admin')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('admin.')->group(function () {
    Route::get('/beranda', [TicketController::class, 'showBeranda'])->name('beranda');


    // Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
});



// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
