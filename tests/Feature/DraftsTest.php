<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\BlogResource\Pages\EditBlog;
use App\Filament\Admin\Resources\BlogResource\Pages\ListBlogs;
use App\Filament\Admin\Resources\ProjectResource\Pages\EditProject;
use App\Http\Middleware\RequireTwoFactor;
use App\Models\Blog;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/** Drafts are private (owner preview only) and can't be published while [VERIFY] markers remain. */
class DraftsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);
        $user->forceFill(['two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), 'two_factor_confirmed_at' => now()])->save();
        $this->withSession([RequireTwoFactor::SESSION_KEY => $user->id]);

        return $user;
    }

    private function draftProject(): Project
    {
        return Project::create([
            'slug' => 'geheim-project',
            'title' => ['nl' => 'Geheim project', 'en' => 'Secret project'],
            'description' => ['nl' => 'Concepttekst.', 'en' => 'Draft text.'],
            'solution' => ['nl' => 'Gebouwd in [VERIFY: jaar].', 'en' => 'Built in [VERIFY: year].'],
            'tags' => 'Laravel',
            'is_published' => false,
        ]);
    }

    private function draftPost(): Blog
    {
        return Blog::create([
            'slug' => 'geheime-notitie',
            'title' => ['nl' => 'Geheime notitie', 'en' => 'Secret note'],
            'content' => ['nl' => 'Ongeveer 690 bezoeken [VERIFY: aantal].', 'en' => 'About 690 visits [VERIFY: number].'],
            'status' => 'draft',
        ]);
    }

    public function test_drafts_are_invisible_to_guests_everywhere(): void
    {
        $project = $this->draftProject();
        $this->draftPost();

        $this->get('/project/geheim-project')->assertNotFound();
        $this->get('/en/project/geheim-project')->assertNotFound();
        $this->get('/project/'.$project->id)->assertNotFound();
        $this->get('/blog/geheime-notitie')->assertNotFound();
        $this->get(route('og', ['type' => 'project', 'id' => $project->id]))->assertNotFound();

        foreach (['/', '/en', '/work', '/blog', '/cv', '/sitemap.xml', '/llms.txt', '/llms-full.txt'] as $url) {
            $this->get($url)->assertOk()
                ->assertDontSee('Geheim project')->assertDontSee('Secret project')
                ->assertDontSee('geheime-notitie')->assertDontSee('Geheime notitie');
        }
    }

    public function test_owner_can_preview_drafts_as_noindex_with_highlighted_markers(): void
    {
        $this->draftProject();
        $this->draftPost();
        $this->actingAs($this->admin());

        $this->get('/project/geheim-project')->assertOk()
            ->assertSee(__('site.preview.title'))
            ->assertSee('<mark class="verify">[VERIFY: jaar]</mark>', false)
            ->assertSee('content="noindex, follow"', false)
            ->assertDontSee('application/ld+json', false);

        $this->get('/en/blog/geheime-notitie')->assertOk()
            ->assertSee('<mark class="verify">[VERIFY: number]</mark>', false)
            ->assertSee('content="noindex, follow"', false);
    }

    public function test_logged_in_without_2fa_is_not_enough_to_preview(): void
    {
        $this->draftProject();
        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/project/geheim-project')->assertNotFound();
    }

    public function test_model_refuses_to_publish_with_markers(): void
    {
        $project = $this->draftProject();

        try {
            $project->update(['is_published' => true]);
            $this->fail('Publishing with [VERIFY] markers should throw.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('solution (nl): [VERIFY: jaar]', $e->errors()['publish'][0]);
        }
        $this->assertFalse($project->fresh()->is_published);

        $post = $this->draftPost();
        $this->expectException(ValidationException::class);
        $post->update(['status' => 'published']);
    }

    public function test_markers_removed_then_publishing_works(): void
    {
        $project = $this->draftProject();
        $project->update(['solution' => ['nl' => 'Gebouwd in 2026.', 'en' => 'Built in 2026.'], 'is_published' => true]);

        $this->assertTrue($project->fresh()->is_published);
        $this->get('/project/geheim-project')->assertOk()->assertDontSee(__('site.preview.title'));
    }

    public function test_filament_shows_where_the_markers_are(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('Admin'));
        $project = $this->draftProject();
        $post = $this->draftPost();

        Livewire::test(EditProject::class, ['record' => $project->getKey()])
            ->fillForm(['is_published' => true])
            ->call('save')
            ->assertHasFormErrors(['is_published']);
        $this->assertFalse($project->fresh()->is_published);

        Livewire::test(EditBlog::class, ['record' => $post->getKey()])
            ->fillForm(['status' => 'published'])
            ->call('save')
            ->assertHasFormErrors(['status']);

        Livewire::test(ListBlogs::class)
            ->callTableAction('publish', $post)
            ->assertNotified('Nog niet te publiceren');
        $this->assertSame('draft', $post->fresh()->status);

        $this->get('/amir/projects')->assertOk()->assertSee('Voorbeeld');
    }
}
