<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Per-language text in a plain string/text column: stored as {"nl": "...", "en": "..."}.
 * Older values that are just a string are read as Dutch, so existing content keeps working
 * without a migration.
 */
class LocalizedText implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : ['nl' => (string) $value];
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_array($value)) {
            $value = array_filter($value, fn ($v) => filled($v));

            return $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : null;
        }

        return filled($value) ? json_encode(['nl' => (string) $value], JSON_UNESCAPED_UNICODE) : null;
    }
}
