<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Logo, favicons, share image and the default theme. */
class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_page_has_the_icons_and_a_share_image(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<link rel="icon" type="image/svg+xml" href="'.asset('favicon.svg').'">', false)
            ->assertSee('<link rel="apple-touch-icon" href="'.asset('apple-touch-icon.png').'">', false)
            ->assertSee('<meta property="og:image" content="'.url('/og/page/home.png'), false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('class="mark-line"', false);   // heartbeat logo in the nav

        foreach (['favicon.svg', 'favicon.ico', 'apple-touch-icon.png'] as $file) {
            $this->assertFileExists(public_path($file));
        }
        $this->assertStringContainsString('M3.5 10.5h3l1.6-4 2.6 8 1.8-5.2 1 1.2h3', file_get_contents(public_path('favicon.svg')));
    }

    public function test_share_image_is_a_real_png(): void
    {
        $response = $this->get('/og/page/home.png')->assertOk();
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
        [$w, $h] = getimagesize($response->baseResponse->getFile()->getPathname());
        $this->assertSame([1200, 630], [$w, $h]);
    }

    public function test_light_is_the_default_theme(): void
    {
        $this->get('/')->assertSee("if (pref !== 'light' && pref !== 'dark' && pref !== 'system') pref = 'light';", false)
            ->assertSee('localStorage.getItem(\'site-theme\')', false);
    }
}
