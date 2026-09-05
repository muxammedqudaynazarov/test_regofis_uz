<?php

namespace App\Jobs;

use App\Models\Download;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 daqiqa
    public int $tries   = 2;

    public function __construct(
        private readonly int    $downloadId,
        private readonly string $exportClass,
        private readonly array  $params = []
    ) {}

    public function handle(): void
    {
        $download = Download::findOrFail($this->downloadId);
        $download->update(['status' => 'processing']);

        try {
            // Export obyektini dinamik yaratish
            $export = new $this->exportClass(...$this->params);

            $path = 'exports/' . $download->filename;
            Excel::store($export, $path, 'local');

            $download->update([
                'status' => 'ready',
                'path'   => $path,
            ]);

        } catch (\Exception $e) {
            Log::error("ExportJob xatolik [{$this->exportClass}]: " . $e->getMessage());
            $download->update([
                'status' => 'failed',
                'reason' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Download::where('id', $this->downloadId)->update([
            'status' => 'failed',
            'reason' => $exception->getMessage(),
        ]);
    }
}
