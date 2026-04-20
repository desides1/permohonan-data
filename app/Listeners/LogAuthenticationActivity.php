<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;

class LogAuthenticationActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof Login) {
            $this->handleLogin($event);
        } elseif ($event instanceof Logout) {
            $this->handleLogout($event);
        } elseif ($event instanceof PasswordReset) {
            $this->handlePasswordReset($event);
        }
    }
    public function handleLogin(Login $event): void
    {
        activity('user-activity')
            ->causedBy($event->user->id)
            ->withProperties([
                'action'     => 'login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("{$event->user->name} berhasil login.");
    }

    public function handleLogout(Logout $event): void
    {
        if (!$event->user) return;

        activity('user-activity')
            ->causedBy($event->user->id)
            ->withProperties([
                'action'     => 'logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("{$event->user->name} logout.");
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        activity('user-activity')
            ->causedBy($event->user->id)
            ->withProperties([
                'action'     => 'password_reset',
                'ip_address' => request()->ip(),
            ])
            ->log("{$event->user->name} mereset password.");
    }
}
