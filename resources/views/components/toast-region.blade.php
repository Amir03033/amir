{{-- Toasts are added by resources/js/toast.js (window.toast). A session flash is shown on load. --}}
<div class="toast-region" data-toasts
     data-label-close="{{ __('messages.nav.close') }}"
     @if(session('toast')) data-flash='@json(session('toast'))' @endif></div>
<template data-toast-icons>
    <span data-type="success">{{ icon('check') }}</span>
    <span data-type="error">{{ icon('circle-alert') }}</span>
    <span data-type="info">{{ icon('info') }}</span>
    <span data-type="close">{{ icon('x') }}</span>
</template>
