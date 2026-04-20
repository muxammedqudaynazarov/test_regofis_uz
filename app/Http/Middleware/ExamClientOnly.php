<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExamClientOnly
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-Exam-Client-RegOfis');

        if ($key !== config('exam.client_key')) {
            abort(403, 'Test faqat rasmiy imtihon dasturi orqali ochiladi');
        }

        return $next($request);
    }
}
