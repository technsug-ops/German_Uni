{{--
    Theme toggle (light/dark). Class-based hook (.js-theme-toggle) so multiple
    instances — desktop header bar and mobile drawer — can coexist; the JS in
    layouts/app.blade.php wires up all of them. Stored in localStorage. Initial
    theme is applied via inline <script> in <head> BEFORE first paint to prevent
    flash-of-wrong-theme.
--}}
<button type="button"
        {{ $attributes->class([
            'js-theme-toggle min-w-[44px] min-h-[44px] hover:bg-white/10 rounded-md transition inline-flex items-center justify-center',
        ]) }}
        title="{{ __('Theme') }}"
        aria-label="{{ __('Switch theme') }}">
    {{-- Sun (visible in dark mode) --}}
    <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
    </svg>
    {{-- Moon (visible in light mode) --}}
    <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
    </svg>
</button>
