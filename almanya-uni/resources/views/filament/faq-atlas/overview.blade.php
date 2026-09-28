@php
    $k = $this->kpis();
    $topics = $this->topics();
    $batch = $this->batchSummary();
    $card = 'border:1px solid #e5e7eb; border-radius:.6rem; background:#fff; padding:.55rem .75rem; min-width:0;';
    $lbl = 'font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.03em;';
    $num = 'font-size:18px; font-weight:700; font-variant-numeric:tabular-nums;';
    $chip = 'display:inline-block; padding:1px 7px; border-radius:9999px; font-size:12px; margin:1px 2px; background:#f3f4f6; color:#374151;';
    $btn = 'display:inline-block; padding:3px 10px; border-radius:9999px; font-size:12px; border:1px solid #d1d5db; background:#fff; color:#374151; text-decoration:none; margin:2px 3px 2px 0;';
    $sc = ['COMPLETE' => '#16a34a', 'EMPTY' => '#dc2626', 'MISSING' => '#dc2626', 'PARTIAL' => '#2563eb', 'BROKEN' => '#d97706', 'STALE' => '#d97706'];
@endphp
<x-filament-widgets::widget>
    <div class="fa-overview" style="display:flex; flex-direction:column; gap:.75rem;">
        <div style="font-size:12px; color:#6b7280;">
            Denetim anlık görüntüsü: <strong style="color:#111827;">{{ $this->audit() ?? '—' }}</strong>
            · sınıflandırma = değişmez denetim kaydı · canlı durum ayrı hesaplanır · sayılar seçili filtreye göre
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:.5rem;">
            <div style="{{ $card }}"><div style="{{ $lbl }}">Toplam küme</div><div style="{{ $num }}">{{ $k['total'] }}</div></div>
            <div style="{{ $card }}"><div style="{{ $lbl }}">TR kalite</div>
                <div>@foreach(['A' => '#16a34a', 'B' => '#d97706', 'C' => '#6b7280', 'D' => '#dc2626'] as $q => $c)<span style="{{ $chip }} color:{{ $c }}; font-weight:600;">{{ $q }} {{ $k['q_'.$q] }}</span>@endforeach</div></div>
            <div style="{{ $card }}"><div style="{{ $lbl }}">Risk</div>
                <div>@foreach(['P0' => '#dc2626', 'P1' => '#d97706', 'P2' => '#6b7280'] as $r => $c)<span style="{{ $chip }} color:{{ $c }}; font-weight:600;">{{ $r }} {{ $k['r_'.$r] }}</span>@endforeach</div></div>
            <div style="{{ $card }}"><div style="{{ $lbl }}">Parite</div>
                <div>@foreach([3, 2, 1, 0] as $p)<span style="{{ $chip }}">{{ $p }}/3 {{ $k['p_'.$p] }}</span>@endforeach</div></div>
            <div style="{{ $card }}"><div style="{{ $lbl }}">Review needed</div><div style="{{ $num }} color:#d97706;">{{ $k['review'] }}</div></div>
            <div style="{{ $card }}"><div style="{{ $lbl }}">Eşleşmeyen</div><div style="{{ $num }} color:{{ $k['unresolved'] ? '#dc2626' : '#111827' }};">{{ $k['unresolved'] }}</div></div>
            <div style="{{ $card }}"><div style="{{ $lbl }}">Chatbot riski</div><div style="{{ $num }}">{{ $k['chatbot'] }}</div></div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem;">
            @foreach(['en' => 'EN', 'de' => 'DE'] as $l => $L)
                <div style="{{ $card }}"><div style="{{ $lbl }}">{{ $L }} atlas durumu</div>
                    <div>@foreach(\App\Models\FaqQualityAtlas::STATUSES as $s)<span style="{{ $chip }} color:{{ $sc[$s] }};">{{ $s }} {{ $k[$l.'_'.$s] }}</span>@endforeach</div></div>
            @endforeach
        </div>

        <div>
            <span style="{{ $lbl }} margin-right:.4rem;">Hazır filtreler</span>
            @foreach(\App\Services\FaqAtlas\AtlasFilters::PRESETS as $key => $label)
                <a href="{{ $this->filterUrl(['preset' => ['value' => $key]]) }}" style="{{ $btn }}">{{ $label }}</a>
            @endforeach
            <a href="{{ $this->filterUrl([]) }}" style="{{ $btn }} color:#6b7280;">Filtreleri temizle</a>
        </div>

        @if($batch)
            <div style="{{ $card }} border-color:#93c5fd; background:#eff6ff;">
                <div style="{{ $lbl }}">Batch özeti — {{ implode(', ', $batch['batches']) }}</div>
                <div style="font-size:13px; margin-top:.2rem;">
                    Toplam <strong>{{ $batch['total'] }}</strong> · P0 <strong>{{ $batch['p0'] }}</strong>
                    · A/B/C/D {{ $batch['a'] }}/{{ $batch['b'] }}/{{ $batch['c'] }}/{{ $batch['d'] }}
                    · hazır <strong>{{ $batch['ready'] }}</strong> · araştırma gerekli <strong>{{ $batch['research'] }}</strong>
                    · EN boşluğu <strong>{{ $batch['en_gap'] }}</strong> · DE boşluğu <strong>{{ $batch['de_gap'] }}</strong>
                </div>
            </div>
        @endif

        <details {{ count($topics) <= 30 ? '' : '' }} style="{{ $card }}">
            <summary style="cursor:pointer; font-weight:600; font-size:13px;">Konu özeti ({{ count($topics) }} konu) — konuya tıklayınca tablo filtrelenir</summary>
            <table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:.5rem;">
                <thead><tr style="text-align:left; color:#6b7280;">
                    <th style="padding:.3rem .4rem;">Konu</th><th>Toplam</th><th>P0</th><th>A</th><th>B</th><th>C</th><th>D</th>
                    <th>EN boşluk</th><th>DE boşluk</th><th>Ort. parite</th><th>3 dilde hazır</th>
                </tr></thead>
                <tbody>
                @foreach($topics as $t)
                    <tr style="border-top:1px solid #f3f4f6; font-variant-numeric:tabular-nums;">
                        <td style="padding:.3rem .4rem;"><a href="{{ $this->filterUrl(['topic' => ['value' => $t['topic']]]) }}" style="color:#2563eb;">{{ $t['topic'] }}</a></td>
                        <td>{{ $t['total'] }}</td><td style="color:{{ $t['p0'] ? '#dc2626' : 'inherit' }};">{{ $t['p0'] }}</td>
                        <td>{{ $t['a'] }}</td><td>{{ $t['b'] }}</td><td>{{ $t['c'] }}</td><td>{{ $t['d'] }}</td>
                        <td>{{ $t['en_gap'] }}</td><td>{{ $t['de_gap'] }}</td><td>{{ $t['avg_parity'] }}</td><td>{{ $t['ready'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </details>
    </div>
</x-filament-widgets::widget>
