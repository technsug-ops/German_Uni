@props(['blocks' => [], 'excludeUrl' => null])

@php
    // Markdown + otomatik iç-linkleme. Sayfa-genel link/tooltip limiti için TÜM bloklar
    // aynı linker instance'ını paylaşır; sayaç sadece ilk blokta sıfırlanır (link tarlası önleme).
    $__linker = app(\App\Services\Content\BlogAutoLinker::class);
    $__firstBlock = true;
    $md = function ($text) use ($__linker, $excludeUrl, &$__firstBlock) {
        $text = $text ?? '';
        if (trim($text) === '') return '';
        $html = $__linker->process(\Illuminate\Support\Str::markdown($text), $excludeUrl, $__firstBlock);
        $__firstBlock = false;
        return $html;
    };
@endphp

@if(empty($blocks))
    <div class="text-gray-500 text-sm italic">
        {{ __('No content has been generated for this page yet. From the admin panel you can hit "Generate Page".') }}
    </div>
@else
    <div class="content-blocks space-y-8">
    @foreach($blocks as $block)
        @php
            $type = $block['type'] ?? null;
            $blockId = ! empty($block['h'])
                ? \Illuminate\Support\Str::slug($block['h'])
                : ($type ? \Illuminate\Support\Str::slug($type) : null);
        @endphp

        {{-- ────────────────────────────── HERO ────────────────────────────── --}}
        @if($type === 'hero')
            <figure class="rounded-2xl overflow-hidden bg-white ring-1 ring-gray-200 w-full h-72 sm:h-96 md:h-[28rem] lg:h-[32rem] flex items-center justify-center shadow-sm">
                <img src="{{ $block['image_url'] }}" alt="{{ $block['alt'] ?? '' }}"
                     class="max-w-full max-h-full w-auto h-full object-contain"
                     loading="lazy"/>
            </figure>

        {{-- ────────────────────────────── INTRO ────────────────────────────── --}}
        @elseif($type === 'intro')
            <div class="prose prose-lg max-w-none prose-p:text-gray-700">
                {!! $md($block['body_md'] ?? '') !!}
            </div>

        {{-- ────────────────────────────── SECTION ────────────────────────────── --}}
        @elseif($type === 'section')
            <section class="prose prose-lg max-w-none prose-p:text-gray-700 prose-headings:text-gray-900">
                @if(!empty($block['h']))
                    <h2 @if($blockId) id="{{ $blockId }}" @endif class="!text-2xl !font-bold !mb-3 scroll-mt-24">{{ $block['h'] }}</h2>
                @endif
                {!! $md($block['body_md'] ?? '') !!}
            </section>

        {{-- ────────────────────────────── QUICK FACTS ────────────────────────────── --}}
        @elseif($type === 'quick_facts')
            <section>
                @if(!empty($block['h']))
                    <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                @endif
                <dl class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($block['items'] ?? [] as $item)
                        <div class="bg-white ring-1 ring-gray-200 rounded-lg p-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wider text-gray-600 font-medium">{{ $item['label'] ?? '' }}</dt>
                            <dd class="text-base font-semibold mt-1 text-gray-900">{{ $item['value'] ?? '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

        {{-- ────────────────────────────── IMAGE ────────────────────────────── --}}
        @elseif($type === 'image')
            @if(!empty($block['url']))
                <figure class="rounded-2xl overflow-hidden bg-white ring-1 ring-gray-200 flex flex-col items-center shadow-sm">
                    <img src="{{ $block['url'] }}" alt="{{ $block['alt'] ?? '' }}" loading="lazy"
                         class="max-w-full max-h-[520px] w-auto h-auto object-contain"/>
                    @if(!empty($block['caption']))
                        <figcaption class="text-sm text-gray-700 p-3 italic w-full text-center">{{ $block['caption'] }}</figcaption>
                    @endif
                </figure>
            @endif

        {{-- ────────────────────────────── GALLERY ────────────────────────────── --}}
        @elseif($type === 'gallery')
            @php
                $imgs = collect($block['items'] ?? [])->filter(fn($i) => !empty(is_array($i) ? ($i['url'] ?? '') : $i))->values();
                $galleryId = 'gallery-' . substr(md5(($block['h'] ?? '') . $imgs->count() . ($imgs[0]['url'] ?? '')), 0, 8);
            @endphp
            @if($imgs->isNotEmpty())
                <section data-gallery="{{ $galleryId }}">
                    @if(!empty($block['h']))
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                    @endif
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($imgs as $idx => $img)
                            @php
                                $url = is_array($img) ? ($img['url'] ?? '') : $img;
                                $alt = is_array($img) ? ($img['alt'] ?? '') : '';
                                $caption = is_array($img) ? ($img['caption'] ?? '') : '';
                                $sourceUrl = is_array($img) ? ($img['source_url'] ?? '') : '';
                            @endphp
                            <figure class="group">
                                <button type="button"
                                        data-lightbox-trigger="{{ $galleryId }}"
                                        data-index="{{ $idx }}"
                                        data-url="{{ $url }}"
                                        data-alt="{{ $alt }}"
                                        data-caption="{{ $caption }}"
                                        data-source="{{ $sourceUrl }}"
                                        class="block w-full aspect-square overflow-hidden rounded-lg bg-white ring-1 ring-gray-200 cursor-zoom-in focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <img src="{{ $url }}" alt="{{ $alt }}" loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform"/>
                                </button>
                                @if($caption)
                                    <figcaption class="text-xs text-gray-700 mt-1 truncate">{{ $caption }}</figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif

        {{-- ────────────────────────────── VIDEO (yalnızca URL varsa render) ────────────────────────────── --}}
        @elseif($type === 'video')
            @php
                $videoUrl = trim($block['url'] ?? '');
                $embedUrl = null;
                if ($videoUrl) {
                    if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{11})#', $videoUrl, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                    } elseif (preg_match('#vimeo\.com/(\d+)#', $videoUrl, $m)) {
                        $embedUrl = 'https://player.vimeo.com/video/' . $m[1];
                    } elseif (str_contains($videoUrl, '/embed/')) {
                        $embedUrl = $videoUrl;
                    }
                }
            @endphp
            @if($embedUrl)
                <section>
                    @if(!empty($block['h']))
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                    @endif
                    <div class="aspect-video rounded-2xl overflow-hidden bg-black ring-1 ring-gray-200">
                        <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" loading="lazy" allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
                    </div>
                    @if(!empty($block['caption']))
                        <p class="text-sm text-gray-700 mt-2 text-center">{{ $block['caption'] }}</p>
                    @endif
                </section>
            @elseif($videoUrl)
                <section>
                    @if(!empty($block['h']))
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                    @endif
                    <a href="{{ $videoUrl }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 bg-white ring-1 ring-gray-200 rounded-2xl p-6 text-center hover:bg-gray-50 text-gray-900">
                        <x-svg-icon name="play" class="w-5 h-5 text-primary-600" /> {{ __('Watch video') }} ↗ <span class="text-sm text-gray-600">{{ $videoUrl }}</span>
                    </a>
                </section>
            @endif

        {{-- ────────────────────────────── TABLE ────────────────────────────── --}}
        @elseif($type === 'table')
            @if(!empty($block['rows']))
                <section>
                    @if(!empty($block['h']))
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                    @endif
                    <div class="overflow-x-auto rounded-lg ring-1 ring-gray-200">
                        <table class="w-full text-sm">
                            @if(!empty($block['headers']))
                                <thead class="bg-gray-100">
                                    <tr>
                                        @foreach($block['headers'] ?? [] as $h)
                                            <th class="px-4 py-3 text-left font-semibold text-gray-900">{{ $h }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                            @endif
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($block['rows'] ?? [] as $row)
                                    <tr class="hover:bg-gray-50">
                                        @foreach((array) $row as $cell)
                                            <td class="px-4 py-3 text-gray-800">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(!empty($block['caption']))
                        <p class="text-xs text-gray-600 mt-2">{{ $block['caption'] }}</p>
                    @endif
                </section>
            @endif

        {{-- ────────────────────────────── COST OF LIVING ────────────────────────────── --}}
        @elseif($type === 'cost_of_living')
            @if(!empty($block['items']))
                <section>
                    <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] ?? __('Monthly Cost of Living (approx.)') }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($block['items'] ?? [] as $item)
                            <div class="bg-white ring-1 ring-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="text-xs text-gray-600 uppercase tracking-wider font-medium">{{ $item['label'] ?? '' }}</div>
                                <div class="text-xl font-bold mt-1 text-gray-900">
                                    {{ $item['amount'] ?? '—' }}
                                    <span class="text-sm text-gray-600 font-normal">{{ $item['currency'] ?? ($block['currency'] ?? '€') }}</span>
                                </div>
                                @if(!empty($item['note']))
                                    <div class="text-xs text-gray-700 mt-1">{{ $item['note'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if(!empty($block['total']))
                        <div class="mt-4 flex items-center justify-end gap-3 bg-primary-50 ring-1 ring-primary-200 rounded-lg p-4">
                            <span class="text-sm font-medium text-gray-700 uppercase tracking-wider">{{ __('Average total') }}</span>
                            <span class="text-2xl font-bold text-primary-700">
                                {{ $block['total'] }} <span class="text-sm font-medium text-gray-600">{{ $block['currency'] ?? '€' }} / {{ __('mo') }}</span>
                            </span>
                        </div>
                    @endif
                    @if(!empty($block['note']))
                        <p class="text-xs text-gray-700 italic mt-2">{{ $block['note'] }}</p>
                    @endif
                </section>
            @endif

        {{-- ────────────────────────────── PLACES ────────────────────────────── --}}
        @elseif($type === 'places')
            @if(!empty($block['items']))
                <section>
                    @if(!empty($block['h']))
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($block['items'] ?? [] as $place)
                            @php
                                $iconNameMap = [
                                    'library' => 'book-open', 'museum' => 'building-office', 'square' => 'building-office',
                                    'park' => 'leaf', 'landmark' => 'flag', 'cafe' => 'cake',
                                    'restaurant' => 'cake', 'university' => 'academic-cap',
                                    'church' => 'building-office', 'monument' => 'flag', 'theater' => 'photo',
                                    'shopping' => 'shopping-bag', 'nature' => 'leaf', 'bar' => 'cake',
                                    'club' => 'photo', 'castle' => 'building-office', 'bridge' => 'map',
                                    'market' => 'shopping-bag', 'stadium' => 'trophy', 'zoo' => 'paw',
                                ];
                                $iconName = $iconNameMap[$place['type'] ?? ''] ?? 'map-pin';
                                // Map English place-type enums to localised labels (i18n keys)
                                $placeTypeLabels = [
                                    'library' => 'Library', 'museum' => 'Museum', 'square' => 'Square',
                                    'park' => 'Park', 'landmark' => 'Landmark', 'cafe' => 'Cafe',
                                    'restaurant' => 'Restaurant', 'university' => 'University',
                                    'church' => 'Church', 'monument' => 'Monument', 'theater' => 'Theater',
                                    'shopping' => 'Shopping', 'nature' => 'Nature', 'bar' => 'Bar',
                                    'club' => 'Club', 'castle' => 'Castle', 'bridge' => 'Bridge',
                                    'market' => 'Market', 'stadium' => 'Stadium', 'zoo' => 'Zoo',
                                ];
                                $placeType = $place['type'] ?? '';
                                $placeTypeLabel = $placeType ? __($placeTypeLabels[$placeType] ?? ucfirst($placeType)) : '';
                            @endphp
                            <div class="flex items-start gap-3 bg-white ring-1 ring-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-50 text-primary-600">
                                    @if (! empty($place['icon']))
                                        {!! e_icon($place['icon'], 'w-5 h-5') !!}
                                    @else
                                        <x-svg-icon :name="$iconName" class="w-5 h-5" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900">{{ $place['name'] ?? '' }}</div>
                                    @if($placeTypeLabel)
                                        <span class="text-xs text-primary-700 font-medium uppercase tracking-wide">{{ $placeTypeLabel }}</span>
                                    @endif
                                    @if(!empty($place['description']))
                                        <p class="text-sm text-gray-700 mt-1">{{ $place['description'] }}</p>
                                    @endif
                                    @if(!empty($place['url']))
                                        <a href="{{ $place['url'] }}" target="_blank" rel="noopener"
                                           class="text-xs text-primary-700 hover:underline mt-1 inline-block">{{ __('Details') }} ↗</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        {{-- ────────────────────────────── STUDENT CULTURE ────────────────────────────── --}}
        @elseif($type === 'student_culture')
            <section class="bg-gradient-to-br from-amber-50 to-orange-50 ring-1 ring-amber-200 rounded-2xl p-6 shadow-sm">
                <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] ?? __('Student Culture') }}</h2>
                @if(!empty($block['body_md']))
                    <div class="text-[15px] leading-relaxed text-gray-800 space-y-3 [&_strong]:font-semibold [&_strong]:text-gray-900 [&_a]:text-amber-700 [&_a]:underline">
                        {!! $md($block['body_md']) !!}
                    </div>
                @endif
                @if(!empty($block['highlights']))
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach($block['highlights'] ?? [] as $h)
                            <li class="flex items-start gap-2 text-gray-900">
                                <x-svg-icon name="star" class="w-4 h-4 mt-0.5 text-amber-600 shrink-0" />
                                <span class="font-medium">{{ $h }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

        {{-- ────────────────────────────── FAQ ────────────────────────────── --}}
        @elseif($type === 'faq')
            <section>
                @if(!empty($block['h']))
                    <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-4 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                @endif
                <div class="space-y-3">
                    @foreach($block['items'] ?? [] as $faq)
                        <details class="group bg-white ring-1 ring-gray-200 rounded-lg p-4 shadow-sm open:ring-primary-300">
                            <summary class="cursor-pointer font-semibold flex items-center justify-between text-gray-900 list-none">
                                <span>{{ $faq['q'] ?? '' }}</span>
                                <span class="text-primary-600 group-open:rotate-180 transition shrink-0 ml-3">▼</span>
                            </summary>
                            <div class="mt-3 text-gray-800 leading-relaxed [&_strong]:text-gray-900 [&_a]:text-primary-700 [&_a]:underline">
                                {!! $md($faq['a'] ?? '') !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            </section>

        {{-- ────────────────────────────── CTA ────────────────────────────── --}}
        @elseif($type === 'cta')
            <div class="bg-gradient-to-r from-primary-600 to-accent-500 text-white rounded-2xl p-6 text-center shadow-sm">
                @if(!empty($block['h']))
                    <h3 class="text-xl font-bold mb-2">{{ $block['h'] }}</h3>
                @endif
                <div class="prose prose-invert max-w-none">
                    {!! $md($block['body_md'] ?? '') !!}
                </div>
            </div>

        {{-- ────────────────────────────── UNIVERSITIES IN CITY ────────────────────────────── --}}
        @elseif($type === 'universities_in_city')
            <section class="bg-gradient-to-br from-blue-50 to-cyan-50 ring-1 ring-blue-200 rounded-2xl p-6 shadow-sm">
                <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] ?? __('Universities') }}</h2>
                <div class="grid grid-cols-3 gap-3 mb-4 text-sm text-gray-900">
                    <div><span class="font-bold text-2xl">{{ $block['total'] ?? 0 }}</span> <span class="text-gray-700">{{ __('total') }}</span></div>
                    <div><span class="font-bold text-2xl">{{ $block['public'] ?? 0 }}</span> <span class="text-gray-700">{{ __('public') }}</span></div>
                    <div><span class="font-bold text-2xl">{{ $block['private'] ?? 0 }}</span> <span class="text-gray-700">{{ __('private') }}</span></div>
                </div>
                @if(!empty($block['top_unis']))
                    <ul class="list-disc list-inside text-sm space-y-1 text-gray-900 font-medium">
                        @foreach($block['top_unis'] ?? [] as $u) <li>{{ $u }}</li> @endforeach
                    </ul>
                @endif
            </section>

        {{-- ────────────────────────────── PROGRAMS SUMMARY ────────────────────────────── --}}
        @elseif($type === 'programs_summary')
            <section class="bg-gradient-to-br from-emerald-50 to-teal-50 ring-1 ring-emerald-200 rounded-2xl p-6 shadow-sm">
                <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] ?? __('Programs') }}</h2>
                <div class="grid grid-cols-4 gap-3 text-sm text-gray-900">
                    <div><span class="font-bold text-2xl">{{ $block['total'] ?? 0 }}</span> <span class="text-gray-700">{{ __('total') }}</span></div>
                    <div><span class="font-bold text-2xl">{{ $block['bachelor'] ?? 0 }}</span> <span class="text-gray-700">Bachelor</span></div>
                    <div><span class="font-bold text-2xl">{{ $block['master'] ?? 0 }}</span> <span class="text-gray-700">Master</span></div>
                    <div><span class="font-bold text-2xl">{{ $block['phd'] ?? 0 }}</span> <span class="text-gray-700">PhD</span></div>
                </div>
            </section>

        {{-- ────────────────────────────── ALMANYAUNI FORUM TOPICS ────────────────────────────── --}}
        @elseif($type === 'almanyauni_forum_topics')
            {{-- Topluluk Türk öğrencilere özel → yalnızca TR sayfalarda göster --}}
            @if(app()->getLocale() === 'tr' && !empty($block['items']))
                <section class="bg-gradient-to-br from-primary-50 to-accent-50 ring-2 ring-primary-300 rounded-2xl p-6 shadow-md">
                    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-100 text-primary-700"><x-svg-icon name="chat-bubble" class="w-6 h-6" /></span>
                            <div>
                                <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold text-gray-900 scroll-mt-24">{{ $block['h'] ?? __('AlmanyaUni Forum') }}</h2>
                                <p class="text-xs text-primary-700 font-medium">{{ __('Experiences from our international student community') }}</p>
                            </div>
                        </div>
                        <a href="{{ $block['cta_url'] ?? '/forum/' }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition">
                            {{ __('Go to forum') }} →
                        </a>
                    </div>
                    <ul class="space-y-2">
                        @foreach($block['items'] ?? [] as $t)
                            <li>
                                <a href="{{ $t['url'] }}"
                                   class="group flex items-start justify-between gap-3 bg-white ring-1 ring-primary-100 hover:ring-primary-400 hover:shadow-md rounded-lg p-3 transition">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 group-hover:text-primary-700 leading-snug">
                                            {{ $t['title'] }}
                                        </h3>
                                        @if(!empty($t['last_post']))
                                            <p class="text-xs text-gray-500 mt-0.5">{{ __('Last activity:') }} {{ $t['last_post'] }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right text-xs text-gray-600 shrink-0">
                                        @if(!empty($t['views']))
                                            <div class="font-semibold text-primary-700 inline-flex items-center gap-1"><x-svg-icon name="cursor-arrow-rays" class="w-3 h-3" /> {{ number_format($t['views']) }}</div>
                                        @endif
                                        @if(!empty($t['replies']))
                                            <div class="inline-flex items-center gap-1"><x-svg-icon name="chat-bubble" class="w-3 h-3" /> {{ __(':n replies', ['n' => $t['replies']]) }}</div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

        {{-- ────────────────────────────── RELATED FORUM TOPICS ────────────────────────────── --}}
        @elseif($type === 'related_forum_topics')
            {{-- Topluluk Türk öğrencilere özel → yalnızca TR sayfalarda göster --}}
            @if(app()->getLocale() === 'tr' && !empty($block['items']))
                <section class="bg-gradient-to-br from-violet-50 to-purple-50 ring-1 ring-violet-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-xl font-bold text-gray-900 scroll-mt-24 inline-flex items-center gap-2"><x-svg-icon name="chat-bubble" class="w-5 h-5 text-violet-600" /> {{ $block['h'] ?? __('Community Discussions') }}</h2>
                        @if(!empty($block['source']))
                            <span class="text-xs text-violet-700 font-medium uppercase tracking-wide">{{ __('Source:') }} {{ $block['source'] }}</span>
                        @endif
                    </div>
                    <ul class="space-y-2">
                        @foreach($block['items'] ?? [] as $t)
                            <li>
                                <a href="{{ $t['url'] }}" target="_blank" rel="noopener nofollow"
                                   class="group flex items-start justify-between gap-3 bg-white ring-1 ring-violet-100 hover:ring-violet-300 hover:shadow-md rounded-lg p-3 transition">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 group-hover:text-violet-700 leading-snug">
                                            {{ $t['title'] }}
                                        </h3>
                                        @if(!empty($t['category']))
                                            <span class="inline-block text-xs text-violet-700 mt-1 uppercase tracking-wide">{{ str_replace('_', ' ', $t['category']) }}</span>
                                        @endif
                                    </div>
                                    <div class="text-right text-xs text-gray-600 shrink-0">
                                        @if(!empty($t['views']))
                                            <div class="font-semibold text-violet-700 inline-flex items-center gap-1"><x-svg-icon name="cursor-arrow-rays" class="w-3 h-3" /> {{ number_format($t['views']) }}</div>
                                        @endif
                                        @if(!empty($t['replies']))
                                            <div class="inline-flex items-center gap-1"><x-svg-icon name="chat-bubble" class="w-3 h-3" /> {{ __(':n replies', ['n' => $t['replies']]) }}</div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-gray-600 mt-3 italic">{!! __('These threads come from an external source (community.deutschstudent.com). To join AlmanyaUni\'s own forum visit <a href="/forum/" class="text-violet-700 underline hover:text-violet-900">/forum</a>.') !!}</p>
                </section>
            @endif

        {{-- ────────────────────────────── EXTERNAL LINKS ────────────────────────────── --}}
        @elseif($type === 'external_links')
            @if(!empty($block['items']))
                <section>
                    @if(!empty($block['h']))
                        <h2 @if($blockId) id="{{ $blockId }}" @endif class="text-lg font-bold mb-3 text-gray-900 scroll-mt-24">{{ $block['h'] }}</h2>
                    @endif
                    <div class="flex flex-wrap gap-2">
                        @foreach($block['items'] ?? [] as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 px-3 py-2 bg-white ring-1 ring-gray-200 hover:bg-gray-50 hover:ring-primary-400 rounded-lg text-sm transition text-gray-900">
                                {{ $link['label'] ?? $link['url'] }} <span class="text-gray-500">↗</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

        {{-- ────────────────────────────── SCHEMA.ORG JSON-LD ────────────────────────────── --}}
        @elseif($type === 'schema_jsonld')
            <script type="application/ld+json">{!! json_encode($block['data'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

        @endif
    @endforeach
    </div>

    {{-- ───────────── Lightbox modal (tüm gallery blokları paylaşır) ───────────── --}}
    @once
        <div id="lightboxModal"
             class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200"
             role="dialog" aria-modal="true" aria-label="{{ __('Image preview') }}">

            <button type="button" id="lightboxClose" aria-label="{{ __('Close') }}"
                    class="absolute top-4 right-4 z-10 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 ring-1 ring-white/30 text-white flex items-center justify-center text-2xl transition">
                ×
            </button>

            <button type="button" id="lightboxPrev" aria-label="{{ __('Previous image') }}"
                    class="absolute left-2 md:left-6 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 ring-1 ring-white/30 text-white flex items-center justify-center text-2xl transition">
                ‹
            </button>

            <button type="button" id="lightboxNext" aria-label="{{ __('Next image') }}"
                    class="absolute right-2 md:right-6 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 ring-1 ring-white/30 text-white flex items-center justify-center text-2xl transition">
                ›
            </button>

            <div class="w-full h-full flex items-center justify-center p-6 md:p-12" id="lightboxBackdrop">
                <figure class="max-w-full max-h-full flex flex-col items-center pointer-events-none">
                    <img id="lightboxImage" src="" alt=""
                         class="max-w-full max-h-[80vh] w-auto h-auto object-contain rounded-lg shadow-2xl pointer-events-auto" loading="lazy" decoding="async"/>
                    <figcaption id="lightboxCaption"
                                class="text-white text-sm mt-3 text-center max-w-2xl pointer-events-auto"></figcaption>
                    <a id="lightboxSource" href="" target="_blank" rel="noopener"
                       class="hidden mt-2 text-xs text-white/70 hover:text-white underline pointer-events-auto">
                        {{ __('Wikipedia source') }} ↗
                    </a>
                </figure>
            </div>
        </div>

        <script>
        (function () {
            const modal = document.getElementById('lightboxModal');
            if (!modal) return;
            const img = document.getElementById('lightboxImage');
            const cap = document.getElementById('lightboxCaption');
            const src = document.getElementById('lightboxSource');
            const closeBtn = document.getElementById('lightboxClose');
            const prevBtn = document.getElementById('lightboxPrev');
            const nextBtn = document.getElementById('lightboxNext');
            const backdrop = document.getElementById('lightboxBackdrop');

            let currentGroup = [];
            let currentIndex = 0;

            function open(group, index) {
                currentGroup = group;
                currentIndex = index;
                render();
                modal.classList.remove('hidden');
                requestAnimationFrame(() => modal.classList.remove('opacity-0'));
                document.body.style.overflow = 'hidden';
            }
            function close() {
                modal.classList.add('opacity-0');
                setTimeout(() => modal.classList.add('hidden'), 200);
                document.body.style.overflow = '';
            }
            function render() {
                const item = currentGroup[currentIndex];
                if (!item) return;
                img.src = item.dataset.url;
                img.alt = item.dataset.alt || '';
                cap.textContent = item.dataset.alt || '';
                if (item.dataset.source) {
                    src.href = item.dataset.source;
                    src.classList.remove('hidden');
                } else {
                    src.classList.add('hidden');
                }
                prevBtn.style.visibility = currentGroup.length > 1 ? 'visible' : 'hidden';
                nextBtn.style.visibility = currentGroup.length > 1 ? 'visible' : 'hidden';
            }
            function step(dir) {
                if (!currentGroup.length) return;
                currentIndex = (currentIndex + dir + currentGroup.length) % currentGroup.length;
                render();
            }

            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('[data-lightbox-trigger]');
                if (!trigger) return;
                e.preventDefault();
                const groupId = trigger.dataset.lightboxTrigger;
                const group = Array.from(document.querySelectorAll(`[data-lightbox-trigger="${groupId}"]`));
                const index = parseInt(trigger.dataset.index || '0', 10);
                open(group, index);
            });
            closeBtn.addEventListener('click', close);
            prevBtn.addEventListener('click', () => step(-1));
            nextBtn.addEventListener('click', () => step(1));
            backdrop.addEventListener('click', (e) => { if (e.target === backdrop) close(); });

            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('hidden')) return;
                if (e.key === 'Escape') close();
                else if (e.key === 'ArrowLeft') step(-1);
                else if (e.key === 'ArrowRight') step(1);
            });
        })();
        </script>
    @endonce
@endif
