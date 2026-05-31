<x-filament-panels::page>
    <div class="text-sm text-gray-500 dark:text-gray-400">
        TR kaynak → EN/DE senkron durumu. <strong>Yeşil</strong> = tam, <strong>sarı</strong> = kısmi, <strong>kırmızı</strong> = eksik.
        Aynı veriyi <code>php artisan i18n:health</code> de verir.
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr class="text-left">
                    <th class="px-4 py-3 font-semibold">İçerik</th>
                    <th class="px-4 py-3 font-semibold text-right">Toplam</th>
                    <th class="px-4 py-3 font-semibold">🇬🇧 EN</th>
                    <th class="px-4 py-3 font-semibold">🇩🇪 DE</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($rows as $r)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $r['type'] }}</td>
                        <td class="px-4 py-3 text-right tabular-nums text-gray-500">{{ number_format($r['total']) }}</td>
                        @foreach(['en','de'] as $loc)
                            @php($c = $r['locales'][$loc])
                            @php($pct = $c['pct'])
                            @php($bar = $pct >= 100 ? 'bg-green-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-red-500'))
                            @php($txt = $pct >= 100 ? 'text-green-600 dark:text-green-400' : ($pct >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400'))
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="tabular-nums w-10 text-right font-semibold {{ $txt }}">{{ $pct }}%</span>
                                </div>
                                @if($c['missing'] > 0)
                                    <div class="text-xs {{ $txt }} mt-1">
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

    <div class="text-xs text-gray-400">
        Not: UI satırında "eksik" = hâlâ Türkçe görünen değer (TR sızıntısı). Diğer satırlarda = o dilde henüz çevrilmemiş kayıt.
    </div>
</x-filament-panels::page>
