# Blog & SSS Üretim Playbook'u

Bu doküman, siteye **blog yazısı** ve **SSS (FAQ)** içeriği üretirken izlenen tekrarlanabilir akışı tarif eder.
Kanonik örnek: Informatik/bilgisayar kümesi (commit `c0723b2`, 4 yazı × TR/DE/EN = 12 kayıt).

---

## 0. Değişmez ilkeler (ikisi için de geçerli)

1. **Her içerik bir data-migration ile eklenir.** Seeder'lar **prod'da çalışmaz** — yeni içerik mutlaka `database/migrations/...` altında `Post`/`Faq` upsert eden bir migration olmalı.
2. **Üç dil zorunlu: TR + DE + EN.** Aynı içeriğin üç locale satırı ortak bir `translation_group_id` paylaşır.
3. **Slug / URL / iç-link HER ZAMAN İngilizce** — TR yazıda bile. TR = temel slug, DE = `-de`, EN = `-en`. (bkz. memory `english-slugs-and-links`)
4. **İç-link locale-doğru olmalı:**
   - TR gövde → `/tr/blog/<temel-slug>`
   - DE gövde → `/de/blog/<temel-slug>-de`
   - EN gövde → `/en/blog/<temel-slug>-en`
5. **TR prose EN/DE satırına SIZAMAZ.** `Faq::looksTurkish()` / `contentIsBroken()` ve içerik-bütünlüğü denetimi (`content:audit`) bunu yakalar ve "bozuk" işaretler. EN/DE gövdesi tamamen o dilde olmalı (özel ad istisna).
6. **Sayıları yıl-etiketle + hedge'le.** Vize eşikleri, maaşlar, Sperrkonto tutarı, Deutschlandticket fiyatı vb. yıllık değişir → "2025 itibarıyla; yıllık güncellenir, başvurudan önce doğrula" notu şart. Asla kalıcı sunma.
7. **İdempotent + slug-bazlı.** Upsert `slug` (+`locale`) anahtarıyla yapılır; migration tekrar çalışsa kopya üretmez, prod ID'lerinden bağımsızdır.
8. **Deploy migrate ÇALIŞTIRMAZ.** Push'tan sonra prod'da **`/admin/ops/migrate`** elle tetiklenir. (bkz. memory `prod-migrations-via-browser`)

---

## 1. BLOG üretim akışı

### Adım 1 — Konuyu seç & doğrula
- En kârlı kaynak: **gelen kullanıcı sorusu** → doğrula → TR/DE/EN blog. (memory `question-to-blog-workflow`)
- Alternatif: alan/karşılaştırma kümesi (tıp, hukuk, psikoloji, Informatik; "Şehir A vs B" serisi — memory `city-vs-city-blog-series`).
- **Önceliklendirmede #1 unsur = bölümler/programlar** (memory `priority-university-programs`).
- Gerçek pain-point'lere dayan (r/germany, r/cscareerquestionsEU mantığı); "harika ama gerçeği az bilinen" açılar dönüşür.

### Adım 2 — Grounding (doğrulanmış gerçekler)
- Yazıda geçecek tüm sayı/kural/kurum adını **önce bir grounding notunda** topla.
- Resmi kaynak tercih: Make it in Germany, BAMF, ABH, anabin, uni International Office, mygermanuniversity (NC).
- DB-topraklı veri kullan (üni/program/şehir tablolarındaki gerçek değerler).

### Adım 3 — 4 açı planı (küme ise)
Standart küme deseni (alan başına 4 yazı):
1. **"X okumak" (yabancı olarak)** — NC, dil, başvuru yolu, gerçek-şok
2. **Dil/erişim açısı** (ör. İngilizce program, Almancasız)
3. **Çalışma/vize açısı** (Blue Card, eşik, rotalar, maaş)
4. **"Diplomayla ne yapılır" / iş piyasası**
Her yazı diğer 3'üne **küme-içi iç-link** verir + ilgili dış bloglara (anabin, studienkolleg, fh-vs-uni, vize) link.

### Adım 4 — Paralel üretim
- Ortak bir **spec dosyası** yaz (şablon + grounding + iç-link envanteri + tuzaklar) → scratchpad'e.
- Her yazı için **bir paralel `general-purpose` agent** spawn et; her biri spec'i okur, tek migration dosyası yazar.
- Agent'lara: tam PHP iskeleti (birebir kopyalanacak), açının H2 başlıkları, gerçek slug'lar, hedge kuralları.

