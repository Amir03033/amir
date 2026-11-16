<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\BlogResource;
use App\Filament\Admin\Resources\ProjectResource;
use App\Http\Middleware\RequireTwoFactor;
use App\Models\Blog;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The edit links Filament generates must open the edit page (not a 404), for slugged and slug-less records. */
class AdminEditUrlTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): void
    {
        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);
        $user->forceFill(['two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), 'two_factor_confirmed_at' => now()])->save();
        $this->actingAs($user)->withSession([RequireTwoFactor::SESSION_KEY => $user->id]);
        Filament::setCurrentPanel(Filament::getPanel('Admin'));
    }

    public function test_project_edit_links_open_the_edit_page(): void
    {
        $this->actingAsAdmin();
        $slugged = Project::create(['slug' => 'serverscope', 'title' => ['nl' => 'ServerScope', 'en' => 'ServerScope'], 'description' => ['nl' => 'x', 'en' => 'x'], 'tags' => 'PHP', 'is_published' => false]);
        $plain = Project::create(['title' => ['nl' => 'Oud', 'en' => 'Old'], 'description' => ['nl' => 'x', 'en' => 'x'], 'tags' => 'PHP']);
        $plain->forceFill(['slug' => null])->saveQuietly();

        foreach ([$slugged, $plain->fresh()] as $project) {
            $url = ProjectResource::getUrl('edit', ['record' => $project]);
            $this->get($url)->assertOk()->assertSee($project->getTranslation('title', 'nl'));
        }

        // The list page links to working edit pages.
        $this->get(ProjectResource::getUrl('index'))->assertOk()
            ->assertSee(ProjectResource::getUrl('edit', ['record' => $slugged]), false);
        $this->get('/amir/projects/'.$slugged->id.'/edit')->assertOk();
    }

    public function test_blog_edit_links_open_the_edit_page(): void
    {
        $this->actingAsAdmin();
        $post = Blog::create(['slug' => 'notitie', 'title' => ['nl' => 'Notitie', 'en' => 'Note'], 'content' => ['nl' => 'x', 'en' => 'x'], 'status' => 'draft']);

        $this->get(BlogResource::getUrl('edit', ['record' => $post]))->assertOk()->assertSee('Notitie');
    }
}
