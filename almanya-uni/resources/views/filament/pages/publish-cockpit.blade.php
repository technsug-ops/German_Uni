<x-filament-panels::page>
    @php
        $manager = app(\App\Services\Social\PublisherManager::class);
        $auto = $manager->isAutomaticActive();
    @endphp

    <div @class([
        'rounded-xl border p-4 text-sm',
        'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200' => $auto,
        'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-200' => ! $auto,
    ])>
        @if ($auto)
            <span class="font-semibold">🤖 Otomatik mod (Ayrshare)</span> — "Otomatik Paylaş" asset'i doğrudan platforma gönderir.
        @else
            <span class="font-semibold">✍️ Manuel-asistan modu</span> — "Paylaş" ile metni kopyala + platformu aç, sonra "✓ Paylaşıldı" ile linki kaydet.
            Otomatik paylaşım için <strong>⚙️ Yayın Ayarları</strong>'ndan Ayrshare key gir.
        @endif
    </div>

    {{ $this->table }}

    <div class="text-xs text-gray-400">
        Yalnızca sosyal asset türleri listelenir (Instagram, X, TikTok, LinkedIn, Pinterest, YouTube). Blog asset'leri
        Brief sayfasındaki <strong>📤 Blog'a Aktar</strong> ile yayınlanır.
    </div>
</x-filament-panels::page>
