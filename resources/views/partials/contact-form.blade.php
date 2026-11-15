{{-- Contact form: JSON post to /contact (CSRF, honeypot, throttle 5/10min, validation in ContactController). --}}
<form class="read mt-10 grid gap-6" novalidate data-reveal
      x-data="ajaxForm(@js(route('contact.send')), { name: '', email: '', message: '' }, @js(['success' => __('site.contact.success'), 'error' => __('site.contact.error', ['email' => config('site.email')]), 'throttle' => __('messages.form.error_throttle')]))"
      @submit.prevent="submit">
    <div class="grid gap-6 sm:grid-cols-2">
        <x-field name="name" id="c-name" :label="__('site.contact.name')" model="form.name" autocomplete="name" />
        <x-field name="email" id="c-email" type="email" :label="__('site.contact.email')" model="form.email" autocomplete="email" inputmode="email" />
    </div>
    <x-field name="message" id="c-message" :label="__('site.contact.message')" :textarea="true" model="form.message" />
    <div class="hp" aria-hidden="true"><label for="c-website">Website</label><input id="c-website" type="text" x-model="form.website" tabindex="-1" autocomplete="off"></div>
    <div>
        <button type="submit" class="btn-primary" :disabled="loading">
            <span x-show="loading" x-cloak>{{ icon('loader-circle', 'spinner') }}</span>
            <span x-text="loading ? @js(__('messages.form.sending')) : @js(__('site.contact.send'))">{{ __('site.contact.send') }}</span>
        </button>
    </div>
</form>
