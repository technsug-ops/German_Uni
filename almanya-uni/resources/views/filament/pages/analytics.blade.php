<x-filament-panels::page>
    {{-- Kendi CSS'imiz — Filament vendor CSS'ine/Tailwind derlemesine BAĞIMSIZ.
         viteTheme yok → 500 riski yok, her zaman render olur. Light + dark uyumlu. --}}
    <style>
        .az-wrap { display: flex; flex-direction: column; gap: 1rem; font-size: 14px; }
        .az-range { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
        .az-range .lbl { color: #6b7280; margin-right: .25rem; }
        .az-btn { padding: .375rem .75rem; border-radius: .5rem; font-weight: 500; border: 1px solid #e5e7eb;
                  background: #f3f4f6; color: #374151; cursor: pointer; }
        .az-btn:hover { background: #e5e7eb; }
        .az-btn.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .az-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        @media (min-width: 768px) { .az-cards { grid-template-columns: repeat(4, 1fr); } }
        .az-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; padding: 1rem; }
        .az-card .k { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        .az-card .v { font-size: 24px; font-weight: 700; margin-top: .25rem; font-variant-numeric: tabular-nums; color: #111827; }
        .az-card .v.green { color: #16a34a; }
        .az-card .s { font-size: 11px; color: #9ca3af; font-variant-numeric: tabular-nums; }
        .az-panel { border: 1px solid #e5e7eb; border-radius: .75rem; overflow: hidden; background: #fff; }
        .az-panel-h { padding: .75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 600; }
        .az-panel-h .muted { font-weight: 400; color: #9ca3af; }
        .az-pad { padding: 1rem; }
        .az-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .5rem; }
        .az-row .name { width: 120px; color: #4b5563; }
        .az-track { flex: 1; height: 12px; border-radius: 9999px; background: #f3f4f6; overflow: hidden; }
        .az-fill { height: 100%; }
        .az-row .pct { width: 96px; text-align: right; font-variant-numeric: tabular-nums; color: #374151; }
        .az-grid2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
        @media (min-width: 1024px) { .az-grid2 { grid-template-columns: 2fr 1fr; } }
        .az-scroll { max-height: 32rem; overflow-y: auto; }
        .az-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .az-table th { position: sticky; top: 0; background: #f9fafb; text-align: left; padding: .5rem 1rem;
                       font-size: 11px; text-transform: uppercase; color: #9ca3af; font-weight: 600; }
        .az-table th.r, .az-table td.r { text-align: right; }
        .az-table td { padding: .5rem 1rem; border-top: 1px solid #f3f4f6; font-variant-numeric: tabular-nums; }
        .az-table tr:hover td { background: #f9fafb; }
        .az-path { font-family: ui-monospace, monospace; font-size: 12px; color: #2563eb; word-break: break-all; text-decoration: none; }
        .az-path:hover { text-decoration: underline; }
        .az-empty { padding: 2rem; text-align: center; font-style: italic; color: #9ca3af; }
        .az-trend { display: flex; align-items: flex-end; gap: 4px; height: 140px; overflow-x: auto; padding-bottom: 4px; }
        .az-trend .col { flex: 1; min-width: 18px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 4px; }
        .az-trend .b { width: 100%; border-radius: 3px 3px 0 0; background: #3b82f6; min-height: 2px; }
        .az-trend .d { font-size: 9px; color: #9ca3af; writing-mode: vertical-rl; white-space: nowrap; }
        .az-note { font-size: 11px; color: #9ca3af; }
        .az-loading { font-size: 11px; color: #9ca3af; margin-left: .5rem; }
        /* Dark mode (Filament <html class="dark">) */
        .dark .az-btn { background: #1f2937; color: #d1d5db; border-color: #374151; }
        .dark .az-btn:hover { background: #374151; }
        .dark .az-card, .dark .az-panel { background: #111827; border-color: #374151; }
        .dark .az-card .v { color: #fff; }
        .dark .az-panel-h { background: #1f2937; border-color: #374151; }
        .dark .az-table th { background: #1f2937; }
        .dark .az-table td { border-color: #1f2937; }
        .dark .az-table tr:hover td { background: rgba(31,41,55,.5); }
        .dark .az-track { background: #374151; }
        .dark .az-range .lbl, .dark .az-row .name, .dark .az-row .pct { color: #9ca3af; }
    </style>

    @php
        $devTotal = max(1, $devices['total'] ?? 0);
        $devRows = [
            ['📱 Mobil', $devices['mobile'] ?? 0, '#10b981'],
            ['💻 Masaüstü', $devices['desktop'] ?? 0, '#3b82f6'],
            ['📲 Tablet', $devices['tablet'] ?? 0, '#f59e0b'],
        ];
        $cards = [
            ['🟢 Çevrimiçi (5 dk)', $overview['online_now'] ?? 0, 'green', null, 'tekil ziyaretçi'],
            ['👤 Bugün', $overview['uv_today'] ?? 0, '', $overview['pv_today'] ?? 0, null],
            ['📅 Son 7 gün', $overview['uv_week'] ?? 0, '', $overview['pv_week'] ?? 0, null],
            ['🗓️ Son 30 gün', $overview['uv_month'] ?? 0, '', $overview['pv_month'] ?? 0, null],
        ];
        $maxPv = max(1, max($trend['pv'] ?? [0]));
    @endphp

    <div class="az-wrap">
        {{-- Tarih aralığı --}}
        <div class="az-range">
            <span class="lbl">Aralık:</span>
            @foreach ([1 => 'Bugün', 7 => '7 gün', 30 => '30 gün', 90 => '90 gün'] as $d => $label)
                <button type="button" wire:click="setDays({{ $d }})" class="az-btn {{ $days === $d ? 'active' : '' }}">{{ $label }}</button>
            @endforeach
            <span wire:loading class="az-loading">yükleniyor…</span>
        </div>

        {{-- Genel bakış --}}
        <div class="az-cards">
            @foreach ($cards as $c)
                <div class="az-card">
                    <div class="k">{{ $c[0] }}</div>
                    <div class="v {{ $c[2] }}">{{ number_format($c[1]) }}</div>
                    @if ($c[3] !== null)
                        <div class="s">{{ number_format($c[3]) }} görüntüleme</div>
                    @else
                        <div class="s">{{ $c[4] }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Cihaz dağılımı --}}
        <div class="az-panel">
            <div class="az-panel-h">Cihaz dağılımı <span class="muted">({{ $this->getRangeLabel() }})</span></div>
            <div class="az-pad">
                @foreach ($devRows as $dr)
                    @php($pct = round(100 * $dr[1] / $devTotal))
                    <div class="az-row">
                        <div class="name">{{ $dr[0] }}</div>
                        <div class="az-track"><div class="az-fill" style="width: {{ $pct }}%; background: {{ $dr[2] }};"></div></div>
                        <div class="pct"><strong>{{ $pct }}%</strong> <span class="az-note">({{ number_format($dr[1]) }})</span></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sayfalar + Referrer --}}
        <div class="az-grid2">
            <div class="az-panel">
                <div class="az-panel-h">📄 En çok ziyaret edilen sayfalar <span class="muted">({{ $this->getRangeLabel() }} · ilk 50)</span></div>
                <div class="az-scroll">
                    <table class="az-table">
                        <thead><tr><th>#</th><th>Yol</th><th class="r">Tekil</th><th class="r">Görnt.</th><th class="r">Ort. ms</th></tr></thead>
                        <tbody>
                            @forelse ($pages as $i => $p)
                                <tr>
                                    <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                                    <td><a href="{{ $p['path'] }}" target="_blank" class="az-path">{{ $p['path'] }}</a></td>
                                    <td class="r"><strong>{{ number_format($p['uv']) }}</strong></td>
                                    <td class="r" style="color:#6b7280;">{{ number_format($p['pv']) }}</td>
                                    <td class="r" style="color:#9ca3af;">{{ $p['avg_ms'] ? number_format($p['avg_ms']) : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="az-empty">Bu aralıkta veri yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="az-panel">
                <div class="az-panel-h">🔗 Trafik kaynakları <span class="muted">(ilk 30)</span></div>
                <div class="az-scroll">
                    <table class="az-table">
                        <thead><tr><th>Kaynak</th><th class="r">Tekil</th><th class="r">Görnt.</th></tr></thead>
                        <tbody>
                            @forelse ($referrers as $r)
                                <tr>
                                    <td style="word-break:break-all; color:#374151;">{{ $r['host'] }}</td>
                                    <td class="r"><strong>{{ number_format($r['uv']) }}</strong></td>
                                    <td class="r" style="color:#6b7280;">{{ number_format($r['pv']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="az-empty">Doğrudan trafik (referrer yok).</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Günlük trend --}}
        <div class="az-panel">
            <div class="az-panel-h">Günlük trend <span class="muted">(görüntüleme · tekil)</span></div>
            <div class="az-pad">
                <div class="az-trend">
                    @foreach (($trend['days'] ?? []) as $i => $day)
                        @php($pv = $trend['pv'][$i] ?? 0)
                        @php($uv = $trend['uv'][$i] ?? 0)
                        @php($h = round(110 * $pv / $maxPv))
                        <div class="col" title="{{ $day }} · {{ $pv }} görüntüleme · {{ $uv }} tekil">
                            <div class="b" style="height: {{ $h }}px;"></div>
                            <div class="d">{{ $day }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="az-note">Self-hosted, KVKK uyumlu (çerezsiz, IP saklanmaz). Botlar hariç. Veriler 5 dk önbelleklenir.</div>
    </div>
</x-filament-panels::page>
