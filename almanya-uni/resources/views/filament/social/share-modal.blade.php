@php /** @var array|null $share */ @endphp

@if (! $share)
    <div class="text-sm text-gray-500">Paylaşım verisi hazırlanamadı (asset içeriği boş olabilir).</div>
@else
    <div class="space-y-4" x-data="{ copied: false }">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $share['label'] }}</span>
            @if ($share['supports_intent'])
                <span class="rounded-full bg-primary-100 px-2 py-0.5 text-xs text-primary-700 dark:bg-primary-900 dark:text-primary-200">hazır-metinli paylaşım destekli</span>
            @else
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">elle paylaşım (kopyala-yapıştır)</span>
            @endif
        </div>

        {{-- Paylaşım metni --}}
        <div>
            <div class="mb-1 flex items-center justify-between">
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Paylaşım metni</label>
                <button type="button"
                    class="rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    x-on:click="navigator.clipboard.writeText($refs.txt.value); copied = true; setTimeout(() => copied = false, 1500)">
                    <span x-show="!copied">📋 Kopyala</span>
                    <span x-show="copied" x-cloak>✓ Kopyalandı</span>
                </button>
            </div>
            <textarea x-ref="txt" readonly rows="9"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 p-3 font-mono text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ $share['text'] }}</textarea>
        </div>

        {{-- Görseller --}}
        @if (count($share['media']))
            <div>
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Görseller ({{ count($share['media']) }})</label>
                <div class="mt-1 flex flex-wrap gap-2">
                    @foreach ($share['media'] as $m)
                        <a href="{{ $m }}" target="_blank" rel="noopener" download
                           class="block overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <img src="{{ $m }}" alt="" class="h-24 w-24 object-cover" />
                        </a>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-400">Görsele tıkla → yeni sekmede aç / indir.</p>
            </div>
        @endif

        {{-- Aç butonları --}}
        <div class="flex flex-wrap gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
            @if ($share['intent_url'])
                <a href="{{ $share['intent_url'] }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white hover:bg-primary-500">
                    🚀 Hazır metinle aç
                </a>
            @endif
            <a href="{{ $share['open_url'] }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                ↗ Platformu aç
            </a>
        </div>

        <p class="text-xs text-gray-400">Paylaştıktan sonra tabloda <strong>✓ Paylaşıldı</strong> ile yayın linkini kaydet.</p>
    </div>
@endif
