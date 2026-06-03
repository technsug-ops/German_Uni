<x-filament-panels::page>
    @php
        $auto = app(\App\Services\Social\PublisherManager::class)->isAutomaticActive();
    @endphp

    <div style="border-radius:.75rem; border:1px solid {{ $auto ? '#a7f3d0' : '#fde68a' }};
                background:{{ $auto ? '#ecfdf5' : '#fffbeb' }}; color:{{ $auto ? '#065f46' : '#92400e' }};
                padding:1rem; font-size:14px; line-height:1.5;">
        @if ($auto)
            <strong>🤖 Otomatik mod (Ayrshare)</strong> — sosyal asset'lerde "Otomatik Paylaş" doğrudan platforma gönderir.
        @else
            <strong>✍️ Manuel-asistan modu</strong> — sosyalde "Paylaş" ile metni kopyala + platformu aç, sonra "✓ Paylaşıldı" ile linki kaydet.
            Otomatik paylaşım için <strong>⚙️ Yayın Ayarları</strong>'ndan Ayrshare key gir.
        @endif
        <br>
        <strong>📝 Blog</strong> satırlarında "Blog'a Aktar" tek tıkla TR(+EN+DE) yayınlar. Çoklu seçip <strong>toplu</strong> da yapabilirsin.
    </div>

    {{ $this->table }}

    <div style="font-size:12px; color:#9ca3af;">
        Blog + sosyal (Instagram, X, TikTok, LinkedIn, Pinterest, YouTube) asset'leri tek yerde. Üstte <strong>Tür</strong> filtresiyle ayır.
        Satır seç → araç çubuğundan <strong>toplu</strong> yayınla/paylaş.
    </div>
</x-filament-panels::page>
