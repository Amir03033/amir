<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\SiteMedia;
use Filament\Facades\Filament;
use App\Models\Blog;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    /** Admin with 2FA set up; actingAs() alone is not enough, the session also needs the passed-2FA flag. */
    private function admin(): User
    {
        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);
        $user->forceFill(['two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), 'two_factor_confirmed_at' => now()])->save();
        $this->withSession([\App\Http\Middleware\RequireTwoFactor::SESSION_KEY => $user->id]);

        return $user;
    }

    public function test_admin_pages_render(): void
    {
        $project = Project::create(['title' => ['nl' => 'Test', 'en' => 'Test'], 'description' => ['nl' => 'x', 'en' => 'x'], 'tags' => 'PHP', 'demo_url' => 'https://a.test']);
        $blog = Blog::create(['slug' => 'concept', 'title' => ['nl' => 'C', 'en' => 'C'], 'content' => ['nl' => 'x', 'en' => 'x'], 'status' => 'draft']);

        $this->actingAs($this->admin());

        foreach (['/amir', '/amir/projects', '/amir/projects/create', "/amir/projects/{$project->id}/edit", '/amir/blogs', "/amir/blogs/{$blog->id}/edit", '/amir/contact-messages', '/amir/certificates', '/amir/visits', '/amir/site-media', '/amir/fail2ban-status', '/amir/site-settings', '/amir/two-factor', '/amir/testimonials', '/amir/testimonials/create', '/amir/questions', '/amir/ideas', '/amir/ideas/create'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_questions_can_be_answered_and_published_in_filament(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('Admin'));
        $question = \App\Models\Question::create(['body' => 'Welke stack gebruik je het liefst?', 'voter_hash' => 'x']);
        $idea = \App\Models\Idea::create(['title' => 'Een RSS-feed', 'voter_hash' => 'x']);

        $this->get("/amir/questions/{$question->id}/edit")->assertOk();
        $this->get("/amir/ideas/{$idea->id}/edit")->assertOk();

        Livewire::test(\App\Filament\Admin\Resources\QuestionResource\Pages\EditQuestion::class, ['record' => $question->getKey()])
            ->fillForm(['answer' => 'Laravel, Filament en Alpine.', 'is_public' => true])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue($question->fresh()->is_public);
        $this->get('/ask')->assertSee('Laravel, Filament en Alpine.');

        Livewire::test(\App\Filament\Admin\Resources\IdeaResource\Pages\EditIdea::class, ['record' => $idea->getKey()])
            ->fillForm(['status' => 'building', 'is_public' => true])
            ->call('save')
            ->assertHasNoErrors();

        $this->get('/roadmap')->assertSee('Een RSS-feed')->assertSee(__('site.ideas.status.building'));
    }

    public function test_project_slug_year_and_role_are_editable(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('Admin'));
        $project = Project::create(['title' => ['nl' => 'Oude naam', 'en' => 'Old'], 'description' => ['nl' => 'x', 'en' => 'x'], 'tags' => 'PHP']);

        Livewire::test(\App\Filament\Admin\Resources\ProjectResource\Pages\EditProject::class, ['record' => $project->getKey()])
            ->fillForm(['slug' => 'nieuwe-naam', 'year' => 2025, 'role' => ['nl' => 'Bouwer', 'en' => 'Builder'], 'summary' => ['nl' => 'Eén regel.', 'en' => 'One line.']])
            ->call('save')
            ->assertHasNoErrors();

        $this->get('/project/nieuwe-naam')->assertOk()->assertSee('2025')->assertSee('Bouwer')->assertSee('Eén regel.');

        Livewire::test(\App\Filament\Admin\Resources\ProjectResource\Pages\EditProject::class, ['record' => $project->getKey()])
            ->fillForm(['slug' => '123'])->call('save')->assertHasFormErrors(['slug']);
    }

    public function test_lookalike_email_cannot_access_admin(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'x'.config('site.admin_email'), 'email_verified_at' => now()]));

        $this->get('/amir')->assertForbidden();
    }

    public function test_drafts_are_hidden_from_the_public_site(): void
    {
        Blog::create(['slug' => 'draft-post', 'title' => ['nl' => 'Geheim concept', 'en' => 'Secret draft'], 'content' => ['nl' => 'x', 'en' => 'x'], 'status' => 'draft']);
        Blog::create(['slug' => 'live-post', 'title' => ['nl' => 'Live post', 'en' => 'Live post'], 'content' => ['nl' => 'x', 'en' => 'x'], 'status' => 'published']);

        $this->get('/')->assertDontSee('Geheim concept')->assertSee('Live post');
        $this->get('/blog')->assertOk()->assertDontSee('Geheim concept')->assertSee('Live post');
        $this->get('/blog/draft-post')->assertNotFound();
        $this->get('/blog/live-post')->assertOk();
    }

    public function test_site_media_saves_and_replaces_images(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('Admin'));

        Storage::disk('public')->put('site/old.webp', 'x');
        Setting::put(['media.avatar' => 'site/old.webp']);
        Storage::disk('public')->put('site/new.webp', 'x');

        Livewire::test(SiteMedia::class)
            ->set('data.avatar', ['upload' => 'site/new.webp'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('site/new.webp', Setting::get('media.avatar'));
        Storage::disk('public')->assertMissing('site/old.webp');
        $this->get('/')->assertSee('storage/site/new.webp', false);
    }

    public function test_site_falls_back_to_bundled_images(): void
    {
        $this->get('/')->assertSee('images/amir-avatar.webp', false)->assertSee('images/og-default.png', false);
    }

    public function test_admin_traffic_is_not_counted_as_visits(): void
    {
        $this->actingAs($this->admin());

        $this->get('/amir');
        $this->post('/lang/switch', ['locale' => 'en']);
        $this->assertSame(0, \App\Models\Visit::count());

        $this->get('/');
        $this->assertSame(1, \App\Models\Visit::count());
    }
}
