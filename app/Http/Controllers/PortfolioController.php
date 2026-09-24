<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Certificate;
use App\Models\Project;

class PortfolioController extends Controller
{
    public function index()
    {
        return view('index', [
            'projects' => Project::latest()->get(),
            'blogs' => Blog::latest()->take(3)->get(),
            'certificates' => Certificate::orderBy('sort_order')->get(),
        ]);
    }

    public function showBlog($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        return view('blog-show', compact('blog'));
    }

    public function showProject(Project $project)
    {
        return view('project-show', compact('project'));
    }

    public function cv()
    {
        return view('cv', [
            'projects' => Project::latest()->get(),
        ]);
    }
}