<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check" color="success">
                Kaydet
            </x-filament::button>
        </div>
    </form>

    <x-filament::section icon="heroicon-o-information-circle" collapsible collapsed>
        <x-slot name="heading">Nasıl çalışır?</x-slot>

        <div class="prose prose-sm dark:prose-invert max-w-none">
            <ul>
                <li><strong>Boş alan = kapalı.</strong> Bir kimlik girilmedikçe o entegrasyonun kodu siteye hiç eklenmez.</li>
                <li><strong>Çerez onayı zorunluyken</strong> (varsayılan), GA4 · Google Ads · Meta · TikTok yalnızca ziyaretçi çerezleri kabul ettikten sonra çalışır. Google için <em>Consent Mode v2</em> kullanılır (onay öncesi tüm depolama reddedilir).</li>
                <li><strong>Search Console / Bing / Yandex doğrulama</strong> etiketleri izleyici değildir; onaydan bağımsız, her zaman basılır.</li>
                <li><strong>Google Merchant Center</strong> doğrulaması için ayrı bir alan yoktur — Merchant, Search Console ile aynı doğrulama etiketini kullanır (yukarıdaki Google alanı yeterli). Ürün besleme/feed yalnızca e-ticaret siteleri içindir, bu sitede gerekmez.</li>
                <li>Değişiklikler <strong>anında yayında</strong> — ayrı bir dağıtım/cache temizliği gerekmez.</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>
