# Chatbot (Semantik RAG) Playbook'u

AlmanyaUni / ApplyToGerman için **referans-site kalitesinde, tam semantik** bir
soru-cevap asistanı. Bu doküman mimariyi, kararları ve uygulama fazlarını sabitler.

> Hedef: gelen ziyaretçiyi **dönüştüren** (acquisition değil, conversion/UX) +
> rakipten ayrıştıran + otorite pekiştiren asistan. Kalite çıtası acımasız:
> **tek uydurma cevap, kattığı tüm faydadan çok itibar yakar.** Bu yüzden sıkı
> grounding + eşik-bazlı "emin değilim" + `chat:eval` ölçüm kapısı pazarlık konusu değil.

---

## 0. Değişmez ilkeler

1. **Sadece getirilen bağlamdan cevap.** Model parametrik bilgisinden konuşmaz;
   yalnız retrieval'in döndürdüğü parçalardan. Bağlam zayıfsa (benzerlik eşik altı)
   → "emin değilim, şu sayfaya bak" + link. Asla uydurma.
2. **Her iddia kaynaklı.** Cevapta satır-içi atıf `[1][2]` → FAQ/blog/program/üni/şehir
   sayfası linki. Kaynaksız cümle = kusur.
3. **Sayı/tarih hedge'i.** Vize eşiği, Sperrkonto, ücret, deadline yıllık değişir →
   "… itibarıyla; başvurudan önce resmi kaynaktan doğrula" (mevcut içerik disiplini botta da geçerli).
4. **Çok dilli.** Kullanıcı diliyle (TR/DE/EN) cevap; retrieval tüm dillerde içerik
   bulur (çok dilli embedding sayesinde TR soru EN/DE dokümanı da getirir).
5. **Ölçülmeden yayın yok.** `chat:eval` altın seti (reddit/telegram havuzundan ~80
   gerçek soru) groundedness + kaynak isabeti ölçmeden canlıya çıkılmaz.
6. **KAS-uyumlu, browser-ops.** SSH yok → embedding üretimi `/admin/ops/kb-embed` ile
   tarayıcıdan tetiklenir (tıpkı `faq-generate`). Yeni harici servis yok.

---

## 1. Mimari — iki retrieval şeridi

```
Kullanıcı sorusu (TR/DE/EN)
   │
   ├─[A] Sorgu anlama: Gemini → niyet + yapısal kriter (alan/derece/dil/NC/şehir)
   │
   ├─[B] RETRIEVAL (paralel iki şerit)
   │      ├─ TAVSİYE şeridi  (SAF SEMANTİK)
   │      │    kaynak: FAQ + blog (paragraf-chunk) + üni/şehir açıklama
   │      │    yöntem: query embedding → tüm chunk'larda cosine (brute-force, küçük set)
   │      │
   │      └─ PROGRAM şeridi  (YAPISAL + SEMANTİK)
   │           kaynak: 18.306 program
   │           yöntem: SQL ön-filtre (kriterle daralt) → daraltılan sette cosine sırala
   │           neden: 18k vektörü her istekte taramak KAS'ta olmaz (~55MB/istek);
   │                  ayrıca "Berlin + İngilizce + NC-frei" KESİN kısıt, bulanık değil → daha kaliteli
   │
   ├─[C] Birleştir + eşik: en iyi benzerlik < T ise → düşük-güven yolu (link + "emin değilim")
   │
   ├─[D] GENERATION: Gemini, SADECE seçilen parçalardan cevap + satır-içi atıf + hedge
   │
   └─[E] Yanıt + kaynak çipleri + "ilgili programlar/yazılar" + 👍/👎 + (lead yakalama)
```

**Özet:** Tavsiye içeriği = saf semantik (bulanıklık burada istenir). Program = yapısal+semantik
(kesinlik + ölçeklenebilirlik). İkisi birlikte = tam semantik kapsama + KAS gerçekliği.

---

## 2. Bileşenler (yeni dosyalar)

| Katman | Dosya | Görev |
|---|---|---|
| Embedding | `app/Services/Rag/GeminiEmbedder.php` | `gemini-embedding-001`, çok dilli, asimetrik task-type (DOCUMENT/QUERY), L2-normalize, float32 pack |
| Depo | migration `create_kb_chunks_table` + (Faz 5) `chat_conversations/messages` | vektör + metin + url + content_hash (artımlı) |
| İndeksleme | `app/Console/Commands/KbEmbed.php` (`kb:embed`) | içerik → chunk → embed → upsert; artımlı (hash); `--source`, `--locale`, `--fresh`, `--dry-run` |
| Ops | route `/admin/ops/kb-embed` | tarayıcıdan embed tetikle (is_admin) |
| Retrieval | `app/Services/Rag/Retriever.php` | iki şerit + cosine + eşik + dedupe |
| Üretim | `app/Services/Rag/ChatService.php` | grounding prompt + citation + guardrail + hedge |
| API | route `POST /{locale}/chat` (throttle:20,1, CSRF) | sohbet uç noktası |
| UI | `resources/views/components/chat-widget.blade.php` (Alpine.js) | yüzen sohbet; `popup.blade.php` desenini taklit eder |
| Kalite | `app/Console/Commands/ChatEval.php` (`chat:eval`) | altın set → recall@k + groundedness (LLM-judge) + kaynak isabeti |

