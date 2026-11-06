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
        return view('project-show', [
            'project' => $project,
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
        $projects = Project::orderByDesc('featured')->orderBy('sort_order')->get();

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('cv-pdf', compact('projects'))
            ->setPaper('a4')
            ->setOption(['chroot' => [public_path(), storage_path('app/public')], 'isRemoteEnabled' => false])
            ->download('CV-'.str_replace(' ', '-', config('site.name')).'.pdf');
    }

    public function sitemap()
    {
        $urls = collect([route('home'), route('cv'), route('blog.index'), route('privacy')])
            ->merge(Project::all()->map(fn ($p) => route('project.show', $p)))
            ->merge(Blog::published()->get()->map(fn ($b) => route('blog.show', $b->slug)));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$urls->map(fn ($u) => '<url><loc>'.e($u).'</loc></url>')->implode('')
            .'</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
