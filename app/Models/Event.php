<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/** A conversion on the public site. Holds no personal data; pruned with the same retention as visits. */
class Event extends Model
{
    use Prunable;

    public const NAMES = ['cv_download', 'booking_click', 'contact_submit', 'outbound'];

    public const UPDATED_AT = null;

    protected $fillable = ['name', 'label', 'locale'];

    public static function record(string $name, ?string $label = null): void
    {
        if (in_array($name, self::NAMES, true)) {
            static::create(['name' => $name, 'label' => $label ? mb_substr($label, 0, 120) : null, 'locale' => app()->getLocale()]);
        }
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(config('site.visits_retention_days')));
    }
}
