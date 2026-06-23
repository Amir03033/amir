<?php
// app/Models/Blog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Blog extends Model
{
    protected $fillable = ['slug', 'title', 'content', 'image'];
    protected $casts = ['title' => 'array', 'content' => 'array'];

    public function getTranslation($field)
    {
        $locale = App::getLocale();
        return $this->$field[$locale] ?? $this->$field['en'] ?? '';
    }
}