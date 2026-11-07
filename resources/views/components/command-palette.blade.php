@php
    $home = lroute('home');
    $groups = [
        ['name' => __('messages.palette.pages'), 'items' => [
            ['title' => 'ServerScope', 'hint' => __('messages.serverscope.label'), 'url' => $home.'#serverscope'],
            ['title' => __('messages.nav.portfolio'), 'hint' => '', 'url' => $home.'#work'],
            ['title' => __('messages.proof.title'), 'hint' => '', 'url' => $home.'#proof'],
            ['title' => __('messages.nav.about'), 'hint' => '', 'url' => $home.'#about'],
            ['title' => __('messages.blog.index_title'), 'hint' => '', 'url' => lroute('blog.index')],
            ['title' => __('messages.nav.cv'), 'hint' => '', 'url' => lroute('cv')],
            ['title' => __('messages.nav.contact'), 'hint' => '', 'url' => $home.'#contact'],
        ]],
        ['name' => __('messages.palette.projects'), 'items' => $paletteItems['projects']],
        ['name' => __('messages.palette.notes'), 'items' => $paletteItems['notes']],
        ['name' => __('messages.palette.actions'), 'items' => [
            ['title' => __('messages.palette.copy_email'), 'hint' => config('site.email'), 'action' => 'copy'],
            ['title' => __('messages.cv.download'), 'hint' => 'PDF', 'url' => lroute('cv.pdf')],
            ['title' => __('messages.theme.toggle'), 'hint' => '☾ / ☀', 'action' => 'theme'],
            ['title' => 'LinkedIn', 'hint' => '↗', 'url' => config('site.linkedin'), 'external' => true],
            ...(config('site.booking_url') ? [['title' => __('messages.booking.cta'), 'hint' => '↗', 'url' => config('site.booking_url'), 'external' => true]] : []),
        ]],
    ];

    // Data for the terminal easter egg (type ">" or "help").
    $term = [
        'name' => config('site.name'),
        'role' => __('messages.home.subtitle').' · '.config('site.location'),
        'open' => __('messages.about.availability_value'),
        'email' => config('site.email'),
        'links' => array_values(array_filter([config('site.linkedin'), config('site.github'), config('site.serverscope.url')])),
        'projects' => $paletteItems['projects'],
        'stack' => collect(__('messages.skills.groups'))->map(fn ($g) => $g['label'].': '.implode(', ', $g['items']))->values()->all(),
        'cv' => lroute('cv.pdf'),
        'status' => lroute('status'),
        'lang' => alternate_url(app()->getLocale() === 'nl' ? 'en' : 'nl'),
    ];
@endphp

<div x-data="{
        open: false,
        q: '',
        active: 0,
        copied: false,
        groups: @js($groups),
        t: @js($term),
        lines: [],
        get isTerm() { return this.q.startsWith('>'); },
        run() {
            const raw = this.q.replace(/^>\s*/, '').trim();
            const [cmd, ...args] = raw.toLowerCase().split(/\s+/);
            const out = (...l) => this.lines.push(...l.map(text => ({ text })));
            this.lines.push({ text: '> ' + raw, cmd: true });
            const go = (url, ext) => setTimeout(() => ext ? window.open(url, '_blank', 'noopener') : (window.location.href = url), 350);
            switch (cmd) {
                case '': break;
                case 'help': out('whoami · projects · open <n> · stack · status · contact · cv · theme · lang · clear · exit'); break;
                case 'whoami': out(this.t.name, this.t.role, 'open to: ' + this.t.open); break;
                case 'projects': out(...this.t.projects.map((p, i) => `[${i + 1}] ${p.title}  ${p.hint ? '— ' + p.hint : ''}`), 'tip: open <n>'); break;
                case 'open': { const p = this.t.projects[parseInt(args[0], 10) - 1]; p ? (out('opening ' + p.title + '…'), go(p.url)) : out('usage: open <n>  (see: projects)'); break; }
                case 'stack': out(...this.t.stack); break;
                case 'status': out('opening live status…'); go(this.t.status); break;
                case 'contact': out('email: ' + this.t.email, ...this.t.links); break;
                case 'mail': out('opening mail…'); go('mailto:' + this.t.email); break;
                case 'cv': out('downloading cv.pdf…'); go(this.t.cv); break;
                case 'theme': window.toggleTheme(); out('theme → ' + document.documentElement.dataset.theme); break;
                case 'lang': out('switching language…'); go(this.t.lang); break;
                case 'clear': this.lines = []; break;
                case 'exit': this.open = false; break;
                case 'sudo': out('permission denied. nice try 😄 — but I am open to a junior role.'); break;
                case 'rm': out('not today.'); break;
                default: out(`command not found: ${cmd}. try 'help'`);
            }
            this.q = '> ';
            this.$nextTick(() => this.$refs.term?.scrollTo({ top: 1e6 }));
        },
        enter() {
            if (this.isTerm) return this.run();
            if (this.q.trim().toLowerCase() === 'help' && !this.flat.length) { this.q = '> help'; return this.run(); }
            this.go(this.flat[this.active]);
        },
        get results() {
            const q = this.q.trim().toLowerCase();
            let i = 0;
            return this.groups
                .map(g => ({ name: g.name, items: g.items.filter(it => !q || (it.title + ' ' + (it.hint || '')).toLowerCase().includes(q)).map(it => ({ ...it, i: i++ })) }))
                .filter(g => g.items.length);
        },
        get flat() { return this.results.flatMap(g => g.items); },
        show() { this.open = true; this.q = ''; this.active = 0; this.$nextTick(() => this.$refs.input.focus()); },
        go(item) {
            if (!item) return;
            if (item.action === 'theme') { window.toggleTheme(); this.open = false; return; }
            if (item.action === 'copy') {
                navigator.clipboard?.writeText(@js(config('site.email')));
                this.copied = true; setTimeout(() => { this.copied = false; this.open = false; }, 900);
                return;
            }
            this.open = false;
            if (item.external) { window.open(item.url, '_blank', 'noopener'); return; }
            window.location.href = item.url;
        },
        move(d) { const n = this.flat.length; if (n) { this.active = (this.active + d + n) % n; this.$nextTick(() => this.$refs.list.querySelector('[data-active]')?.scrollIntoView({ block: 'nearest' })); } }
    }"
    @keydown.window.prevent.cmd.k="open ? open = false : show()"
    @keydown.window.prevent.ctrl.k="open ? open = false : show()"
    @open-palette.window="show()"
    @keydown.escape.window="open = false"
