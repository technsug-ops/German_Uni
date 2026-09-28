@php
    /** @var \App\Models\FaqQualityAtlas $r */
    $r = $this->getRecord();
    $locs = $this->locales();
    $dups = $this->duplicateTargets();
    $sc = fn ($s) => match ($s) {
        'COMPLETE', 'CONTENT' => '#16a34a', 'EMPTY', 'MISSING' => '#dc2626',
        'BROKEN', 'STALE', 'UNPUBLISHED' => '#d97706', 'PARTIAL', 'THIN' => '#2563eb', default => '#6b7280',
    };
    $badge = fn ($s) => '<span style="display:inline-block;padding:1px 8px;border-radius:9999px;font-size:12px;font-weight:600;border:1px solid '.$sc($s).';color:'.$sc($s).';">'.e($s).'</span>';
    $card = 'border:1px solid #e5e7eb; border-radius:.75rem; background:#fff; padding:.9rem 1rem;';
    $lbl = 'font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.03em;';
    $qc = ['A' => '#16a34a', 'B' => '#d97706', 'C' => '#6b7280', 'D' => '#dc2626'][$r->tr_quality] ?? '#6b7280';
@endphp
<x-filament-panels::page>
    <div style="display:flex; flex-direction:column; gap:1rem;">
        <div style="{{ $card }} display:flex; flex-wrap:wrap; gap:1.25rem; align-items:center;">
            <div><div style="{{ $lbl }}">TR kalite</div><div style="font-size:22px; font-weight:800; color:{{ $qc }};">{{ $r->tr_quality }}</div></div>
            <div><div style="{{ $lbl }}">Risk</div><div style="font-weight:700;">{{ $r->risk_level }}</div></div>
            <div><div style="{{ $lbl }}">Konu</div><div>{{ $r->topic }}</div></div>
            <div><div style="{{ $lbl }}">Öncelik</div><div>{{ $r->priority_score ?? '—' }}</div></div>
            <div><div style="{{ $lbl }}">Batch</div><div>{{ $r->proposed_batch === 'OUTSIDE_BATCH' ? 'Batch dışı' : $r->proposed_batch }}</div></div>
            <div><div style="{{ $lbl }}">Strateji</div><div>{{ str_replace('_', ' ', (string) $r->translation_strategy) ?: '—' }}</div></div>
            <div><div style="{{ $lbl }}">Parite</div><div>{{ $r->parity_score }}/3</div></div>
            <div><div style="{{ $lbl }}">Review</div><div style="font-weight:700; color:{{ $r->review_needed ? '#d97706' : '#16a34a' }};">{{ $r->review_needed ? 'REVIEW NEEDED' : 'Değişiklik yok' }}</div></div>
            <div><div style="{{ $lbl }}">Denetim</div><div>{{ $r->audit_label }} · {{ $r->audited_at?->format('d.m.Y H:i') }}</div></div>
            @unless($r->resolved)
                <div style="color:#dc2626;"><div style="{{ $lbl }}">Eşleşmedi</div><div>{{ $r->unresolved_reason }}</div></div>
            @endunless
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:.75rem;">
            @foreach($locs as $l => $x)
                <div style="{{ $card }}">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.4rem;">
                        <strong style="font-size:15px;">{{ strtoupper($l) }}</strong>
                        <span>{!! 'atlas '.$badge($x['atlas_status']) !!} {!! 'canlı '.$badge($x['live_status']) !!}</span>
                    </div>
                    @if($x['differs'] || $x['updated_after_audit'])
                        <div style="font-size:12px; color:#d97706; margin-bottom:.4rem;">
                            ⚠️ {{ $x['differs'] ? 'Denetimden bu yana yapısal durum değişti.' : '' }} {{ $x['updated_after_audit'] ? 'Kayıt denetimden sonra güncellendi.' : '' }}
                        </div>
                    @endif
                    <div style="font-weight:600; margin-bottom:.3rem;">{{ $x['question'] ?? ($l === 'tr' ? $r->tr_question_at_audit : '— kayıt yok —') }}</div>
                    <div style="font-size:12px; color:#6b7280; word-break:break-all;">slug: {{ $x['slug'] ?? '—' }}</div>
                    <div style="font-size:12px; color:#6b7280;">
                        dil: {{ $l }} · yayında: {{ $x['faq'] ? ($x['published'] ? 'evet' : 'hayır') : '—' }} · görünür cevap: {{ $x['chars'] }} karakter
                    </div>
                    @if($x['status_reason'])
                        <div style="font-size:12px; color:#6b7280; margin-top:.2rem;">denetim notu: {{ $x['status_reason'] }}</div>
                    @endif
                    <div style="font-size:13px; color:#374151; background:#f9fafb; border-radius:.5rem; padding:.5rem .6rem; margin:.5rem 0; min-height:3rem; white-space:pre-line;">{{ $x['preview'] ?: '— görünür cevap yok —' }}</div>
                    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                        @if($x['path'])
                            <a href="{{ url($x['path']) }}" target="_blank" rel="noopener" style="font-size:12px; color:#2563eb;">Canlı sayfayı aç ↗</a>
                        @endif
                        @if($x['edit_url'])
                            <a href="{{ $x['edit_url'] }}" style="font-size:12px; color:#2563eb;">Admin'de düzenle (FaqResource)</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="{{ $card }}">
            <div style="{{ $lbl }} margin-bottom:.4rem;">TR denetim paneli (değişmez anlık görüntü)</div>
            <div style="font-size:13px; margin-bottom:.5rem;"><strong>Önerilen aksiyon:</strong> {{ $r->recommended_action ?? '—' }}</div>
            <div style="font-size:13px;"><strong>Sorunlar:</strong>
                @if($r->tr_issues)
                    <ul style="margin:.3rem 0 0 1.1rem; list-style:disc;">
                        @foreach($r->tr_issues as $issue)<li style="margin:.15rem 0;">{{ $issue }}</li>@endforeach
                    </ul>
                @else
                    <span style="color:#16a34a;">kayıtlı sorun yok</span>
                @endif
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:.6rem; margin-top:.75rem; font-size:13px;">
                <div><div style="{{ $lbl }}">Otoriter iç rehber</div>
                    @if($r->authoritative_internal_source)<a href="{{ url($r->authoritative_internal_source) }}" target="_blank" rel="noopener" style="color:#2563eb;">{{ $r->authoritative_internal_source }}</a>@else — yok —@endif</div>
                <div><div style="{{ $lbl }}">Dış araştırma</div>{{ $r->external_source_needed ? 'gerekli' : 'gerekmiyor' }}</div>
                <div><div style="{{ $lbl }}">Chatbot riski</div>{{ $r->chatbot_risk ? 'evet' : 'hayır' }}</div>
                <div><div style="{{ $lbl }}">Kalite-hazır diller</div>{{ $r->quality_ready_locales ? implode(' / ', $r->quality_ready_locales) : '—' }}</div>
                <div><div style="{{ $lbl }}">Anlamsız (junk)</div>{{ $r->junk ? 'evet — incelenmeli, karar yok' : 'hayır' }}</div>
            </div>
            <div style="margin-top:.75rem; font-size:13px;"><div style="{{ $lbl }}">Birleştirme hedefleri</div>
                @forelse($dups as $d)
                    <div><a href="{{ $d['url'] }}" style="color:#2563eb;">{{ $d['question'] ?? $d['slug'] }}</a> <span style="color:#6b7280;">({{ $d['quality'] }})</span></div>
                @empty
                    — yok —
                @endforelse
            </div>
            @php($sm = $r->stale_markers ?? [])
            @if(! empty($sm['tr']) || ! empty($sm['en_hidden_answer_stale']) || ! empty($sm['de_hidden_answer_stale']))
                <div style="margin-top:.75rem; font-size:13px;"><div style="{{ $lbl }}">Denetimde kayıtlı eski-bilgi işaretleri</div>
                    @foreach(($sm['tr'] ?? []) as $m)<div style="color:#b45309;">TR: …{{ $m }}…</div>@endforeach
                    @if(! empty($sm['en_hidden_answer_stale']))<div style="color:#b45309;">EN: görünmeyen (render edilmeyen) cevap metninde eski bilgi var — chatbot KB'sine giriyor.</div>@endif
                    @if(! empty($sm['de_hidden_answer_stale']))<div style="color:#b45309;">DE: görünmeyen (render edilmeyen) cevap metninde eski bilgi var — chatbot KB'sine giriyor.</div>@endif
                </div>
            @endif
            @if($r->notes)
                <div style="margin-top:.75rem; font-size:13px;"><div style="{{ $lbl }}">Notlar</div>{{ $r->notes }}</div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
