<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Support\Facades\App;

/**
 * /llms.txt and /llms-full.txt: a plain summary for AI assistants, built from config/seo.php and the
 * database so it never drifts from the site. Same canonical sentence as jebbari.nl.
 */
class LlmsController extends Controller
{
    public function index()
    {
        return $this->text($this->build(full: false));
    }

    public function full()
    {
        return $this->text($this->build(full: true));
    }

    private function build(bool $full): string
    {
        App::setLocale('en');
        $projects = Project::published()->ordered()->get();
        $notes = Blog::published()->latest('published_at')->get();
        $home = lroute('home', [], 'en');

        $out = [];
        $out[] = '# '.config('seo.name');
        $out[] = '';
        $out[] = '> '.config('seo.description.en').' He builds web applications with Laravel.';
        $out[] = '';
        $out[] = config('seo.description.nl');
        $out[] = '';
        $out[] = 'amirs.life is the personal site of '.config('seo.name').': his work, notes, a public roadmap and a place to ask him questions. '
            .config('seo.company.name').' ('.config('seo.company.url').') is his web agency. Contact: '.config('seo.email').'.';
        $out[] = '';
        $out[] = '## Main pages';
        foreach ([
            ['Home (Dutch)', lroute('home', [], 'nl'), 'who Amir Jebbari is, his work and how to reach him'],
            ['Home (English)', $home, 'the same page in English'],
            ['Work', lroute('work', [], 'en'), 'all projects with case studies'],
            ['Notes', lroute('blog.index', [], 'en'), 'articles written while building'],
            ['Ask Amir', lroute('ask', [], 'en'), 'questions from visitors, answered by Amir'],
            ['Roadmap', lroute('roadmap', [], 'en'), 'visitor ideas and their status'],
            ['CV', lroute('cv', [], 'en'), 'stack, projects and contact details'],
        ] as [$label, $url, $what]) {
            $out[] = "- [{$label}]({$url}): {$what}";
        }

        if ($projects->isNotEmpty()) {
            $out[] = '';
            $out[] = '## Projects';
            foreach ($projects as $p) {
                $line = '- ['.$p->getTranslation('title').']('.lroute('project.show', $p, 'en').'): '.$p->oneLiner();
                if ($p->jebbariUrl()) {
                    $line .= ' Built with Jebbari: '.$p->jebbariUrl();
                }
                $out[] = $line;

                if ($full) {
                    foreach (array_filter([
                        'Year' => $p->year,
                        'Role' => $p->getTranslation('role'),
                        'Stack' => implode(', ', $p->tagList()),
                        'Live' => $p->hasDemo() ? $p->demo_url : null,
                    ]) as $k => $v) {
                        $out[] = "  - {$k}: {$v}";
                    }
                    foreach ($p->caseBlocks() ?: ['about' => trim($p->getTranslation('description'))] as $k => $text) {
                        $out[] = '  - '.ucfirst($k).': '.preg_replace('/\s+/', ' ', $text);
                    }
                }
            }
        }

        if ($notes->isNotEmpty()) {
            $out[] = '';
            $out[] = '## Notes by '.config('seo.name');
            foreach ($notes as $b) {
                $out[] = '- ['.$b->getTranslation('title').']('.lroute('blog.show', $b->slug, 'en').'): '.$b->excerpt(200);
            }
        }

        if ($full) {
            $out[] = '';
            $out[] = '## About';
            $out[] = __('site.about.text');
            $out[] = __('site.about.text_2');
            $out[] = '';
            $out[] = '## Facts';
            $out[] = '- Name: '.config('seo.name');
            $out[] = '- Role: '.config('seo.role.en');
            $out[] = '- Based in: '.implode(' and ', config('seo.locations')).', the Netherlands';
            $out[] = '- Company: '.config('seo.company.name').', '.config('seo.company.url');
            $out[] = '- Email: '.config('seo.email');
            $out[] = '- Works with: '.implode(', ', config('seo.knows_about'));
        }

        $out[] = '';
        $out[] = '## Related sites and profiles';
        $out[] = '- ['.config('seo.company.name').']('.config('seo.company.url').'): web agency founded by '.config('seo.name');
        $out[] = '- [About Amir on jebbari.nl]('.config('seo.company.about.en').')';
        foreach (config('seo.same_as') as $url) {
            if (! str_starts_with($url, config('seo.company.url'))) {
                $out[] = '- ['.preg_replace('#^https?://(www\.)?#', '', $url).']('.$url.')';
            }
        }
        if (config('site.serverscope.url')) {
            $out[] = '- [ServerScope]('.config('site.serverscope.url').'): server and domain monitoring, built by '.config('seo.name');
        }

        return implode("\n", $out)."\n";
    }

    private function text(string $body)
    {
        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8', 'Cache-Control' => 'public, max-age=3600']);
    }
}