### Adım 5 — Blog migration şablonu
Dosya: `database/migrations/<TS>_blog_<ad>.php`. Birebir iskelet:

```php
return new class extends Migration {
    public function up(): void {
        $groupId = '<EŞSİZ-UUID>';                       // yazı başına benzersiz
        $userId = DB::table('users')->where('email','yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug','halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name','Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('slug','almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug','universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
        ... (NOWDOC: $ ve \ literal; iç-link /tr/blog/...) ...
        MD;
        $deBody = <<<'MD' ... /de/blog/...-de ... MD;
        $enBody = <<<'MD' ... /en/blog/...-en ... MD;

        $variants = [
            'tr' => ['slug'=>'<temel-slug>',     'title'=>..., 'excerpt'=>..., 'meta_title'=>..., 'meta_description'=>..., 'body'=>$trBody],
            'de' => ['slug'=>'<temel-slug>-de',  ... 'body'=>$deBody],
            'en' => ['slug'=>'<temel-slug>-en',  ... 'body'=>$enBody],
        ];
        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input'=>'allow','allow_unsafe_links'=>false]);
            $payload = [
                'locale'=>$locale, 'translation_group_id'=>$groupId,
                'user_id'=>$userId, 'category_id'=>$categoryId,
                'title'=>$v['title'], 'excerpt'=>Str::limit($v['excerpt'],250,'…'),
                'content_md'=>$v['body'], 'content_html'=>$html,
                'meta_title'=>$v['meta_title'], 'meta_description'=>Str::limit($v['meta_description'],158,'…'),
                'reading_minutes'=>max(1,(int)round(str_word_count(strip_tags($html))/200)),
                'is_published'=>true, 'published_at'=>now(),
            ];
            $existing = Post::where('slug',$v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload+['slug'=>$v['slug']]);
        }
    }
    public function down(): void {
        Post::whereIn('slug', ['<temel-slug>','<temel-slug>-de','<temel-slug>-en'])->delete();
    }
};
```

Stil: `du`/`dein` (Almanca informal), 6–8 H2, tablo kullan, kalın anahtar gerçekler, "Sonuç & dürüst tavsiye" + italik 2026 disclaimer ile bitir. **Gövdeye yazar satırı EKLEME** (byline `$userId` ile gelir).

---

## 2. SSS (FAQ) üretim akışı

FAQ modeli (`App\Models\Faq`) blogdan farklı; bazı alanlar **otomatik** üretilir:
- `answer_md` set edilip `save()` çağrılınca model hook'u → `answer_html`, `answer_minutes`, `has_answer` **otomatik** hesaplanır. Sen sadece `answer_md` (markdown) yazarsın.
- `intent` → `Faq::detectIntent()` ile TR sorudan otomatik sınıflanır (nasil/ne-kadar/ne-zaman/hangi/...); rozet `intentLabel()` ile locale'e çevrilir.
- Soru bir **`FaqTopic`'e** bağlanır (`faq_topic_id`) — konu havuzları (vize, dil, para/sigorta, yurt/şehir, master/studienkolleg, bürokrasi).
- `locale` + `translation_group_id` blogdaki mantıkla aynı (TR/DE/EN üç satır).

### FAQ migration örüntüsü
İki durum var:

**(a) Mevcut soruya cevap yaz** (community-import soruları zaten varsa) — slug'la bul, `answer_md` doldur:
```php
$faq = Faq::where('slug',$slug)->where('locale',$locale)->first();
if ($faq) { $faq->answer_md = trim($md); $faq->save(); }   // html/minutes/has_answer otomatik
```

