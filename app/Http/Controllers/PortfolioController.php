<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Blog;

class PortfolioController extends Controller
{
    public function index()
    {
        return view('index', [
            'projects' => Project::latest()->get(),
            'blogs' => Blog::latest()->take(3)->get() // Alleen de 3 nieuwste op de home
        ]);
    }

    public function showBlog($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return view('blog-show', compact('blog'));
    }
}