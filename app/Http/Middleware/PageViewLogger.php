<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PageViewLogger
{
    // Faqat shu sahifalar loglanadi — har bir sahifa emas
    private array $watchedPaths = [
        'home/final-results',
        'home/applications',
        'home/statistics',
        'home/subjects-register',
        'home/lessons',
        'student/tests',
        'student/results',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && $request->isMethod('get')) {
            $currentPath = $request->path();

            $shouldLog = collect($this->watchedPaths)
                ->contains(fn($path) => str_starts_with($currentPath, $path));

            if ($shouldLog) {
                activity('page_view')
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'ip'         => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url'        => $request->fullUrl(),
                        'method'     => 'GET',
                    ])
                    ->log('Sahifa: ' . $currentPath);
            }
        }

        return $response;
    }
}
