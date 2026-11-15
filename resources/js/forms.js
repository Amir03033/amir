// Alpine component for every small form on the site (contact, Ask Amir, ideas).
// JSON post with the CSRF token; 422 → inline field errors, 429 → throttle toast, success → toast + reset.
export function registerForms(Alpine) {
    Alpine.data('ajaxForm', (url, fields = {}, messages = {}, options = {}) => ({
        form: { ...fields, website: '', locale: document.documentElement.lang },
        initial: { ...fields },
        errors: {},
        loading: false,

        async submit() {
            if (this.loading) return;
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success !== false) {
                    window.toast?.(data.message || messages.success, { type: 'success' });
                    this.form = { ...this.initial, website: '', locale: this.form.locale };
                    this.$dispatch(options.event || 'form-sent', data);
                } else if (response.status === 422 && data.errors) {
                    this.errors = data.errors;
                    // Move focus to the first invalid field so keyboard and screen-reader users land on the error.
                    this.$nextTick(() => this.$el.querySelector('[aria-invalid="true"]')?.focus());
                } else if (response.status === 429) {
                    window.toast?.(messages.throttle, { type: 'error' });
                } else {
                    window.toast?.(messages.error, { type: 'error' });
                }
            } catch (e) {
                window.toast?.(messages.error, { type: 'error' });
            } finally {
                this.loading = false;
            }
        },
    }));
}