**(b) Sıfırdan yeni SSS ekle** — topic id'sini çöz, üç locale satırı upsert et:
```php
$topicId = DB::table('faq_topics')->where('slug','vize')->value('id');
$groupId = '<EŞSİZ-UUID>';
foreach (['tr'=>[...],'de'=>[...],'en'=>[...]] as $locale=>$v) {
    $faq = Faq::firstOrNew(['slug'=>$v['slug'], 'locale'=>$locale]);
    $faq->fill([
        'faq_topic_id'=>$topicId, 'translation_group_id'=>$groupId,
        'question'=>$v['question'], 'answer_md'=>trim($v['answer_md']),
        'category'=>$v['category'] ?? null, 'is_published'=>true,
        // intent otomatik (detectIntent), html/minutes/has_answer otomatik (saving hook)
    ]);
    $faq->save();
}
```
> Not: `intent` TR sorudan türetilir; DE/EN satırında da TR `intent` slug'ı saklanır (rozet `__()` ile çevrilir) — bu kasıtlı, dokunma.

Stil: cevap kısa-net, adım-adım listeler + "Önemli Süreler" gibi alt başlıklar, sayılar yıl-etiketli. Soru ≤200 karakter (uzun = bozuk sayılır).

---

## 3. Doğrulama checklist (commit ÖNCESİ — ikisi için ortak)

```bash
# 1) PHP lint
php -l database/migrations/<dosya>.php

# 2) Markdown render + gövde sayısı (DB'siz; vendor/autoload + Str::markdown)
#    her dosyada 3 NOWDOC gövdesi, her biri temiz HTML render etmeli

# 3) Slug + groupId eşsizliği
grep -hoE "'slug'\s*=> '[^']+'" database/migrations/<dosyalar>
grep -hoE "\$groupId = '[^']+'" database/migrations/<dosyalar>

# 4) İç-link locale-path doğruluğu (TR gövde /de//en içermemeli; hedefler gerçek slug)
grep -oE '/(tr|de|en)/blog/[a-z0-9-]+' database/migrations/<dosya>

# 5) (DB ayaktaysa) lokal migrate
php artisan migrate --path=database/migrations/<dosya> --force
```
Lokal MySQL kapalıysa (yaygın): lint + render-check yeterli; gerçek apply prod'da olur.

---

## 4. Deploy akışı (ikisi için ortak)

1. **Sadece ilgili migration'ları stage'le** (HK WIP / `.claude` gibi alakasız dosyalara dokunma).
2. Açıklayıcı commit (TR), `Co-Authored-By: Claude Opus 4.8 (1M context)` ile bitir.
3. **`git push origin main`** → GitHub Actions → FTPS → server pulse otomatik deploy.
   - ⚠️ Push bazen 2 dk'da asılır (stale-connection). Çözüm: `git push --no-verify` + arka planda uzun pencere; sonra `git ls-remote origin -h refs/heads/main` ile remote ucunu doğrula. (memory `deploy-and-render-gotchas`)
4. **Prod'da `/admin/ops/migrate`** sayfasını aç ve tetikle — yazılar/SSS ancak bundan sonra DB'ye girer.
5. **Canlıda 200 doğrula** (prod slug lokalden farklı olabilir): bir TR + bir EN URL'sini aç. Kanonik domain `applytogerman.com`. (memory `domain-and-seo-setup`)
6. Prod'a push **açık onayla** yapılır.

---

## 5. Sık tuzaklar

| Tuzak | Sonuç | Önlem |
|---|---|---|
| Seeder ile içerik eklemek | Prod'da görünmez | Data-migration kullan |
| TR slug / Türkçe URL | SEO + tutarlılık bozulur | Slug hep İngilizce, DE `-de` / EN `-en` |
| TR prose EN/DE gövdesinde | `content:audit` bozuk işaretler | Her gövde tamamen kendi dilinde |
| İç-link locale-path yanlış | Çapraz-dil kırık gezinme | TR→/tr, DE→/de…-de, EN→/en…-en |
| Sabit vize/maaş sayısı | Hızla eskir, yanlış bilgi | Yıl-etiketle + "doğrula" hedge'i |
| Migrate'i prod'da unutmak | Push'a rağmen içerik yok | `/admin/ops/migrate` elle çalıştır |
| Push asıldı sanmak | Gereksiz tekrar | `git ls-remote` ile remote ucunu teyit et |
| `down()` eksik slug | Geri alma yarım | 3 locale slug'ını da listele |

---

İlgili memory'ler: `question-to-blog-workflow`, `city-vs-city-blog-series`, `english-slugs-and-links`,
`i18n-english-key-convention`, `prod-migrations-via-browser`, `deploy-via-github-push`,
`deploy-and-render-gotchas`, `content-integrity-system`, `priority-university-programs`.
