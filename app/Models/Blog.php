<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = ['slug', 'title', 'content', 'image'];
    protected $casts = ['title' => 'array', 'content' => 'array'];

    public function getTranslation($field)
    {
        $locale = App::getLocale();
        $value = $this->$field;

        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? $value['nl'] ?? '';
        }

        return $value ?? '';
    }

    public function excerpt(int $limit = 180): string
    {
        return Str::limit(preg_replace('/\s+/', ' ', strip_tags(Str::markdown($this->getTranslation('content')))), $limit);
    }
}
