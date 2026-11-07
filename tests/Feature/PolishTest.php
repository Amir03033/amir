<?php

namespace Tests\Feature;

use App\Mail\ContactConfirmation;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Project;
use App\Models\Setting;
use App\Models\SiteCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PolishTest extends TestCase
{
    use RefreshDatabase;

    private function project(): Project
    {
        return Project::create([
            'title' => ['nl' => 'Jungle Tuinen', 'en' => 'Jungle Gardens'],
            'description' => ['nl' => 'Een tuinsite', 'en' => 'A garden site'],
            'tags' => 'Vue, Tailwind',
            'demo_url' => 'https://jungle.test',
        ]);
    }

    // --- /en URLs ---

    public function test_url_decides_the_language(): void
    {
        $project = $this->project();

        $this->get('/')->assertSee('<html lang="nl"', false)->assertSee('Jungle Tuinen');
        $this->get('/en')->assertSee('<html lang="en"', false)->assertSee('Jungle Gardens');
        $this->get('/en/project/'.$project->id)->assertOk()->assertSee('Jungle Gardens');
    }

    public function test_hreflang_and_switch_point_to_the_same_page(): void
    {
        $project = $this->project();

        $this->get('/project/'.$project->id)
            ->assertSee('hreflang="en" href="'.url('/en/project/'.$project->id).'"', false)
            ->assertSee('hreflang="nl" href="'.url('/project/'.$project->id).'"', false);
    }

    public function test_sitemap_lists_both_languages(): void
    {
        $this->project();

        $this->get('/sitemap.xml')->assertOk()->assertSee(url('/en/project/1'), false)->assertSee('hreflang="en"', false);
    }

    public function test_confirmation_mail_uses_the_visitors_language(): void
    {
        Mail::fake();

        $this->postJson('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hello, are you available?', 'locale' => 'en'])->assertOk();

        Mail::assertSent(ContactConfirmation::class, fn ($m) => $m->envelope()->subject === 'Thanks for your message');
    }

    // --- Hero, status, uptime ---

    public function test_hero_is_calm_and_now_line_is_optional(): void
    {
        $this->get('/')->assertDontSee(__('messages.now.label'))->assertDontSee('data-boot', false);

        Setting::put(['now.nl' => 'alerts in ServerScope verbeteren']);

        $this->get('/')->assertSee('alerts in ServerScope verbeteren');
    }

    public function test_status_page_shows_uptime_and_outages(): void
    {
        foreach ([[true, 3], [false, 2], [true, 1]] as [$ok, $hoursAgo]) {
            SiteCheck::create(['label' => 'Jungle', 'url' => 'https://jungle.test', 'ok' => $ok, 'status_code' => $ok ? 200 : 503, 'response_ms' => 200, 'checked_at' => now()->subHours($hoursAgo)]);
        }

        $this->get('/status')->assertOk()->assertSee('Jungle')->assertSee('66.67%')->assertSee('HTTP 503');
        $this->get('/en/status')->assertOk()->assertSee('Uptime (30 days)');
    }

    public function test_case_page_shows_uptime_of_the_project(): void
    {
        $project = $this->project();
        SiteCheck::create(['label' => 'Jungle Gardens', 'url' => 'https://jungle.test', 'ok' => true, 'status_code' => 200, 'response_ms' => 180, 'checked_at' => now()]);

        $this->get('/project/'.$project->id)->assertSee('100%')->assertSee('data-uptime-bar', false);
    }

    // --- SEO ---

    public function test_json_ld_on_home_and_case(): void
    {
        $project = $this->project();

        $this->get('/')->assertSee('"@type":"Person"', false);
        $this->get('/project/'.$project->id)->assertSee('"@type":"CreativeWork"', false)->assertSee(route('og', ['type' => 'project', 'id' => $project->id, 'lang' => 'nl']), false);
    }

    public function test_og_image_is_a_png(): void
    {
        Storage::fake('public');
        $project = $this->project();

        $response = $this->get(route('og', ['type' => 'project', 'id' => $project->id]))->assertOk()->assertHeader('content-type', 'image/png');
        $this->assertSame([1200, 630], array_slice(getimagesize($response->getFile()->getPathname()), 0, 2));
    }

    public function test_og_image_hides_drafts(): void
    {
        $blog = Blog::create(['slug' => 'draft', 'title' => ['nl' => 'Concept'], 'content' => ['nl' => 'x'], 'status' => 'draft']);

        $this->get(route('og', ['type' => 'blog', 'id' => $blog->id]))->assertNotFound();
    }

    // --- Conversions ---

    public function test_beacon_records_known_events_only(): void
    {
        $this->post('/e', ['name' => 'booking_click', 'label' => 'hero'])->assertNoContent();
        $this->post('/e', ['name' => 'outbound', 'label' => 'linkedin.com'])->assertNoContent();
        $this->post('/e', ['name' => 'drop_table', 'label' => 'x'])->assertNoContent();

        $this->assertSame(['booking_click', 'outbound'], Event::orderBy('id')->pluck('name')->all());
    }

    public function test_cv_download_and_contact_are_counted(): void
    {
        Mail::fake();

        $this->get('/cv.pdf');
        $this->postJson('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hallo, ben je beschikbaar?']);

        $this->assertSame(1, Event::where('name', 'cv_download')->count());
        $this->assertSame(1, Event::where('name', 'contact_submit')->count());
    }

    // --- Terminal ---

    public function test_terminal_easter_egg_is_wired_into_the_palette(): void
    {
        $this->get('/')->assertSee('amir@portfolio', false)->assertSee('command not found', false);
    }

    public function test_hero_shows_live_card_only_with_real_measurements(): void
    {
        $this->get('/')->assertDontSee(__('messages.hero.more_stats'));

        SiteCheck::create(['label' => 'Jungle', 'url' => 'https://jungle.test', 'ok' => true, 'status_code' => 200, 'response_ms' => 222, 'checked_at' => now()]);
        cache()->forget('status-panel');

        $this->get('/')->assertSee(__('messages.hero.more_stats'))->assertSee('jungle.test')->assertSee('222 ms');
    }
}
