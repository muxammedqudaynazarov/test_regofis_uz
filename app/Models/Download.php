<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'filename',
        'path',
        'status',
        'reason',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'    => 'Navbatda',
            'processing' => 'Tayyorlanmoqda',
            'ready'      => 'Tayyor',
            'failed'     => 'Xatolik',
            default      => 'Noma\'lum',
        };
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'pending'    => 'secondary',
            'processing' => 'warning',
            'ready'      => 'success',
            'failed'     => 'danger',
            default      => 'secondary',
        };
    }
}
