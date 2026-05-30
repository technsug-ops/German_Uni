@props([
    'title' => null,
    'subtitle' => null,
    'faqs' => [],
    'icon' => 'question-mark-circle',
    'compact' => false,
])

@php
    $items = collect($faqs)
        ->filter(fn ($f) => !empty($f['q'] ?? null) && !empty($f['a'] ?? null))
        ->values();
@endphp

@if ($items->isNotEmpty())
    <x-json-ld :data="\App\Support\Seo::genericFaqPage($items->all())" />

    <section class="{{ $compact ? 'py-8' : 'py-12' }}" itemscope itemtype="https://schema.org/FAQPage">
        <div class="max-w-4xl mx-auto px-4">
            @if ($title)
                <header class="mb-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon :name="$icon" class="w-6 h-6 text-primary-600" />
                        {{ $title }}
                    </h2>
                    @if ($subtitle)
                        <p class="text-gray-600 mt-1">{{ $subtitle }}</p>
                    @endif
                </header>
            @endif

            <div class="space-y-3">
                @foreach ($items as $faq)
                    <details
                        class="group bg-white border border-gray-200 rounded-xl p-4 shadow-sm open:ring-2 open:ring-primary-300 transition"
                        itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"
                    >
                        <summary class="cursor-pointer font-semibold text-gray-900 flex items-start justify-between list-none gap-3">
                            <span itemprop="name">{{ $faq['q'] }}</span>
                            <span class="text-primary-600 group-open:rotate-180 transition shrink-0 mt-0.5">▼</span>
                        </summary>
                        <div
                            class="mt-3 text-sm text-gray-800 leading-relaxed prose prose-sm max-w-none"
                            itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"
                        >
                            <div itemprop="text">
                                {!! \Illuminate\Support\Str::markdown($faq['a']) !!}
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
