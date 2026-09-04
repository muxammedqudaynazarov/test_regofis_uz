<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogPageViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int    $userId,
        private readonly string $userModel,
        private readonly string $ip,
        private readonly string $userAgent,
        private readonly string $url,
        private readonly string $path
    )
    {
    }

    public function handle(): void
    {
        activity('page_view')
            ->causedByAnonymous()
            ->withProperties([
                'user_id' => $this->userId,
                'ip' => $this->ip,
                'user_agent' => $this->userAgent,
                'url' => $this->url,
                'method' => 'GET',
            ])
            ->log("Sahifa: " . $this->path);
    }
}
