<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentTransferLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'package_type',
        'item_count',
        'summary',
        'details',
    ];

    protected $casts = [
        'item_count' => 'integer',
        'details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        ?User $user,
        string $action,
        string $packageType,
        int $itemCount,
        string $summary,
        array $details = []
    ): self {
        return self::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'package_type' => $packageType,
            'item_count' => $itemCount,
            'summary' => $summary,
            'details' => $details,
        ]);
    }
}
