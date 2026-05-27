/**
 * Form error UX:
 *   - On page load: if there are validation errors visible, scroll the FIRST
 *     error into view and focus its associated input (best guess by proximity).
 *   - On form submit: if the browser's built-in validation blocks the submit
 *     (HTML5 :invalid), do the same scroll + focus for the first :invalid field.
 *
 * Server-rendered errors come from Laravel's @error directive / x-input-error
 * component which we marked with `.form-error-list` (role="alert").
 */
(function () {
    'use strict';

    function findInputForError(errorEl) {
        // Strategy: walk up from the error <ul> to the nearest container that
        // also holds an input/select/textarea. Then return the first.
        let container = errorEl.parentElement;
        for (let depth = 0; depth < 4 && container; depth++) {
            const input = container.querySelector('input:not([type=hidden]), textarea, select');
            if (input) return input;
            container = container.parentElement;
        }
        return null;
    }

    function highlightInput(input) {
        if (!input) return;
        // Soft red border to make the field obviously invalid
        input.classList.add('border-red-400', 'ring-1', 'ring-red-200');
        input.setAttribute('aria-invalid', 'true');

        const clear = () => {
            input.classList.remove('border-red-400', 'ring-1', 'ring-red-200');
            input.removeAttribute('aria-invalid');
            input.removeEventListener('input', clear);
            input.removeEventListener('change', clear);
        };
        input.addEventListener('input', clear, { once: true });
        input.addEventListener('change', clear, { once: true });
    }

    function scrollIntoViewAndFocus(target) {
        if (!target) return;
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Slight delay so smooth scroll completes before focus jumps
        setTimeout(() => {
            const input = (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT')
                ? target
                : findInputForError(target);
            if (input) {
                input.focus({ preventScroll: true });
                highlightInput(input);
            }
        }, 250);
    }

    // ── 1. Page load: scroll to first server-side error ──
    document.addEventListener('DOMContentLoaded', function () {
        const firstError = document.querySelector('.form-error-list, [role="alert"][aria-live]');
        if (!firstError) return;
        // Don't auto-scroll if user is already past first error (e.g. on long pages)
        const rect = firstError.getBoundingClientRect();
        if (rect.top > 0 && rect.top < window.innerHeight) return; // already visible
        scrollIntoViewAndFocus(firstError);
    });

    // ── 2. Form submit: catch HTML5 :invalid before submit ──
    document.addEventListener('invalid', function (e) {
        if (!(e.target instanceof HTMLElement)) return;
        // Browser will show its own bubble — we just ensure visibility + focus
        // Only act on the FIRST invalid (subsequent invalid events also fire)
        const form = e.target.form;
        if (!form || form.dataset.errorHandled === '1') return;
        form.dataset.errorHandled = '1';
        setTimeout(() => { delete form.dataset.errorHandled; }, 500);
        scrollIntoViewAndFocus(e.target);
    }, true); // capture phase — invalid doesn't bubble

})();
