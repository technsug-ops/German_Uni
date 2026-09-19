<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Şartlı kabulle vize başvurusu — 36F/39F karışıklığı ve dil seviyesi.
 *
 * KAYNAK: Türkiye'deki Alman temsilciliklerinin kendi bilgi notu
 * (tuerkei.diplo.de → "Merkblatt für türkische Studienbewerber", Stand 18.08.2025) PDF'i
 * indirilip metni çıkarıldı. Doğrulananlar:
 *   - Geçim: aylık en az ~992 € · Sperrkonto = yıllık BAföG azami oranı 11.904 €, 1/12 aylık ödenir
 *   - Alternatif: §§ 66-68 AufenthG Verpflichtungserklärung; belgede "zum Sprachkurs/Studium"
 *     ibaresi ŞART
 *   - Dil: yeterlilik üniversitede DSH veya TestDaF ile kanıtlanır, sınav YALNIZCA BİR KEZ
 *     tekrarlanabilir; geçilemezse VORLÄUFIGE ZULASSUNG DÜŞER ve kesin kayıt yapılamaz
 *   - Dil seviyesi kazanıldıktan sonra "eski" şartlı kabul GEÇERSİZ → yeniden başvuru gerekir
 *   - Her üniversite dil kursu açmıyor; bazıları ücretli (Münih), bazıları başvuruda dil şartı arıyor
 *   - Studienbewerber vizesi: azami 9 ay · pasaport en az 1 YIL geçerli · dil kursu kaydı veya
 *     okul/üniversite teyidi · lise diploması + ÖSYM belgesi (onaylı kopya + tercüme) · başvuru
 *     Almanca doldurulmuş 2 nüsha + 3 fotoğraf, şahsen · dosya ABH'ye iletilir, ~4-6 hafta,
 *     vize genelde 6 ay geçerli
 *
 * TASLAKTAN DÜZELTİLEN HATALAR (kullanıcı taslağı AI ile üretilmişti):
 *   1. "Aufenthaltsgesetz § 39f" diye bir madde YOK. 36F/39F, Türkiye'deki temsilciliklerin
 *      BİLGİ NOTU numaralarıdır. Hukuki dayanak § 16b (öğrenim amacı hazırlık tedbirlerini de
 *      kapsar) veya § 16f (öğrenimle bağlantısız dil kursu).
 *   2. Taslak 39F'i "dil kursu + üniversite" vizesi sanıyordu; temsilciliğin kendi sayfasında
 *      39F = "Dil Bilgisine Akseptanslı Lisans" (dil şartını ZATEN sağlayanlar),
 *      36F = "Yükseköğrenime Hazırlık Amaçlı Dil Kursu" (şartlı kabul + dil kursu).
 *   3. Sperrkonto tutarı 934 €/11.208 € (2024) yazıyordu — güncel değer 992 € / 11.904 €.
 *   4. "Pasaport en az 6 ay" yazıyordu — bilgi notu en az 1 yıl diyor.
 *   5. "Konsolosluk A1 istiyor" kesin hüküm olarak yazılmıştı; resmî belge listesinde vize için
 *      seviye şartı geçmiyor. Yazıda bu dürüstçe ayrıldı: bağlayıcı olan üniversitenin sınavı.
 * Yazar: Halil Yaprakli. Kategori: vize.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd5b71e93-0a48-4c62-9f37-6e1b2c84a750';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->where('slug', 'basvuru')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Şartlı kabul mektubun geldi ve şimdi vize için aynı iki soruyu soruyorsun: **"36F mi 39F mi başvuracağım?"** ve **"konsolosluk A1 mi istiyor, B1 mi?"**

Önce en yaygın yanlışı temizleyelim, çünkü bu yanlış internette her yerde: **39F bir kanun maddesi değil.** Aufenthaltsgesetz'te "§ 39f" diye bir hüküm yok (§ 39, istihdama onay hakkındadır). **36F ve 39F, Türkiye'deki Alman temsilciliklerinin bilgi notu (Merkblatt) numaralarıdır** — yani belge listesi başlıklarıdır, hukuki dayanak değil.

Hukuki dayanak şudur: öğrenim amaçlı oturum, **öğrenime hazırlık tedbirlerini de kapsar** (§ 16b AufenthG) — yükseköğrenime hazırlık amaçlı dil kursu buraya girer. Öğrenimle bağlantısı olmayan saf dil kursu ise § 16f'e girer.

## Hangi bilgi notu senin durumun?

| Bilgi notu | Kapsamı | Kim için |
|---|---|---|
| **36F** | Yükseköğrenime hazırlık amaçlı dil kursu (*studienvorbereitender Sprachkurs*) | **Şartlı kabulü olan ve önce dil kursuna gidecek olan** |
| **39F** | Dil bilgisiyle birlikte lisans kabulü (*Bachelor mit Zulassung und Sprachkenntnissen*) | Dil şartını **zaten sağlamış**, doğrudan programa başlayacak olan |

Yani şartın hâlâ Almanca dil seviyesiyse, aradığın liste **36F**'tir. Numaralar zaman zaman değişebildiği için başvurmadan önce **kendi temsilciliğinin güncel bilgi notu listesine** bak — bu yazının verdiği çerçeve kalıcı, numaralar değil.

Şartlı kabulün ne olduğunu ve nasıl alındığını ayrı bir yazıda anlattık: [şartlı kabul (bedingte Zulassung) rehberi](/tr/blog/germany-conditional-admission-bedingte-zulassung-guide).

## "A1 mi, B1 mi?" — dürüst cevap

İnternetteki kesin cümlelerin aksine, temsilciliğin öğrenci adayları için yayımladığı bilgi notunda **vize için şu seviyede sertifika şart** diyen bir satır yok. Bağlayıcı olan şudur:

> Yeterli Almanca bilgisini **üniversitede, öğrenime başlamadan önce** bir dil sınavıyla (DSH veya TestDaF) kanıtlaman gerekir.

Pratikte ne oluyor? Temsilcilikler dosyada **dil kursu kaydını veya okulun teyidini** görmek ister ve niyeti değerlendirirken elindeki dil belgesine bakabilir; vize memurunun takdir yetkisi vardır. Yani:

- **Belirli bir seviye şartını resmî liste dayatmıyor.**
- **Ama elinde bir A1/A2 belgesi varsa dosyanı güçlendirir** — özellikle "gerçekten dil öğrenmeye gidiyor" sorusunu kapatır.
- **Asıl eşik üniversitenin sınavıdır** (DSH/TestDaF). Sınav tiplerini [TestDaF, DSH, telc karşılaştırmamızda](/tr/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in) anlattık.

Kısacası "A1 yeter mi?" sorusunun cevabı temsilciliğe göre değişebilir; "DSH/TestDaF'ı geçmem gerekiyor mu?" sorusunun cevabı ise kesindir: **evet.**

## Kimsenin söylemediği üç risk

Bilgi notunun içinde, danışmanlık sitelerinde nadiren geçen üç uyarı var:

**1. Dil sınavını yalnızca bir kez tekrarlayabilirsin.** DSH veya TestDaF'ı geçemezsen ve tekrarında da geçemezsen, **şartlı kabulün düşer** ve kesin kayıt yapamazsın. O noktada bir dönem daha dil kursuna devam edip sınava yeniden girmen gerekir.

**2. Dil seviyeni kazandıktan sonra eski şartlı kabulün geçerli değildir.** Sınavı geçtikten sonra **yeniden başvurman** gerekir. Yani "şartlı kabulüm elimde, dili halledince otomatik başlarım" varsayımı yanlış.

