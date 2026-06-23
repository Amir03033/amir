<?php
// app/Models/Project.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'image', 'tags', 'github_url', 'demo_url'];
    protected $casts = ['title' => 'array', 'description' => 'array'];

    public function getTranslation($field)
    {
        $locale = App::getLocale();
        return $this->$field[$locale] ?? $this->$field['en'] ?? '';
    }
}