<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Simple key/value store for site-wide settings, such as images managed in Filament. */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function values(): array
    {
        return Cache::rememberForever('settings', fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::values()[$key] ?? $default;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings');
    }

    /** Public URL of an uploaded image, or the bundled fallback when nothing is uploaded. */
    public static function media(string $key, ?string $fallback = null): ?string
    {
        $path = static::get("media.{$key}");

        if ($path) {
            return asset('storage/'.$path);
        }

        return $fallback ? asset($fallback) : null;
    }
}
