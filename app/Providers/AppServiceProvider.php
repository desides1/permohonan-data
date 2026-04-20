<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Log authentication events
        $listener = new LogAuthenticationActivity();
        Event::listen(Login::class, [$listener, 'handleLogin']);
        Event::listen(Logout::class, [$listener, 'handleLogout']);
        Event::listen(PasswordReset::class, [$listener, 'handlePasswordReset']);
    }
}