---

## 3. Veri modeli — `kb_chunks`

| Kolon | Tip | Not |
|---|---|---|
| `id` | bigint PK | |
| `source_type` | string(16) | faq \| post \| program \| university \| city |
| `source_id` | unsignedBigInt | kaynak satır id |
| `locale` | string(5) | tr/de/en |
| `chunk_index` | unsignedInt | çok-parçalı içerikte sıra |
| `title` | string | atıf/gösterim başlığı |
| `url` | string | public link (locale-aware) |
| `content` | longText | embed edilen ham metin |
| `token_estimate` | unsignedInt | kabaca |
| `embedding` | longBlob | float32 LE, **L2-normalize** (cosine = dot) |
| `dims` | smallint | 768 |
| `model` | string(40) | embedding model adı |
| `content_hash` | char(64) | sha256(content) → artımlı atlama |
| `embedded_at` | timestamp | |

İndeks: `(source_type, source_id, locale, chunk_index)` + `content_hash` + `(source_type, locale)`.

**Neden BLOB float32, JSON değil:** 768 float × 4B = ~3KB/satır (JSON ~9KB). Program dahil
~20k satırda BLOB ~60MB, JSON ~180MB. Vektörler **L2-normalize** saklanır → benzerlik = nokta çarpımı.

---

## 4. Chunking stratejisi

- **FAQ:** 1 chunk = `Soru\n\nCevap(md→düz metin)`. Kısa, tek parça. title=soru, url=`faqs.show`.
- **Blog (Post):** `content_md` başlık/paragraf sınırından ~400-600 token chunk'lara böl;
  her chunk'a yazı başlığı önek. url=`blog.show`. Hem blog hem news.
- **Program:** 1 chunk = ad(de/en/tr) + derece + dil + admission_mode + açıklama(tr/en) + üni + şehir. url=`programs.show`.
- **University:** ad + açıklama + content_blocks düz metni. url=`universities.show`.
- **City:** ad + content_blocks düz metni. url=`cities.show`.
- **Locale:** FAQ/Post locale satırlıdır → her locale ayrı embed. Program/Üni/Şehir çok-dil
  kolonludur → her locale kendi kolonundan chunk üretir.

---

## 5. Model & parametre kararları

- **Embedding:** `gemini-embedding-001` (çok dilli, `outputDimensionality=768`).
  Task-type: indeksleme `RETRIEVAL_DOCUMENT`, sorgu `RETRIEVAL_QUERY` (asimetrik = daha isabetli).
- **Üretim:** `gemini-2.5-flash` (temp ~0.2, düşük). Zor/uzun cevapta `2.5-pro`'ya yükseltilebilir.
- **Eşik T:** altın set kalibrasyonuyla belirlenir (başlangıç ~0.62 cosine).
- **top-K:** tavsiye 6, program 6; toplam bağlam ~12 parça, token bütçesine göre kırpılır.
- **Streaming:** v1'de yok (önce sağlamlık); KAS FastCGI buffering riski → Faz 4'te SSE denenir, fallback'li.

---

## 6. Fazlar

1. **Embedding altyapısı** — `GeminiEmbedder` + `kb_chunks` + `kb:embed` + `/admin/ops/kb-embed`.
   İlk kapsam: FAQ + blog. Lokalde gerçek embed + vektör doğrulama.
2. **Chat çekirdeği** — `Retriever` (tavsiye şeridi) + `ChatService` + `POST /chat` + grounding/citation.
   SSS+blog botu uçtan uca canlı (widget öncesi JSON API olarak test edilir).
3. **Program şeridi** — yapısal+semantik program-bulucu (#1 öncelik) + üni/şehir embed.
4. **Widget + UX** — Alpine.js yüzen sohbet, kaynak çipleri, önerilen sorular, mobil, (streaming).
5. **Kalite + lead** — `chat:eval` altın set, 👍/👎 toplama, lead yakalama, TR/DE/EN cila.

---

## 7. Maliyet & ops

- Embedding tek seferlik: ~20k chunk × ~birkaç sent. Yeniden-embed yalnız değişen içerik (content_hash).
- Sohbet başına: 1 query-embed + 1 generation ≈ $0.001-0.005 (flash). İhmal edilebilir.
- İçerik değişince `kb:embed` (artımlı) yeniden çalıştırılır → `/admin/ops/kb-embed`.

---

## 8. Riskler & önlemler

| Risk | Önlem |
|---|---|
| Halüsinasyon (referans-site katili) | Sıkı grounding promptu + benzerlik eşiği + "emin değilim" yolu + `chat:eval` kapısı |
| 18k program tarama yükü | Program şeridi yapısal ön-filtre + daraltılmış cosine |
| Bayat embedding | content_hash artımlı + içerik düzenlemede yeniden-embed |
| Abuse/maliyet | throttle:20,1 + günlük token tavanı (Faz 5) |
| Çok dilli sızıntı (TR prose EN cevaba) | Cevap dili = kullanıcı locale; mevcut `looksTurkish` disiplini |
| KAS streaming buffering | v1 non-stream; streaming opsiyonel + fallback |

---

_İlgili memory: `chatbot-rag-project`, `question-to-blog-workflow`, `i18n-english-key-convention`,
`prod-migrations-via-browser`, `deploy-via-github-push`._
