@props(['id', 'title'])
{{-- Native <dialog>: focus trap, Esc and top layer for free. Open with data-dialog-open="{{ $id }}". --}}
<dialog id="{{ $id }}" class="dialog" aria-labelledby="{{ $id }}-title">
    <div class="dialog-head">
        <h2 id="{{ $id }}-title" class="text-h3">{{ $title }}</h2>
        <button type="button" class="btn-icon" data-dialog-close aria-label="{{ __('messages.nav.close') }}">{{ icon('x') }}</button>
    </div>
    <div class="dialog-body">{{ $slot }}</div>
</dialog>
