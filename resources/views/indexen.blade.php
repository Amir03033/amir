<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Amir Jebbari</title>
    <script src="https://kit.fontawesome.com/4ecee1cbb4.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    @vite(['resources/css/app.css'])
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="text-content">
            <h1>I'm<span>Amir Jebbari</span></h1>
            <p>
                I am Amir Jebbari, a passionate software developer at the beginning of my journey. Currently, I am
                working on mastering the basics of web development with HTML and CSS, and I am excited to learn how to
                create functional and visually appealing websites. As I continue my education, I look forward to
                expanding my skills and delving deeper into more advanced programming languages and frameworks. With a
                strong curiosity and drive to improve, I am determined to grow in the tech industry.</p>
            <div class="buttons">
                <a href="#" class="btn"><i class="fas fa-user"></i> More About Me</a>
                <a href="#" class=" btn-portofolio btn"><i class="fas fa-briefcase"></i>Portfolio</a>
            </div>
        </div>
        <div class="image-content">
            <img src="Amirfoto%20copy.png" alt="Amirfoto">
        </div>
    </div>
    <!--    <div class="socials">-->
    <!--        <a href="https://www.instagram.com/ar.zk12/" target="_blank">-->
    <!--            <i class="fa-brands fa-instagram"></i>-->
    <!--        </a>-->
    <!--        <a href="https://x.com/Amir1378804" target="_blank">-->
    <!--            <i class="fa-solid fa-x"></i>-->
    <!--        </a>-->
    <!--        <a href="https://www.linkedin.com/in/amir-jebbari-19a63b330/" target="_blank">-->
    <!--            <i class="fa-brands fa-linkedin"></i>-->
    <!--        </a>-->

    <!--    </div>-->
    <a href="{{ route('index') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
        </button>
    </a>
</div>
</body>
</html>