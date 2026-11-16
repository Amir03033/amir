<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

/**
 * Seeds three blog posts as DRAFTS (status "draft", no publish date). Idempotent: matched by slug.
 * An existing post with the same slug (published, or a draft you are editing) is never touched
 * unless CONTENT_SEED_OVERWRITE=1.
 *
 *   php artisan db:seed --class=BlogDraftsSeeder --force
 */
class BlogDraftsSeeder extends Seeder
{
    public function run(): void
    {
        $overwrite = (bool) env('CONTENT_SEED_OVERWRITE', false);

        foreach (require database_path('seeders/content/posts.php') as $post) {
            $existing = Blog::where('slug', $post['slug'])->first();

            if ($existing && ! $overwrite) {
                $this->command?->warn("Skipped {$post['slug']}: a post with this slug exists. Set CONTENT_SEED_OVERWRITE=1 to replace it.");

                continue;
            }

            $attributes = [
                'title' => $post['title'],
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'meta_title' => $post['meta_title'],
                'meta_description' => $post['meta_description'],
                'category' => $post['category'],
                'tags' => $post['tags'],
                'status' => 'draft',
                'published_at' => null,
            ];

            if ($existing) {
                $existing->forceFill($attributes)->save();
                $this->command?->info("Updated draft: {$post['slug']}");
            } else {
                Blog::create(['slug' => $post['slug'], ...$attributes]);
                $this->command?->info("Created draft: {$post['slug']}");
            }
        }
    }
}
