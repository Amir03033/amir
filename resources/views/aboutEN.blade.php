@extends('layouts.app')
@section('title', 'Over mij')
@section('content')
    <div class="textbackground">
        <div class="text-content">
            <h1>I am<span> Amir Jebbari</span></h1>
            <p>
                My name is <strong>Amir Jebbari</strong>, I am 18 years old and live in Overijsel.
                As an only child, I have always had the space and freedom to discover myself
                and develop my interests. This has given me a broad perspective on life and
                a clear understanding of what motivates me.
            </p>

            <p>
                In my free time, I am very engaged in <strong>fitness</strong>. I believe it is
                important to stay strong both physically and mentally. I also enjoy spending
                time with friends — from working out together to simply hanging out. These
                social moments give me energy and help me maintain a good balance in life.
            </p>

            <p>
                One of my biggest passions is <strong>programming</strong>. I enjoy working on
                projects and solving technical problems. It gives me satisfaction to build
                something from scratch and continuously improve my skills. I also see strong
                opportunities for my future in this field.
            </p>

            <p>
                I also have an <strong>Iranian background</strong>, which I am very proud of.
                My culture and heritage play an important role in who I am. It has shaped my
                values and gives me a unique identity that I carry with me everywhere.
            </p>
        </div>
    </div>
    <a href="{{ route('aboutme') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802"/>
            </svg>
        </button>
    </a>
@endsection
