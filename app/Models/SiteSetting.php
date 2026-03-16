<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $settings = self::allAsKeyValue();

        return $settings[$key] ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('site_settings.key_value');
    }

    public static function allAsKeyValue(): array
    {
        return Cache::remember('site_settings.key_value', now()->addMinutes(10), function () {
            return self::query()
                ->pluck('value', 'key')
                ->toArray();
        });
    }
}
