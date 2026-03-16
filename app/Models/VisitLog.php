<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visit_type',
        'path',
        'route_name',
        'full_url',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'device_type',
        'browser',
        'is_authenticated',
        'visited_at',
    ];

    protected $casts = [
        'is_authenticated' => 'boolean',
        'visited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSite(Builder $query): Builder
    {
        return $query->where('visit_type', 'site');
    }

    public function scopePage(Builder $query): Builder
    {
        return $query->where('visit_type', 'page');
    }
}
