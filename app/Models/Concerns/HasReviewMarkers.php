<?php

namespace App\Models\Concerns;

use Illuminate\Validation\ValidationException;

/**
 * Content with "[VERIFY: …]" markers can be saved as a draft, but never published.
 * The model guard is the last line of defence; Filament shows the same check as a form error.
 */
trait HasReviewMarkers
{
    public const MARKER = '[VERIFY';

    /** @return list<string> attributes that may contain markers */
    abstract public function reviewFields(): array;

    abstract public function isPublishedState(): bool;

    public static function bootHasReviewMarkers(): void
    {
        static::saving(function ($model) {
            if ($model->isPublishedState() && ($found = $model->reviewMarkers())) {
                throw ValidationException::withMessages(['publish' => static::markerMessage($found)]);
            }
        });
    }

    /**
     * Where markers still are, e.g. "solution (en): [VERIFY: launch date]".
     *
     * @return list<string>
     */
    public function reviewMarkers(?array $attributes = null): array
    {
        $found = [];
        foreach ($this->reviewFields() as $field) {
            $value = $attributes !== null ? ($attributes[$field] ?? null) : $this->getAttribute($field);
            foreach (static::flattenForReview($value) as $locale => $text) {
                if (preg_match_all('/\[VERIFY[^\]]*\]/u', (string) $text, $m)) {
                    foreach ($m[0] as $marker) {
                        $found[] = $field.($locale !== '' ? " ({$locale})" : '').': '.$marker;
                    }
                }
            }
        }

        return $found;
    }

    /** @return array<string, string> locale (or path) => text */
    private static function flattenForReview(mixed $value, string $prefix = ''): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : $value;
        }
        if (! is_array($value)) {
            return [$prefix => (string) $value];
        }
        $out = [];
        foreach ($value as $key => $item) {
            $out += static::flattenForReview($item, $prefix === '' ? (string) $key : $prefix.'.'.$key);
        }

        return $out;
    }

    /** @param  list<string>  $found */
    public static function markerMessage(array $found): string
    {
        return trans_choice(
            '{1} Nog 1 [VERIFY]-marker; haal die eerst weg: :list|[2,*] Nog :count [VERIFY]-markers; haal die eerst weg: :list',
            count($found),
            ['count' => count($found), 'list' => implode(' · ', array_slice($found, 0, 12)).(count($found) > 12 ? ' …' : '')]
        );
    }
}
