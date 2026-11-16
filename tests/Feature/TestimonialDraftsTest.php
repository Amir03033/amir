<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\TestimonialResource\Pages\EditTestimonial;
use App\Http\Middleware\RequireTwoFactor;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\ContentDraftsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/** Pilot reviews: hidden, text only, placeholders for every person, and impossible to show unchanged. */
class TestimonialDraftsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        putenv('CONTENT_SEED_OVERWRITE');
    }

    public function test_pilot_reviews_are_seeded_hidden_without_photos_and_with_markers(): void
    {
        $this->seed(ContentDraftsSeeder::class);

        $this->assertSame(4, Testimonial::count());
        foreach (Testimonial::with('project')->get() as $t) {
            $this->assertFalse($t->is_visible);
            $this->assertNull($t->avatar);
            $this->assertStringStartsWith('[VERIFY', $t->name, 'no invented names');
            $this->assertNotEmpty($t->quote['nl']);
            $this->assertNotEmpty($t->quote['en']);
            $this->assertStringContainsString('[VERIFY', $t->quote['nl']);
            $this->assertStringContainsString('[VERIFY', $t->quote['en']);
        }
        $this->assertEqualsCanonicalizing(['serverscope', 'barbercuts', 'irooni', null], Testimonial::with('project')->get()->map(fn ($t) => $t->project?->slug)->all());

        // Idempotent: a second run adds nothing.
        $this->seed(ContentDraftsSeeder::class);
        $this->assertSame(4, Testimonial::count());

        // Hidden from the site.
        $this->get('/')->assertOk()->assertDontSee('ServerScope-gebruiker')->assertDontSee('[VERIFY', false);
    }

    public function test_a_review_with_markers_cannot_be_made_visible(): void
    {
        $this->seed(ContentDraftsSeeder::class);
        $review = Testimonial::firstOrFail();

        try {
            $review->update(['is_visible' => true]);
            $this->fail('A review with [VERIFY] markers was made visible.');
        } catch (ValidationException) {
        }
        $this->assertFalse($review->fresh()->is_visible);

        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);
        $user->forceFill(['two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), 'two_factor_confirmed_at' => now()])->save();
        $this->actingAs($user)->withSession([RequireTwoFactor::SESSION_KEY => $user->id]);
        Filament::setCurrentPanel(Filament::getPanel('Admin'));

        Livewire::test(EditTestimonial::class, ['record' => $review->getKey()])
            ->fillForm(['is_visible' => true])
            ->call('save')
            ->assertHasFormErrors(['is_visible']);

        // Once the client approved a real name and text, it can go live.
        Livewire::test(EditTestimonial::class, ['record' => $review->getKey()])
            ->fillForm(['name' => 'Echte Naam', 'role' => 'Eigenaar', 'company' => '', 'quote' => ['nl' => 'Goedgekeurde tekst.', 'en' => 'Approved text.'], 'is_visible' => true])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertTrue($review->fresh()->is_visible);
    }
}
