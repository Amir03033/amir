<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Visit extends Model
{
    protected $guarded = [];

    protected function referrerDomain(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->referrer) return null;
            return parse_url($this->referrer, PHP_URL_HOST) ?? $this->referrer;
        });
    }
}