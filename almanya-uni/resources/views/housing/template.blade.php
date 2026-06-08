@extends('layouts.app')

@section('title', $template->localized('title') . ' — ' . __('Email Template') . ' — ' . brand('name'))

<x-seo
    :title="$template->localized('title') . ' (' . __('Email Template') . ')'"
    :description="$template->localized('description')"
/>

@section('content')
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-8">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="{{ lroute('housing.index') }}" class="hover:text-white">{{ __('Housing') }}</a>
            <span class="mx-2">/</span>
            <span>{{ __('Email Template') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">{{ $template->localized('title') }}</h1>
        @if ($template->localized('description'))
            <p class="text-primary-100 mt-3 max-w-3xl">{{ $template->localized('description') }}</p>
        @endif
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

    <main class="lg:col-span-2 space-y-6">

        {{-- Subject --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="envelope" class="w-5 h-5 text-gray-500" /> {{ __('Subject (Betreff)') }}</h2>
            <div class="bg-gray-50 border border-gray-200 rounded p-3 font-mono text-sm" id="mailSubject">{{ $template->subject_de }}</div>
            <button onclick="copyText('mailSubject', this)" class="mt-3 text-sm font-semibold bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded transition inline-flex items-center gap-1.5"><x-svg-icon name="check" class="w-4 h-4" /> {{ __('Copy') }}</button>
        </section>

        {{-- Body --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="pencil" class="w-5 h-5 text-gray-500" /> {{ __('Email Body (German)') }}</h2>
            <div class="bg-gray-50 border border-gray-200 rounded p-4 font-mono text-sm whitespace-pre-line leading-relaxed" id="mailBody">{{ $template->body_de }}</div>
            <button onclick="copyText('mailBody', this)" class="mt-3 text-sm font-semibold bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded transition inline-flex items-center gap-1.5"><x-svg-icon name="check" class="w-4 h-4" /> {{ __('Copy') }}</button>
        </section>

        {{-- Placeholder açıklamaları --}}
        @if (! empty($template->placeholders))
            <section class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h2 class="text-lg font-bold text-blue-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="wrench-screwdriver" class="w-5 h-5" /> {{ __('Fields to fill in') }}</h2>
                <p class="text-sm text-blue-800 mb-3">{{ __('Replace the placeholders inside curly braces with your own information:') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($template->placeholders as $ph)
                        <code class="inline-block bg-white border border-blue-200 text-blue-700 px-2 py-1 rounded font-mono text-xs">{ {{ $ph }} }</code>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Açıklama / İpuçları --}}
        @if ($template->body_tr_explanation)
            <section class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <h2 class="text-lg font-bold text-amber-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('Explanation & Tips') }}</h2>
                <div class="text-amber-900 leading-relaxed whitespace-pre-line">{{ $template->body_tr_explanation }}</div>
            </section>
        @endif
    </main>

    <aside class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 sticky top-20">
            <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">{{ __('Other Templates') }}</h3>
            <ul class="space-y-2 text-sm">
                @foreach ($others as $o)
                    <li>
                        <a href="{{ lroute('housing.template', ['slug' => $o->slug]) }}" class="text-primary-700 hover:text-primary-900 hover:underline">
                            {{ $o->localized('title') }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <a href="{{ lroute('housing.index') }}" class="block mt-4 text-center bg-primary-50 hover:bg-primary-100 text-primary-700 font-semibold px-3 py-2 rounded transition">
                ← {{ __('Housing Guide') }}
            </a>
        </div>
    </aside>
</div>

<script>
function copyText(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    navigator.clipboard.writeText(el.innerText).then(() => {
        const orig = btn.innerText;
        btn.innerText = '{{ __('✓ Copied') }}';
        setTimeout(() => btn.innerText = orig, 2000);
    });
}
</script>
@endsection
