<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PageViewLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Faqat login qilgan foydalanuvchilar va GET so'rovlar uchun log yozamiz
        if (auth()->check() && $request->isMethod('get')) {
            activity('page_view')
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method()
                ])
                ->log("Sahifa: " . $request->path());
        }

        return $response;
    }
}
