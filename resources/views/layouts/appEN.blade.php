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
    @vite(['resources/css/app.css'])

{{--    @if(Route::currentRouteName() === 'home')--}}
{{--        @vite(['resources/css/home.css'])--}}
{{--    @endif--}}

</head>
<body id="grad">
<div class="container">
    <nav class="sidebar">
        <ul>
            <li><a href="{{ route('home') }}" class="active"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="{{ route('aboutme') }}"><i class="fas fa-user"></i> <span>aaaaa me</span></a></li>
            <li><a href="{{ route('portfolio') }}"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a></li>
            <li><a href="{{ route('blog') }}"><i class="fas fa-blog"></i> <span>Blog</span></a></li>
            <li><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
        </ul>
    </nav>


    <div class="main-content">
        @yield('content')
    </div>
</div>
</body>
</html>