<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds the ServerScope, BarberCuts, Jebbari.nl and Irooni case studies as UNPUBLISHED drafts, with their
 * screenshots. Idempotent: matched by slug, so running it twice creates no duplicates.
 *
 * A project that already exists (published, or a draft you are editing) is never touched, so a
 * second run cannot undo your review work. Set CONTENT_SEED_OVERWRITE=1 to replace it with the
 * seeded draft anyway (a published project then goes offline until you publish it again).
 *
 *   php artisan db:seed --class=CaseStudiesSeeder --force
 */
class CaseStudiesSeeder extends Seeder
{
    public function run(): void
    {
        $overwrite = (bool) env('CONTENT_SEED_OVERWRITE', false);

        foreach (require database_path('seeders/content/case-studies.php') as $slug => $data) {
            $project = Project::where('slug', $slug)->first();

            if ($project && ! $overwrite) {
                $state = $project->is_published ? 'a published project' : 'a draft';
                $this->command?->warn("Skipped {$slug}: {$state} with this slug exists. Set CONTENT_SEED_OVERWRITE=1 to replace it.");

                continue;
            }

            [$image, $imageDark, $gallery, $galleryDark, $alts] = $this->media($slug, $data);

            $attributes = [
                'title' => $data['title'],
                'summary' => $data['summary'],
                'description' => $data['description'],
                'role' => $data['role'],
                'year' => $data['year'],
                'status' => $data['status'],
                'tags' => $data['tags'],
                'demo_url' => $data['demo_url'],
                'jebbari_url' => $data['jebbari_url'],
                'problem' => $data['problem'],
                'solution' => $data['solution'],
                'lessons' => $data['lessons'],
                'result' => $data['result'],
                'seo_title' => $data['seo_title'],
                'seo_description' => $data['seo_description'],
                'image' => $image,
                'image_dark' => $imageDark,
                'image_alt' => $data['image_alt'],
                'gallery' => $gallery,
                'gallery_dark' => $galleryDark,
                'gallery_alts' => $alts,
                'featured' => $data['featured'],
                'sort_order' => $data['sort_order'],
                'is_published' => false,
            ];

            if ($project) {
                $project->forceFill($attributes)->save();
                $this->command?->info("Updated draft: {$slug}");
            } else {
                Project::create(['slug' => $slug, ...$attributes]);
                $this->command?->info("Created draft: {$slug}");
            }
        }
    }

    /**
     * Copy the screenshots to storage/app/public/projects/<slug>/ (same path every time).
     *
     * Gallery entries are [file, alt NL, alt EN] or [file, alt NL, alt EN, dark file].
     *
     * @return array{0: ?string, 1: ?string, 2: list<string>, 3: list<?string>, 4: list<array{nl: string, en: string}>}
     */
    private function media(string $slug, array $data): array
    {
        $source = database_path("seeders/media/case-studies/{$slug}");
        $disk = Storage::disk('public');
        $copy = function (string $file) use ($source, $disk, $slug): ?string {
            if (! is_file("{$source}/{$file}")) {
                $this->command?->warn("Missing screenshot {$slug}/{$file}");

                return null;
            }
            $target = "projects/{$slug}/{$file}";
            $disk->put($target, file_get_contents("{$source}/{$file}"));

            return $target;
        };

        $gallery = [];
        $galleryDark = [];
        $alts = [];
        foreach ($data['gallery'] as $entry) {
            [$file, $nl, $en] = $entry;
            if ($path = $copy($file)) {
                $gallery[] = $path;
                $galleryDark[] = isset($entry[3]) ? $copy($entry[3]) : null;
                $alts[] = ['nl' => $nl, 'en' => $en];
            }
        }

        return [
            $copy($data['image']),
            isset($data['image_dark']) ? $copy($data['image_dark']) : null,
            $gallery,
            array_filter($galleryDark) ? $galleryDark : [],
            $alts,
        ];
    }
}
