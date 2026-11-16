<?php

namespace Tests\Feature;

use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The visitor counter skips bots, scripts and scanners, and never stores a full IP address. */
class TrackVisitTest extends TestCase
{
    use RefreshDatabase;

    private const BROWSER = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15';

    public function test_a_normal_page_view_is_counted_with_a_hashed_and_shortened_ip(): void
    {
        $this->withHeaders(['User-Agent' => self::BROWSER])
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])
            ->get('/?utm_source=test')->assertOk();

        $visit = Visit::sole();
        $this->assertSame('203.0.113.0', $visit->ip_address);
        $this->assertSame(hash('sha256', '203.0.113.42'.config('app.key')), $visit->ip_hash);
        $this->assertSame('test', $visit->utm_source);
    }

    public function test_crawlers_scripts_and_scanners_are_not_counted(): void
    {
        foreach (['Googlebot/2.1 (+http://www.google.com/bot.html)', 'python-requests/2.31', 'curl/8.5.0', 'Go-http-client/1.1', 'Mozilla/5.0 HeadlessChrome/120'] as $ua) {
            $this->withHeaders(['User-Agent' => $ua])->get('/');
        }

        foreach (['/wp-login.php', '/wp-admin/', '/.env', '/.git/config', '/wp-config.php.bak'] as $path) {
            $this->withHeaders(['User-Agent' => self::BROWSER])->get($path);
        }

        $this->assertSame(0, Visit::count());
    }

    public function test_admin_and_machine_requests_are_not_counted(): void
    {
        foreach (['/amir/login', '/sitemap.xml', '/up'] as $path) {
            $this->withHeaders(['User-Agent' => self::BROWSER])->get($path);
        }
        $this->withHeaders(['User-Agent' => self::BROWSER])->post('/e', ['name' => 'cta_click', 'label' => 'nav']);

        $this->assertSame(0, Visit::count());
    }
}
