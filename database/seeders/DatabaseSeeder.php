<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Blog;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::create([
            'title' => ['nl' => 'Cyber-Portfolio Architectuur', 'en' => 'Cyber-Portfolio Architecture'],
            'description' => [
                'nl' => 'Een ultrasnel portfolio gebouwd met Laravel 11, Blade componenten en Alpine.js.',
                'en' => 'An ultra-fast portfolio built with Laravel 11, Blade components, and Alpine.js.'
            ],
            'tags' => 'Laravel 11, Alpine.js, Tailwind',
            'github_url' => 'https://github.com',
            'demo_url' => 'https://example.com'
        ]);

        Blog::create([
            'slug' => 'mijn-tech-stack-2026',
            'title' => ['nl' => 'Mijn Tech Stack Keuze van 2026', 'en' => 'My Tech Stack Selection of 2026'],
            'content' => [
                'nl' => "Waarom ik kies voor Laravel & Tailwind CSS in 2026...\n\nHet bouwen vanaf nul geeft de ultieme controle over prestaties en design.",
                'en' => "Why I build with Laravel & Tailwind CSS in 2026...\n\nBuilding from scratch provides the ultimate control over performance and modern design."
            ]
        ]);
    }
}