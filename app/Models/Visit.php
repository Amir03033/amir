<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Visit extends Model
{
    use Prunable;

    protected $guarded = [];

    /** Raw visits are deleted after the retention period (daily totals stay in visit_days, see visits:rollup). */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(config('site.visits_retention_days')));
    }

    protected function referrerDomain(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->referrer) return null;
            return parse_url($this->referrer, PHP_URL_HOST) ?? $this->referrer;
        });
    }
}