<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seeds pilot reviews as HIDDEN drafts: text only, no photos. Each one is a suggested text to send
 * to a real client; name, role and company are [VERIFY] placeholders. See REVIEW.md.
 *
 * Idempotent: a review is skipped when its project already has a review (for the general one:
 * when a review without a project exists). Nothing is ever overwritten.
 *
 *   php artisan db:seed --class=TestimonialDraftsSeeder --force
 */
class TestimonialDraftsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (require database_path('seeders/content/testimonials.php') as $review) {
            $projectId = null;
            if ($review['project']) {
                $projectId = Project::where('slug', $review['project'])->value('id');
                if (! $projectId) {
                    $this->command?->warn("Skipped review for {$review['project']}: that project doesn't exist (run CaseStudiesSeeder first).");

                    continue;
                }
            }

            $label = $review['project'] ?? 'general';
            if (Testimonial::where('project_id', $projectId)->exists()) {
                $this->command?->warn("Skipped review ({$label}): there already is one. Nothing changed.");

                continue;
            }

            Testimonial::create([
                'name' => $review['name'],
                'role' => $review['role'],
                'company' => $review['company'],
                'quote' => $review['quote'],
                'avatar' => null,
                'url' => null,
                'project_id' => $projectId,
                'is_visible' => false,
                'sort_order' => $review['sort_order'],
            ]);
            $this->command?->info("Created hidden pilot review: {$label}");
        }
    }
}
