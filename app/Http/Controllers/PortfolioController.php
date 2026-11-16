<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Certificate;
use App\Models\Idea;
use App\Models\Question;
use App\Models\Project;
use App\Models\Setting;
use App\Models\SiteCheck;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::published()->ordered()->get();
        // Up to three pinned chapters: the featured ones, or simply the first three.
        $chapters = $projects->where('featured', true)->take(3);
        if ($chapters->isEmpty()) {
            $chapters = $projects->take(3);
        }

        return view('index', [
            'chapters' => $chapters->values(),
            'more' => $projects->reject(fn ($p) => $chapters->contains('id', $p->id))->take(6)->values(),
            'projectCount' => $projects->count(),
            'blogs' => Blog::published()->latest('published_at')->latest()->take(3)->get(),
            'blogCount' => Blog::published()->count(),
            'certificates' => Certificate::orderBy('sort_order')->orderByDesc('issued_at')->get(),
            'testimonials' => Testimonial::visible()->with('project')->get(),
            'now' => Setting::get('now.'.app()->getLocale()) ?: Setting::get('now.nl'),
            'questions' => Question::published()->take(3)->get(),
            'ideas' => Idea::published()->whereIn('status', Idea::VOTABLE)->ranked()->take(5)->get(),
            'voted' => IdeaController::votedIds($request),
            'pingSites' => Cache::remember('ping-sites', 60, fn () => self::pingSites()),
        ]);
    }

    /** Latest real measurement per site (from site:check), Jebbari and ServerScope first. */
    private static function pingSites(): \Illuminate\Support\Collection
    {
        $first = ['Jebbari', 'ServerScope'];

        return SiteCheck::latestByUrl()
            ->map(fn ($c) => [
                'label' => $c->label,
                'host' => preg_replace('/^www\./', '', (string) parse_url($c->url, PHP_URL_HOST)),
                'ms' => $c->response_ms,
                'ok' => $c->ok,
                'at' => $c->checked_at,
            ])
            ->sortBy(fn ($s) => [in_array($s['label'], $first, true) ? array_search($s['label'], $first, true) : 9, $s['label']])
            ->values();
    }

    public function blogs()
    {
        return view('blog-index', [
            'blogs' => Blog::published()->latest('published_at')->latest()->get(),
        ]);
    }

    public function showBlog($slug)
    {
        // Drafts exist only for the logged-in owner (preview); guests get a 404.
        $blog = Blog::query()->when(! can_preview(), fn ($q) => $q->published())->where('slug', $slug)->firstOrFail();

        return view('blog-show', compact('blog'));
    }

    public function work()
    {
        $projects = Project::published()->ordered()->get();

        return view('work', [
            'projects' => $projects,
            'tags' => $projects->flatMap->tagList()->countBy()->sortDesc()->keys()->take(8)->values(),
        ]);
    }

    public function showProject(Request $request, Project $project)
    {
        abort_unless($project->is_published || can_preview(), 404);

        // Old links used the numeric id: send them (and search engines) to the slug URL for good.
        if ($request->route()->originalParameter('project') !== (string) $project->getRouteKey()) {
            return redirect()->to(lroute('project.show', $project), 301);
        }

        $ordered = Project::published()->ordered()->get(['id', 'slug', 'title', 'year', 'image']);
        if (! $project->is_published) {
            $ordered->push($project); // a previewed draft still gets previous/next links
        }
        $position = $ordered->search(fn ($p) => $p->id === $project->id);
        $url = $project->hasDemo() ? $project->demo_url : null;

        return view('project-show', [
            'project' => $project,
            'uptimeDays' => $url ? SiteCheck::daily($url) : collect(),
            'uptime' => $url ? SiteCheck::uptime($url, 30) : null,
            'measuredSince' => $url ? SiteCheck::firstCheck($url) : null,
            'previous' => $ordered->count() > 1 ? $ordered[($position - 1 + $ordered->count()) % $ordered->count()] : null,
            'next' => $ordered->count() > 1 ? $ordered[($position + 1) % $ordered->count()] : null,
        ]);
    }

    public function certificates()
    {
        return view('certificates', [
            'certificates' => Certificate::orderBy('sort_order')->orderByDesc('issued_at')->get(),
        ]);
    }

    public function cv()
    {
        return view('cv', [
            'projects' => Project::published()->ordered()->get(),
        ]);
    }

    public function cvPdf()
    {
        // Without the PDF package (e.g. a deploy where composer did not run) fall back to the printable CV page.
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->lroute('cv');
        }

        $projects = Project::published()->ordered()->get();
        \App\Models\Event::record('cv_download');

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('cv-pdf', compact('projects'))
            ->setPaper('a4')
            ->setOption(['chroot' => [public_path(), storage_path('app/public')], 'isRemoteEnabled' => false])
            ->download('CV-'.str_replace(' ', '-', config('site.name')).'.pdf');
    }

    /**
     * sitemap.xml: canonical URLs only (slugs, no redirects, nothing noindex), both languages with
     * reciprocal hreflang, lastmod from real updated_at values. Built on request, so it is always current.
     */
    public function sitemap()
    {
        $projects = Project::published()->ordered()->get(['id', 'slug', 'updated_at']);
        $blogs = Blog::published()->get(['id', 'slug', 'updated_at', 'published_at', 'created_at']);
        $max = fn (...$dates) => collect($dates)->filter()->max();

        $projectsAt = $projects->max('updated_at');
        $blogsAt = $blogs->max('updated_at');
        $certsAt = Certificate::max('updated_at');
        $askAt = Question::published()->max('updated_at');
        $roadmapAt = Idea::published()->max('updated_at');

        $pages = collect([
            ['home', [], $max($projectsAt, $blogsAt, $certsAt, $askAt, $roadmapAt)],
            ['work', [], $projectsAt],
            ['certificates', [], $certsAt],
            ['ask', [], $askAt],
            ['roadmap', [], $roadmapAt],
            ['blog.index', [], $blogsAt],
            ['cv', [], $projectsAt],
            ['privacy', [], '2026-11-16'],
        ])
            ->merge($projects->map(fn ($p) => ['project.show', [$p], $p->updated_at]))
            ->merge($blogs->map(fn ($b) => ['blog.show', [$b->slug], $b->updated_at ?? $b->publishedDate()]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        foreach ($pages as [$name, $params, $lastmod]) {
            $urls = ['nl' => lroute($name, $params, 'nl'), 'en' => lroute($name, $params, 'en')];
            $alternates = collect($urls)->map(fn ($u, $l) => '<xhtml:link rel="alternate" hreflang="'.$l.'" href="'.e($u).'"/>')->implode('')
                .'<xhtml:link rel="alternate" hreflang="x-default" href="'.e($urls['nl']).'"/>';
            $mod = $lastmod ? '<lastmod>'.\Illuminate\Support\Carbon::parse($lastmod)->toAtomString().'</lastmod>' : '';

            foreach ($urls as $url) {
                $xml .= '<url><loc>'.e($url).'</loc>'.$mod.$alternates.'</url>';
            }
        }

        return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml', 'Cache-Control' => 'public, max-age=3600']);
    }
}
