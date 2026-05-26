@extends('layouts.app')

@section('title', __('Cookie Policy') . ' — ' . brand('name'))

<x-seo
    :title="__('Cookie Policy') . ' — ' . brand('name')"
    :description="__('AlmanyaUni\'s cookie usage: necessary, statistics and advertising cookies.')" />

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <p class="text-sm uppercase tracking-wide text-primary-200 mb-3">{{ __('Legal Notice') }}</p>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Cookie Policy') }}</h1>
        <p class="text-primary-100">{{ __('Last updated: :date', ['date' => $updated_at]) }}</p>
    </div>
</section>

<article class="max-w-3xl mx-auto px-4 py-12 prose prose-lg max-w-none
                prose-headings:font-extrabold prose-headings:text-gray-900
                prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-li:text-gray-700
                prose-a:text-primary-700 prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900">

    <p>
        {{ __('Cookies are small text files saved in your browser. When you visit the site they are used to remember your session, store your language preference, and provide site functionality.') }}
    </p>

    <h2>{{ __('Which cookies are used?') }}</h2>

    <h3>🟢 {{ __('Necessary cookies (always active)') }}</h3>
    <p>{{ __('Without these cookies the site cannot operate. Consent is not required (under GDPR Art. 6(1)(f) and equivalent legislation).') }}</p>
    <table class="not-prose w-full border-collapse text-sm mb-6">
        <thead><tr class="bg-primary-50">
            <th class="border border-gray-200 px-3 py-2 text-left">{{ __('Cookie') }}</th>
            <th class="border border-gray-200 px-3 py-2 text-left">{{ __('Purpose') }}</th>
            <th class="border border-gray-200 px-3 py-2 text-left">{{ __('Duration') }}</th>
        </tr></thead>
        <tbody>
            <tr><td class="border border-gray-200 px-3 py-2 font-mono">almanyauni_session</td><td class="border border-gray-200 px-3 py-2">{{ __('Session management (login, cart, forms)') }}</td><td class="border border-gray-200 px-3 py-2">{{ __('2 hours') }}</td></tr>
            <tr><td class="border border-gray-200 px-3 py-2 font-mono">XSRF-TOKEN</td><td class="border border-gray-200 px-3 py-2">{{ __('CSRF attack prevention') }}</td><td class="border border-gray-200 px-3 py-2">{{ __('2 hours') }}</td></tr>
            <tr><td class="border border-gray-200 px-3 py-2 font-mono">locale</td><td class="border border-gray-200 px-3 py-2">{{ __('Language preference (TR/EN/DE)') }}</td><td class="border border-gray-200 px-3 py-2">{{ __('1 year') }}</td></tr>
            <tr><td class="border border-gray-200 px-3 py-2 font-mono">phpbb3_*</td><td class="border border-gray-200 px-3 py-2">{{ __('Forum session (phpBB)') }}</td><td class="border border-gray-200 px-3 py-2">{{ __('Session duration') }}</td></tr>
        </tbody>
    </table>

    <h3>🟡 {{ __('Statistics cookies (currently inactive)') }}</h3>
    <p>
        {{ __('May be used to measure site traffic anonymously (e.g. Google Analytics, Plausible).') }}
        <strong>{{ __('Currently inactive.') }}</strong> {{ __('Once activated, explicit consent will be requested on site entry.') }}
    </p>

    <h3>🟠 {{ __('Advertising cookies (currently inactive)') }}</h3>
    <p>
        {{ __('May be used for Google AdSense or similar ad networks.') }}
        <strong>{{ __('Currently inactive.') }}</strong> {{ __('Once activated, an "Accept / reject advertising cookies" option will be shown.') }}
    </p>

    <h2>{{ __('How do I manage cookies?') }}</h2>
    <p>
        {{ __('From your browser settings you can delete cookies, block them, or allow them for specific sites. For details:') }}
    </p>
    <ul>
        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Chrome</a></li>
        <li><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank" rel="noopener">Firefox</a></li>
        <li><a href="https://support.apple.com/guide/safari/sfri11471/mac" target="_blank" rel="noopener">Safari</a></li>
        <li><a href="https://support.microsoft.com/en-us/windows/manage-cookies-in-microsoft-edge-view-allow-block-delete-and-use-168dab11-0753-043d-7c16-ede5947fc64d" target="_blank" rel="noopener">Edge</a></li>
    </ul>
    <p>
        <strong>{{ __('Warning:') }}</strong> {{ __('If you block necessary cookies, some site functions (login, form submission) will not work.') }}
    </p>

    <h2>{{ __('Third-party cookies') }}</h2>
    <p>
        {{ __('We currently do not use active third-party cookies. Once AdSense is enabled,') }}
        <a href="https://policies.google.com/technologies/cookies" target="_blank" rel="noopener">{{ __('Google\'s cookie policy') }}</a>
        {{ __('will apply. This page will be updated before activation.') }}
    </p>

    <h2>{{ __('Contact') }}</h2>
    <p>
        {{ __('For questions:') }} <a href="mailto:{{ $contact_email }}">{{ $contact_email }}</a>
    </p>

    <p class="text-sm text-gray-500 mt-12 pt-6 border-t border-gray-200">
        {{ __('This document was published on :date.', ['date' => $updated_at]) }}
        {{ __('Related:') }} <a href="{{ route('legal.privacy') }}">{{ __('Privacy Policy') }}</a> ·
        <a href="{{ route('legal.terms') }}">{{ __('Terms of Use') }}</a>
    </p>

</article>

@endsection
