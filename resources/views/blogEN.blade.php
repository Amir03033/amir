@extends('layouts.app')
@section('title', 'Amir Jebbari:home')
@section('content')
    <div class="textbackgroundblog">
        <div class="textcontentblog">
            <p></p>
            <h1> <br><span id="blogs">Mijn eerste ervaringen met laravel, PHP en Frontend Development</span></h1><br><style>
                #blogs{
                    display: flex;
                    justify-items: center;
                }
            </style>
            <p>As a first-year software development student, I have recently been working a lot with <strong>Laravel, PHP, Git, HTML, CSS, and JavaScript</strong>. It is a lot to learn, but I am starting to better understand how everything works together.</p><br>

            <h2>Working with Laravel</h2>
            <p>Laravel is my first experience with a PHP framework, and I find it impressive how it simplifies many complex tasks. Routing, databases, and authentication are easier to manage than when coding everything from scratch.</p><br>

            <code>Route::get('/posts', [PostController::class, 'index']);</code>
            <p>This ensures that when someone visits the <code>/posts</code> page, the <code>index</code> method in the <code>PostController</code> is executed.</p>

            <h2>Frontend with HTML, CSS, and JavaScript</h2><br>
            <p>Of course, I also work on the frontend. I already know a bit about HTML and CSS, but now I am learning <strong>more about JavaScript</strong> to create interactive pages.</p>

            <code>
                document.getElementById("myButton").addEventListener("click", function() {
                alert("Button clicked!");
                });
            </code>
            <p>This displays an alert when someone clicks the button. Small things, but they make a big difference in user experience.</p>

            <h2>Version Control with Git</h2><br>
            <p>Git is another tool I work with a lot. It takes some getting used to, but using <code>git commit</code> and <code>git push</code> feels more natural over time.</p>

            <code>
                git checkout -b new-feature
                git add .
                git commit -m "Added new feature"
                git push origin new-feature
            </code>
            <p>Using Git makes collaboration and version control much more organized.</p>

            <h2>What’s Next?</h2><br>
            <p>I am looking forward to <strong>learning more about databases, APIs, and complex projects</strong>. My goal is to create a fully functional web application where <strong>Laravel and JavaScript come together</strong>. Maybe I’ll write an update about that soon!</p><br>

            <p>🚀 Do you have any tips or experiences with Laravel and frontend development? Let me know!</p>

    <a href="{{ route('blog') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
        </button>
    </a>
@endsection
