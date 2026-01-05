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

// Ticket Workflow Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/tickets/{ticket}/verify', [TicketWorkflowController::class, 'verify']);
    Route::post('/tickets/{ticket}/approve', [TicketWorkflowController::class, 'approve']);
    Route::post('/tickets/{ticket}/reject', [TicketWorkflowController::class, 'reject']);

    Route::post('/tickets/{ticket}/assign/seksi-1', [TicketWorkflowController::class, 'assignSeksi1']);
    Route::post('/tickets/{ticket}/assign/seksi-2', [TicketWorkflowController::class, 'assignSeksi2']);

    Route::post('/tickets/{ticket}/mark-ready', [TicketWorkflowController::class, 'markReady']);
    Route::post('/tickets/{ticket}/finalize', [TicketWorkflowController::class, 'finalize']);

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
});




// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });
