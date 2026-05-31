# i18n Stil Rehberi & Terminoloji Sözlüğü (EN / DE)

**Amaç:** Birebir çeviri yerine **SEO-destekli native dil**. Bu dosya tüm EN/DE çeviri/revizyon işlerinin tek referansıdır.

**Referans kaynak (DE):** study-in-germany.com/de (DAAD resmi portalı) — native Almanca, öğrenci-odaklı, "du" register.
Güncellendi: 2026-05-31.

---

## 1. Almanca register: `du` (ZORUNLU, tutarlı)

Resmi study-in-germany portalı baştan sona **`du`** kullanır: *"Wir suchen dich!"*, *"Du möchtest für eine bestimmte Zeit in Deutschland leben?"*, *"Bewirb dich jetzt"*.

- **Adres:** `du / dein / dich / dir` — asla `Sie / Ihr / Ihnen`.
- **Fiil:** `Nutze`, `Vergleiche`, `Finde`, `Plane`, `Bewirb dich` (imperatif, 2. tekil).
- **Ton:** samimi, aksiyon-odaklı, promosyonel ama faktüel. Bürokratik değil, davetkâr.
- **İstisna:** Hukuki/yasal uyarı (disclaimer) metinleri nötr/impersonal kalabilir ("Bitte beachte..." yine du; resmi alıntılar verbatim).

⚠️ Şu an site du/Sie KARIŞIK — revize ederken hepsini `du`'ya çek.

## 2. İngilizce register

- Doğrudan, "you" (samimi-profesyonel). Aktif fiil, kısa cümle.
- Amerikan değil nötr/uluslararası İngilizce. Cümle kısa, tarama-dostu.

## 3. Terminoloji sözlüğü (native + SEO)

| Kavram | DE (native, SEO) | EN | Not |
|---|---|---|---|
| Üniversite/yüksekokul | **Hochschule(n)** (şemsiye), Universität(en) | university / universities | Hochschule = Uni + FH şemsiyesi, SEO için güçlü |
| Program | **Studiengang / Studiengänge**, Studienangebote | study program(s), degree program | "Programme" değil **Studiengänge** |
| İngilizce program | **englischsprachige Studiengänge** | English-taught programs | "Englische Programme" DEĞİL |
| Başvuru | **Bewerbung**, sich bewerben | application, to apply | |
| Kayıt | **Immatrikulation**, Einschreibung | enrollment | |
| Lise diploması denkliği | **Hochschulzugangsberechtigung (HZB)** | university entrance qualification | yüksek SEO compound |
| Finansman / kanıt | **Finanzierung**, **Finanzierungsnachweis** | financing / proof of funds | Sperrkonto = blocked account |
| Vize / oturum | **Visum**, **Aufenthaltserlaubnis** | visa / residence permit | |
| Burs | **Stipendium / Stipendien** | scholarship(s) | |
| Hazırlık | **Studienkolleg(s)**, Vorbereitungskurse | foundation course | verbatim |
| Almanca bilgisi | **Deutschkenntnisse** | German language skills | |
| Yaşam masrafı | **Lebenshaltungskosten** | cost of living | |
| Son tarih | **Frist / Fristen**, Bewerbungsfrist | deadline(s) | "Fristenkalender" = deadline calendar |
| Yan iş | **Nebenjob**, Werkstudent | part-time job / working student | |
| Üni şehirleri | **Unistädte** | university cities | |

**Proper noun verbatim (çevirme):** DAAD, Studienkolleg, Sperrkonto, Anabin, uni-assist, Numerus Clausus / NC, Semesterticket, BAföG, Bachelor, Master.

## 4. CTA / başlık kalıpları (referanstan)

DE örnekleri: *"In 4 Schritten nach Deutschland"*, *"Dein Karrierestart in Deutschland"*, *"Planung beginnen"*, *"Bewirb dich jetzt"*, *"Jetzt entdecken"*, *"Mehr erfahren"*.

- Buton: kısa imperatif — `Jetzt vergleichen`, `Auf der Karte entdecken`, `Universitäten suchen`.
- Başlık: somut + sayı/değer — *"603 Hochschulen, alle Studiengänge an einem Ort"*.

## 5. SEO compound kelimeler (DE — arama hacmi yüksek)

`Hochschulzugangsberechtigung` · `englischsprachige Studiengänge` · `Studienkolleg` · `Finanzierungsnachweis` · `Aufenthaltserlaubnis` · `Lebenshaltungskosten` · `Bewerbungsfrist` · `Studienalltag` · `Unistädte` · `Nebenjob` · `Sperrkonto`

## 6. İş akışı

1. Revize edilecek yüzeyin string'lerini çıkar (mevcut EN/DE).
2. Bu sözlük + register'a göre native+SEO revize et.
3. Before/after tabloyu kullanıcıya onaylat.
4. `lang/{tr,en,de}.json` formatını koru (4-boşluk + `\/` escape), `php artisan i18n:audit` çalıştır.
5. Onay sonrası commit.
