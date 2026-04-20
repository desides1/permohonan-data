<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $user = $request->user();

        if ($user->hasRole('pemohon')) {
            return redirect()->intended('/pemohon/hasil-permohonan');
        }

        return redirect()->intended('/admin/beranda');
    }
}