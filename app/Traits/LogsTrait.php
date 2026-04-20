<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

trait LogsTrait
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logExcept([
                'correct', 'type',
            ])
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }
}
