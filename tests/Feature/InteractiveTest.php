<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\IdeaVote;
use App\Models\Project;
use App\Models\Question;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Ask Amir, ideas + votes, the roadmap, Ping and slug URLs. */
class InteractiveTest extends TestCase
{
    use RefreshDatabase;

    // --- Ask Amir ---

    public function test_question_is_stored_privately(): void
    {
        $this->postJson('/ask', ['body' => 'Hoe begin je een webbureau naast je studie?', 'name' => 'Sam'])
            ->assertOk()->assertJson(['success' => true]);

        $q = Question::sole();
        $this->assertSame('Sam', $q->name);
        $this->assertFalse($q->is_public);
        $this->assertSame(64, strlen($q->voter_hash));
        $this->get('/ask')->assertDontSee('Hoe begin je een webbureau');
    }

    public function test_question_validation_is_localized(): void
    {
        $this->postJson('/ask', ['body' => 'kort', 'locale' => 'nl'])
            ->assertStatus(422)->assertJsonPath('errors.body.0', 'Vraag moet minstens 10 tekens zijn.');
        $this->postJson('/ask', ['body' => 'short', 'email' => 'nope', 'locale' => 'en'])
            ->assertStatus(422)->assertJsonValidationErrors(['body', 'email']);
    }

    public function test_question_honeypot_drops_bots(): void
    {
        $this->postJson('/ask', ['body' => 'Buy cheap followers now please', 'website' => 'http://spam.test'])->assertOk();

        $this->assertSame(0, Question::count());
    }

    public function test_question_endpoint_is_throttled(): void
    {
        foreach (range(1, 3) as $i) {
            $this->postJson('/ask', ['body' => "Een echte vraag nummer {$i}?"])->assertOk();
        }

        $this->postJson('/ask', ['body' => 'Een vierde vraag, te snel achter elkaar.'])->assertStatus(429);
    }

    public function test_only_answered_public_questions_are_shown(): void
    {
        Question::create(['body' => 'Onbeantwoord maar publiek?', 'is_public' => true, 'voter_hash' => 'x']);
        Question::create(['body' => 'Beantwoord maar privé?', 'answer' => 'Ja.', 'voter_hash' => 'x']);
        Question::create(['body' => 'Beantwoord en publiek?', 'answer' => 'Dit is het antwoord.', 'is_public' => true, 'voter_hash' => 'x', 'email' => 'geheim@example.com']);

        $this->get('/ask')->assertOk()
            ->assertSee('Beantwoord en publiek?')->assertSee('Dit is het antwoord.')
            ->assertDontSee('Onbeantwoord maar publiek?')->assertDontSee('Beantwoord maar privé?')
            ->assertDontSee('geheim@example.com');
        $this->get('/')->assertSee('Dit is het antwoord.');
    }

    public function test_clearing_an_answer_unpublishes_the_question(): void
    {
        $q = Question::create(['body' => 'Een vraag?', 'answer' => 'Antwoord', 'is_public' => true, 'voter_hash' => 'x']);
        $this->assertNotNull($q->answered_at);

        $q->update(['answer' => null]);

        $this->assertFalse($q->fresh()->is_public);
        $this->assertNull($q->fresh()->answered_at);
    }

    // --- Ideas, votes, roadmap ---

    public function test_new_idea_is_hidden_until_approved(): void
    {
        $this->postJson('/ideas', ['title' => 'Een RSS-feed voor notities', 'detail' => 'Zodat ik ze kan volgen.'])->assertOk();

        $idea = Idea::sole();
        $this->assertSame('new', $idea->status);
        $this->assertFalse($idea->is_public);
        $this->get('/roadmap')->assertDontSee('Een RSS-feed voor notities');

        $idea->update(['is_public' => true, 'status' => 'planned']);
        $this->get('/roadmap')->assertSee('Een RSS-feed voor notities')->assertSee(__('site.ideas.status.planned'));
    }

