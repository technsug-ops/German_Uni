<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            En Çok Ziyaret Edilen (son 7 gün)
        </x-slot>

        @if (empty($pages) && empty($referrers))
            <div style="text-align:center;padding:24px;color:#6b7280;font-size:14px;">
                Henüz veri yok. Trafik biriktirken görünecek.
            </div>
        @else
            <div style="display:grid;grid-template-columns:1fr;gap:24px;">
                <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="fi-grid-cols-2-md">

                    {{-- TOP PAGES --}}
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;margin-bottom:8px;padding-bottom:4px;">
                            📄 Sayfalar
                        </div>
                        <table style="width:100%;border-collapse:collapse;font-size:13px;table-layout:fixed;">
                            <thead>
                                <tr style="border-bottom:1px solid #e5e7eb;">
                                    <th style="text-align:left;padding:6px 8px 6px 0;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;">Yol</th>
                                    <th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;width:55px;">Tekil</th>
                                    <th style="text-align:right;padding:6px 0 6px 8px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;width:80px;">Görnt.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($pages))
                                    <tr><td colspan="3" style="padding:12px 0;text-align:center;color:#9ca3af;font-style:italic;font-size:12px;">Veri yok</td></tr>
                                @else
                                    @foreach ($pages as $p)
                                        <tr style="border-bottom:1px solid #f3f4f6;">
                                            <td style="padding:8px 8px 8px 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                <a href="{{ $p['path'] }}" target="_blank" title="{{ $p['path'] }}"
                                                   style="color:#2563eb;text-decoration:none;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;">
                                                    {{ \Illuminate\Support\Str::limit($p['path'], 32) }}
                                                </a>
                                            </td>
                                            <td style="padding:8px;text-align:right;font-weight:600;color:#111827;font-variant-numeric:tabular-nums;">
                                                {{ number_format($p['uv']) }}
                                            </td>
                                            <td style="padding:8px 0 8px 8px;text-align:right;color:#6b7280;font-variant-numeric:tabular-nums;font-size:12px;">
                                                {{ number_format($p['pv']) }}@if ($p['avg_ms'] > 0)<span style="color:#9ca3af;margin-left:4px;font-size:10px;">{{ $p['avg_ms'] }}ms</span>@endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- TOP REFERRERS --}}
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;margin-bottom:8px;padding-bottom:4px;">
                            🔗 Trafik Kaynakları
                        </div>
                        <table style="width:100%;border-collapse:collapse;font-size:13px;table-layout:fixed;">
                            <thead>
                                <tr style="border-bottom:1px solid #e5e7eb;">
                                    <th style="text-align:left;padding:6px 8px 6px 0;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;">Kaynak</th>
                                    <th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;width:55px;">Tekil</th>
                                    <th style="text-align:right;padding:6px 0 6px 8px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;width:65px;">Görnt.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($referrers))
                                    <tr><td colspan="3" style="padding:12px 0;text-align:center;color:#9ca3af;font-style:italic;font-size:12px;">Doğrudan trafik</td></tr>
                                @else
                                    @foreach ($referrers as $r)
                                        <tr style="border-bottom:1px solid #f3f4f6;">
                                            <td style="padding:8px 8px 8px 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;color:#111827;font-size:12px;" title="{{ $r['host'] }}">
                                                {{ $r['host'] }}
                                            </td>
                                            <td style="padding:8px;text-align:right;font-weight:600;color:#111827;font-variant-numeric:tabular-nums;">
                                                {{ number_format($r['uv']) }}
                                            </td>
                                            <td style="padding:8px 0 8px 8px;text-align:right;color:#6b7280;font-variant-numeric:tabular-nums;font-size:12px;">
                                                {{ number_format($r['pv']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <style>
                @media (min-width: 1024px) {
                    .fi-grid-cols-2-md { grid-template-columns: 1fr 1fr !important; }
                }
            </style>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
