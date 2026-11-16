<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Project;
use Database\Seeders\ContentDraftsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The seeded case studies and blog posts: idempotent, unpublished, invisible to guests,
 * and the seeded text itself passes the self-check (complete nl/en, markers, no private data).
 */
class ContentDraftsTest extends TestCase
{
    use RefreshDatabase;

    private const PROJECTS = ['serverscope', 'barbercuts', 'jebbari', 'irooni'];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        putenv('CONTENT_SEED_OVERWRITE');
    }

    private function seedDrafts(): void
    {
        $this->seed(ContentDraftsSeeder::class);
    }

    public function test_seeder_creates_four_unpublished_case_studies_and_three_draft_posts(): void
    {
        $this->seedDrafts();

        $this->assertSame(self::PROJECTS, Project::orderBy('sort_order')->pluck('slug')->all());
        $this->assertSame(0, Project::published()->count());
        $this->assertSame(3, Blog::where('status', 'draft')->whereNull('published_at')->count());
        $this->assertSame(0, Blog::published()->count());

        foreach (Project::all() as $project) {
            Storage::disk('public')->assertExists($project->image);
            $this->assertNotEmpty($project->gallery);
            $this->assertCount(count($project->gallery), $project->gallery_alts);
            foreach ($project->gallery as $path) {
                Storage::disk('public')->assertExists($path);
            }
        }
    }

    public function test_seeder_is_idempotent_and_never_undoes_review_work(): void
    {
        $this->seedDrafts();
        $this->seedDrafts();

        $this->assertSame(count(self::PROJECTS), Project::count());
        $this->assertSame(3, Blog::count());

        // An edited draft and a published project survive a re-run untouched.
        $edited = Project::where('slug', 'barbercuts')->first();
        $edited->forceFill(['summary' => ['nl' => 'Mijn eigen tekst.', 'en' => 'My own text.']])->save();
        $live = Project::where('slug', 'serverscope')->first();
        $live->forceFill(['problem' => null, 'solution' => null, 'lessons' => null, 'result' => null, 'role' => null, 'is_published' => true])->save();
        Blog::where('slug', 'what-building-serverscope-taught-me')->first()
            ->forceFill(['title' => ['nl' => 'Eigen titel', 'en' => 'Own title']])->save();

        $this->seedDrafts();

        $this->assertSame('Mijn eigen tekst.', $edited->fresh()->getTranslation('summary', 'nl'));
        $this->assertTrue($live->fresh()->is_published);
        $this->assertSame('Eigen titel', Blog::where('slug', 'what-building-serverscope-taught-me')->first()->getTranslation('title', 'nl'));
        $this->assertSame(count(self::PROJECTS), Project::count());

        // With the explicit flag the seeded draft replaces it (and it goes offline).
        putenv('CONTENT_SEED_OVERWRITE=1');
        try {
            $this->seedDrafts();
        } finally {
            putenv('CONTENT_SEED_OVERWRITE');
        }
        $this->assertFalse($live->fresh()->is_published);
        $this->assertNotSame('Mijn eigen tekst.', $edited->fresh()->getTranslation('summary', 'nl'));
        $this->assertSame(count(self::PROJECTS), Project::count());
    }

    public function test_seeded_drafts_are_hidden_from_guests_lists_sitemap_and_llms(): void
    {
        $this->seedDrafts();

        foreach (self::PROJECTS as $slug) {
            $this->get("/project/{$slug}")->assertNotFound();
            $this->get("/en/project/{$slug}")->assertNotFound();
        }
        foreach (Blog::pluck('slug') as $slug) {
            $this->get("/blog/{$slug}")->assertNotFound();
            $this->get("/en/blog/{$slug}")->assertNotFound();
        }
        foreach (Project::pluck('id') as $id) {
            $this->get("/og/project/{$id}.png")->assertNotFound();
        }

        foreach (['/', '/work', '/en/work', '/blog', '/en/blog', '/werk-met-mij', '/sitemap.xml', '/llms.txt', '/llms-full.txt'] as $url) {
            $this->get($url)->assertOk()
                ->assertDontSee('/project/barbercuts', false)
                ->assertDontSee('/project/irooni', false)
                ->assertDontSee('how-i-hardened-my-vps-with-fail2ban', false)
                ->assertDontSee('[VERIFY', false);
        }
        $this->get('/sitemap.xml')->assertDontSee('/project/serverscope', false)->assertDontSee('/project/jebbari', false);
    }

    public function test_seeded_text_passes_the_self_check(): void
    {
        $projects = require database_path('seeders/content/case-studies.php');
        $posts = require database_path('seeders/content/posts.php');
        $texts = [];

        foreach ($projects as $slug => $p) {
            foreach (['title', 'summary', 'role', 'description', 'problem', 'solution', 'lessons', 'result', 'seo_title', 'seo_description', 'image_alt'] as $field) {
                foreach (['nl', 'en'] as $locale) {
                    $this->assertNotEmpty($p[$field][$locale] ?? null, "{$slug}.{$field}.{$locale} is empty");
                    $texts["{$slug}.{$field}.{$locale}"] = $p[$field][$locale];
                }
            }
            $this->assertLessThanOrEqual(60, mb_strlen(strip_tags($p['seo_title']['nl'])), "{$slug} seo_title too long");
            foreach (['nl', 'en'] as $locale) {
                $this->assertLessThanOrEqual(160, mb_strlen($p['seo_description'][$locale]), "{$slug}.seo_description.{$locale} too long");
            }
            foreach ($p['gallery'] as $i => [$file, $nl, $en]) {
                $this->assertFileExists(database_path("seeders/media/case-studies/{$slug}/{$file}"));
                $texts["{$slug}.gallery.{$i}"] = $nl.' '.$en;
            }
        }

        foreach ($posts as $post) {
            foreach (['title', 'excerpt', 'content', 'meta_title', 'meta_description'] as $field) {
                foreach (['nl', 'en'] as $locale) {
                    $this->assertNotEmpty($post[$field][$locale] ?? null, "{$post['slug']}.{$field}.{$locale} is empty");
                    $texts["{$post['slug']}.{$field}.{$locale}"] = $post[$field][$locale];
                }
                $this->assertLessThanOrEqual(155, mb_strlen($post['meta_description']['nl']));
            }
            foreach (['nl', 'en'] as $locale) {
                $words = str_word_count(preg_replace('/```.*?```/s', '', $post['content'][$locale]));
                $this->assertGreaterThanOrEqual(650, $words, "{$post['slug']}.{$locale}: {$words} words");
                $this->assertLessThanOrEqual(1150, $words, "{$post['slug']}.{$locale}: {$words} words");
            }
        }

        $all = implode("\n", $texts);

        // Every item still has review markers: nothing seeded can be published unseen.
        foreach (array_keys($projects) as $slug) {
            $this->assertStringContainsString('[VERIFY', implode(' ', array_filter($texts, fn ($k) => str_starts_with($k, $slug.'.'), ARRAY_FILTER_USE_KEY)));
        }

        // Private data: only RFC 5737 documentation IPs, only the public mail address, no secrets or home paths.
        preg_match_all('/\b(\d{1,3}(?:\.\d{1,3}){3})\b/', $all, $ips);
        foreach ($ips[1] as $ip) {
            $this->assertMatchesRegularExpression('/^(127\.0\.0\.1|192\.0\.2\.\d+|198\.51\.100\.\d+|203\.0\.113\.\d+)$/', $ip, "Non-documentation IP {$ip}");
        }
        preg_match_all('/[\w.+-]+@[\w-]+\.[\w.]+/', $all, $mails);
        $this->assertSame([], array_values(array_diff(array_unique($mails[0]), ['info@jebbari.nl'])), 'Unexpected email address');
        $this->assertDoesNotMatchRegularExpression('#/home/\w+|/Users/\w+|C:\\\\Users#i', $all, 'Path with a username');
        $this->assertDoesNotMatchRegularExpression('/\b(sk|pk|ghp|xox[bp])[-_][A-Za-z0-9]{12,}|-----BEGIN|password\s*[:=]\s*\S+/i', $all, 'Looks like a secret');

        // House style: none of the sloppy filler words.
        foreach (['passionate', 'gepassioneerd', 'cutting-edge', 'state-of-the-art', 'seamless', 'naadloos', 'unleash', 'game-changer', 'world-class', 'next-level', 'revolutionary', 'revolutionair', 'synergy', 'leverage', 'delve'] as $word) {
            $this->assertStringNotContainsStringIgnoringCase($word, $all, "Sloppy phrase: {$word}");
        }

        // No invented social proof.
        $this->assertDoesNotMatchRegularExpression('/testimonial|“[^”]{10,}”\s*[—-]\s*[A-Z]/u', $all);
    }
}
