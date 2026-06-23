{{--@extends('layouts.app')--}}
{{--@section('title', 'Amir Jebbari')--}}
{{--@section('content')--}}
{{--    <div class="BackgroundIndex">--}}
{{--<div class="text-content">--}}
{{--    <h1>Ik ben <span>Amir Jebbari</span></h1>--}}
{{--    <p>--}}
{{--        Ik ben <strong>Amir Jebbari</strong>, een gedreven softwareontwikkelaar aan het begin van mijn carrière.--}}
{{--        Momenteel ben ik actief bezig met het ontwikkelen van mijn vaardigheden binnen webontwikkeling,--}}
{{--        waarbij ik werk met HTML en CSS als basis, maar ook steeds meer ervaring opdoe met moderne--}}
{{--        technologieën zoals Node.js, Vue.js en Laravel.--}}
{{--    </p>--}}

{{--    <p>--}}
{{--        Ik vind het interessant om te werken met <strong>API’s</strong> en leer hoe ik dynamische en interactieve--}}
{{--        applicaties kan bouwen die zowel functioneel als gebruiksvriendelijk zijn. Door praktijkgericht--}}
{{--        te werken aan projecten blijf ik mezelf continu uitdagen en verbeteren.--}}
{{--    </p>--}}

{{--    <p>--}}
{{--        Terwijl ik mijn opleiding voortzet, richt ik me op het verdiepen van mijn kennis en het verbreden--}}
{{--        van mijn technische stack. Met een sterke nieuwsgierigheid en een duidelijke motivatie om te groeien,--}}
{{--        ben ik vastberaden om mezelf te ontwikkelen tot een professionele developer en een waardevolle--}}
{{--        bijdrage te leveren binnen de tech-industrie.--}}
{{--    </p>--}}
{{--    <div class="buttons">--}}
{{--        <a href="{{ route('contact') }}" class="btn"><i class="fas fa-user"></i> Contact</a>--}}
{{--        <a href="{{ route('portfolio') }}" class=" btn-portofolio btn"><i class="fas fa-briefcase"></i>Portfolio</a>--}}
{{--    </div>--}}
{{--    <a href="{{ route('index-en') }}">--}}
{{--        <button class="slide-button">--}}
{{--            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">--}}
{{--                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />--}}
{{--            </svg>--}}
{{--        </button>--}}
{{--    </a>--}}
{{--    <li><a href="{{ route('home') }}" class="active"><i class="fas fa-home"></i> <span>Home</span></a></li>--}}

{{--</div>--}}
{{--    </div>--}}
{{--<div class="image-content">--}}
{{--    <img src="Amirfoto%20copy.png" alt="Amirfoto">--}}
{{--</div>--}}
{{--@endsection--}}
@extends('layouts.layout')

@section('nav')
    <nav class="fixed top-0 left-0 w-full z-50 backdrop-blur-md border-b border-slate-900/50 bg-cyber-bg/70">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="#home" class="text-lg font-mono font-bold tracking-wider bg-gradient-to-r from-cyan-400 to-violet-500 bg-clip-text text-transparent">AMIR<span class="text-white">_</span></a>
            <div class="flex items-center space-x-8">
                <div class="hidden md:flex space-x-6 text-xs uppercase tracking-widest font-mono text-slate-400">
                    <a href="#home" class="hover:text-cyan-400 transition" x-text="messages.nav.home"></a>
                    <a href="#about" class="hover:text-cyan-400 transition" x-text="messages.nav.about"></a>
                    <a href="#portfolio" class="hover:text-cyan-400 transition" x-text="messages.nav.portfolio"></a>
                    <a href="#blog" class="hover:text-cyan-400 transition" x-text="messages.nav.blog"></a>
                    <a href="#contact" class="hover:text-cyan-400 transition" x-text="messages.nav.contact"></a>
                </div>
                <button @click="switchLanguage(locale === 'nl' ? 'en' : 'nl')" class="relative inline-flex items-center justify-between w-14 h-7 p-1 rounded-full bg-slate-900 border border-slate-800 cursor-pointer">
                    <span class="text-[9px] font-bold z-10 pl-1" :class="locale === 'nl' ? 'text-slate-900' : 'text-slate-500'">EN</span>
                    <span class="text-[9px] font-bold z-10 pr-1" :class="locale === 'en' ? 'text-slate-900' : 'text-slate-500'">NL</span>
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 transition-transform duration-300" :class="locale === 'nl' ? 'transform translate-x-7' : ''"></span>
                </button>
            </div>
        </div>
    </nav>
@endsection

@section('content')
    <!-- HOME / HERO -->
    <x-section id="home" class="min-h-screen flex items-center justify-center pt-16">
        <div class="text-center space-y-6 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyan-500/20 bg-cyan-500/5 text-cyan-400 text-xs font-mono tracking-widest uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span> Level 18 Dev Located in Zwolle
            </div>
            <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white">Amir Jebbari</h1>
            <p class="text-lg md:text-xl font-mono text-cyan-400/80" x-text="messages.home.subtitle"></p>
            <p class="text-slate-400 leading-relaxed text-sm md:text-base" x-text="messages.home.text"></p>
            <div class="pt-4">
                <a href="#portfolio" class="px-6 py-3 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold shadow-lg shadow-cyan-500/10 hover:shadow-cyan-500/30 transition transform hover:-translate-y-0.5 text-sm" x-text="messages.home.cta"></a>
            </div>
        </div>
    </x-section>

    <!-- OVER MIJ -->
    <x-section id="about" bg="card">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
            <div class="border border-slate-800 bg-cyber-card p-6 rounded-xl space-y-4 font-mono text-xs relative">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800 pb-2" x-text="messages.about.details"></h3>
                <div><span class="text-slate-500 block" x-text="messages.about.age"></span><span class="text-cyan-400 text-sm font-bold">18</span></div>
                <div><span class="text-slate-500 block" x-text="messages.about.location"></span><span class="text-slate-300 text-sm">Zwolle</span></div>
                <div><span class="text-slate-500 block">Culture / Roots</span><span class="text-violet-400 text-sm">Iranian Pride 🇮🇷</span></div>
                <div><span class="text-slate-500 block" x-text="messages.about.interests"></span><span class="text-slate-300 text-xs" x-text="messages.about.interests_list"></span></div>
            </div>
            <div class="md:col-span-2 space-y-4">
                <h2 class="text-2xl font-bold font-mono text-white flex items-center gap-2"><span class="text-cyan-500">//</span><span x-text="messages.about.title"></span></h2>
                <p class="text-slate-400 text-sm md:text-base leading-relaxed" x-text="messages.about.text"></p>
            </div>
        </div>
    </x-section>

    <!-- PORTFOLIO -->
    <x-section id="portfolio" cyberTitle="messages.nav.portfolio">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="border border-slate-900 bg-cyber-card p-5 rounded-xl group hover:border-cyan-500/40 transition-all duration-300">
                    <div class="h-40 bg-slate-950 rounded-lg mb-4 overflow-hidden flex items-center justify-center border border-slate-900 text-slate-700 font-mono text-xs">
                        @if($project->image) <img src="{{ asset('storage/' . $project->image) }}" class="w-full h-full object-cover"> @else [img_placeholder] @endif
                    </div>
                    <h3 class="text-md font-bold text-white mb-1">{{ $project->getTranslation('title') }}</h3>
                    <p class="text-slate-400 text-xs mb-4 line-clamp-2">{{ $project->getTranslation('description') }}</p>
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="text-cyan-400/80">{{ $project->tags }}</span>
                        <div class="flex space-x-2">
                            @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" class="text-slate-500 hover:text-white">Git</a>@endif
                            @if($project->demo_url)<a href="{{ $project->demo_url }}" target="_blank" class="text-cyan-400 hover:underline">Live</a>@endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-xs font-mono text-slate-600">[ Geen projecten gevonden in database. Draai seeders of voeg data toe. ]</div>
            @endforelse
        </div>
    </x-section>

    <!-- BLOGS -->
    <x-section id="blog" bg="card" cyberTitle="messages.nav.blog">
        <div class="space-y-4">
            @forelse($blogs as $blog)
                <a href="{{ route('blog.show', $blog->slug) }}" class="block p-5 border border-slate-900 bg-cyber-card/50 rounded-xl hover:border-violet-500/40 transition duration-300 flex justify-between items-center group">
                    <div>
                        <span class="text-[10px] font-mono text-slate-500">{{ $blog->created_at->format('Y-m-d') }}</span>
                        <h3 class="text-md font-semibold text-slate-200 group-hover:text-violet-400 transition">{{ $blog->getTranslation('title') }}</h3>
                    </div>
                    <span class="text-slate-600 text-xs font-mono group-hover:translate-x-1 transition-transform">READ_LOG -></span>
                </a>
            @empty
                <div class="text-center py-12 text-xs font-mono text-slate-600">[ Geen logs geschreven. ]</div>
            @endforelse
        </div>
    </x-section>

    <!-- CONTACT FORMULIER (Met AJAX & Alpine validation) -->
    <x-section id="contact">
        <div x-data="{
            form: { name: '', email: '', message: '' },
            errors: {},
            success: false,
            loading: false,
            async submit() {
                this.loading = true; this.errors = {}; this.success = false;
                let response = await fetch('{{ route('contact.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.form)
                });
                let data = await response.json();
                this.loading = false;
                if(data.success) { this.success = true; this.form = { name: '', email: '', message: '' }; }
                else { this.errors = data.errors; }
            }
        }" class="max-w-xl mx-auto border border-slate-900 bg-gradient-to-b from-cyber-card to-slate-950 p-8 rounded-2xl relative shadow-2xl">
            <div class="text-center space-y-2 mb-8">
                <h2 class="text-2xl font-bold font-mono text-white" x-text="messages.contact.title"></h2>
                <p class="text-xs text-slate-400 leading-relaxed" x-text="messages.contact.subtitle"></p>
            </div>

            <div x-show="success" x-cloak class="p-4 mb-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-lg font-mono" x-text="messages.contact.success"></div>

            <form @submit.prevent="submit" class="space-y-4 font-mono text-xs">
                <div>
                    <label class="block text-slate-400 mb-1" x-text="messages.contact.label_name"></label>
                    <input type="text" x-model="form.name" class="w-full bg-slate-950 border border-slate-900 rounded-lg p-3 text-slate-200 focus:outline-none focus:border-cyan-500 transition">
                    <span class="text-rose-500 mt-1 block" x-text="errors.name ? errors.name[0] : ''"></span>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1" x-text="messages.contact.label_email"></label>
                    <input type="email" x-model="form.email" class="w-full bg-slate-950 border border-slate-900 rounded-lg p-3 text-slate-200 focus:outline-none focus:border-cyan-500 transition">
                    <span class="text-rose-500 mt-1 block" x-text="errors.email ? errors.email[0] : ''"></span>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1" x-text="messages.contact.label_message"></label>
                    <textarea x-model="form.message" rows="4" class="w-full bg-slate-950 border border-slate-900 rounded-lg p-3 text-slate-200 focus:outline-none focus:border-cyan-500 transition"></textarea>
                    <span class="text-rose-500 mt-1 block" x-text="errors.message ? errors.message[0] : ''"></span>
                </div>
                <button type="submit" :disabled="loading" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg text-white font-bold hover:opacity-90 transition flex items-center justify-center gap-2">
                    <span x-show="loading" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span x-text="messages.contact.btn_send"></span>
                </button>
            </form>
        </div>
    </x-section>
@endsection