**3. Her üniversite dil kursu açmıyor.** Bazıları yalnızca ileri seviye kurs veriyor, bazılarında kurslar **ücretli** (bilgi notu Münih'i örnek veriyor), bazıları ise başvuru aşamasında zaten dil belgesi istiyor. Kabul mektubunu aldığın üniversiteye kursun var mı, ücretli mi, kontenjanı ne diye **yazılı olarak sor.**

## İngilizce master + şartlı kabul: çelişki değil, şartın kendisi belirleyici

"Programım İngilizce, neden Almanca isteniyor?" sorusunun cevabı kabul mektubunda yazıyor. Doğru soru şu: **şartın ne?**

- **Şart Almanca dil seviyesiyse:** gidiş amacın fiilen dil kursudur, dosyan da buna göre kurulur (36F çerçevesi).
- **Şart başka bir şeyse** (eksik belge, not ortalaması, ön koşul dersi, VPD): bu bir dil kursu dosyası değildir; o şartı nasıl kapatacağını gösteren belgeleri sunarsın.
- **Şart yoksa, doğrudan kabulse:** Almanca belgesine gerek yoktur; İngilizce yeterlilik belgen (TOEFL/IELTS vb.) ve genel öğrenci vizesi dosyası yeterlidir. Adım adım: [öğrenci vizesi rehberi](/tr/blog/germany-student-visa-2026-application-steps-documents-rejection).

## Resmî belge listesi: ne isteniyor?

Temsilciliğin bilgi notunda geçen çekirdek belgeler:

| Belge | Ayrıntı |
|---|---|
| **Finansman kanıtı** | Aylık en az **992 €**. Ya Almanya'da **Sperrkonto** (yıllık BAföG azami oranı **11.904 €**, aylık 1/12 çekilir) ya da **§§ 66-68 AufenthG Verpflichtungserklärung** |
| Verpflichtungserklärung ayrıntısı | Belgede **"zum Sprachkurs/Studium"** ibaresi bulunmalı — eksikse dosya geri döner |
| **Dil kursu kaydı** | Kurs kaydı veya dil okulu/üniversite teyidi |
| **Pasaport** | En az **1 yıl** geçerli (6 ay değil) |
| **Öğrenim hakkı belgesi** | Lise diploması ve ÖSYM belgesi — onaylı kopya + tercüme |
| **Başvuru formu** | **Almanca** doldurulmuş, iki nüsha, üç fotoğraf, şahsen teslim |

Sperrkonto sağlayıcıları ve açılış süreleri için [Sperrkonto rehberimize](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) bak.

## Süreç ve süre

Temsilcilik dosyayı inceledikten sonra **Almanya'daki Ausländerbehörde'ye iletir.** Bilgi notuna göre gerekli incelemeler bittikten sonra vize genelde **dört ila altı hafta** içinde verilir ve çoğunlukla **altı ay geçerlidir**. Randevu beklemesi bunun üstüne eklenir — şehir stratejisi için [konsolosluk randevu yazımıza](/tr/blog/germany-consulate-visa-appointment-2026-waiting-times-and-city-strategy) bak.

Almanya'ya girdikten sonra vizeyi oturum iznine çevirme işi Ausländerbehörde'dedir ve kabul/başvuru belgelerini görmek ister. O aşamada süreç tıkanırsa haklarını [Ausländerbehörde gecikmesi yazımızda](/tr/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage) anlattık.

## Sıkça Sorulanlar

### Evraklarımın Almancası yok, İngilizcesi var. Sorun olur mu?
Bilgi notu, öğrenim hakkı belgeleri için **onaylı kopya ve tercüme** istiyor ve başvuru formunun **Almanca** doldurulmasını şart koşuyor. Üniversite yazışmaları İngilizce kabul edilebilir, ama resmî Türk belgeleri (diploma, ÖSYM) için yeminli tercüme yaptırmak güvenli yoldur. Kesin liste temsilciliğin kendi sayfasındadır.

### Şartlı kabul vizesi için gerçekten A1 şart mı?
Resmî belge listesinde vize için "şu seviye" diye bir şart yazmıyor; istenen, dil kursu kaydı ve finansmandır. Buna rağmen elinde bir seviye belgesi olması dosyayı güçlendirir. Kesin ve bağlayıcı olan eşik üniversitenin DSH/TestDaF sınavıdır.

### 36F mi 39F mi başvurmalıyım?
Şartın hâlâ dil seviyesiyse **36F** (yükseköğrenime hazırlık amaçlı dil kursu). Dil şartını zaten sağladıysan ve doğrudan programa başlıyorsan **39F**. Numaralar değişebilir; temsilciliğin güncel listesini kontrol et.

### B1 sertifikam var, görüşmede yine Almanca soru sorarlar mı?
Sorabilirler. Vize memurunun takdir yetkisi var ve sertifikanın günlük iletişime yansıyıp yansımadığını görmek isteyebilir. Sorular genelde basittir: neden Almanya, hangi şehir, hangi program. Takılırsan İngilizce devam etmeyi rica edebilirsin.

### Dil sınavını geçemezsem ne olur?
Bilgi notu net: DSH/TestDaF yalnızca **bir kez** tekrarlanabilir; geçemezsen **şartlı kabulün düşer** ve kesin kayıt yapamazsın. Bir dönem daha kursa devam edip sınava yeniden girmen gerekir. Kaydın hiç açılmadığı için bu bir [Exmatrikulation](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) değildir, ama sonucu da benzer biçimde bir dönem kaybıdır.

### Dili bitirince otomatik olarak programa başlar mıyım?
Hayır. Bilgi notuna göre dil seviyesini kazandıktan sonra **eski şartlı kabulün geçerliliğini yitirir ve yeniden başvurman gerekir.** Takvimini buna göre kur.

## Sonuç ve dürüst tavsiye

Bu konudaki kafa karışıklığının kaynağı, forumlarda dolaşan numaraların kanun maddesi sanılması. Doğru çerçeve basit: **numaralar belge listesidir, hukuki dayanak § 16b'dir, bağlayıcı dil eşiği üniversitenin sınavıdır.**

Başvurmadan önce üç şeyi yazılı olarak netleştir: **kabul mektubundaki şart tam olarak nedir**, **üniversitenin dil kursu var mı ve ücretli mi**, **temsilciliğin güncel belge listesinde ne yazıyor.** Bu üçü elinde olduğunda "A1 mi B1 mi" sorusu kendiliğinden cevaplanıyor.

*Bu yazıdaki belge listesi ve tutarlar, Türkiye'deki Alman temsilciliklerinin öğrenci adayları için yayımladığı bilgi notunun 18.08.2025 tarihli sürümüne dayanır ve 2026 Eylül itibarıyla geçerlidir. Bilgi notu numaraları, tutarlar ve belge listeleri güncellenebilir — başvurudan önce temsilciliğin kendi sayfasını esas al.*
MD;

        $deBody = <<<'MD'
Die bedingte Zulassung ist da, und beim Visum stellen sich immer dieselben zwei Fragen: **„Beantrage ich 36F oder 39F?"** und **„Verlangt die Auslandsvertretung A1 oder B1?"**

Räumen wir zuerst den verbreitetsten Irrtum aus: **39F ist kein Gesetzesparagraf.** Im Aufenthaltsgesetz gibt es keinen „§ 39f" (§ 39 betrifft die Zustimmung zur Beschäftigung). **36F und 39F sind Merkblattnummern der deutschen Auslandsvertretungen in der Türkei** — also Überschriften von Unterlagenlisten, keine Rechtsgrundlage.

Die Rechtsgrundlage lautet: Der Aufenthaltszweck des Studiums **umfasst auch studienvorbereitende Maßnahmen** (§ 16b AufenthG) — dazu gehört der studienvorbereitende Sprachkurs. Ein Sprachkurs ohne Studienbezug fällt dagegen unter § 16f.

## Welches Merkblatt passt zu dir?

| Merkblatt | Inhalt | Für wen |
|---|---|---|
| **36F** | Studienvorbereitender Sprachkurs | **Wer eine bedingte Zulassung hat und zuerst den Sprachkurs besucht** |
| **39F** | Bachelor mit Zulassung und Sprachkenntnissen | Wer die Sprachvoraussetzung **bereits erfüllt** und direkt studiert |

Ist deine Auflage also weiterhin das Sprachniveau, suchst du die Liste zu **36F**. Da sich Nummern ändern können, prüfe vor der Antragstellung die **aktuelle Merkblattliste deiner Vertretung** — der Rahmen in diesem Artikel bleibt, die Nummern nicht zwingend.

## „A1 oder B1?" — die ehrliche Antwort

Anders als die apodiktischen Sätze im Netz enthält das Merkblatt für Studienbewerber **keine Zeile, die für das Visum ein bestimmtes Zertifikatsniveau vorschreibt.** Verbindlich ist dies:

> Ausreichende Deutschkenntnisse musst du **an der deutschen Hochschule vor Studienbeginn** durch eine Sprachprüfung (DSH oder TestDaF) nachweisen.

Was passiert in der Praxis? Die Vertretungen wollen die **Anmeldung zum Sprachkurs oder eine Bestätigung der Sprachschule** in der Akte sehen und dürfen bei der Beurteilung auch vorhandene Sprachnachweise heranziehen; es besteht Ermessensspielraum. Also:

- **Ein bestimmtes Niveau schreibt die offizielle Liste nicht vor.**
- **Ein A1/A2-Zertifikat stärkt die Akte trotzdem** — es beantwortet die Frage, ob du wirklich zum Sprachenlernen kommst.
- **Die eigentliche Hürde ist die Hochschulprüfung** (DSH/TestDaF). Die Prüfungstypen vergleichen wir im [TestDaF-DSH-telc-Artikel](/de/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in-de).

Kurz: „Reicht A1?" hängt von der Vertretung ab. „Muss ich DSH oder TestDaF bestehen?" hat dagegen eine klare Antwort: **ja.**

## Drei Risiken, über die selten jemand spricht

Im Merkblatt stehen drei Hinweise, die auf Beratungsseiten kaum auftauchen:

**1. Die Sprachprüfung darf nur einmal wiederholt werden.** Wer DSH oder TestDaF auch im zweiten Anlauf nicht besteht, **verliert die vorläufige Zulassung** und kann sich nicht einschreiben. Dann heißt es: ein weiteres Semester Sprachkurs und erneut zur Prüfung.

**2. Nach dem Erwerb der Sprachkenntnisse ist die „alte" vorläufige Zulassung nicht mehr gültig.** Du musst dich **erneut bewerben**. Die Annahme „ich habe ja die Zulassung, nach dem Kurs starte ich automatisch" stimmt nicht.

**3. Nicht jede Hochschule bietet Deutschkurse an.** Manche nur für Fortgeschrittene, an manchen sind die Kurse **kostenpflichtig** (das Merkblatt nennt München als Beispiel), und einige akzeptieren ohnehin keine Bewerbungen ohne Sprachnachweis. Frag deine Hochschule **schriftlich**: Gibt es einen Kurs, was kostet er, wie viele Plätze?

## Englischsprachiger Master plus bedingte Zulassung: kein Widerspruch

Die Antwort auf „Mein Studium ist auf Englisch, warum Deutsch?" steht im Zulassungsbescheid. Die richtige Frage lautet: **Worin besteht die Auflage?**

- **Ist die Auflage das Sprachniveau:** dann reist du faktisch zum Sprachkurs ein, und die Akte wird entsprechend aufgebaut (Rahmen 36F).
- **Ist die Auflage etwas anderes** (fehlende Unterlagen, Notendurchschnitt, Vorkurs, VPD): das ist keine Sprachkursakte; du weist nach, wie du diese Auflage erfüllst.
- **Gibt es keine Auflage:** dann brauchst du keinen Deutschnachweis; dein Englischnachweis und die reguläre Studierendenvisum-Akte genügen. Schritt für Schritt im [Studentenvisum-Leitfaden](/de/blog/germany-student-visa-2026-application-steps-documents-rejection-de).

## Die offizielle Unterlagenliste

Die Kernpunkte aus dem Merkblatt:

| Unterlage | Details |
|---|---|
| **Finanzierungsnachweis** | Mindestens **992 € monatlich**. Entweder ein **Sperrkonto** in Deutschland (jährlicher BAföG-Höchstsatz **11.904 €**, monatlich 1/12 verfügbar) oder eine **Verpflichtungserklärung nach §§ 66-68 AufenthG** |
| Detail zur Verpflichtungserklärung | Sie muss die Angabe **„zum Sprachkurs/Studium"** enthalten — fehlt sie, kommt die Akte zurück |
| **Anmeldung zum Sprachkurs** | Kursanmeldung oder Bestätigung der Sprachschule bzw. Universität |
| **Reisepass** | Mindestens **ein Jahr** gültig (nicht sechs Monate) |
| **Hochschulzugangsberechtigung** | Lise Diploması und ÖSYM-Nachweis — beglaubigte Kopie und Übersetzung |
| **Antragsformular** | **Auf Deutsch** ausgefüllt, in zweifacher Ausführung, drei Lichtbilder, persönliche Abgabe |

Anbieter und Eröffnungszeiten für das Sperrkonto findest du in unserem [Sperrkonto-Ratgeber](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de).

## Ablauf und Dauer

Die Vertretung prüft die Akte und leitet sie an die zuständige **Ausländerbehörde** in Deutschland weiter. Laut Merkblatt wird das Visum nach Abschluss der Ermittlungen in der Regel innerhalb von **vier bis sechs Wochen** erteilt und ist meist **sechs Monate** gültig. Die Wartezeit auf den Termin kommt obendrauf — zur Terminstrategie siehe unseren [Ratgeber](/de/blog/germany-consulate-visa-appointment-2026-waiting-times-and-city-strategy-de).

Die Umwandlung in eine Aufenthaltserlaubnis nach der Einreise übernimmt die Ausländerbehörde, die dafür die Zulassungs- bzw. Bewerbungsunterlagen sehen will. Stockt es dort, erklärt unser Artikel zu [Verzögerungen](/de/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-de) deine Rechte.

## Häufige Fragen

### Meine Unterlagen liegen auf Englisch vor, nicht auf Deutsch. Ein Problem?
Das Merkblatt verlangt für die Hochschulzugangsberechtigung **beglaubigte Kopie und Übersetzung** und ein **auf Deutsch** ausgefülltes Antragsformular. Hochschulkorrespondenz auf Englisch wird meist akzeptiert; für amtliche türkische Dokumente ist eine beglaubigte Übersetzung der sichere Weg. Maßgeblich ist die Liste deiner Vertretung.

### Ist A1 für das Visum wirklich Pflicht?
In der offiziellen Liste steht kein Niveau; verlangt werden Kursanmeldung und Finanzierung. Ein Zertifikat stärkt die Akte dennoch. Verbindlich ist allein die Hochschulprüfung (DSH/TestDaF).

### 36F oder 39F?
Besteht die Auflage weiterhin im Sprachniveau: **36F**. Erfüllst du die Sprachvoraussetzung bereits und startest direkt ins Studium: **39F**. Nummern können sich ändern — prüfe die aktuelle Liste.

### Ich habe B1 — wird im Gespräch trotzdem Deutsch gefragt?
Möglich. Die Entscheidenden haben Ermessensspielraum und wollen sehen, ob das Zertifikat im Alltag trägt. Die Fragen sind meist einfach: warum Deutschland, welche Stadt, welches Programm. Wenn es hakt, darfst du um Englisch bitten.

### Was passiert, wenn ich die Sprachprüfung nicht bestehe?
Das Merkblatt ist eindeutig: DSH/TestDaF darf **einmal** wiederholt werden; scheiterst du erneut, **verfällt die vorläufige Zulassung** und eine Einschreibung ist nicht möglich. Es folgt ein weiteres Semester Sprachkurs und ein neuer Prüfungsversuch.

### Starte ich nach dem Sprachkurs automatisch ins Studium?
Nein. Nach dem Erwerb der Sprachkenntnisse verliert die alte vorläufige Zulassung ihre Gültigkeit, und du musst dich **erneut bewerben**. Plane den Zeitrahmen entsprechend.

## Fazit und ehrlicher Rat

Die Verwirrung entsteht, weil in Foren kursierende Nummern für Gesetzesparagrafen gehalten werden. Der richtige Rahmen ist einfach: **Die Nummern sind Unterlagenlisten, die Rechtsgrundlage ist § 16b, und die verbindliche Sprachhürde ist die Hochschulprüfung.**

Kläre vor dem Antrag drei Dinge schriftlich: **Wie lautet die Auflage im Zulassungsbescheid? Bietet die Hochschule einen Sprachkurs an, und kostet er etwas? Was steht in der aktuellen Unterlagenliste deiner Vertretung?** Mit diesen drei Antworten beantwortet sich „A1 oder B1" von selbst.

*Unterlagenliste und Beträge stützen sich auf das Merkblatt der deutschen Auslandsvertretungen in der Türkei für Studienbewerber, Stand 18.08.2025, und gelten mit Stand September 2026. Merkblattnummern, Beträge und Listen können sich ändern — maßgeblich ist die Seite deiner Vertretung.*
MD;

        $enBody = <<<'MD'
Your conditional admission has arrived, and the same two questions come up about the visa: **"Do I apply under 36F or 39F?"** and **"Does the mission want A1 or B1?"**

Let us clear up the most widespread error first: **39F is not a section of the law.** There is no "Section 39f" in the Residence Act (Section 39 concerns approval for employment). **36F and 39F are information-sheet (Merkblatt) numbers used by the German missions in Türkiye** — headings on document checklists, not legal bases.

The legal basis is this: the residence purpose of study **also covers study-preparatory measures** (Section 16b AufenthG), which includes a study-preparatory language course. A language course unconnected to studies falls under Section 16f instead.

## Which information sheet applies to you?

| Sheet | Scope | For whom |
|---|---|---|
| **36F** | Study-preparatory language course | **Those with a conditional admission who attend a language course first** |
| **39F** | Bachelor with admission and language skills | Those who **already meet** the language requirement and start their programme directly |

So if your condition is still the language level, the list you want is **36F**. Numbers do change from time to time, so check **your mission's current list of information sheets** before applying — the framework in this article is stable, the numbers are not necessarily.

## "A1 or B1?" — the honest answer

Contrary to the categorical statements circulating online, the missions' information sheet for study applicants contains **no line prescribing a particular certificate level for the visa.** What is binding is this:

> You must prove sufficient German **at the German university, before your studies begin**, through a language examination (DSH or TestDaF).

What happens in practice? Missions want to see your **language course registration or a confirmation from the school** in the file, and they may take any language certificate you hold into account; the decision involves discretion. So:

- **The official list does not impose a specific level.**
- **An A1/A2 certificate still strengthens your file** — it answers the question of whether you are genuinely coming to learn the language.
- **The real hurdle is the university's exam** (DSH/TestDaF). We compare the exam types in our [TestDaF, DSH and telc guide](/en/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in-en).

In short: "Is A1 enough?" depends on the mission. "Do I have to pass DSH or TestDaF?" has a firm answer: **yes.**

## Three risks nobody mentions

The information sheet contains three warnings that rarely appear on consultancy websites:

**1. You may retake the language exam only once.** If you fail DSH or TestDaF and fail the retake, **your provisional admission lapses** and you cannot enrol. From there it means another semester of language courses and another attempt.

**2. Once you have acquired the language skills, your "old" provisional admission is no longer valid.** You have to **apply again**. The assumption "I already hold the admission, so I will start automatically after the course" is wrong.

**3. Not every university offers German courses.** Some only run advanced courses, at some the courses are **charged for** (the sheet names Munich as an example), and some do not accept applicants without a language certificate at all. Ask your university **in writing**: is there a course, what does it cost, how many places are there?

## An English-taught master's with a conditional admission: not a contradiction

The answer to "my programme is in English, so why German?" is in your admission letter. The right question is: **what exactly is the condition?**

- **If the condition is a language level:** you are in practice travelling for a language course, and your file is built accordingly (the 36F framework).
- **If the condition is something else** (missing documents, a grade average, a preparatory module, a VPD): this is not a language-course file; you show how you will meet that condition instead.
- **If there is no condition and the admission is direct:** you need no German certificate; your English test result and a standard student visa file suffice. Step by step in our [student visa guide](/en/blog/germany-student-visa-2026-application-steps-documents-rejection-en).

## The official document list

The core items from the information sheet:

| Document | Detail |
|---|---|
| **Proof of funding** | At least **€992 per month**. Either a **blocked account** in Germany (the annual BAföG maximum of **€11,904**, released at one twelfth per month) or a **declaration of commitment under Sections 66–68 AufenthG** |
| Detail on the declaration | It must contain the wording **"zum Sprachkurs/Studium"** — without it the file comes back |
| **Language course registration** | Course registration or confirmation from the language school or university |
| **Passport** | Valid for at least **one year** (not six months) |
| **University entrance qualification** | Your school diploma and ÖSYM documentation — certified copy plus translation |
| **Application form** | Completed **in German**, in duplicate, with three photographs, submitted in person |

For providers and opening times for the blocked account, see our [Sperrkonto guide](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en).

## Process and timing

The mission reviews the file and forwards it to the relevant **immigration office** in Germany. According to the information sheet, once the necessary checks are complete the visa is usually issued within **four to six weeks** and is normally valid for **six months**. Waiting for an appointment comes on top of that — for city strategy, see our [appointment guide](/en/blog/germany-consulate-visa-appointment-2026-waiting-times-and-city-strategy-en).

Converting the visa into a residence permit after arrival is the immigration office's job, and it will want to see your admission or application documents. If things stall there, our article on [delays](/en/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en) explains your rights.

## Frequently asked questions

### My documents are in English, not German. Is that a problem?
The information sheet requires a **certified copy and translation** of your university entrance qualification and an application form completed **in German**. Correspondence from universities in English is generally accepted; for official Turkish documents a sworn translation is the safe route. Your mission's own list is decisive.

### Is A1 really mandatory for the visa?
The official list states no level; what it requires is course registration and funding. A certificate still strengthens your file. The only binding threshold is the university's DSH/TestDaF exam.

### 36F or 39F?
If your condition is still the language level: **36F**. If you already meet the language requirement and start your programme directly: **39F**. Numbers can change — check the current list.

### I have B1 — will they still ask questions in German?
They may. Decision-makers have discretion and want to see whether the certificate holds up in conversation. The questions are usually simple: why Germany, which city, which programme. If you get stuck, you may ask to continue in English.

### What if I fail the language exam?
The sheet is explicit: DSH/TestDaF may be retaken **once**; fail again and **your provisional admission lapses**, so you cannot enrol. Another semester of courses and a further attempt follow.

### Will I move into my programme automatically after the course?
No. Once you have the language skills, the old provisional admission loses its validity and you must **apply again**. Plan your timeline around that.

## Conclusion and honest advice

The confusion comes from treating numbers that circulate in forums as sections of the law. The correct framework is simple: **the numbers are document checklists, the legal basis is Section 16b, and the binding language threshold is the university's exam.**

Before applying, settle three things in writing: **what exactly the condition in your admission letter is**, **whether your university runs a language course and what it costs**, and **what your mission's current document list says.** With those three answers, "A1 or B1" answers itself.

*The document list and amounts here follow the information sheet for study applicants published by the German missions in Türkiye, version of 18 August 2025, and are current as of September 2026. Sheet numbers, amounts and checklists can change — treat your mission's own page as authoritative.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'conditional-admission-visa-germany-which-merkblatt-and-language-level',
                'title' => 'Şartlı Kabulle Vize: 36F mi 39F mi, A1 mi B1 mi?',
                'excerpt' => '"39f" diye bir kanun maddesi yok — bunlar temsilciliğin bilgi notu numaraları. Hangi durumda 36F hangi durumda 39F, resmî belge listesi (992 €/ay, 11.904 € Sperrkonto, pasaport 1 yıl) ve dil seviyesi sorusunun dürüst cevabı.',
                'meta_title' => 'Şartlı Kabul Vizesi: 36F mi 39F mi, Hangi Dil Seviyesi?',
                'meta_description' => 'Şartlı kabulle vize: 36F/39F farkı, "39f maddesi" yanılgısı, resmî belge listesi, 992 €/ay finansman ve A1-B1 sorusunun gerçek cevabı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'conditional-admission-visa-germany-which-merkblatt-and-language-level-de',
                'title' => 'Visum mit bedingter Zulassung: 36F oder 39F — und welches Sprachniveau?',
                'excerpt' => 'Einen „§ 39f" gibt es nicht — 36F und 39F sind Merkblattnummern der Auslandsvertretungen. Wann welches Merkblatt gilt, die offizielle Unterlagenliste (992 €/Monat, 11.904 € Sperrkonto, Pass mindestens ein Jahr) und die ehrliche Antwort zur Sprachfrage.',
                'meta_title' => 'Bedingte Zulassung und Visum: 36F oder 39F, welches Niveau?',
                'meta_description' => 'Visum bei bedingter Zulassung: Unterschied 36F/39F, der Irrtum „§ 39f", Unterlagenliste, 992 € monatlich und die Wahrheit zur A1-B1-Frage (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'conditional-admission-visa-germany-which-merkblatt-and-language-level-en',
                'title' => 'Visa with a Conditional Admission: 36F or 39F — and Which Language Level?',
                'excerpt' => 'There is no "Section 39f" — 36F and 39F are information-sheet numbers used by the German missions. Which sheet applies when, the official document list (€992 a month, €11,904 blocked account, a passport valid a year) and an honest answer on the language question.',
                'meta_title' => 'Conditional Admission Visa: 36F or 39F, and Which Level?',
                'meta_description' => 'Visa with conditional admission: 36F vs 39F, the "Section 39f" myth, the official document list, €992 monthly funding and the truth about A1 vs B1 (2026).',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'], 'content_html' => $html,
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published' => true, 'published_at' => now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'conditional-admission-visa-germany-which-merkblatt-and-language-level',
            'conditional-admission-visa-germany-which-merkblatt-and-language-level-de',
            'conditional-admission-visa-germany-which-merkblatt-and-language-level-en',
        ])->delete();
    }
};
