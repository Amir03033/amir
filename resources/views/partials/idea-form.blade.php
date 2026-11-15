<form class="grid gap-6" novalidate
      x-data="ajaxForm(@js(route('ideas.store')), { title: '', detail: '' }, @js(['success' => __('site.ideas.success'), 'error' => __('messages.form.error_generic'), 'throttle' => __('messages.form.error_throttle')]))"
      @submit.prevent="submit">
    <x-field name="title" id="i-title" :label="__('site.ideas.idea')" model="form.title" maxlength="140" />
    <x-field name="detail" id="i-detail" :label="__('site.ideas.detail')" :optional="true" :textarea="true" :rows="3" model="form.detail" maxlength="1000" />
    <div class="hp" aria-hidden="true"><label for="i-website">Website</label><input id="i-website" type="text" x-model="form.website" tabindex="-1" autocomplete="off"></div>
    <div>
        <button type="submit" class="btn-primary" :disabled="loading">
            <span x-show="loading" x-cloak>{{ icon('loader-circle', 'spinner') }}</span>{{ __('site.ideas.send') }}
        </button>
    </div>
</form>
