@php
    $home = route('home');
    $groups = [
        ['name' => __('messages.palette.pages'), 'items' => [
            ['title' => 'ServerScope', 'hint' => __('messages.serverscope.label'), 'url' => $home.'#serverscope'],
            ['title' => __('messages.nav.portfolio'), 'hint' => '', 'url' => $home.'#work'],
            ['title' => __('messages.proof.title'), 'hint' => '', 'url' => $home.'#proof'],
            ['title' => __('messages.nav.about'), 'hint' => '', 'url' => $home.'#about'],
            ['title' => __('messages.blog.index_title'), 'hint' => '', 'url' => route('blog.index')],
            ['title' => __('messages.nav.cv'), 'hint' => '', 'url' => route('cv')],
            ['title' => __('messages.nav.contact'), 'hint' => '', 'url' => $home.'#contact'],
        ]],
        ['name' => __('messages.palette.projects'), 'items' => $paletteItems['projects']],
        ['name' => __('messages.palette.notes'), 'items' => $paletteItems['notes']],
        ['name' => __('messages.palette.actions'), 'items' => [
            ['title' => __('messages.palette.copy_email'), 'hint' => config('site.email'), 'action' => 'copy'],
            ['title' => 'LinkedIn', 'hint' => '↗', 'url' => config('site.linkedin'), 'external' => true],
        ]],
    ];
@endphp

<div x-data="{
        open: false,
        q: '',
        active: 0,
        copied: false,
        groups: @js($groups),
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
                <input x-ref="input" x-model="q" @input="active = 0" @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="go(flat[active])"
                       type="text" placeholder="{{ __('messages.palette.placeholder') }}" class="h-14 w-full bg-transparent text-[15px] text-ink-text placeholder:text-ink-muted focus:outline-none" aria-label="{{ __('messages.palette.placeholder') }}">
                <span class="kbd">esc</span>
            </div>

            <div x-ref="list" class="max-h-[50vh] overflow-y-auto p-2">
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
            </div>
        </div>
    </div>
</div>
