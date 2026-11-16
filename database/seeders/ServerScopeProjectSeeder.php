<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Optional, one-off: turns the hard-coded ServerScope section into a normal (featured) project,
 * using only the real copy that was already on the site. Safe to run twice: skips if it exists.
 *
 *   php artisan db:seed --class=ServerScopeProjectSeeder --force
 */
class ServerScopeProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::where('slug', 'serverscope')->exists()) {
            $this->command?->info('ServerScope project already exists, nothing to do.');

            return;
        }

        $copy = fn (string $locale) => [
            'tagline' => trans('messages.serverscope.tagline', [], $locale),
            'intro' => trans('messages.serverscope.intro', [], $locale),
        ];

        Project::create([
            'slug' => 'serverscope',
            'title' => ['nl' => 'ServerScope', 'en' => 'ServerScope'],
            'summary' => ['nl' => $copy('nl')['tagline'], 'en' => $copy('en')['tagline']],
            'description' => ['nl' => $copy('nl')['intro'], 'en' => $copy('en')['intro']],
            'role' => ['nl' => 'Ontwerp en bouw, van begin tot eind', 'en' => 'Design and build, end to end'],
            'tags' => implode(', ', trans('messages.serverscope.stack', [], 'en')),
            'demo_url' => config('site.serverscope.url'),
            // ServerScope is also a Jebbari project: https://jebbari.nl/nl/werk/serverscope
            'jebbari_url' => ['nl' => 'https://jebbari.nl/nl/werk/serverscope', 'en' => 'https://jebbari.nl/en/work/serverscope'],
            'featured' => true,
            'sort_order' => 0,
        ]);

        $this->command?->info('ServerScope project created. Add the year, screenshots and case text in Filament.');
    }
}
