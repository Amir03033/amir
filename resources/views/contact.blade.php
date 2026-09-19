@extends('layouts.app')
@section('title', 'Contact')
@section('content')

    <div class="text-content">
        <p class="contactmain">Contact</p>
        <a class="email" href="info@amirs.life">Email Me</a>
        <a class="phonenumer" href="tel:+31612121212">Bel Mij</a>
        <div class="buttons">
            <a href="https://www.instagram.com/ar.zk12/" class="btn"><i class="fa-brands fa-instagram"></i>Instagram</a>
            <a href="https://x.com/Amir1378804" class="btn"><i class="fa-solid fa-x"></i>Twitter</a>
            <a href="https://www.linkedin.com/in/amir-jebbari-19a63b330/" class="btn"> <i
                        class="fa-brands fa-linkedin"></i>Linkedin</a>
        </div>
    </div>
    <a href="{{ route('contactEN') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802"/>
            </svg>
        </button>
    </a>

@endsection