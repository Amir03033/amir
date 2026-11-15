// <x-dialog>: open with [data-dialog-open="id"], close with [data-dialog-close], Esc or a click on the scrim.
export function initDialogs() {
    document.addEventListener('click', (e) => {
        const opener = e.target.closest('[data-dialog-open]');
        if (opener) {
            document.getElementById(opener.dataset.dialogOpen)?.showModal();
            return;
        }
        const closer = e.target.closest('[data-dialog-close]');
        if (closer) closer.closest('dialog')?.close();
        // A click on the backdrop lands on the dialog element itself.
        if (e.target instanceof HTMLDialogElement && e.target.classList.contains('dialog')) e.target.close();
    });
}
