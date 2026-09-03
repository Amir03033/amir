@extends('layouts.layout')

@section('nav')
    <x-site-nav :on-home="true" />
@endsection

@section('content')
    <x-section id="home" class="min-h-screen flex items-center justify-center pt-16">
        <div class="grid grid-cols-1 md:grid-cols-[1.2fr_0.8fr] gap-12 items-center w-full">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyan-500/20 bg-cyan-500/5 text-cyan-400 text-xs font-mono tracking-widest uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                    {{ __('messages.home.badge') }}
                </div>
                <h1 class="text-5xl md:text-6xl font-black tracking-tight text-white">{{ config('site.name') }}</h1>
                <p class="text-lg md:text-xl font-medium text-cyan-400/90">{{ __('messages.home.subtitle') }}</p>
                <p class="text-slate-400 leading-relaxed text-sm md:text-base max-w-xl">{{ __('messages.home.text') }}</p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <a href="#portfolio" class="btn-primary">{{ __('messages.home.cta') }}</a>
                    <a href="{{ route('cv') }}" class="btn-secondary">{{ __('messages.home.cta_cv') }}</a>
                    <a href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer" class="btn-secondary">LinkedIn</a>
                </div>
            </div>
            <div class="justify-self-center md:justify-self-end">
                <div class="relative">
                    <div class="absolute -inset-1 rounded-2xl bg-gradient-to-br from-cyan-500/30 to-violet-600/20 blur-lg"></div>
                    <img src="{{ asset(config('site.photo')) }}" alt="{{ config('site.name') }}" class="relative w-64 md:w-72 h-80 object-cover rounded-2xl border border-slate-800 shadow-2xl">
                </div>
            </div>
        </div>
    </x-section>

    <x-section id="about" bg="card" :title="__('messages.about.title')">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
            <div class="border border-slate-800 bg-cyber-card p-6 rounded-xl space-y-4 text-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800 pb-2">{{ __('messages.about.details') }}</h3>
                <div>
                    <span class="text-slate-500 block text-xs">{{ __('messages.about.age') }}</span>
                    <span class="text-cyan-400 font-semibold">{{ __('messages.about.age_value') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-xs">{{ __('messages.about.location') }}</span>
                    <span class="text-slate-200">{{ config('site.location') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-xs">{{ __('messages.about.role') }}</span>
                    <span class="text-slate-200">{{ __('messages.about.role_value') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-xs">{{ __('messages.about.availability') }}</span>
                    <span class="text-slate-200">{{ __('messages.about.availability_value') }}</span>
                </div>
            </div>
            <div class="md:col-span-2">
                <p class="text-slate-300 text-sm md:text-base leading-relaxed">{{ __('messages.about.text') }}</p>
            </div>
        </div>
    </x-section>

    <x-section id="skills" :title="__('messages.skills.title')" :intro="__('messages.skills.intro')">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(__('messages.skills.groups') as $group)
                <div class="border border-slate-800 bg-cyber-card p-6 rounded-xl">
                    <h3 class="text-sm font-semibold text-white mb-4">{{ $group['label'] }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($group['items'] as $item)
                            <span class="px-2.5 py-1 rounded-md bg-slate-950 border border-slate-800 text-xs text-slate-300">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-section>

    @if($projects->isNotEmpty())
        <x-section id="portfolio" bg="card" :title="__('messages.portfolio.title')" :intro="__('messages.portfolio.intro')">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        </x-section>
    @endif

    @if($blogs->isNotEmpty())
        <x-section id="blog" :title="__('messages.blog.title')" :intro="__('messages.blog.intro')">
            <div class="space-y-4">
                @foreach($blogs as $blog)
                    <a href="{{ route('blog.show', $blog->slug) }}" class="block p-5 border border-slate-800 bg-cyber-card/50 rounded-xl hover:border-violet-500/40 transition flex justify-between items-start gap-6 group">
                        <div>
                            <span class="text-[11px] font-mono text-slate-500">{{ $blog->created_at->format('d M Y') }}</span>
                            <h3 class="text-base font-semibold text-slate-100 group-hover:text-violet-300 transition mt-1">{{ $blog->getTranslation('title') }}</h3>
                            <p class="text-slate-400 text-sm mt-2 leading-relaxed">{{ $blog->excerpt() }}</p>
                        </div>
                        <span class="text-slate-500 text-xs font-mono whitespace-nowrap mt-1 group-hover:text-violet-300">{{ __('messages.blog.read') }} →</span>
                    </a>
                @endforeach
            </div>
        </x-section>
    @endif

    <x-section id="contact">
        <div x-data="{
            form: { name: '', email: '', message: '' },
            errors: {},
            success: false,
            failed: false,
            loading: false,
            async submit() {
                this.loading = true; this.errors = {}; this.success = false; this.failed = false;
                try {
                    let response = await fetch('{{ route('contact.send') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(this.form)
                    });
                    let data = await response.json();
                    this.loading = false;
                    if (data.success) { this.success = true; this.form = { name: '', email: '', message: '' }; }
                    else { this.errors = data.errors || {}; this.failed = !data.errors; }
                } catch (e) {
                    this.loading = false;
                    this.failed = true;
                }
            }
        }" class="max-w-xl mx-auto border border-slate-800 bg-gradient-to-b from-cyber-card to-slate-950 p-8 rounded-2xl shadow-2xl">
            <div class="text-center space-y-2 mb-8">
                <h2 class="text-2xl font-bold text-white">{{ __('messages.contact.title') }}</h2>
                <p class="text-sm text-slate-400 leading-relaxed">{{ __('messages.contact.subtitle') }}</p>
            </div>

            <div x-show="success" x-cloak class="p-4 mb-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-lg">{{ __('messages.contact.success') }}</div>
            <div x-show="failed" x-cloak class="p-4 mb-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-lg">{{ __('messages.contact.error') }}</div>

            <form @submit.prevent="submit" class="space-y-4 text-sm">
                <div>
                    <label class="block text-slate-400 mb-1">{{ __('messages.contact.label_name') }}</label>
                    <input type="text" x-model="form.name" class="input-field" autocomplete="name">
                    <span class="text-rose-500 mt-1 block text-xs" x-text="errors.name ? errors.name[0] : ''"></span>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">{{ __('messages.contact.label_email') }}</label>
                    <input type="email" x-model="form.email" class="input-field" autocomplete="email">
                    <span class="text-rose-500 mt-1 block text-xs" x-text="errors.email ? errors.email[0] : ''"></span>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">{{ __('messages.contact.label_message') }}</label>
                    <textarea x-model="form.message" rows="4" class="input-field"></textarea>
                    <span class="text-rose-500 mt-1 block text-xs" x-text="errors.message ? errors.message[0] : ''"></span>
                </div>
                <button type="submit" :disabled="loading" class="btn-primary w-full">
                    <span x-show="loading" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>{{ __('messages.contact.btn_send') }}</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-800 text-center text-xs text-slate-500 space-y-2">
                <p>{{ __('messages.contact.or') }}</p>
                <p>
                    <a class="text-cyan-400 hover:underline" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                    ·
                    <a class="text-cyan-400 hover:underline" href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                    ·
                    <a class="text-cyan-400 hover:underline" href="tel:{{ config('site.phone_href') }}">{{ config('site.phone') }}</a>
                </p>
            </div>
        </div>
    </x-section>
@endsection
