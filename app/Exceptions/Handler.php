<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, \Throwable $e)
    {
        if ($e instanceof ThrottleRequestsException) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
            $minutes = ceil($retryAfter / 60);
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "So‘rovlar limiti oshib ketdi. {$minutes} daqiqadan so‘ng urinib ko‘ring.",
                ], 429);
            }
            return redirect()->back()
                ->with('error', "Juda ko‘p so‘rov yuborildi. {$minutes} daqiqadan so‘ng qayta urinib ko‘ring.");
        }
        return parent::render($request, $e);
    }

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