>
    <div x-cloak x-show="open" class="fixed inset-0 z-[80] flex items-start justify-center px-4 pt-[12vh]" role="dialog" aria-modal="true" aria-label="{{ __('messages.palette.open') }}">
        <div x-show="open" x-transition.opacity.duration.200ms class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 scale-95 -translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="panel relative w-full max-w-xl overflow-hidden shadow-[0_40px_120px_-20px_rgb(0_0_0/.9)]">
            <div class="flex items-center gap-3 border-b border-ink-line px-4">
                <svg class="h-4 w-4 shrink-0 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input x-ref="input" x-model="q" @input="active = 0" @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="enter()"
                       type="text" placeholder="{{ __('messages.palette.placeholder') }}" class="h-14 w-full bg-transparent text-[15px] text-ink-text placeholder:text-ink-muted focus:outline-none" aria-label="{{ __('messages.palette.placeholder') }}">
                <span class="kbd">esc</span>
            </div>

            <div x-show="isTerm" x-ref="term" class="max-h-[50vh] min-h-[180px] overflow-y-auto p-4 font-mono text-[13px] leading-relaxed">
                <p class="text-ink-muted">amir@portfolio — type <span class="text-scope-accent">help</span></p>
                <template x-for="(line, i) in lines" :key="i">
                    <p :class="line.cmd ? 'mt-2 text-scope-accent' : 'text-ink-soft'" class="whitespace-pre-wrap break-words" x-text="line.text"></p>
                </template>
                <p class="mt-2 text-scope-accent">&gt; <span class="inline-block h-4 w-2 translate-y-0.5 bg-scope-accent motion-safe:animate-blink"></span></p>
            </div>

            <div x-show="!isTerm" x-ref="list" class="max-h-[50vh] overflow-y-auto p-2">
                <template x-for="group in results" :key="group.name">
                    <div class="mb-1">
                        <p class="px-3 pb-1 pt-3 text-[10px] font-semibold uppercase tracking-[.2em] text-ink-muted" x-text="group.name"></p>
                        <template x-for="item in group.items" :key="item.i">
                            <button type="button" @click="go(item)" @mousemove="active = item.i" :data-active="active === item.i ? '' : null"
                                    class="flex w-full items-center justify-between gap-4 rounded-xl px-3 py-2.5 text-left text-sm text-ink-soft transition-colors data-[active]:bg-ink-panel2 data-[active]:text-ink-text">
                                <span class="flex min-w-0 items-center gap-3">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-ink-strong" :class="active === item.i && 'bg-scope-accent'"></span>
                                    <span class="truncate" x-text="item.action === 'copy' && copied ? @js(__('messages.palette.copied')) + ' ✓' : item.title"></span>
                                </span>
                                <span class="shrink-0 truncate text-xs text-ink-muted" x-text="item.hint"></span>
                            </button>
                        </template>
                    </div>
                </template>
                <p x-show="!flat.length" class="px-3 py-8 text-center text-sm text-ink-muted">{{ __('messages.palette.empty') }}</p>
            </div>

            <div class="flex items-center gap-3 border-t border-ink-line px-4 py-2.5 text-[11px] text-ink-muted">
                <span class="kbd">↑</span><span class="kbd">↓</span> {{ __('messages.palette.hint') }}
                <span class="kbd ml-3">↵</span>
                <span class="ml-auto hidden sm:inline">{!! __('messages.palette.terminal_hint') !!}</span>
            </div>
        </div>
    </div>
</div>
