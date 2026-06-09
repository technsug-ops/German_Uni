<x-filament-panels::page>
    {{ $this->form }}

    @if($errorMessage)
        <x-filament::section>
            <div class="flex items-start gap-3 text-danger-700 dark:text-danger-400">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 shrink-0 mt-0.5" />
                <div class="text-sm">
                    <span class="font-semibold">Hata:</span> {{ $errorMessage }}
                </div>
            </div>
        </x-filament::section>
    @endif

    @php
        $formatMeta = [
            'how_to'          => ['How-to', 'info'],
            'listicle'        => ['Listicle', 'primary'],
            'comparison'      => ['Karşılaştırma', 'warning'],
            'case_study'      => ['Case Study', 'success'],
            'deep_dive'       => ['Deep Dive', 'info'],
            'myth_busting'    => ['Mit Çürüt', 'danger'],
            'checklist'       => ['Checklist', 'success'],
            'interview'       => ['Röportaj', 'gray'],
            'news_analysis'   => ['Haber Analiz', 'warning'],
            'calculator_tool' => ['Hesaplayıcı', 'primary'],
            'data_driven'     => ['Data-Driven', 'info'],
        ];
        $intentColor = [
            'informational' => 'gray',
            'navigational'  => 'info',
            'transactional' => 'success',
        ];
    @endphp

    @if(count($suggestions) > 0)
        {{-- Üst bar: sayı + toplu aksiyon + model/token --}}
        <x-filament::section>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                        <x-filament::icon icon="heroicon-o-light-bulb" class="h-5 w-5" />
                    </span>
                    <div>
                        <div class="text-base font-semibold text-gray-950 dark:text-white">
                            {{ count($suggestions) }} Brief Önerisi
                        </div>
                        @if($modelUsed || $tokens)
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($modelUsed)
                                    <span class="inline-flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-m-cpu-chip" class="h-3.5 w-3.5" />
                                        {{ $modelUsed }}
                                    </span>
                                @endif
                                @if($tokens)
                                    <span aria-hidden="true">·</span>
                                    <span>{{ number_format($tokens['input']) }} in / {{ number_format($tokens['output']) }} out token</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <x-filament::button
                    color="success"
                    icon="heroicon-m-check-circle"
                    wire:click="acceptAll"
                    wire:confirm="{{ count($suggestions) }} brief'i birden oluşturmak istediğinden emin misin?">
                    Hepsini Brief'e Çevir
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Öneri kartları --}}
        <div class="space-y-4">
            @foreach($suggestions as $i => $s)
                @php
                    $fmt = $s['content_format'] ?? null;
                    [$fmtLabel, $fmtColor] = $formatMeta[$fmt] ?? [$fmt, 'gray'];
                    $intent = $s['search_intent'] ?? null;
                @endphp
                <x-filament::section>
                    {{-- Başlık + aksiyonlar --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline gap-2">
                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">#{{ $i + 1 }}</span>
                                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    {{ $s['title'] ?? '(başlıksız)' }}
                                </h3>
                            </div>

                            {{-- Rozetler --}}
                            <div class="mt-3 flex flex-wrap items-center gap-1.5">
                                @if($fmt)
                                    <x-filament::badge :color="$fmtColor" icon="heroicon-m-squares-2x2">
                                        {{ $fmtLabel }}
                                    </x-filament::badge>
                                @endif
                                @if(!empty($s['primary_keyword']))
                                    <x-filament::badge color="primary" icon="heroicon-m-magnifying-glass">
                                        {{ $s['primary_keyword'] }}
                                    </x-filament::badge>
                                @endif
                                @if($intent)
                                    <x-filament::badge :color="$intentColor[$intent] ?? 'gray'">
                                        {{ $intent }}
                                    </x-filament::badge>
                                @endif
                                @if(!empty($s['target_word_count']))
                                    <x-filament::badge color="gray" icon="heroicon-m-document-text">
                                        {{ number_format($s['target_word_count']) }} kelime
                                    </x-filament::badge>
                                @endif
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <x-filament::button
                                size="sm"
                                color="success"
                                icon="heroicon-m-plus"
                                wire:click="acceptSuggestion({{ $i }})">
                                Brief Oluştur
                            </x-filament::button>
                            <x-filament::icon-button
                                color="gray"
                                icon="heroicon-m-trash"
                                label="Sil"
                                wire:click="dismissSuggestion({{ $i }})" />
                        </div>
                    </div>

                    {{-- Pain point --}}
                    @if(!empty($s['pain_point']))
                        <div class="mt-4 rounded-lg bg-gray-50 p-3 dark:bg-white/5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pain point</div>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $s['pain_point'] }}</p>
                        </div>
                    @endif

                    {{-- Unique angle --}}
                    @if(!empty($s['unique_angle']))
                        <div class="mt-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Unique angle</div>
                            <p class="mt-1 text-sm italic text-gray-600 dark:text-gray-400">{{ $s['unique_angle'] }}</p>
                        </div>
                    @endif

                    {{-- İkincil keywordler --}}
                    @if(!empty($s['secondary_keywords']))
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">İkincil:</span>
                            @foreach((array) $s['secondary_keywords'] as $kw)
                                <x-filament::badge color="gray" size="sm">{{ $kw }}</x-filament::badge>
                            @endforeach
                        </div>
                    @endif

                    {{-- Ham telegram soruları --}}
                    @if(!empty($s['source_questions']))
                        <details class="group mt-4">
                            <summary class="inline-flex cursor-pointer items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-m-chat-bubble-left-right" class="h-4 w-4" />
                                {{ count((array) $s['source_questions']) }} ham topluluk sorusu
                                <x-filament::icon icon="heroicon-m-chevron-down" class="h-4 w-4 transition group-open:rotate-180" />
                            </summary>
                            <ul class="mt-2 space-y-1 pl-5 text-xs text-gray-600 dark:text-gray-400">
                                @foreach((array) $s['source_questions'] as $q)
                                    <li class="list-disc">{{ $q }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                </x-filament::section>
            @endforeach
        </div>
    @elseif(!$errorMessage)
        {{-- Boş durum --}}
        <x-filament::section>
            <div class="flex flex-col items-center gap-4 py-8 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-500 dark:bg-primary-500/10 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-o-sparkles" class="h-7 w-7" />
                </span>
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Henüz öneri yok</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Topic + hedef kitle seç, yukarıdaki <span class="font-medium">AI ile Öner</span> butonuna bas.
                    </p>
                </div>

                @if(!empty($sourceStats))
                    <div class="mt-2 w-full max-w-2xl text-left">
                        <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Kaynak veri (manipüle edilmemiş)
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 dark:border-white/10">
                                <x-filament::icon icon="heroicon-o-device-phone-mobile" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
                                <div>
                                    <div class="text-sm font-semibold text-gray-950 dark:text-white">Telegram</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($sourceStats['telegram_topics'] ?? 0) }} topic ·
                                        {{ number_format($sourceStats['telegram_total_questions'] ?? 0) }} soru
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 dark:border-white/10">
                                <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
                                <div>
                                    <div class="text-sm font-semibold text-gray-950 dark:text-white">Forum (DeutschStudent)</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($sourceStats['forum_top_topics'] ?? 0) }} top topic ·
                                        {{ number_format($sourceStats['forum_trending_keywords'] ?? 0) }} trending kw ·
                                        {{ number_format($sourceStats['forum_categories'] ?? 0) }} kategori
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            AI önerileri <span class="font-medium">iki kaynağın kesişimini</span> tarar — reklamsız,
                            yönlendirilmemiş, 5 yıllık Türk öğrenci pain-point havuzu.
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
