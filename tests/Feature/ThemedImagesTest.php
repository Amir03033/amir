<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\ProjectResource\Pages\EditProject;
use App\Http\Middleware\RequireTwoFactor;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/** A project can have a light and a dark version of each screenshot; a missing one falls back to the other. */
class ThemedImagesTest extends TestCase
{
    use RefreshDatabase;

    private function project(array $attributes): Project
    {
        return Project::create([
            'slug' => 'shop',
            'title' => ['nl' => 'Shop', 'en' => 'Shop'],
            'description' => ['nl' => 'x', 'en' => 'x'],
            'tags' => 'PHP',
            ...$attributes,
        ]);
    }

    public function test_cover_with_both_versions_renders_one_image_per_theme(): void
    {
        $this->project(['image' => 'projects/l.webp', 'image_dark' => 'projects/d.webp']);

        $html = $this->get('/project/shop')->assertOk()->getContent();
        $this->assertStringContainsString('storage/projects/l.webp', $html);
        $this->assertStringContainsString('storage/projects/d.webp', $html);
        $this->assertMatchesRegularExpression('#<img src="[^"]*projects/l\.webp"[^>]*class="[^"]*only-light#', $html);
        $this->assertMatchesRegularExpression('#<img src="[^"]*projects/d\.webp"[^>]*class="[^"]*only-dark#', $html);
    }

    public function test_a_missing_version_falls_back_to_the_other(): void
    {
        $onlyDark = $this->project(['image' => null, 'image_dark' => 'projects/d.webp']);
        $this->assertStringEndsWith('projects/d.webp', $onlyDark->coverUrl());
        $this->assertStringEndsWith('projects/d.webp', $onlyDark->coverDarkUrl());

        $html = $this->get('/project/shop')->getContent();
        $this->assertStringNotContainsString('only-light', $html);   // one image, shown in both themes
        $this->assertStringContainsString('storage/projects/d.webp', $html);

        $onlyDark->forceFill(['image' => 'projects/l.webp', 'image_dark' => null])->save();
        $this->assertStringEndsWith('projects/l.webp', $onlyDark->fresh()->coverDarkUrl());
    }

    public function test_gallery_items_pair_versions_and_fall_back_per_item(): void
    {
        $project = $this->project([
            'gallery' => ['projects/gallery/a.webp', null, 'projects/gallery/c.webp'],
            'gallery_dark' => ['projects/gallery/a-dark.webp', 'projects/gallery/b-dark.webp'],
            'gallery_alts' => [['nl' => 'A', 'en' => 'A'], ['nl' => 'B', 'en' => 'B'], ['nl' => 'C', 'en' => 'C']],
        ]);

        $items = $project->galleryItems();
        $this->assertCount(3, $items);
        $this->assertStringEndsWith('a.webp', $items[0]['light']);
        $this->assertStringEndsWith('a-dark.webp', $items[0]['dark']);
        $this->assertStringEndsWith('b-dark.webp', $items[1]['light']);   // only dark: used for light too
        $this->assertStringEndsWith('c.webp', $items[2]['dark']);         // only light: used for dark too
        $this->assertSame('B', $items[1]['alt']);

        $this->get('/project/shop')->assertOk()->assertSee('gallery/a-dark.webp', false)->assertSee('gallery/b-dark.webp', false);
    }

    public function test_admin_rows_round_trip_to_the_columns(): void
    {
        $columns = Project::galleryColumns([
            ['light' => 'projects/gallery/a.webp', 'dark' => ['uuid-1' => 'projects/gallery/a-dark.webp'], 'alt_nl' => 'A', 'alt_en' => 'A'],
            ['light' => null, 'dark' => null, 'alt_nl' => 'leeg', 'alt_en' => 'empty'],   // dropped
            ['light' => null, 'dark' => 'projects/gallery/b-dark.webp', 'alt_nl' => 'B', 'alt_en' => 'B'],
        ]);

        $this->assertSame(['projects/gallery/a.webp', null], $columns['gallery']);
        $this->assertSame(['projects/gallery/a-dark.webp', 'projects/gallery/b-dark.webp'], $columns['gallery_dark']);
        $this->assertSame([['nl' => 'A', 'en' => 'A'], ['nl' => 'B', 'en' => 'B']], $columns['gallery_alts']);

        $rows = Project::galleryRows($columns);
        $this->assertSame('projects/gallery/b-dark.webp', $rows[1]['dark']);
        $this->assertNull($rows[1]['light']);

        // Light-only galleries don't store a list of nulls for the dark side.
        $this->assertSame([], Project::galleryColumns([['light' => 'x.webp', 'dark' => null]])['gallery_dark']);
    }

    public function test_saving_in_filament_keeps_the_gallery_and_markers_in_alts_still_block_publishing(): void
    {
        $user = User::factory()->create(['email' => config('site.admin_email'), 'email_verified_at' => now()]);
        $user->forceFill(['two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), 'two_factor_confirmed_at' => now()])->save();
        $this->actingAs($user)->withSession([RequireTwoFactor::SESSION_KEY => $user->id]);
        Filament::setCurrentPanel(Filament::getPanel('Admin'));
        Storage::fake('public');
        Storage::disk('public')->put('projects/gallery/a.webp', 'x');
        Storage::disk('public')->put('projects/gallery/a-dark.webp', 'x');

        $project = $this->project([
            'is_published' => false,
            'gallery' => ['projects/gallery/a.webp'],
            'gallery_dark' => ['projects/gallery/a-dark.webp'],
            'gallery_alts' => [['nl' => 'Scherm [VERIFY: klopt dit?]', 'en' => 'Screen']],
        ]);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertSet('data.gallery_rows', fn ($rows) => count($rows) === 1)
            ->fillForm(['is_published' => true])
            ->call('save')
            ->assertHasFormErrors(['is_published']);

        $this->assertFalse($project->fresh()->is_published);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->call('save')
            ->assertHasNoFormErrors();

        $fresh = $project->fresh();
        $this->assertSame(['projects/gallery/a.webp'], $fresh->gallery);
        $this->assertSame(['projects/gallery/a-dark.webp'], $fresh->gallery_dark);
        $this->assertSame('Scherm [VERIFY: klopt dit?]', $fresh->gallery_alts[0]['nl']);
    }
}
