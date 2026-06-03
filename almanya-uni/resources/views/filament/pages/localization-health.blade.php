<x-filament-panels::page>
    <div style="font-size:13px; color:#6b7280; margin-bottom:.75rem;">
        TR kaynak → EN/DE senkron durumu. <strong>Yeşil</strong> = tam, <strong>sarı</strong> = kısmi, <strong>kırmızı</strong> = eksik.
        Aynı veriyi <code style="background:#f3f4f6; padding:1px 5px; border-radius:4px;">php artisan i18n:health</code> de verir.
    </div>

    <div class="lh-card" style="overflow:hidden; border-radius:.75rem; border:1px solid #e5e7eb; background:#fff;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr class="lh-head" style="background:#f9fafb; text-align:left;">
                    <th style="padding:.7rem 1rem; font-weight:600;">İçerik</th>
                    <th style="padding:.7rem 1rem; font-weight:600; text-align:right;">Toplam</th>
                    <th style="padding:.7rem 1rem; font-weight:600;">🇬🇧 EN</th>
                    <th style="padding:.7rem 1rem; font-weight:600;">🇩🇪 DE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    <tr class="lh-row" style="border-top:1px solid #f3f4f6;">
                        <td style="padding:.7rem 1rem; font-weight:500;">{{ $r['type'] }}</td>
                        <td style="padding:.7rem 1rem; text-align:right; font-variant-numeric:tabular-nums; color:#6b7280;">{{ number_format($r['total']) }}</td>
                        @foreach(['en','de'] as $loc)
                            @php($c = $r['locales'][$loc])
                            @php($pct = $c['pct'])
                            @php($barColor = $pct >= 100 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444'))
                            @php($txtColor = $pct >= 100 ? '#16a34a' : ($pct >= 50 ? '#d97706' : '#dc2626'))
                            <td style="padding:.7rem 1rem;">
                                <div style="display:flex; align-items:center; gap:.5rem;">
                                    <div style="flex:1; height:8px; border-radius:9999px; background:#f3f4f6; overflow:hidden;">
                                        <div style="height:100%; width:{{ $pct }}%; background:{{ $barColor }};"></div>
                                    </div>
                                    <span style="font-variant-numeric:tabular-nums; width:2.75rem; text-align:right; font-weight:600; color:{{ $txtColor }};">{{ $pct }}%</span>
                                </div>
                                @if($c['missing'] > 0)
                                    <div style="font-size:11px; color:{{ $txtColor }}; margin-top:.25rem;">
                                        {{ $c['missing'] }} {{ $r['missing_label'] }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="font-size:11px; color:#9ca3af; margin-top:.75rem;">
        Not: UI satırında "eksik" = hâlâ Türkçe görünen değer (TR sızıntısı). Diğer satırlarda = o dilde henüz çevrilmemiş kayıt.
    </div>

    <style>
        .dark .lh-card { background:#111827 !important; border-color:#374151 !important; }
        .dark .lh-head { background:#1f2937 !important; }
        .dark .lh-row { border-color:#1f2937 !important; }
    </style>
</x-filament-panels::page>
