<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'title', 'issuer', 'issued_at', 'file',
        'external_url', 'featured', 'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'issued_at' => 'date',
        'featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTranslation($field)
    {
        $locale = App::getLocale();
        $value = $this->$field;

        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? $value['nl'] ?? '';
        }

        return $value ?? '';
    }

    public function fileUrl(): ?string
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function isPdf(): bool
    {
        return $this->file && Str::endsWith(strtolower($this->file), '.pdf');
    }
}