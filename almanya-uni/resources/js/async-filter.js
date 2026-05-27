/**
 * Async filter for listing pages.
 *
 * Wires any <form data-async-filter-form="#async-filter-results"> to:
 *   1. Intercept submit / input changes (debounced)
 *   2. Show skeleton placeholders in the target container
 *   3. Fetch the new HTML via GET (?partial=1) — server returns just the grid
 *   4. Replace target container's children with response
 *   5. Update browser URL via history.pushState so the back button works
 *   6. Clicks inside the result container on pagination links are also intercepted
 *
 * Server contract: when ?partial=1 or X-Requested-With header is set,
 * controller returns only the grid HTML (no <html> wrapper).
 */
(function () {
    'use strict';

    const SKELETON_HTML = `
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            ${'<div class="bg-white rounded-xl overflow-hidden border border-gray-200 animate-pulse"><div class="bg-gray-200 aspect-[16/9]"></div><div class="p-4 space-y-2"><div class="h-4 bg-gray-200 rounded w-3/4"></div><div class="h-3 bg-gray-100 rounded w-1/2"></div></div></div>'.repeat(12)}
        </div>
    `;

    function bind(form) {
        const targetSelector = form.dataset.asyncFilterForm;
        const target = document.querySelector(targetSelector);
        if (!target) return;

        let abortController = null;
        let debounceTimer = null;

        async function submitForm(pushState = true) {
            // Cancel any in-flight request
            if (abortController) abortController.abort();
            abortController = new AbortController();

            // Build URL with current form data
            const data = new FormData(form);
            const params = new URLSearchParams();
            for (const [k, v] of data.entries()) {
                if (v !== '' && v !== null) params.append(k, v);
            }
            params.set('partial', '1');

            const action = form.getAttribute('action') || window.location.pathname;
            const url = action + '?' + params.toString();

            // Show skeleton
            target.setAttribute('aria-busy', 'true');
            target.innerHTML = SKELETON_HTML;

            try {
                const resp = await fetch(url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    signal: abortController.signal,
                    credentials: 'same-origin',
                });
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const html = await resp.text();
                target.innerHTML = html;
                target.setAttribute('aria-busy', 'false');

                if (pushState) {
                    // Update URL (drop partial param for clean history)
                    params.delete('partial');
                    const cleanUrl = action + (params.toString() ? '?' + params.toString() : '');
                    history.pushState({ asyncFilter: true }, '', cleanUrl);
                }

                // Smooth scroll to results
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.error('Async filter failed:', err);
                target.setAttribute('aria-busy', 'false');
                target.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">Failed to load — please reload the page.</div>';
            }
        }

        // Submit handler — debounce so rapid clicks don't pile up
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            submitForm(true);
        });

        // Auto-submit on input/select change (live filtering)
        form.querySelectorAll('select, input[type=checkbox], input[type=radio]').forEach(input => {
            input.addEventListener('change', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => submitForm(true), 150);
            });
        });
        // Text inputs — debounce longer to avoid hammering the server
        form.querySelectorAll('input[type=text], input[type=search]').forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => submitForm(true), 500);
            });
        });

        // Pagination + sort links inside the target: intercept and fetch
        target.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!link) return;
            // Only intercept links to the same path (pagination)
            const sameOrigin = link.origin === window.location.origin;
            if (!sameOrigin) return;
            const pathMatches = link.pathname === (form.getAttribute('action') || window.location.pathname);
            if (!pathMatches) return;
            // Allow modifier-click to open in new tab
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) return;

            e.preventDefault();
            // Copy link's query into the URL, then fetch (don't re-read form state)
            const url = link.href + (link.href.includes('?') ? '&' : '?') + 'partial=1';
            target.setAttribute('aria-busy', 'true');
            target.innerHTML = SKELETON_HTML;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
                .then(r => r.text())
                .then(html => {
                    target.innerHTML = html;
                    target.setAttribute('aria-busy', 'false');
                    history.pushState({ asyncFilter: true }, '', link.href);
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(() => { window.location.href = link.href; }); // fallback: hard nav
        });

        // Back/forward button — reload page (simpler than rebuilding form state)
        window.addEventListener('popstate', function (e) {
            if (e.state && e.state.asyncFilter) {
                window.location.reload();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-async-filter-form]').forEach(bind);
    });
})();
