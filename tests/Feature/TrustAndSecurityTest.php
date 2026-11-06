<?php

namespace Tests\Feature;

use App\Http\Middleware\RequireTwoFactor;
use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage as ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Visit;
use App\Support\GeoIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TrustAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'JBSWY3DPEHPK3PXP';

    private function admin(bool $twoFactor = true): User
    {
        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);

        if ($twoFactor) {
            $user->forceFill([
                'two_factor_secret' => encrypt(self::SECRET),
                'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-one', 'recovery-code-two'])),
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        return $user;
    }

    private function project(array $extra = []): Project
    {
        return Project::create(['title' => ['nl' => 'KlusKlaar', 'en' => 'KlusKlaar'], 'description' => ['nl' => 'Omschrijving', 'en' => 'Description'], 'tags' => 'PHP', ...$extra]);
    }

    // --- 2FA ---

    public function test_admin_without_2fa_is_sent_to_setup(): void
    {
        $this->actingAs($this->admin(twoFactor: false));

        $this->get('/admin')->assertRedirect('/admin/two-factor');
        $this->get('/admin/two-factor')->assertOk()->assertSee('Instellen');
    }

    public function test_admin_with_2fa_must_enter_a_code(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/projects')->assertRedirect(route('two-factor.check'));
        $this->get(route('two-factor.check'))->assertOk();

        $this->post(route('two-factor.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->get('/admin')->assertRedirect(route('two-factor.check'));

        $code = (new Google2FA)->getCurrentOtp(self::SECRET);
        $this->post(route('two-factor.verify'), ['code' => $code])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_recovery_code_works_once(): void
    {
        $user = $this->admin();
        $this->actingAs($user);

        $this->post(route('two-factor.verify'), ['recovery_code' => 'recovery-code-one'])->assertRedirect();
        $this->get('/admin')->assertOk();
        $this->assertNotContains('recovery-code-one', $user->fresh()->recoveryCodes());
    }

    public function test_registration_is_closed(): void
    {
        $this->post('/register', ['name' => 'x', 'email' => 'x@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'])->assertNotFound();
        $this->assertSame(0, User::count());
    }

    // --- Contact ---

    public function test_contact_form_mails_me_and_confirms_to_sender(): void
    {
        Mail::fake();

        $this->postJson('/contact', ['name' => 'Sam', 'email' => 'sam@example.com', 'message' => 'Hallo, ik zoek een stagiair.'])->assertJson(['success' => true]);

        Mail::assertSent(ContactMessageMail::class, fn ($m) => $m->hasTo(config('site.email')));
        Mail::assertSent(ContactConfirmation::class, fn ($m) => $m->hasTo('sam@example.com'));
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        Mail::fake();

        $this->postJson('/contact', ['name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Buy cheap stuff now!!', 'website' => 'http://spam.test'])->assertJson(['success' => true]);

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    // --- Trust content ---

    public function test_reviews_only_show_when_visible(): void
    {
        $this->get('/')->assertDontSee(__('messages.testimonials.title'));

        Testimonial::create(['name' => 'Eigenaar Jungle', 'quote' => ['nl' => 'Snel en duidelijk geholpen.'], 'is_visible' => false]);
        $this->get('/')->assertDontSee('Snel en duidelijk geholpen.');

        Testimonial::create(['name' => 'Eigenaar Klus', 'quote' => ['nl' => 'Top samenwerking.'], 'is_visible' => true]);
        $this->get('/')->assertSee('Top samenwerking.')->assertSee('Eigenaar Klus');
    }

    public function test_project_case_blocks_render(): void
    {
        $project = $this->project(['problem' => ['nl' => 'Geen online aanvragen.'], 'result' => ['nl' => 'Aanvragen komen nu binnen via de site.']]);

        $this->get(route('project.show', $project))
            ->assertSee('Geen online aanvragen.')
            ->assertSee('Aanvragen komen nu binnen via de site.')
            ->assertDontSee(__('messages.case.solution'));
    }

    public function test_cv_downloads_as_pdf(): void
    {
        $this->project();

        $response = $this->get(route('cv.pdf'))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_settings_from_filament_override_env(): void
    {
        $this->get('/')->assertDontSee('cal.com/amir');

        Setting::put(['links.booking' => 'https://cal.com/amir/intro', 'links.serverscope_status' => 'https://app.serverscope.test/status/amir']);
        \App\Filament\Admin\Pages\SiteSettings::applyToConfig();

        $this->get('/')->assertSee('https://cal.com/amir/intro', false)->assertSee('status/amir/badge.svg', false);
    }

    public function test_privacy_page_states_the_retention_period(): void
    {
        $this->get('/privacy')->assertOk()->assertSee((string) config('site.visits_retention_days'))->assertSee('DB-IP');
    }

    // --- Privacy & visit data ---

    public function test_geoip_and_anonymization(): void
    {
        DB::table('geoip_ranges')->insert([
            ['ip_from' => GeoIp::key('8.8.8.0'), 'ip_to' => GeoIp::key('8.8.8.255'), 'country' => 'US'],
            ['ip_from' => GeoIp::key('2a02:2479::'), 'ip_to' => GeoIp::key('2a02:2479:ffff:ffff:ffff:ffff:ffff:ffff'), 'country' => 'DE'],
        ]);

        $this->assertSame('US', GeoIp::country('8.8.8.8'));
        $this->assertSame('DE', GeoIp::country('2a02:2479:df:9100::1'));
        $this->assertNull(GeoIp::country('9.9.9.9'));
        $this->assertSame('8.8.8.0', GeoIp::anonymize('8.8.8.8'));
        $this->assertSame('2a02:2479:df::', GeoIp::anonymize('2a02:2479:df:9100::1'));
    }

    public function test_visits_store_anonymized_ip_and_country(): void
    {
        DB::table('geoip_ranges')->insert(['ip_from' => GeoIp::key('127.0.0.0'), 'ip_to' => GeoIp::key('127.255.255.255'), 'country' => 'NL']);

        $this->get('/');

        $visit = Visit::sole();
        $this->assertSame('127.0.0.0', $visit->ip_address);
        $this->assertSame('NL', $visit->country);
    }

    public function test_old_visits_are_rolled_up_then_pruned(): void
    {
        Visit::create(['url' => '/', 'ip_hash' => 'a', 'created_at' => now()->subDays(200)]);
        Visit::create(['url' => '/', 'ip_hash' => 'b', 'created_at' => now()->subDays(200)]);
        Visit::create(['url' => '/', 'ip_hash' => 'c', 'created_at' => now()->subDays(2)]);

        $this->artisan('visits:rollup')->assertSuccessful();
        $this->artisan('model:prune', ['--model' => [Visit::class]])->assertSuccessful();

        $this->assertSame(1, Visit::count());
        $day = DB::table('visit_days')->where('date', now()->subDays(200)->toDateString())->first();
        $this->assertSame([2, 2], [(int) $day->views, (int) $day->visitors]);
    }
}
