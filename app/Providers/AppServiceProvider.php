<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        Paginator::useBootstrap();

        // Ariza yuborish uchun — student_id asosida cheklash
        RateLimiter::for('application-store', function (Request $request) {
            return Limit::perHour(3)
                ->by(auth('student')->id() ?? $request->ip())
                ->response(function () {
                    return redirect()->back()
                        ->with('error', 'Siz bugun arizalaringizni yangilash limitiga yettingiz. 1 soatdan so\'ng qayta urinib ko\'ring.');
                });
        });

        // Imtihon boshlash uchun — 1 soatda faqat 5 marta
        RateLimiter::for('exam-start', function (Request $request) {
            return Limit::perHour(5)
                ->by(auth('student')->id() ?? $request->ip());
        });

        // Admin API operatsiyalari
        RateLimiter::for('admin-api', function (Request $request) {
            return Limit::perMinute(30)
                ->by(auth()->id() ?? $request->ip());
        });
    }
}