    public function test_one_vote_per_visitor(): void
    {
        $idea = Idea::create(['title' => 'Dark mode', 'is_public' => true, 'voter_hash' => 'x']);
        // Keep one browser session across requests, like a real visitor (fetch sends the session cookie).
        $this->withCredentials()->withCookie(config('session.cookie'), str_repeat('a', 40));

        $this->postJson("/ideas/{$idea->id}/vote")->assertOk()->assertJson(['voted' => true, 'votes' => 1]);
        $this->postJson("/ideas/{$idea->id}/vote")->assertOk()->assertJson(['votes' => 1]);

        // Same session from another IP still counts as the same voter.
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])->postJson("/ideas/{$idea->id}/vote")->assertJson(['votes' => 1]);

        $this->assertSame(1, IdeaVote::count());
        $this->assertSame(1, $idea->fresh()->votes_count);

        // A different visitor (new IP, new session) can vote.
        $this->withCookie(config('session.cookie'), str_repeat('b', 40))
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.10'])->postJson("/ideas/{$idea->id}/vote")->assertJson(['votes' => 2]);
    }

    public function test_hidden_or_closed_ideas_cannot_be_voted_on(): void
    {
        $hidden = Idea::create(['title' => 'Verborgen', 'is_public' => false, 'voter_hash' => 'x']);
        $shipped = Idea::create(['title' => 'Al live', 'is_public' => true, 'status' => 'shipped', 'voter_hash' => 'x']);

        $this->postJson("/ideas/{$hidden->id}/vote")->assertNotFound();
        $this->postJson("/ideas/{$shipped->id}/vote")->assertNotFound();
        $this->assertSame(0, IdeaVote::count());
    }

    public function test_idea_endpoints_are_throttled(): void
    {
        foreach (range(1, 3) as $i) {
            $this->postJson('/ideas', ['title' => "Idee nummer {$i}"])->assertOk();
        }
        $this->postJson('/ideas', ['title' => 'Idee nummer 4'])->assertStatus(429);
    }

    // --- Ping, slugs, pages ---

    public function test_ping_endpoint_is_tiny_and_not_counted_as_a_visit(): void
    {
        $this->get('/ping')->assertNoContent()->assertHeader('Cache-Control', 'no-store, private');

        $this->assertSame(0, Visit::count());
    }

    public function test_project_ids_redirect_permanently_to_slugs(): void
    {
        $project = Project::create(['title' => ['nl' => 'Jungle Tuinen', 'en' => 'Jungle Gardens'], 'description' => ['nl' => 'x'], 'tags' => 'Vue']);

        $this->assertSame('jungle-tuinen', $project->slug);
        $this->get('/project/'.$project->id)->assertStatus(301)->assertRedirect(url('/project/jungle-tuinen'));
        $this->get('/en/project/'.$project->id)->assertStatus(301)->assertRedirect(url('/en/project/jungle-tuinen'));
        $this->get('/project/jungle-tuinen')->assertOk()->assertSee('Jungle Tuinen');
        $this->get('/project/does-not-exist')->assertNotFound();
    }

    public function test_slugs_stay_unique_and_never_numeric(): void
    {
        $a = Project::create(['title' => ['nl' => 'Shop'], 'description' => ['nl' => 'x'], 'tags' => 'PHP']);
        $b = Project::create(['title' => ['nl' => 'Shop'], 'description' => ['nl' => 'x'], 'tags' => 'PHP']);
        $c = Project::create(['title' => ['nl' => '2024'], 'description' => ['nl' => 'x'], 'tags' => 'PHP']);

        $this->assertSame(['shop', 'shop-2', '2024-2'], [$a->slug, $b->slug, $c->slug]);
    }

    public function test_new_pages_render_in_both_languages(): void
    {
        foreach (['/work', '/certificates', '/ask', '/roadmap', '/en/work', '/en/certificates', '/en/ask', '/en/roadmap'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/sitemap.xml')->assertSee(url('/roadmap'), false)->assertSee(url('/en/ask'), false);
    }

    public function test_no_stock_images_or_old_email_on_public_pages(): void
    {
        Project::create(['title' => ['nl' => 'KlusKlaar'], 'description' => ['nl' => 'x'], 'tags' => 'PHP']);

        foreach (['/', '/work', '/project/klusklaar', '/cv'] as $url) {
            $this->get($url)->assertDontSee('images/projects/klusklaar', false)->assertDontSee('mastermind.webp', false)
                ->assertDontSee('gmail.com', false)->assertSee('info@jebbari.nl', false);
        }
    }
}
