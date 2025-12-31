<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing Page Routes
Route::get('/', fn() => Inertia::render('LandingPage/Home'));
Route::get('/formulir', fn() => Inertia::render('LandingPage/Form'));
Route::get('/lacak', fn() => Inertia::render('LandingPage/TrackRequest'));
Route::get('/bantuan', fn() => Inertia::render('LandingPage/FAQ'));




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
