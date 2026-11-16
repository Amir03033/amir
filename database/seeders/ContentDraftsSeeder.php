<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * All review drafts in one go: three case studies and three blog posts, unpublished.
 * Review and publish them in Filament (see REVIEW.md).
 *
 *   php artisan db:seed --class=ContentDraftsSeeder --force
 */
class ContentDraftsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([CaseStudiesSeeder::class, BlogDraftsSeeder::class]);
    }
}
