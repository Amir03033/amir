<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Certificate;
use App\Models\Project;
use App\Models\SiteCheck;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    public function index()
    {
        $projects = Project::orderByDesc('featured')->orderBy('sort_order')->latest()->get();

        return view('index', [
            'projects' => $projects,
            'blogs' => Blog::published()->latest('published_at')->latest()->take(3)->get(),
            'blogCount' => Blog::published()->count(),
            'certificates' => Certificate::orderBy('sort_order')->get(),
            'testimonials' => Testimonial::visible()->with('project')->get(),
            'dashboard' => Cache::remember('status-panel', 300, fn () => SiteCheck::dashboard()),
            'live' => Cache::remember('status-live', 300, fn () => SiteCheck::latestByUrl()),
            'tags' => $projects->flatMap->tagList()->countBy()->sortDesc()->keys()->take(6)->values(),
        ]);
    }

    public function blogs()
    {
        return view('blog-index', [
            'blogs' => Blog::published()->latest('published_at')->latest()->get(),
        ]);
    }

    public function showBlog($slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        return view('blog-show', compact('blog'));
    }

    public function showProject(Project $project)
    {
        $url = $project->hasDemo() ? $project->demo_url : null;

        return view('project-show', [
            'project' => $project,
            'uptimeDays' => $url ? SiteCheck::daily($url) : collect(),
            'uptime' => $url ? SiteCheck::uptime($url, 30) : null,
            'measuredSince' => $url ? SiteCheck::firstCheck($url) : null,
            'next' => Project::where('id', '!=', $project->id)->orderByDesc('featured')->orderBy('sort_order')->first(),
        ]);
    }

    public function cv()
    {
        return view('cv', [
            'projects' => Project::orderByDesc('featured')->orderBy('sort_order')->get(),
        ]);
    }

    public function cvPdf()
    {
        // Without the PDF package (e.g. a deploy where composer did not run) fall back to the printable CV page.
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->lroute('cv');
        }

        $projects = Project::orderByDesc('featured')->orderBy('sort_order')->get();
        \App\Models\Event::record('cv_download');

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('cv-pdf', compact('projects'))
            ->setPaper('a4')
            ->setOption(['chroot' => [public_path(), storage_path('app/public')], 'isRemoteEnabled' => false])
            ->download('CV-'.str_replace(' ', '-', config('site.name')).'.pdf');
    }

    public function sitemap()
    {
        $pages = collect([['home', []], ['cv', []], ['blog.index', []], ['status', []], ['privacy', []]])
            ->merge(Project::all()->map(fn ($p) => ['project.show', [$p]]))
            ->merge(Blog::published()->get()->map(fn ($b) => ['blog.show', [$b->slug]]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        foreach ($pages as [$name, $params]) {
            $urls = ['nl' => lroute($name, $params, 'nl'), 'en' => lroute($name, $params, 'en')];
            $alternates = collect($urls)->map(fn ($u, $l) => '<xhtml:link rel="alternate" hreflang="'.$l.'" href="'.e($u).'"/>')->implode('');

            foreach ($urls as $url) {
                $xml .= '<url><loc>'.e($url).'</loc>'.$alternates.'</url>';
            }
        }

        return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml']);
    }
}
