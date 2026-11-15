<form class="grid gap-6" novalidate
      x-data="ajaxForm(@js(route('ask.store')), { body: '', name: '', email: '' }, @js(['success' => __('site.ask.success'), 'error' => __('messages.form.error_generic'), 'throttle' => __('messages.form.error_throttle')]))"
      @submit.prevent="submit">
    <x-field name="body" id="q-body" :label="__('site.ask.body')" :textarea="true" :rows="4" model="form.body" maxlength="1000" />
    <div class="grid gap-6 sm:grid-cols-2">
        <x-field name="name" id="q-name" :label="__('site.ask.name')" :optional="true" model="form.name" autocomplete="name" maxlength="80" />
        <x-field name="email" id="q-email" type="email" :label="__('site.ask.email')" :optional="true" :help="__('site.ask.email_help')" model="form.email" autocomplete="email" />
    </div>
    <div class="hp" aria-hidden="true"><label for="q-website">Website</label><input id="q-website" type="text" x-model="form.website" tabindex="-1" autocomplete="off"></div>
    <div>
        <button type="submit" class="btn-primary" :disabled="loading">
            <span x-show="loading" x-cloak>{{ icon('loader-circle', 'spinner') }}</span>{{ __('site.ask.send') }}
        </button>
    </div>
</form>
