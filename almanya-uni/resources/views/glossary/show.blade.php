@extends('layouts.app')

@section('title', $entry['title'] . ' — ' . __('Germany Education Glossary') . ' — ' . brand('name'))

<x-seo
    :title="$entry['title'] . ' — ' . __('Germany Education Glossary')"
    :description="$entry['short']"
/>

@push('head')
{{-- Schema.org DefinedTerm (semantic SEO, knowledge graph) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'DefinedTerm',
    'name' => $entry['title'],
    'description' => $entry['short'],
    'url' => route('glossary.show', $entry['slug']),
    'inDefinedTermSet' => [
        '@type' => 'DefinedTermSet',
        'name' => __('Germany Education Glossary'),
        'url' => route('glossary.index'),
    ],
    'sameAs' => $entry['official_url'] ? [$entry['official_url']] : null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

{{-- Schema.org WebPage with SpeakableSpecification — Google Assistant TTS hint --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $entry['title'],
    'description' => $entry['short'],
    'url' => route('glossary.show', $entry['slug']),
    'speakable' => [
        '@type' => 'SpeakableSpecification',
        'cssSelector' => ['h1', '.prose'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>

{{-- Schema.org FAQPage — Google rich snippet için --}}
@if (! empty($entry['faq']))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($entry['faq'])->map(fn ($item) => [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['a'],
        ],
    ])->all(),
    'speakable' => [
        '@type' => 'SpeakableSpecification',
        'cssSelector' => ['h1', '.prose'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
@endpush

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white">
    <div class="max-w-[900px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-indigo-100 mb-3 flex items-center gap-2 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="opacity-60">›</span>
            <a href="{{ route('glossary.index') }}" class="hover:text-white">{{ __('Glossary') }}</a>
            <span class="opacity-60">›</span>
            <span class="text-white">{{ $entry['title'] }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ $entry['icon'] }} {{ $entry['title'] }}
        </h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ $entry['short'] }}
        </p>
    </div>
</section>

<div class="max-w-[900px] mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Main content --}}
    <article class="lg:col-span-2">

        {{-- Detailed body --}}
        <section class="prose prose-lg max-w-none bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-8">
            @php
                // Markdown-light: paragraphs from double newlines, bold markdown
                $body = e($entry['body']);
                $body = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $body);
                $paragraphs = preg_split('/\n\n+/u', trim($body));
            @endphp
            @foreach ($paragraphs as $p)
                @php $p = trim($p); @endphp
                @if ($p === '') @continue @endif
                @if (str_starts_with($p, '| ') || str_starts_with($p, '|---'))
                    {{-- Markdown table — basic render --}}
                    <table class="min-w-full text-sm border border-gray-200 my-4">
                        @foreach (explode("\n", $p) as $row)
                            @php
                                $cells = array_filter(array_map('trim', explode('|', $row)), fn($c) => $c !== '');
                                $isHeader = str_contains($row, '---');
                            @endphp
                            @if ($isHeader) @continue @endif
                            <tr class="border-b border-gray-200">
                                @foreach ($cells as $cell)
                                    <td class="px-3 py-2">{!! $cell !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                @elseif (str_starts_with($p, '- '))
                    {{-- Bullet list --}}
                    <ul class="list-disc list-outside ml-6 space-y-1 my-3">
                        @foreach (explode("\n", $p) as $line)
                            @php $line = trim($line); @endphp
                            @if (str_starts_with($line, '- '))
                                <li>{!! substr($line, 2) !!}</li>
                            @endif
                        @endforeach
                    </ul>
                @elseif (preg_match('/^\d+\.\s/', $p))
                    {{-- Numbered list --}}
                    <ol class="list-decimal list-outside ml-6 space-y-1 my-3">
                        @foreach (explode("\n", $p) as $line)
                            @php $line = trim($line); @endphp
                            @if (preg_match('/^\d+\.\s(.*)/', $line, $m))
                                <li>{!! $m[1] !!}</li>
                            @endif
                        @endforeach
                    </ol>
                @else
                    <p class="my-3 leading-relaxed">{!! nl2br($p) !!}</p>
                @endif
            @endforeach

            @if ($entry['official_url'])
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">{{ __('Official source:') }}</p>
                    <a href="{{ $entry['official_url'] }}" target="_blank" rel="noopener nofollow"
                       class="inline-flex items-center gap-2 text-indigo-700 hover:text-indigo-900 font-semibold"
                       title="{{ $entry['title'] }} — {{ __('Official site') }}">
                        🔗 {{ parse_url($entry['official_url'], PHP_URL_HOST) }} →
                    </a>
                </div>
            @endif
        </section>

        {{-- FAQ section --}}
        @if (! empty($entry['faq']))
            <section class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">❓ {{ __('Frequently Asked Questions') }}</h2>
                <div class="space-y-3">
                    @foreach ($entry['faq'] as $i => $item)
                        <details class="group bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition cursor-pointer">
                            <summary class="font-semibold text-gray-900 cursor-pointer list-none flex justify-between items-start gap-3">
                                <span>{{ $item['q'] }}</span>
                                <span class="text-indigo-600 group-open:rotate-180 transition-transform shrink-0">▾</span>
                            </summary>
                            <p class="mt-3 text-gray-700 leading-relaxed">{{ $item['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        @endif
    </article>

    {{-- Sidebar --}}
    <aside class="lg:col-span-1 space-y-6">
        {{-- Related entries --}}
        @if (! empty($related))
            <section class="bg-white border border-gray-200 rounded-2xl p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-3">🔗 {{ __('Related terms') }}</h2>
                <div class="space-y-2">
                    @foreach ($related as $rel)
                        <a href="{{ route('glossary.show', $rel['slug']) }}"
                           title="{{ $rel['title'] }}"
                           class="group flex items-start gap-3 p-3 bg-gray-50 hover:bg-indigo-50 rounded-lg transition">
                            <span class="text-2xl shrink-0">{{ $rel['icon'] }}</span>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 group-hover:text-indigo-700 leading-tight">{{ $rel['title'] }}</p>
                                <p class="text-xs text-gray-600 line-clamp-2 mt-0.5">{{ $rel['short'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Tools CTA --}}
        <section class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl p-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-700 mb-3">🛠️ {{ __('Related Tools') }}</h2>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('tools.eligibility-checker') }}" class="text-indigo-700 hover:text-indigo-900 font-medium" title="{{ __('Eligibility Checker') }}">🎓 {{ __('Eligibility Checker') }}</a></li>
                <li><a href="{{ route('tools.deadlines') }}" class="text-indigo-700 hover:text-indigo-900 font-medium" title="{{ __('Application Calendar') }}">📅 {{ __('Application Calendar') }}</a></li>
                <li><a href="{{ route('tools.cost-of-living') }}" class="text-indigo-700 hover:text-indigo-900 font-medium" title="{{ __('Cost of Living') }}">💰 {{ __('Cost of Living') }}</a></li>
                <li><a href="{{ route('faqs.index') }}" class="text-indigo-700 hover:text-indigo-900 font-medium" title="{{ __('FAQ') }}">💬 {{ __('Frequently Asked Questions') }}</a></li>
            </ul>
        </section>
    </aside>
</div>

@endsection
