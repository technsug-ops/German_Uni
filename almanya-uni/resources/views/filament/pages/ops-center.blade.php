<x-filament-panels::page>
    <div style="font-size:13px; color:#6b7280; margin-bottom:.5rem;">
        Sık kullanılan işlemler — tek tık, URL yazmaya gerek yok. Çıktı bildirimde görünür.
        <span wire:loading wire:target="runOp" style="color:#2563eb;">· çalışıyor…</span>
    </div>

    @foreach (\App\Filament\Pages\OpsCenter::GROUPS as $group => $ops)
        <div style="margin-bottom:1.25rem;">
            <div style="font-weight:700; font-size:14px; margin-bottom:.6rem; color:#374151;">{{ $group }}</div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:.75rem;">
                @foreach ($ops as [$key, $emoji, $titleTxt, $desc, $confirm])
                    <div style="border:1px solid #e5e7eb; border-radius:.75rem; background:#fff; padding:1rem; display:flex; flex-direction:column; gap:.5rem;"
                         class="ops-card">
                        <div style="font-size:15px; font-weight:600; color:#111827;">{{ $emoji }} {{ $titleTxt }}</div>
                        <div style="font-size:12px; color:#6b7280; line-height:1.45; flex:1;">{{ $desc }}</div>
                        <div>
                            <x-filament::button
                                size="sm"
                                icon="heroicon-o-play"
                                wire:click="runOp('{{ $key }}')"
                                wire:confirm="{{ $confirm }}"
                                wire:loading.attr="disabled"
                                wire:target="runOp">
                                Çalıştır
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <style>
        .dark .ops-card { background:#111827 !important; border-color:#374151 !important; }
        .dark .ops-card > div:first-child { color:#fff !important; }
    </style>
</x-filament-panels::page>
