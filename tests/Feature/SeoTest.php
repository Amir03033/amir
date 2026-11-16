<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Certificate;
use App\Models\Idea;
use App\Models\Project;
use App\Models\Question;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/** Search engines and AI assistants: meta, structured data, sitemap, robots, canonical host. */
class SeoTest extends TestCase
{
    use RefreshDatabase;

    private const SCHEMA_TYPES = ['Person', 'Organization', 'WebSite', 'WebPage', 'ProfilePage', 'CollectionPage', 'BreadcrumbList', 'ListItem', 'ItemList', 'CreativeWork', 'BlogPosting', 'PostalAddress', 'ImageObject', 'City'];

    private Project $project;

    private Blog $blog;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->project = Project::create([
            'title' => ['nl' => 'ServerScope', 'en' => 'ServerScope'],
            'summary' => ['nl' => 'Monitoring voor servers en domeinen.', 'en' => 'Monitoring for servers and domains.'],
            'description' => ['nl' => 'Een monitoringplatform.', 'en' => 'A monitoring platform.'],
            'tags' => 'Laravel, Filament',
            'year' => 2025,
            'jebbari_url' => ['nl' => 'https://jebbari.nl/nl/werk/serverscope', 'en' => 'https://jebbari.nl/en/work/serverscope'],
        ]);
        $this->blog = Blog::create(['slug' => 'queues', 'title' => ['nl' => 'Over queues', 'en' => 'About queues'], 'content' => ['nl' => 'Tekst over queues.', 'en' => 'Text about queues.'], 'status' => 'published']);
        Certificate::create(['title' => ['nl' => 'Laravel', 'en' => 'Laravel'], 'issuer' => 'Laracasts', 'issued_at' => now()]);
        Question::create(['body' => 'Hoe begin je?', 'answer' => 'Klein.', 'is_public' => true, 'voter_hash' => 'x']);
        Idea::create(['title' => 'RSS', 'is_public' => true, 'voter_hash' => 'x']);
    }

    /** @return list<string> indexable pages in one language */
    private function pages(string $prefix = ''): array
    {
        return array_map(fn ($p) => $prefix.$p, ['/', '/work', '/project/serverscope', '/certificates', '/ask', '/roadmap', '/blog', '/blog/queues', '/cv', '/privacy']);
    }

    private function allPages(): array
    {
        return [...$this->pages(), ...array_map(fn ($p) => $p === '/en/' ? '/en' : $p, $this->pages('/en'))];
    }

    private function dom(string $html): \DOMXPath
    {
        $doc = new \DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        return new \DOMXPath($doc);
    }

    private function graph(string $url): array
    {
        $html = $this->get($url)->assertOk()->getContent();
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $this->assertCount(1, $m[1], "{$url}: exactly one JSON-LD block");

        $data = json_decode($m[1][0], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $data['@context']);

        return $data['@graph'];
    }

    // --- JSON-LD ---

    public function test_every_page_outputs_valid_connected_json_ld(): void
    {
        foreach ($this->allPages() as $url) {
            $graph = $this->graph($url);
            $ids = array_filter(array_column($graph, '@id'));
            $this->assertSame(count($ids), count(array_unique($ids)), "{$url}: @id values are unique");

            array_walk_recursive($graph, function ($v, $k) use ($url) {
                if ($k === '@type') {
                    $this->assertContains($v, self::SCHEMA_TYPES, "{$url}: known schema.org type");
                }
            });

            // Every {"@id": …} reference points at a node in the same graph.
            $refs = [];
            $walk = function ($node) use (&$walk, &$refs) {
                if (is_array($node)) {
                    if (array_keys($node) === ['@id']) {
                        $refs[] = $node['@id'];
                    }
                    array_map($walk, $node);
                }
            };
            $walk($graph);
            foreach (array_unique($refs) as $ref) {
                $this->assertContains($ref, $ids, "{$url}: reference {$ref} resolves");
            }

            $byType = collect($graph)->keyBy('@type');
            $person = $byType['Person'];
            $this->assertSame('https://amirs.life/#amir', $person['@id']);
            $this->assertSame('Amir Jebbari', $person['name']);
            $this->assertSame('https://jebbari.nl/#org', $person['worksFor']['@id']);
            $this->assertSame(['Amsterdam', 'Zwolle'], array_column($person['address'], 'addressLocality'));
            $this->assertSame('mailto:info@jebbari.nl', $person['email']);
            $this->assertContains('https://jebbari.nl/#amir', $person['sameAs']);
            $this->assertSame('https://amirs.life/#amir', $byType['Organization']['founder']['@id']);
            $this->assertSame('https://jebbari.nl/#org', $byType['Organization']['@id']);

            if (! in_array($url, ['/', '/en'], true)) {
                $crumbs = $byType['BreadcrumbList']['itemListElement'];
                $this->assertSame(range(1, count($crumbs)), array_column($crumbs, 'position'), "{$url}: breadcrumb positions");
                foreach ($crumbs as $c) {
                    $this->assertStringStartsWith('http', $c['item']);
                }
            }
        }
    }

    public function test_home_is_a_profile_page_about_amir(): void
    {
        foreach (['/' => 'Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle.', '/en' => 'Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle.'] as $url => $sentence) {
            $graph = collect($this->graph($url))->keyBy('@type');
            $this->assertSame('https://amirs.life/#amir', $graph['ProfilePage']['mainEntity']['@id']);
            $this->assertSame($sentence, $graph['Person']['description']);
            $this->get($url)->assertSee('og:type" content="profile', false);
        }
    }

    public function test_project_and_blog_have_their_own_schema(): void
    {
        $work = collect($this->graph('/en/project/serverscope'))->keyBy('@type')['CreativeWork'];
        $this->assertSame('https://amirs.life/#amir', $work['author']['@id']);
        $this->assertSame('2025', $work['dateCreated']);
        $this->assertSame(url('/en/project/serverscope'), $work['url']);
        $this->assertSame('https://jebbari.nl/#org', $work['producer']['@id']);
        $this->assertContains('https://jebbari.nl/en/work/serverscope', $work['sameAs']);

        $post = collect($this->graph('/blog/queues'))->keyBy('@type')['BlogPosting'];
        $this->assertSame('Over queues', $post['headline']);
        $this->assertSame('https://amirs.life/#amir', $post['author']['@id']);
        $this->assertNotEmpty($post['datePublished']);
        $this->assertNotEmpty($post['dateModified']);
        $this->assertNotEmpty($post['image']);
    }

    // --- On-page ---

    public function test_titles_and_descriptions_are_unique_and_within_limits(): void
    {
        foreach (['', '/en'] as $prefix) {
            $titles = [];
            $descriptions = [];
            foreach ($this->pages($prefix) as $url) {
                $url = $url === '/en/' ? '/en' : $url;
                $x = $this->dom($this->get($url)->getContent());
                $title = trim($x->query('//title')->item(0)->textContent);
                $desc = $x->query('//meta[@name="description"]/@content')->item(0)->value;

                $this->assertLessThanOrEqual(60, mb_strlen($title), "{$url}: title '{$title}'");
                $this->assertLessThanOrEqual(155, mb_strlen($desc), "{$url}: description");
                $this->assertGreaterThan(30, mb_strlen($desc), "{$url}: description is meaningful");
                $titles[] = $title;
                $descriptions[] = $desc;
            }
            $this->assertSame(count($titles), count(array_unique($titles)), "unique titles ({$prefix})");
            $this->assertSame(count($descriptions), count(array_unique($descriptions)), "unique descriptions ({$prefix})");
        }

        $this->get('/')->assertSee('<title>Amir Jebbari – oprichter van Jebbari, Amsterdam en Zwolle</title>', false);
        $this->get('/en')->assertSee('<title>Amir Jebbari – founder of Jebbari, Amsterdam and Zwolle</title>', false);
    }

    public function test_one_h1_and_no_skipped_heading_levels(): void
    {
        foreach ($this->allPages() as $url) {
            $x = $this->dom($this->get($url)->getContent());
            $this->assertSame(1, $x->query('//h1')->length, "{$url}: exactly one h1");

            $previous = 0;
            foreach ($x->query('//main//h1|//main//h2|//main//h3|//main//h4|//main//h5|//main//h6') as $h) {
                $level = (int) substr($h->nodeName, 1);
                $this->assertLessThanOrEqual($previous + 1, $level, "{$url}: heading <{$h->nodeName}> '".trim($h->textContent)."' skips a level");
                $previous = $level;
            }
        }
    }

    public function test_every_image_has_an_alt_attribute(): void
    {
        foreach ($this->allPages() as $url) {
            foreach ($this->dom($this->get($url)->getContent())->query('//img') as $img) {
                $this->assertTrue($img->hasAttribute('alt'), "{$url}: <img src=\"{$img->getAttribute('src')}\"> has alt");
            }
        }
    }

    public function test_canonical_and_reciprocal_hreflang(): void
    {
        foreach ($this->pages() as $nl) {
            $en = $nl === '/' ? '/en' : '/en'.$nl;
            $xn = $this->dom($this->get($nl)->getContent());
            $xe = $this->dom($this->get($en)->getContent());

            $this->assertSame(url($nl === '/' ? '/' : $nl), $xn->query('//link[@rel="canonical"]/@href')->item(0)->value, "{$nl}: canonical");
            $this->assertSame(url($en), $xe->query('//link[@rel="canonical"]/@href')->item(0)->value, "{$en}: canonical");

            $alt = fn ($x, $lang) => $x->query("//link[@rel='alternate'][@hreflang='{$lang}']/@href")->item(0)->value;
            // Reciprocal: both pages point at the same pair; x-default is Dutch.
            foreach ([$xn, $xe] as $x) {
                $this->assertSame(url($nl === '/' ? '/' : $nl), $alt($x, 'nl'));
                $this->assertSame(url($en), $alt($x, 'en'));
                $this->assertSame(url($nl === '/' ? '/' : $nl), $alt($x, 'x-default'));
            }
            $this->assertStringContainsString('<html lang="nl"', $this->get($nl)->getContent());
            $this->assertStringContainsString('<html lang="en"', $this->get($en)->getContent());
        }
    }

    public function test_open_graph_and_twitter_per_page(): void
    {
        $x = $this->dom($this->get('/en/work')->getContent());
        $og = fn ($p) => $x->query("//meta[@property='{$p}']/@content")->item(0)?->value;

        $this->assertSame('en_US', $og('og:locale'));
        $this->assertSame('nl_NL', $og('og:locale:alternate'));
        $this->assertSame(url('/en/work'), $og('og:url'));
        $this->assertStringContainsString('/og/page/work.png', $og('og:image'));
        $this->assertSame('summary_large_image', $x->query("//meta[@name='twitter:card']/@content")->item(0)->value);

        $image = $this->get(route('og.page', ['key' => 'work', 'lang' => 'en']))->assertOk()->assertHeader('content-type', 'image/png');
        $this->assertSame([1200, 630], array_slice(getimagesize($image->getFile()->getPathname()), 0, 2));
        $this->get('/og/page/admin.png')->assertNotFound();
    }

    // --- Technical ---

    public function test_sitemap_lists_only_canonical_urls_with_lastmod(): void
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')->getContent());
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $locs = array_map('strval', $xml->xpath('//s:url/s:loc'));

        $this->assertContains(url('/project/serverscope'), $locs);
        $this->assertContains(url('/en/project/serverscope'), $locs);
        $this->assertContains(url('/certificates'), $locs);
        $this->assertNotContains(url('/project/'.$this->project->id), $locs, 'no id URLs');
        $this->assertNotContains(url('/status'), $locs, 'noindex pages are not listed');

        foreach ($locs as $loc) {
            $this->get(str_replace(url('/'), '', $loc) ?: '/')->assertOk(); // no redirects, no 404s
        }

        $entry = $xml->xpath('//s:url[s:loc="'.url('/project/serverscope').'"]')[0];
        $this->assertSame($this->project->updated_at->toAtomString(), (string) $entry->lastmod);
        $this->assertCount(3, $entry->children('http://www.w3.org/1999/xhtml')); // nl, en, x-default
    }

    public function test_robots_txt_points_at_the_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: https://amirs.life/sitemap.xml', $robots);
        $this->assertMatchesRegularExpression('/^Allow: \/$/m', $robots);
        $this->assertDoesNotMatchRegularExpression('/^Disallow: \/$/m', $robots);

        // No Disallow rule may block a URL from the sitemap (prefix match, like crawlers do).
        preg_match_all('/^Disallow:\s*(\S+)/m', $robots, $m);
        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());
        foreach ($xml->url as $u) {
            $path = parse_url((string) $u->loc, PHP_URL_PATH) ?: '/';
            foreach ($m[1] as $rule) {
                $this->assertFalse(str_starts_with($path, $rule), "robots.txt rule '{$rule}' blocks {$path}");
            }
        }
    }

    public function test_private_pages_are_noindex_and_public_pages_are_indexable(): void
    {
        $this->get('/amir/login')->assertHeader('X-Robots-Tag', 'noindex, follow');
        $this->get('/login')->assertHeader('X-Robots-Tag', 'noindex, follow')->assertSee('content="noindex, follow"', false);
        $this->get('/status')->assertHeader('X-Robots-Tag', 'noindex, follow');
        $this->get('/ping')->assertHeader('X-Robots-Tag', 'noindex, follow');
        $this->get('/admin')->assertNotFound();

        $home = $this->get('/');
        $this->assertFalse($home->headers->has('X-Robots-Tag'));
        $home->assertSee('content="index, follow, max-image-preview:large"', false)
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_unknown_pages_are_real_404s(): void
    {
        foreach (['/bestaat-niet', '/project/bestaat-niet', '/blog/bestaat-niet', '/en/project/bestaat-niet'] as $url) {
            $this->get($url)->assertNotFound()->assertSee('content="noindex, follow"', false)->assertDontSee('rel="canonical"', false);
        }
    }

    public function test_www_redirects_to_the_canonical_host(): void
    {
        $this->get('http://www.amirs.life/work?x=1')->assertStatus(301)->assertRedirect('https://amirs.life/work?x=1');
    }

    public function test_production_uses_the_canonical_host_everywhere(): void
    {
        URL::forceRootUrl(config('seo.url'));
        URL::forceScheme('https');

        $html = $this->get('http://amirs-preview.example/work')->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="https://amirs.life/work">', $html);
        $this->assertStringContainsString('hreflang="en" href="https://amirs.life/en/work"', $html);
    }

    public function test_llms_txt_describes_amir_and_jebbari(): void
    {
        $this->get('/llms.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('# Amir Jebbari')
            ->assertSee('Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle.')
            ->assertSee('Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle.')
            ->assertSee(url('/en/project/serverscope'))
            ->assertSee('https://jebbari.nl/en/work/serverscope')
            ->assertSee('info@jebbari.nl');

        $this->get('/llms-full.txt')->assertOk()->assertSee('## Facts')->assertSee('Monitoring for servers and domains.');
    }

    public function test_search_bots_can_crawl_but_are_not_counted(): void
    {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])->get('/')->assertOk();
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'])->get('/en/work')->assertOk();
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)'])->get('/llms.txt')->assertOk();

        $this->assertSame(0, Visit::count());
    }

    // --- Entity and content ---

    public function test_jebbari_is_linked_with_consistent_anchor_text(): void
    {
        $x = $this->dom($this->get('/')->getContent());
        $links = $x->query('//a[starts-with(@href, "https://jebbari.nl")]');
        $this->assertGreaterThanOrEqual(3, $links->length, 'hero, about and footer');
        foreach ($links as $a) {
            // Links to the agency itself use the anchor text "Jebbari"; CTA links to its request flow say what they do.
            if (preg_match('#^https://jebbari\.nl(/nl|/en)?/?(\?|$)#', $a->getAttribute('href'))) {
                $this->assertSame('Jebbari', trim($a->textContent));
            }
        }

        $this->get('/project/serverscope')->assertSee('href="https://jebbari.nl/nl/werk/serverscope?utm_source=amirs.life', false);
    }

    public function test_no_education_age_or_internship_mentions_anywhere(): void
    {
        $banned = '/\b(student|studeer|studeren|studie|opleiding|education|studying|stage|internships?|junior|18 jaar|18-year)\b/i';

        foreach ([...$this->allPages(), '/llms.txt', '/llms-full.txt'] as $url) {
            $text = strip_tags(preg_replace('#<(script|style)\b.*?</\1>#s', '', $this->get($url)->getContent()));
            $this->assertDoesNotMatchRegularExpression($banned, $text, "{$url} mentions education/age/internships");
        }

        $pdf = $this->get('/cv.pdf');
        if (str_contains((string) $pdf->headers->get('content-type'), 'pdf')) {
            $this->assertDoesNotMatchRegularExpression('/Opleiding|Education/', $pdf->getContent());
        }
    }
}
