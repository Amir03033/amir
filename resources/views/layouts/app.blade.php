<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/4ecee1cbb4.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>@yield("title")</title>
    @vite(['resources/css/legacy.css'])
</head>
<body id="grad">
<div class="container">

<nav class="sidebar">
    <ul>
        <li><a href="{{ route('home') }}" class="active"><i class="fas fa-home"></i> <span>Home</span></a></li>
        <li><a href="{{ route('aboutme') }}"><i class="fas fa-user"></i> <span>Over mij</span></a></li>
        <li><a href="{{ route('portfolio') }}"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a></li>
        <li><a href="{{ route('blog') }}"><i class="fas fa-blog"></i> <span>Blog</span></a></li>
        <li><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
    </ul>
</nav>
    <a href="{{ route('appEn') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
        </button>
    </a>

<div class="main-content">
    @yield('content')
</div>
</div>
</body>
</html>