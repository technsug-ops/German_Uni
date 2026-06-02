<x-filament-panels::page>
    {{-- ── Tarih aralığı seçici ── --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm text-gray-500 dark:text-gray-400 mr-1">Aralık:</span>
        @foreach ([1 => 'Bugün', 7 => '7 gün', 30 => '30 gün', 90 => '90 gün'] as $d => $label)
            <button type="button" wire:click="setDays({{ $d }})"
                @class([
                    'px-3 py-1.5 rounded-lg text-sm font-medium transition',
                    'bg-primary-600 text-white shadow' => $days === $d,
                    'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' => $days !== $d,
                ])>
                {{ $label }}
            </button>
        @endforeach
        <span wire:loading class="text-xs text-gray-400 ml-2">yükleniyor…</span>
    </div>

    {{-- ── Genel bakış kartları ── --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @php
            $cards = [
                ['Çevrimiçi (5 dk)', $overview['online_now'] ?? 0, 'text-green-600 dark:text-green-400', '🟢'],
                ['Bugün', $overview['uv_today'] ?? 0, 'text-gray-900 dark:text-white', '👤', $overview['pv_today'] ?? 0],
                ['Son 7 gün', $overview['uv_week'] ?? 0, 'text-gray-900 dark:text-white', '📅', $overview['pv_week'] ?? 0],
                ['Son 30 gün', $overview['uv_month'] ?? 0, 'text-gray-900 dark:text-white', '🗓️', $overview['pv_month'] ?? 0],
            ];
        @endphp
        @foreach ($cards as $c)
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $c[3] }} {{ $c[0] }}</div>
                <div class="mt-1 text-2xl font-bold tabular-nums {{ $c[2] }}">{{ number_format($c[1]) }}</div>
                @isset($c[4])
                    <div class="text-xs text-gray-400 tabular-nums">{{ number_format($c[4]) }} görüntüleme</div>
                @endisset
                @empty($c[4])
                    <div class="text-xs text-gray-400">tekil ziyaretçi</div>
                @endempty
            </div>
        @endforeach
    </div>

    {{-- ── Cihaz dağılımı ── --}}
    @php
        $devTotal = max(1, $devices['total'] ?? 0);
        $devRows = [
            ['📱 Mobil', $devices['mobile'] ?? 0, 'bg-emerald-500'],
            ['💻 Masaüstü', $devices['desktop'] ?? 0, 'bg-blue-500'],
            ['📲 Tablet', $devices['tablet'] ?? 0, 'bg-amber-500'],
        ];
    @endphp
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <div class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Cihaz dağılımı <span class="font-normal text-gray-400">({{ $this->getRangeLabel() }})</span></div>
        <div class="space-y-2">
            @foreach ($devRows as $dr)
                @php($pct = round(100 * $dr[1] / $devTotal))
                <div class="flex items-center gap-3">
                    <div class="w-28 text-sm text-gray-600 dark:text-gray-300">{{ $dr[0] }}</div>
                    <div class="flex-1 h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="h-full {{ $dr[2] }}" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="w-24 text-right text-sm tabular-nums text-gray-700 dark:text-gray-300">
                        <span class="font-semibold">{{ $pct }}%</span>
                        <span class="text-xs text-gray-400">({{ number_format($dr[1]) }})</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Sayfalar + Referrer (yan yana) ── --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- En çok ziyaret edilen sayfalar (geniş) --}}
        <div class="lg:col-span-2 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold dark:border-gray-700 dark:bg-gray-800">
                📄 En çok ziyaret edilen sayfalar <span class="font-normal text-gray-400">({{ $this->getRangeLabel() }} · ilk 50)</span>
            </div>
            <div class="max-h-[32rem] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800">
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                            <th class="px-4 py-2 font-semibold">#</th>
                            <th class="px-4 py-2 font-semibold">Yol</th>
                            <th class="px-4 py-2 text-right font-semibold">Tekil</th>
                            <th class="px-4 py-2 text-right font-semibold">Görnt.</th>
                            <th class="px-4 py-2 text-right font-semibold">Ort. ms</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($pages as $i => $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2 text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ $p['path'] }}" target="_blank"
                                       class="font-mono text-xs text-primary-600 hover:underline dark:text-primary-400 break-all">{{ $p['path'] }}</a>
                                </td>
                                <td class="px-4 py-2 text-right font-semibold tabular-nums">{{ number_format($p['uv']) }}</td>
                                <td class="px-4 py-2 text-right tabular-nums text-gray-500">{{ number_format($p['pv']) }}</td>
                                <td class="px-4 py-2 text-right tabular-nums text-gray-400">{{ $p['avg_ms'] ? number_format($p['avg_ms']) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center italic text-gray-400">Bu aralıkta veri yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Trafik kaynakları --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold dark:border-gray-700 dark:bg-gray-800">
                🔗 Trafik kaynakları <span class="font-normal text-gray-400">(ilk 30)</span>
            </div>
            <div class="max-h-[32rem] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800">
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                            <th class="px-4 py-2 font-semibold">Kaynak</th>
                            <th class="px-4 py-2 text-right font-semibold">Tekil</th>
                            <th class="px-4 py-2 text-right font-semibold">Görnt.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($referrers as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2 break-all text-gray-700 dark:text-gray-300">{{ $r['host'] }}</td>
                                <td class="px-4 py-2 text-right font-semibold tabular-nums">{{ number_format($r['uv']) }}</td>
                                <td class="px-4 py-2 text-right tabular-nums text-gray-500">{{ number_format($r['pv']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center italic text-gray-400">Doğrudan trafik (referrer yok).</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Günlük trend ── --}}
    @php($maxPv = max(1, max($trend['pv'] ?? [0])))
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <div class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Günlük trend <span class="font-normal text-gray-400">(görüntüleme · tekil)</span></div>
        <div class="flex items-end gap-1 overflow-x-auto pb-1" style="height: 140px;">
            @foreach (($trend['days'] ?? []) as $i => $day)
                @php($pv = $trend['pv'][$i] ?? 0)
                @php($uv = $trend['uv'][$i] ?? 0)
                @php($h = round(110 * $pv / $maxPv))
                <div class="flex flex-1 min-w-[18px] flex-col items-center justify-end gap-1" title="{{ $day }} · {{ $pv }} görüntüleme · {{ $uv }} tekil">
                    <div class="w-full rounded-t bg-primary-500/80 dark:bg-primary-500" style="height: {{ $h }}px; min-height: 2px;"></div>
                    <div class="text-[9px] text-gray-400 whitespace-nowrap" style="writing-mode: vertical-rl;">{{ $day }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="text-xs text-gray-400">
        Self-hosted, KVKK uyumlu (çerezsiz, IP saklanmaz). Botlar hariç. Veriler 5 dk önbelleklenir.
    </div>
</x-filament-panels::page>
