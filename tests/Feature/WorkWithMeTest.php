<?php

namespace Tests\Feature;

use App\Filament\Admin\Widgets\CtaClicks;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The "Work with me" path to Jebbari: page, CTA placements, UTM links and click counts. */
class WorkWithMeTest extends TestCase
{
    use RefreshDatabase;

    private const UTM = 'utm_source=amirs.life&amp;utm_medium=portfolio&amp;utm_campaign=';

    public function test_page_renders_in_both_languages_with_reciprocal_hreflang(): void
    {
        $this->get('/werk-met-mij')->assertOk()
            ->assertSee('<html lang="nl"', false)
            ->assertSee('<h1', false)
            ->assertSee('Iets laten bouwen?')
            ->assertSee('hreflang="en" href="'.url('/en/work-with-me').'"', false)
            ->assertSee('<link rel="canonical" href="'.url('/werk-met-mij').'">', false);

        $this->get('/en/work-with-me?via=case')->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Need something built?')
            ->assertSee('hreflang="nl" href="'.url('/werk-met-mij').'"', false)
            ->assertSee('<link rel="canonical" href="'.url('/en/work-with-me').'">', false); // no ?via in canonical

        $this->get('/sitemap.xml')->assertSee(url('/werk-met-mij'), false)->assertSee(url('/en/work-with-me'), false);
    }

    public function test_primary_action_is_the_real_jebbari_request_flow_with_utm(): void
    {
        $this->get('/werk-met-mij?via=case')
            ->assertSee('href="https://jebbari.nl/nl/aanvraag/stap/1?'.self::UTM.'work-with-me&amp;utm_content=case"', false)
            ->assertSee('href="mailto:info@jebbari.nl"', false)
            ->assertSee('https://jebbari.nl/nl/diensten/maatwerk-webapplicaties?'.self::UTM.'work-with-me', false);

        $this->get('/en/work-with-me')
            ->assertSee('href="https://jebbari.nl/en/request/step/1?'.self::UTM.'work-with-me"', false);

        // An unknown ?via is ignored, never echoed into the link.
        $this->get('/werk-met-mij?via=<script>')->assertDontSee('utm_content=%3Cscript', false)->assertDontSee('&lt;script', false)
            ->assertSee('utm_campaign=work-with-me"', false);
    }

    public function test_cta_appears_in_every_placement_with_its_own_campaign(): void
    {
        $project = Project::create(['title' => ['nl' => 'Shop', 'en' => 'Shop'], 'description' => ['nl' => 'x', 'en' => 'x'], 'tags' => 'PHP']);
        Blog::create(['slug' => 'notitie', 'title' => ['nl' => 'Notitie', 'en' => 'Note'], 'content' => ['nl' => 'Tekst.', 'en' => 'Text.'], 'status' => 'published']);

        $home = $this->get('/')->assertOk();
        $home->assertSee('href="'.url('/werk-met-mij').'?via=nav"', false)          // nav
            ->assertSee('data-track-label="home"', false)                             // home block
            ->assertSee(self::UTM.'home', false)
            ->assertSee(self::UTM.'hero', false)                                      // hero sentence
            ->assertSee(self::UTM.'about', false)                                     // about section
            ->assertSee('href="'.url('/werk-met-mij').'?via=footer"', false)       // footer
            ->assertSee(self::UTM.'footer', false);

        $this->get('/project/'.$project->slug)->assertSee(self::UTM.'case', false)->assertSee('?via=case', false);
        $this->get('/blog/notitie')->assertSee(self::UTM.'post', false)->assertSee('?via=post', false);
    }

    public function test_clicks_are_logged_and_counted_per_placement(): void
    {
        $this->post('/e', ['name' => 'cta_click', 'label' => 'case'])->assertNoContent();
        $this->post('/e', ['name' => 'cta_jebbari', 'label' => 'case'])->assertNoContent();
        $this->post('/e', ['name' => 'cta_jebbari', 'label' => 'work-with-me:service'])->assertNoContent();
        $this->post('/e', ['name' => 'cta_mail', 'label' => 'work-with-me'])->assertNoContent();

        $this->assertSame(4, Event::count());
        $counts = CtaClicks::counts();
        $this->assertSame(['page' => 1, 'jebbari' => 1, 'mail' => 0], $counts['case']);
        $this->assertSame(['page' => 0, 'jebbari' => 1, 'mail' => 1], $counts['work-with-me']);
    }
}
