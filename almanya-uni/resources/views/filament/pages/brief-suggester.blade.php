<x-filament-panels::page>
    {{ $this->form }}

    @if($errorMessage)
        <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
            <div class="text-red-700 dark:text-red-300 text-sm">
                <strong>Hata:</strong> {{ $errorMessage }}
            </div>
        </div>
    @endif

    @if($tokens)
        <div class="text-xs text-gray-500">
            🪙 Tokens: {{ $tokens['input'] }} in / {{ $tokens['output'] }} out
        </div>
    @endif

    @if(count($suggestions) > 0)
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">💡 {{ count($suggestions) }} Brief Önerisi</h2>
            <button wire:click="acceptAll" wire:confirm="{{ count($suggestions) }} brief'i birden oluşturmak istediğinden emin misin?"
                    class="px-4 py-2 bg-success-600 hover:bg-success-700 text-white text-sm font-semibold rounded-lg">
                ✅ Hepsini Brief'e Çevir
            </button>
        </div>

        <div class="space-y-4">
            @foreach($suggestions as $i => $s)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                #{{ $i + 1 }} — {{ $s['title'] ?? '?' }}
                            </h3>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                @if(!empty($s['content_format']))
                                    @php
                                        $fmt = $s['content_format'];
                                        $fmtLabel = match($fmt) {
                                            'how_to' => '📋 How-to',
                                            'listicle' => '🔢 Listicle',
                                            'comparison' => '⚖️ Karşılaştırma',
                                            'case_study' => '📖 Case Study',
                                            'deep_dive' => '🔍 Deep Dive',
                                            'myth_busting' => '💥 Mit Çürüt',
                                            'checklist' => '✅ Checklist',
                                            'interview' => '🎤 Röportaj',
                                            'news_analysis' => '📰 Haber Analiz',
                                            'calculator_tool' => '🧮 Hesaplayıcı',
                                            'data_driven' => '📊 Data-Driven',
                                            default => $fmt,
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 font-semibold">
                                        {{ $fmtLabel }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-1 rounded bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                                    🎯 {{ $s['primary_keyword'] ?? '-' }}
                                </span>
                                @if(!empty($s['search_intent']))
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-info-100 text-info-700 dark:bg-info-900/30 dark:text-info-300">
                                        {{ $s['search_intent'] }}
                                    </span>
                                @endif
                                @if(!empty($s['target_word_count']))
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        📝 {{ $s['target_word_count'] }} kelime
                                    </span>
                                @endif
                            </div>
                            @if(!empty($s['unique_angle']))
                                <div class="mt-2 text-xs italic text-gray-600 dark:text-gray-400">
                                    ✨ <strong>Unique angle:</strong> {{ $s['unique_angle'] }}
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button wire:click="acceptSuggestion({{ $i }})"
                                    class="px-3 py-1.5 bg-success-600 hover:bg-success-700 text-white text-xs font-semibold rounded-lg">
                                ✅ Brief Oluştur
                            </button>
                            <button wire:click="dismissSuggestion({{ $i }})"
                                    class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg">
                                ✗ Sil
                            </button>
                        </div>
                    </div>

                    @if(!empty($s['pain_point']))
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <strong>Pain point:</strong> {{ $s['pain_point'] }}
                        </div>
                    @endif

                    @if(!empty($s['secondary_keywords']))
                        <div class="mt-2 flex flex-wrap gap-1 text-xs">
                            <span class="text-gray-500">İkincil:</span>
                            @foreach((array)$s['secondary_keywords'] as $kw)
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $kw }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($s['source_questions']))
                        <details class="mt-3">
                            <summary class="cursor-pointer text-xs text-primary-600 dark:text-primary-400 hover:underline">
                                💬 {{ count($s['source_questions']) }} ham telegram sorusu
                            </summary>
                            <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-400 pl-4">
                                @foreach((array)$s['source_questions'] as $q)
                                    <li class="list-disc">{{ $q }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif(!$errorMessage)
        @php
            $stats = app(\App\Services\Content\BriefSuggestionService::class)->stats();
        @endphp
        <div class="text-center text-gray-500 dark:text-gray-400 py-12 space-y-4">
            <div class="text-6xl">🪄</div>
            <p class="font-medium">Topic + hedef kitle seç, "AI ile Öner" butonuna bas.</p>
            <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5 text-left">
                <div class="text-sm font-semibold text-gray-900 dark:text-white mb-3">📚 Kaynak Veri (Manipüle Edilmemiş)</div>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📱</span>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Telegram</div>
                            <div class="text-gray-500">{{ $stats['telegram_topics'] }} topic · {{ number_format($stats['telegram_total_questions']) }} soru</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">💬</span>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Forum (DeutschStudent)</div>
                            <div class="text-gray-500">{{ $stats['forum_top_topics'] }} top topic · {{ $stats['forum_trending_keywords'] }} trending kw · {{ $stats['forum_categories'] }} kategori</div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4">
                    AI önerileri <strong>iki kaynağın kesişimini</strong> tarar — reklamsız, yönlendirilmemiş, 5 yıllık Türk öğrenci pain-point havuzu.
                </p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
