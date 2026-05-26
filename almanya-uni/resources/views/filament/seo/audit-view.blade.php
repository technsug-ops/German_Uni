<x-filament-panels::page>
    @php
        $r = $this->record;

        $contentLen = (int) $r->content_length;
        $h1 = (int) $r->h1_count;
        $h2 = (int) $r->h2_count;
        $img = (int) $r->image_count;
        $link = (int) $r->internal_link_count;

        $checks = [
            [
                'icon' => '📝', 'label' => 'İçerik Uzunluğu',
                'value' => number_format($contentLen) . ' karakter',
                'ideal' => '2.500–6.000 karakter',
                'status' => $contentLen >= 4000 ? 'good' : ($contentLen >= 2000 ? 'mid' : 'bad'),
                'progress' => min(100, $contentLen / 50),
                'rec' => $contentLen < 2000 ? 'İçerik çok kısa. Google için min 2.500 karakter, SEO sweet-spot 4.000+.' : ($contentLen < 4000 ? '2-3 yeni section daha ekle, topic authority artar.' : 'İçerik uzunluğu iyi.'),
            ],
            [
                'icon' => '🎯', 'label' => 'H1 Etiket',
                'value' => $h1 . ' adet',
                'ideal' => 'Tam olarak 1 adet',
                'status' => $h1 === 1 ? 'good' : 'bad',
                'progress' => $h1 === 1 ? 100 : ($h1 === 0 ? 0 : 50),
                'rec' => $h1 === 0 ? 'H1 yok! Her sayfada sayfa başlığını H1 olarak ekle.' : ($h1 > 1 ? 'Birden fazla H1 var. Sadece 1 H1 bırak.' : 'H1 doğru.'),
            ],
            [
                'icon' => '📑', 'label' => 'H2 Bölümler',
                'value' => $h2 . ' adet',
                'ideal' => '6–15 adet',
                'status' => $h2 >= 6 ? 'good' : ($h2 >= 3 ? 'mid' : 'bad'),
                'progress' => min(100, ($h2 / 10) * 100),
                'rec' => $h2 < 3 ? 'Çok az H2 var. Sayfayı 6+ ana bölüme böl.' : ($h2 < 6 ? 'Daha fazla H2 bölüm (target: 8-12).' : 'H2 yapısı iyi.'),
            ],
            [
                'icon' => '🖼️', 'label' => 'Görseller',
                'value' => $img . ' adet',
                'ideal' => '3+ görsel',
                'status' => $img >= 3 ? 'good' : ($img >= 1 ? 'mid' : 'bad'),
                'progress' => min(100, ($img / 5) * 100),
                'rec' => $img === 0 ? 'Hiç görsel yok. OG image + content image + diyagram ekle.' : ($img < 3 ? 'Görsel sayısı az (target: 3+).' : 'Görsel sayısı iyi.'),
            ],
            [
                'icon' => '🔗', 'label' => 'İç Linkler',
                'value' => $link . ' adet',
                'ideal' => '5–20 adet',
                'status' => $link >= 5 ? 'good' : ($link >= 2 ? 'mid' : 'bad'),
                'progress' => min(100, ($link / 12) * 100),
                'rec' => $link < 2 ? 'Çok az iç link var. İlgili sayfalara en az 5 link ekle.' : ($link < 5 ? 'Daha çok iç link.' : 'İç link sayısı iyi.'),
            ],
        ];

        $goodCount = count(array_filter($checks, fn ($c) => $c['status'] === 'good'));
        $totalCount = count($checks);
        $healthScore = (int) round(($goodCount / $totalCount) * 100);

        $oppColor = $r->opportunity_score >= 90 ? '#dc2626' : ($r->opportunity_score >= 70 ? '#f59e0b' : ($r->opportunity_score >= 50 ? '#6366f1' : '#10b981'));
        $healthColor = $healthScore >= 80 ? '#10b981' : ($healthScore >= 50 ? '#f59e0b' : '#dc2626');
        $oppEmoji = $r->opportunity_score >= 70 ? '🚨' : ($r->opportunity_score >= 50 ? '⚠️' : '✅');
        $oppLabel = $r->opportunity_score >= 70 ? 'Büyük fırsat — yüksek öncelik' : ($r->opportunity_score >= 50 ? 'Orta fırsat' : 'Sayfa iyi durumda');

        $topGaps = array_slice((array) $r->high_value_gaps, 0, 10, true);
        $maxGapScore = ! empty($topGaps) ? max($topGaps) : 1;

        $entityMap = [
            'city_detail' => 'şehir', 'university_detail' => 'üniversite',
            'program_detail' => 'program', 'field_detail' => 'alan', 'blog_detail' => 'blog yazısı',
        ];
        $entityLabel = $entityMap[$r->template] ?? null;
    @endphp

    {{-- Inline CSS — Filament Tailwind çakışmasını bypass --}}
    <style>
        .seo-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
        .seo-row { display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; }
        .seo-row-between { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; justify-content: space-between; }
        .seo-grid-2 { display: grid; grid-template-columns: 1fr; gap: 16px; }
        @media (min-width: 768px) { .seo-grid-2 { grid-template-columns: 1fr 1fr; } }
        .seo-stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        @media (min-width: 768px) { .seo-stat-grid { grid-template-columns: repeat(4, 1fr); } }
        .seo-stat { background: #f9fafb; border-radius: 8px; padding: 12px; }
        .seo-stat-label { font-size: 11px; color: #6b7280; }
        .seo-stat-value { font-size: 18px; font-weight: 600; margin-top: 4px; color: #111827; }
        .seo-h2 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .seo-h3 { font-size: 16px; font-weight: 700; color: #111827; }
        .seo-sub { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
        .seo-check { border: 1px solid; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 12px; align-items: flex-start; }
        .seo-check.good { background: #ecfdf5; border-color: #a7f3d0; }
        .seo-check.mid { background: #fffbeb; border-color: #fde68a; }
        .seo-check.bad { background: #fef2f2; border-color: #fecaca; }
        .seo-check-header { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 6px; }
        .seo-progress-bg { width: 100%; height: 6px; background: #fff; border-radius: 9999px; overflow: hidden; margin: 6px 0; }
        .seo-progress-fill { height: 100%; transition: width .3s; }
        .seo-progress-fill.good { background: #10b981; }
        .seo-progress-fill.mid { background: #f59e0b; }
        .seo-progress-fill.bad { background: #dc2626; }
        .seo-bar-row { margin-bottom: 8px; }
        .seo-bar-bg { width: 100%; height: 12px; background: #f3f4f6; border-radius: 9999px; overflow: hidden; }
        .seo-bar-fill { height: 100%; background: linear-gradient(to right, #f87171, #dc2626); }
        .seo-kw-grid { display: flex; flex-wrap: wrap; gap: 4px; max-height: 280px; overflow-y: auto; }
        .seo-kw { display: inline-flex; align-items: center; padding: 2px 8px; font-size: 11px; border-radius: 4px; border: 1px solid; }
        .seo-kw.found { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .seo-kw.missing { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .seo-req-grid { display: grid; grid-template-columns: 1fr; gap: 10px; }
        @media (min-width: 768px) { .seo-req-grid { grid-template-columns: 1fr 1fr; } }
        .seo-req { background: #fff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 12px; display: flex; gap: 8px; align-items: flex-start; }
        .seo-summary-box { border: 1px solid; border-radius: 8px; padding: 16px; font-size: 14px; }
        .seo-summary-box.bad { background: #fef2f2; border-color: #fecaca; color: #7f1d1d; }
        .seo-summary-box.mid { background: #fffbeb; border-color: #fde68a; color: #78350f; }
        .seo-summary-box.good { background: #ecfdf5; border-color: #a7f3d0; color: #064e3b; }
        .seo-toggle { width: 100%; padding: 16px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; background: transparent; border: 0; gap: 12px; }
        .seo-toggle:hover { background: rgba(255,255,255,.5); }
        .seo-chevron { width: 20px; height: 20px; color: #6b7280; transition: transform .2s; }
        .seo-chevron.open { transform: rotate(180deg); }
        .seo-label-badge { display: inline-block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 4px 8px; border-radius: 4px; background: #ede9fe; color: #5b21b6; }
        .seo-title { font-size: 22px; font-weight: 800; color: #111827; margin-top: 6px; }
        .seo-link { font-size: 12px; color: #4f46e5; text-decoration: none; }
        .seo-link:hover { text-decoration: underline; }
        .seo-mini { font-size: 11px; color: #6b7280; }
        .seo-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; transition: all .2s; }
        .seo-iframe-wrap { width: 100%; height: 600px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; background: #f9fafb; }
        .seo-collapse { display: none; }
        .seo-collapse.open { display: block; }
        .seo-tag-amber { display: inline-flex; gap: 4px; align-items: center; padding: 4px 8px; font-size: 11px; border-radius: 4px; background: #fffbeb; color: #92400e; }
        .seo-tag-success { display: inline-flex; gap: 4px; align-items: center; padding: 4px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border-radius: 4px; background: #d1fae5; color: #065f46; }
    </style>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        {{-- =========== KULLANIM KILAVUZU =========== --}}
        @include('filament.seo._guide', ['compact' => true, 'defaultOpen' => false])

        {{-- =========== HEADER + 2 GAUGE =========== --}}
        <div class="seo-card">
            <div class="seo-row-between">
                <div style="flex: 1; min-width: 280px;">
                    <span class="seo-label-badge">{{ \App\Models\SeoAudit::TEMPLATES[$r->template] ?? $r->template }}</span>
                    <h2 class="seo-title">{{ $r->page_title ?: '(başlık yok)' }}</h2>
                    <a href="{{ $r->sample_url }}" target="_blank" rel="noopener" class="seo-link" style="display: inline-block; margin-top: 6px;">
                        🔗 {{ $r->sample_url }} ↗
                    </a>
                    <p class="seo-mini" style="margin-top: 8px;">Son audit: {{ $r->last_audited_at?->diffForHumans() ?? 'hiç' }}</p>
                </div>

                <div style="display: flex; gap: 24px; flex-shrink: 0;">
                    {{-- Opportunity Score --}}
                    <div style="text-align: center; width: 128px;">
                        <div style="position: relative; width: 128px; height: 128px;">
                            <svg width="128" height="128" viewBox="0 0 100 100" style="display:block; transform: rotate(-90deg);">
                                <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="42" stroke="{{ $oppColor }}" stroke-width="8" fill="none" stroke-linecap="round" stroke-dasharray="263.89" stroke-dashoffset="{{ 263.89 * (1 - $r->opportunity_score/100) }}"/>
                            </svg>
                            <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <span style="font-size: 30px; font-weight: 800; color: {{ $oppColor }};">{{ $r->opportunity_score }}</span>
                                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280;">/100</span>
                            </div>
                        </div>
                        <p style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-top: 8px;">🎯 SEO Fırsat</p>
                        <p style="font-size: 10px; color: #9ca3af;">Yüksek = eksik çok</p>
                    </div>

                    {{-- Health Score --}}
                    <div style="text-align: center; width: 128px;">
                        <div style="position: relative; width: 128px; height: 128px;">
                            <svg width="128" height="128" viewBox="0 0 100 100" style="display:block; transform: rotate(-90deg);">
                                <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="42" stroke="{{ $healthColor }}" stroke-width="8" fill="none" stroke-linecap="round" stroke-dasharray="263.89" stroke-dashoffset="{{ 263.89 * (1 - $healthScore/100) }}"/>
                            </svg>
                            <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <span style="font-size: 30px; font-weight: 800; color: {{ $healthColor }};">{{ $healthScore }}</span>
                                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280;">/100</span>
                            </div>
                        </div>
                        <p style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-top: 8px;">💚 Sağlık</p>
                        <p style="font-size: 10px; color: #9ca3af;">{{ $goodCount }}/{{ $totalCount }} kontrol geçti</p>
                    </div>
                </div>
            </div>

            {{-- Stats grid --}}
            <div class="seo-stat-grid" style="margin-top: 20px;">
                <div class="seo-stat"><div class="seo-stat-label">İçerik uzunluğu</div><div class="seo-stat-value">{{ number_format($r->content_length) }} char</div></div>
                <div class="seo-stat"><div class="seo-stat-label">H1 / H2</div><div class="seo-stat-value">{{ $r->h1_count }} / {{ $r->h2_count }}</div></div>
                <div class="seo-stat"><div class="seo-stat-label">Görsel</div><div class="seo-stat-value">{{ $r->image_count }}</div></div>
                <div class="seo-stat"><div class="seo-stat-label">İç link</div><div class="seo-stat-value">{{ $r->internal_link_count }}</div></div>
            </div>

            <div class="seo-summary-box {{ $r->opportunity_score >= 70 ? 'bad' : ($r->opportunity_score >= 50 ? 'mid' : 'good') }}" style="margin-top: 20px;">
                <strong style="font-size: 16px;">{{ $oppEmoji }} {{ $oppLabel }}</strong>
                <p style="margin-top: 6px; opacity: .9;">
                    @if ($r->opportunity_score >= 70)
                        Topluluk verisinde {{ count((array) $r->keywords_missing) }} konu var ama bu sayfada yok. <strong>"AI Öneri Üret"</strong> → <strong>"İçerik Üret &amp; Aktive Et"</strong> ile hızlı kazanç.
                    @elseif ($r->opportunity_score >= 50)
                        Sayfa ortalama. {{ count((array) $r->keywords_missing) }} eksik keyword'in en yüksek skorlulardan 5-10'unu ekle.
                    @else
                        Sayfa iyi durumda. İçeriği güncel tut.
                    @endif
                </p>
            </div>
        </div>

        {{-- =========== SAYFA ÖN İZLEME =========== --}}
        <div class="seo-card" style="padding: 0; overflow: hidden;">
            <button type="button" class="seo-toggle" onclick="document.getElementById('seoPreview').classList.toggle('open'); this.querySelector('.seo-chevron').classList.toggle('open');">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="font-size: 24px;">🖥️</div>
                    <div style="text-align: left;">
                        <div class="seo-h3">Sayfa Önizleme</div>
                        <div class="seo-mini" style="margin-top: 2px; max-width: 500px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $r->sample_url }}</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <a href="{{ $r->sample_url }}" target="_blank" rel="noopener" class="seo-link" onclick="event.stopPropagation()">Yeni sekmede ↗</a>
                    <svg class="seo-chevron" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                </div>
            </button>
            <div id="seoPreview" class="seo-collapse" style="padding: 0 20px 20px;">
                <div style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                    <span class="seo-tag-amber">🔒 Sandboxed iframe</span>
                    <span class="seo-tag-amber">⚠️ Local URL ise php artisan serve çalışmalı</span>
                </div>
                <div class="seo-iframe-wrap">
                    <iframe src="{{ $r->sample_url }}" style="width: 100%; height: 100%; border: 0;" sandbox="allow-same-origin allow-scripts" loading="lazy" title="Sayfa önizleme"></iframe>
                </div>
                <p class="seo-mini" style="margin-top: 8px;">💡 İçerik üretip aktive ettikten sonra F5 ile yenile.</p>
            </div>
        </div>

        {{-- =========== HEALTH CHECKLIST =========== --}}
        <div class="seo-card">
            <h3 class="seo-h2">🔍 Teknik SEO Sağlık Kontrolü</h3>
            <p class="seo-sub">Her metrik için: mevcut durum, ideal, ne yapılmalı</p>

            @foreach ($checks as $check)
                @php $statusEmoji = match($check['status']) { 'good' => '✅', 'mid' => '⚠️', 'bad' => '🔴' }; @endphp
                <div class="seo-check {{ $check['status'] }}">
                    <div style="font-size: 28px; flex-shrink: 0;">{{ $check['icon'] }}</div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="seo-check-header">
                            <div>
                                <span style="font-weight: 700; color: #111827;">{{ $statusEmoji }} {{ $check['label'] }}</span>
                                <span style="margin-left: 8px; color: #374151;">{{ $check['value'] }}</span>
                            </div>
                            <span class="seo-mini">İdeal: <strong>{{ $check['ideal'] }}</strong></span>
                        </div>
                        <div class="seo-progress-bg">
                            <div class="seo-progress-fill {{ $check['status'] }}" style="width: {{ $check['progress'] }}%;"></div>
                        </div>
                        <p style="font-size: 12px; color: #374151;">💡 {{ $check['rec'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- =========== TOP 10 EKSİK BAR CHART =========== --}}
        @if (! empty($topGaps))
            <div class="seo-card">
                <div style="display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <h3 class="seo-h2">🔴 En Yüksek Değerli 10 Eksik Keyword</h3>
                    <span class="seo-mini">Toplam {{ count((array) $r->keywords_missing) }} eksik</span>
                </div>
                <p class="seo-sub">Forum + Telegram'da çok konuşulan ama sayfada olmayan. Skor = topluluk hacmi × kaynak ağırlığı.</p>

                @foreach ($topGaps as $kw => $score)
                    @php $pct = $maxGapScore > 0 ? ($score / $maxGapScore) * 100 : 0; @endphp
                    <div class="seo-bar-row">
                        <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">{{ $kw }}</span>
                            <span style="font-family: monospace; font-size: 12px; color: #b91c1c;">{{ number_format($score) }}</span>
                        </div>
                        <div class="seo-bar-bg">
                            <div class="seo-bar-fill" style="width: {{ $pct }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- =========== OLMASI GEREKEN REHBER =========== --}}
        <div class="seo-card" style="background: linear-gradient(to bottom right, #eef2ff, #faf5ff); border-color: #c7d2fe;">
            <h3 class="seo-h2">📋 Bu Template İçin Olması Gerekenler</h3>
            <p class="seo-sub">Standart "{{ \App\Models\SeoAudit::TEMPLATES[$r->template] ?? $r->template }}" sayfası için referans.</p>

            <div class="seo-req-grid">
                @foreach ($this->getTemplateRequirements() as $req)
                    <div class="seo-req">
                        <span style="font-size: 16px; flex-shrink: 0;">{{ $req['icon'] }}</span>
                        <div>
                            <p style="font-weight: 600; color: #111827; font-size: 14px;">{{ $req['title'] }}</p>
                            <p style="font-size: 12px; color: #4b5563; margin-top: 2px;">{{ $req['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =========== AI ÖNERİSİ =========== --}}
        @if ($r->ai_suggestions)
            <div class="seo-card" style="background: #ecfdf5; border-color: #6ee7b7;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
                    <div>
                        <h3 class="seo-h2" style="color: #065f46;">🪄 AI Bölüm Önerisi</h3>
                        @if ($r->ai_meta)
                            <p style="font-size: 11px; color: #047857; margin-top: 4px;">
                                {{ $r->ai_meta['model'] ?? '?' }} · {{ ($r->ai_meta['input_tokens'] ?? 0) + ($r->ai_meta['output_tokens'] ?? 0) }} token
                            </p>
                        @endif
                    </div>
                    @if ($entityLabel)
                        <span class="seo-tag-success">⚡ {{ $entityLabel }} entity'sine aktive edilebilir</span>
                    @endif
                </div>
                <div style="background: #fff; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.6; color: #374151;">
                    {!! \Illuminate\Support\Str::markdown($r->ai_suggestions) !!}
                </div>
            </div>
        @else
            <div class="seo-card" style="border: 2px dashed #d1d5db; text-align: center; padding: 32px;">
                <div style="font-size: 48px; margin-bottom: 8px;">🪄</div>
                <h3 class="seo-h2">Henüz AI önerisi üretilmedi</h3>
                <p style="font-size: 14px; color: #4b5563; margin-top: 4px;">Yukarıdaki <strong>"AI Öneri Üret"</strong> butonu ile Gemini'den eksik konular için section önerisi al.</p>
            </div>
        @endif

        {{-- =========== FOUND vs MISSING (compact) =========== --}}
        <div class="seo-grid-2">
            <div class="seo-card">
                <h3 style="font-weight: 600; color: #047857; font-size: 14px; margin-bottom: 12px;">✅ Sayfada Bulunan ({{ count((array) $r->keywords_found) }})</h3>
                <div class="seo-kw-grid">
                    @foreach (array_slice((array) $r->keywords_found, 0, 60) as $kw)
                        <span class="seo-kw found">{{ $kw }}</span>
                    @endforeach
                    @if (count((array) $r->keywords_found) > 60)
                        <span class="seo-mini" style="padding: 2px 8px;">+{{ count((array) $r->keywords_found) - 60 }} daha…</span>
                    @endif
                </div>
            </div>

            <div class="seo-card">
                <h3 style="font-weight: 600; color: #b91c1c; font-size: 14px; margin-bottom: 12px;">🔴 Eksik Keyword'ler ({{ count((array) $r->keywords_missing) }})</h3>
                <div class="seo-kw-grid">
                    @foreach (array_slice((array) $r->keywords_missing, 0, 60) as $kw)
                        <span class="seo-kw missing">{{ $kw }}</span>
                    @endforeach
                    @if (count((array) $r->keywords_missing) > 60)
                        <span class="seo-mini" style="padding: 2px 8px;">+{{ count((array) $r->keywords_missing) - 60 }} daha…</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
