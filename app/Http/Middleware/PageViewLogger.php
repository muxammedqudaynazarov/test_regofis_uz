<?php

namespace App\Http\Middleware;

use App\Jobs\LogPageViewJob;
use Closure;
use Illuminate\Http\Request;

class PageViewLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && $request->isMethod('get')) {
            // DB ga to'g'ri yozish o'rniga queue'ga yuborish
            dispatch(new LogPageViewJob(
                auth()->id(),
                auth()->user()->getTable(), // 'users'
                $request->ip(),
                $request->userAgent(),
                $request->fullUrl(),
                $request->path()
            ))->onQueue('logs');
        }

        return $response;
    }
}
