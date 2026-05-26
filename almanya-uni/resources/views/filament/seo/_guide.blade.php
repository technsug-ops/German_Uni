@php
    $compact = $compact ?? false;
    $defaultOpen = $defaultOpen ?? false;
@endphp

<style>
    .seo-guide { background: linear-gradient(to bottom right, #eef2ff, #faf5ff); border: 1px solid #c7d2fe; border-radius: 12px; overflow: hidden; }
    .seo-guide-summary { padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px; cursor: pointer; list-style: none; }
    .seo-guide-summary::-webkit-details-marker { display: none; }
    .seo-guide-summary:hover { background: rgba(255,255,255,.4); }
    .seo-guide-summary-left { display: flex; align-items: center; gap: 12px; }
    .seo-guide-icon { font-size: 28px; }
    .seo-guide-title { font-weight: 700; color: #111827; font-size: 16px; }
    .seo-guide-subtitle { font-size: 12px; color: #4b5563; }
    .seo-guide-chevron { width: 20px; height: 20px; color: #6b7280; transition: transform .2s; }
    details[open] .seo-guide-chevron { transform: rotate(180deg); }
    .seo-guide-body { padding: 0 16px 20px; border-top: 1px solid rgba(199,210,254,.6); display: flex; flex-direction: column; gap: 16px; padding-top: 16px; }
    .seo-guide-block { background: #fff; border-radius: 8px; padding: 16px; }
    .seo-guide-block-title { font-weight: 700; color: #111827; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .seo-guide-list { display: flex; flex-direction: column; gap: 8px; font-size: 14px; color: #374151; }
    .seo-guide-list li { margin-left: 18px; list-style: decimal; }
    .seo-guide-grid-2 { display: grid; grid-template-columns: 1fr; gap: 12px; }
    @media (min-width: 768px) { .seo-guide-grid-2 { grid-template-columns: 1fr 1fr; } }
    .seo-guide-score { background: #fff; border-radius: 8px; padding: 16px; border-left: 4px solid; }
    .seo-guide-score.amber { border-left-color: #f59e0b; }
    .seo-guide-score.green { border-left-color: #10b981; }
    .seo-guide-score-meta { font-size: 12px; color: #4b5563; margin-top: 8px; }
    .seo-guide-score-meta p { margin-bottom: 4px; }
    .seo-guide-action { display: flex; gap: 12px; padding: 12px; border-radius: 6px; align-items: flex-start; margin-bottom: 8px; }
    .seo-guide-action.neutral { background: #f9fafb; }
    .seo-guide-action.warn { background: #fffbeb; border: 1px solid #fde68a; }
    .seo-guide-action.success { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .seo-guide-action-icon { font-size: 22px; flex-shrink: 0; }
    .seo-guide-action-title { font-weight: 600; color: #111827; font-size: 14px; }
    .seo-guide-action-desc { font-size: 12px; color: #4b5563; margin-top: 2px; }
    .seo-guide-workflow { background: linear-gradient(to right, #e0e7ff, #ede9fe); border-radius: 8px; padding: 16px; }
    .seo-guide-workflow-step { display: flex; gap: 8px; font-size: 14px; color: #1f2937; margin-bottom: 6px; }
    .seo-guide-step-num { font-weight: 700; color: #4f46e5; }
    .seo-guide-faq summary { font-weight: 600; color: #111827; cursor: pointer; padding: 4px 0; }
    .seo-guide-faq summary:hover { color: #4f46e5; }
    .seo-guide-faq p { font-size: 12px; color: #4b5563; margin-top: 4px; padding-left: 16px; }
    .seo-guide-badge { display: inline-block; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 2px 6px; border-radius: 4px; background: #d1fae5; color: #065f46; margin-left: 8px; }
    .seo-guide-cols-info { display: grid; grid-template-columns: 1fr; gap: 8px; font-size: 14px; }
    @media (min-width: 768px) { .seo-guide-cols-info { grid-template-columns: 1fr 1fr; } }
    .seo-guide-cols-info strong { color: #111827; }
    .seo-guide-cols-info span { color: #4b5563; }
    .seo-guide-warn { font-size: 11px; color: #6b7280; margin-top: 8px; font-style: italic; }
</style>

<details class="seo-guide" {{ $defaultOpen ? 'open' : '' }}>
    <summary class="seo-guide-summary">
        <div class="seo-guide-summary-left">
            <div class="seo-guide-icon">📖</div>
            <div>
                <div class="seo-guide-title">SEO Audit Kullanım Kılavuzu</div>
                <div class="seo-guide-subtitle">Hangi başlık ne anlama geliyor? Ne yapmalıyım?</div>
            </div>
        </div>
        <svg class="seo-guide-chevron" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
        </svg>
    </summary>

    <div class="seo-guide-body">

        {{-- 1. SİSTEM AKIŞI --}}
        <div class="seo-guide-block">
            <div class="seo-guide-block-title">🔄 Sistem Nasıl Çalışıyor?</div>
            <ol class="seo-guide-list">
                <li><strong>Audit:</strong> Sayfa fetch edilir → metin + H1/H2/görsel/link sayısı çıkarılır</li>
                <li><strong>Keyword havuzu:</strong> Forum (120K mesaj) + Telegram (142K + 716K mesaj) cache'lerinden ağırlıklı keyword listesi yüklenir</li>
                <li><strong>Karşılaştırma:</strong> Sayfa metni vs havuz → bulunan / eksik liste + fırsat skoru</li>
                <li><strong>AI Öneri (opsiyonel):</strong> Gemini ile eksik konulara section önerisi üretilir</li>
                <li><strong>Aktivasyon (opsiyonel):</strong> Entity-tabanlı template ise (city/uni/field) AI ile content_blocks üretilip ilgili kayıt güncellenir</li>
            </ol>
        </div>

        {{-- 2. SKORLAR --}}
        <div class="seo-guide-grid-2">
            <div class="seo-guide-score amber">
                <div class="seo-guide-block-title">🎯 SEO Fırsat Skoru (0-100)</div>
                <p style="font-size: 14px; color: #374151;"><strong>Ne demek:</strong> Topluluk verisinde sıkça konuşulan ama sayfada eksik konuların ağırlık toplamı.</p>
                <div class="seo-guide-score-meta">
                    <p>🚨 <strong>90+:</strong> Çok büyük fırsat</p>
                    <p>⚠️ <strong>70-89:</strong> Büyük fırsat — yüksek öncelik</p>
                    <p>📊 <strong>50-69:</strong> Orta fırsat — hızlı kazanç mümkün</p>
                    <p>✅ <strong>0-49:</strong> Sayfa iyi durumda</p>
                </div>
                <p class="seo-guide-warn">⚠️ Yüksek skor = daha kötü sayfa. Bu "fırsat" skorudur, kalite değil.</p>
            </div>

            <div class="seo-guide-score green">
                <div class="seo-guide-block-title">💚 Sağlık Skoru (0-100)</div>
                <p style="font-size: 14px; color: #374151;"><strong>Ne demek:</strong> 5 teknik kontrolden (içerik uzunluğu, H1, H2, görsel, iç link) kaç tanesi iyi durumda.</p>
                <div class="seo-guide-score-meta">
                    <p>💚 <strong>80+:</strong> Teknik yapı sağlam</p>
                    <p>💛 <strong>50-79:</strong> Bazı eksiklikler var</p>
                    <p>❤️ <strong>0-49:</strong> Temel teknik problem</p>
                </div>
            </div>
        </div>

        {{-- 3. KOLONLAR --}}
        <div class="seo-guide-block">
            <div class="seo-guide-block-title">📊 Liste Sayfası Kolonları</div>
            <div class="seo-guide-cols-info">
                <p><strong>Template:</strong> <span>Sayfa tipi (Üni Detay, Şehir Detay vb.)</span></p>
                <p><strong>Sayfa Başlık:</strong> <span>Sayfanın &lt;title&gt;</span></p>
                <p><strong>Fırsat:</strong> <span>0-100 skoru (yüksek = daha kötü)</span></p>
                <p><strong>✓ Found:</strong> <span>Sayfada bulunan keyword sayısı</span></p>
                <p><strong>✗ Missing:</strong> <span>Sayfada olmayan ama topluluğun konuştuğu keyword sayısı</span></p>
                <p><strong>Karakter:</strong> <span>İçerik uzunluğu (2.500+ ideal)</span></p>
                <p><strong>H2:</strong> <span>Alt-bölüm sayısı (6+ ideal)</span></p>
                <p><strong>Audit:</strong> <span>Son audit zamanı</span></p>
            </div>
        </div>

        {{-- 4. AKSİYONLAR --}}
        <div class="seo-guide-block">
            <div class="seo-guide-block-title">⚙️ Aksiyon Butonları</div>
            <div class="seo-guide-action neutral">
                <span class="seo-guide-action-icon">🔍</span>
                <div>
                    <p class="seo-guide-action-title">Detay</p>
                    <p class="seo-guide-action-desc">Sayfanın tam analizini aç: 2 gauge, sağlık checklist, top 10 eksik keyword grafiği, olması gereken rehberi.</p>
                </div>
            </div>
            <div class="seo-guide-action neutral">
                <span class="seo-guide-action-icon">🔄</span>
                <div>
                    <p class="seo-guide-action-title">Yeniden Audit</p>
                    <p class="seo-guide-action-desc">Sayfayı tekrar fetch et + skor güncelle. <strong>Ne zaman:</strong> içerik eklendikten sonra doğrulamak için.</p>
                </div>
            </div>
            <div class="seo-guide-action warn">
                <span class="seo-guide-action-icon">🪄</span>
                <div>
                    <p class="seo-guide-action-title">AI Öneri Üret</p>
                    <p class="seo-guide-action-desc">Gemini'den eksik konulara section önerisi al (Markdown). <strong>Maliyet:</strong> ~5-10K token. <strong>Ne zaman:</strong> içerik üretmeden önce yol haritası için.</p>
                </div>
            </div>
            <div class="seo-guide-action success">
                <span class="seo-guide-action-icon">⚡</span>
                <div>
                    <p class="seo-guide-action-title">İçerik Üret &amp; Aktive Et <span class="seo-guide-badge">ana özellik</span></p>
                    <p class="seo-guide-action-desc">Sadece entity-tabanlı template'lerde (city/uni/field/program/blog). Slug gir → Forum + Telegram insights + audit gap'leri + AI ile content_blocks üretilir, entity'nin record'una uygulanır. Sayfada anında görünür. <strong>Maliyet:</strong> ~10-20K token.</p>
                </div>
            </div>
        </div>

        @if (! $compact)
            {{-- 5. İŞ AKIŞI --}}
            <div class="seo-guide-workflow">
                <div class="seo-guide-block-title">🚀 Tipik İş Akışı (3 adım)</div>
                <div class="seo-guide-workflow-step">
                    <span class="seo-guide-step-num">①</span>
                    <p><strong>Listede sırala</strong> — "Fırsat" kolonunu büyükten küçüğe sırala. En tepedekiler en büyük SEO fırsatı.</p>
                </div>
                <div class="seo-guide-workflow-step">
                    <span class="seo-guide-step-num">②</span>
                    <p><strong>Detay → AI Öneri Üret</strong> — eksik konulara section önerisi al, manuel okuyup planla.</p>
                </div>
                <div class="seo-guide-workflow-step">
                    <span class="seo-guide-step-num">③</span>
                    <p><strong>İçerik Üret &amp; Aktive Et</strong> — entity slug'ını gir. AI içerik üretir + entity'nin sayfası anında zenginleşir. Sonra <strong>Yeniden Audit</strong> ile skoru doğrula.</p>
                </div>
            </div>

            {{-- 6. SSS --}}
            <div class="seo-guide-block">
                <div class="seo-guide-block-title">❓ Sıkça Sorulanlar</div>
                <div class="seo-guide-faq">
                    <details>
                        <summary>Keyword havuzu nereden geliyor?</summary>
                        <p>2 kaynak: <strong>(1)</strong> DeutschStudent forum'undan trigram/bigram/anchor + trending keyword'ler (önceden indekslenmiş JSON). <strong>(2)</strong> Telegram raporlarından (visa/denklik + genel) en çok konuşulan konular. Hepsi ağırlıklı, stop word filtrelenmiş.</p>
                    </details>
                    <details style="margin-top: 8px;">
                        <summary>Aktivasyon hangi template'lerde çalışıyor?</summary>
                        <p><code>city_detail</code>, <code>university_detail</code>, <code>program_detail</code>, <code>field_detail</code>, <code>blog_detail</code>. Static sayfalarda (home, about, tools) yok — onları manuel düzenle.</p>
                    </details>
                    <details style="margin-top: 8px;">
                        <summary>Aktivasyon mevcut içeriği siler mi?</summary>
                        <p>Entity-tabanlı sayfalar için <code>content_blocks</code> tamamen yenilenir (override). DB snapshot al. Program/Blog için <strong>append</strong> edilir, mevcut içerik korunur.</p>
                    </details>
                    <details style="margin-top: 8px;">
                        <summary>AI ne zaman halüsinasyon yapar?</summary>
                        <p>Prompt'ta "halüsinasyon yok, emin değilsen 'resmi sayfadan doğrula' de" kuralı var. Yine de spesifik <strong>tarihler, ücretler, ders adları</strong> manuel kontrol edilmeli.</p>
                    </details>
                    <details style="margin-top: 8px;">
                        <summary>Neden skor hep 100/100?</summary>
                        <p>Audit'lenen URL'in fetch'i fail etmiş olabilir (local server kapalıydı vs.) → içerik boş → tüm keyword "eksik" sayılır. Sample URL'leri server çalışırken yeniden audit'le.</p>
                    </details>
                </div>
            </div>
        @endif
    </div>
</details>
