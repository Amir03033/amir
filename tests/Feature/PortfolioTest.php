<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\SiteCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    private function project(): Project
    {
        return Project::create([
            'title' => ['nl' => 'KlusKlaar', 'en' => 'KlusKlaar'],
            'description' => ['nl' => 'Omschrijving', 'en' => 'Description'],
            'tags' => 'PHP, Laravel',
            'demo_url' => 'https://klus.test',
        ]);
    }

    public function test_site_check_stores_results_for_project_demo_urls(): void
    {
        $this->project();
        Http::fake(['klus.test*' => Http::response('ok', 200)]);

        $this->artisan('site:check')->assertSuccessful();

        $check = SiteCheck::sole();
        $this->assertSame('KlusKlaar', $check->label);
        $this->assertTrue($check->ok);
        $this->assertSame(200, $check->status_code);
    }

    public function test_site_check_records_a_down_site(): void
    {
        $this->project();
        Http::fake(['klus.test*' => Http::response('', 503)]);

        $this->artisan('site:check')->assertSuccessful();

        $this->assertFalse(SiteCheck::sole()->ok);
    }

    public function test_status_panel_shows_real_measurements_only(): void
    {
        $this->get('/')->assertOk()->assertSee(__('messages.status.empty'));

        SiteCheck::create(['label' => 'KlusKlaar', 'url' => 'https://klus.test', 'ok' => true, 'status_code' => 200, 'response_ms' => 321, 'checked_at' => now()]);
        cache()->forget('status-panel');

        $this->get('/')->assertOk()->assertSee('321')->assertSee('klus.test');
    }

    public function test_pages_render(): void
    {
        $project = $this->project();

        $this->get('/cv')->assertOk();
        $this->get(route('project.show', $project))->assertOk()->assertSee('KlusKlaar');
        $this->get('/does-not-exist')->assertNotFound();
    }

    public function test_sitemap_lists_projects(): void
    {
        $project = $this->project();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('project.show', $project), false);
    }

    public function test_placeholder_phone_is_not_shown_without_config(): void
    {
        config(['site.phone' => null]);

        $this->get('/')->assertDontSee('tel:', false);
    }
}
