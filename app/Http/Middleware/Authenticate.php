<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Agar so'rov student login sahifasiga o'xshasa yoki shunga mos mantiq bo'lsa
            if ($request->is('student/*')) {
                return route('login.student');
            }
            return route('login.user');
        }
    }
}
