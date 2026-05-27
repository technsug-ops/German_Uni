/**
 * Global form loading-state handler.
 *
 * Behavior:
 *   - On submit of any <form>: disables all submit buttons inside it,
 *     adds spinner + "Sending..." text, sets aria-busy.
 *   - Skips forms with [data-no-loading] attribute (e.g. forms that handle
 *     their own state — newsletter form has its own data-newsletter-form JS).
 *   - Skips invalid forms (HTML5 :invalid catches via browser, no submit fires).
 *   - On pageshow (bfcache): re-enables buttons (user clicks back).
 *
 * Why vanilla JS not Alpine: forms exist server-rendered before Alpine boots,
 * and we want this to work even if Alpine fails to load.
 */
(function () {
    'use strict';

    const LOADING_CLASS = 'is-submitting';
    const SPINNER_HTML = '<span class="inline-block w-4 h-4 mr-1.5 border-2 border-current border-t-transparent rounded-full animate-spin align-[-3px]" aria-hidden="true"></span>';

    // Read translated label from <html data-i18n-sending="..."> if provided,
    // fallback to English.
    const sendingLabel = document.documentElement.getAttribute('data-i18n-sending') || 'Sending…';

    function applyLoadingState(form) {
        const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        buttons.forEach(btn => {
            if (btn.dataset.originalHtml === undefined) {
                btn.dataset.originalHtml = btn.innerHTML || btn.value || '';
            }
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            // Don't replace HTML for <input type=submit> (can't have child elements)
            if (btn.tagName === 'BUTTON') {
                btn.innerHTML = SPINNER_HTML + sendingLabel;
            }
        });
        form.classList.add(LOADING_CLASS);
        form.setAttribute('aria-busy', 'true');
    }

    function restoreFormState(form) {
        const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        buttons.forEach(btn => {
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            if (btn.dataset.originalHtml !== undefined && btn.tagName === 'BUTTON') {
                btn.innerHTML = btn.dataset.originalHtml;
            }
            delete btn.dataset.originalHtml;
        });
        form.classList.remove(LOADING_CLASS);
        form.removeAttribute('aria-busy');
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        // Opt-out for forms that manage their own state
        if (form.hasAttribute('data-no-loading')) return;

        // Only POST-type forms (GET forms = search/filter, fast)
        const method = (form.getAttribute('method') || 'get').toLowerCase();
        if (method !== 'post') return;

        // Browser's HTML5 validation will have stopped invalid submits before
        // this handler fires, so we're safe to disable here.
        applyLoadingState(form);

        // Safety net: if the form is somehow cancelled after our handler, restore.
        // (e.g. preventDefault by a later handler — rare)
        setTimeout(() => {
            if (!e.defaultPrevented) return;
            restoreFormState(form);
        }, 50);
    }, true);

    // Back/forward cache: when user navigates back, browser restores form
    // but our spinner state lingers. Reset on pageshow.
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.querySelectorAll('form.' + LOADING_CLASS).forEach(restoreFormState);
    });
})();
