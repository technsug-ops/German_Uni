<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+EN+DE): Alman üniversitelerinde not sistemi ve sınav tekrar hakkı.
 *
 * Ana mesaj: Almanya'da tek bir ulusal "3 hak" kuralı yok; bağlayıcı olan ilgili Prüfungsordnung, eyalet
 * hukuku ve üniversite/program kuralları. 10 kurum örneği (TUM, LMU, RWTH, TU Berlin, KIT, Uni Hamburg WiSo,
 * Goethe Frankfurt WiWi, Uni Köln WiSo, TH Köln, FU Berlin BWL) program-özel uyarıyla.
 *
 * Doğrulanmış kaynaklar (26.09.2026): HRK/KMK Diploma Supplement (not ölçeği, 4 = en düşük geçer), KMK
 * Strukturvorgaben 2010, KMK modifiye Bavyera formülü (kesme, yuvarlama yok); HG NRW §§ 50/51/64, HmbHG
 * §§ 42/44/65, BayHIG Art. 84/91/94, HessHG § 63; § 16b, § 7(2), § 20 AufenthG + BMI Anwendungshinweise 06/2024
 * (oturum: otomatik kayıp yok, kısaltma takdire bağlı, program değişikliği yeni izinle).
 * Not dönüştürücü yalnız "yol gösterici" olarak anılır (araç koduna dokunulmadı — backlog).
 * bachelor-vs-master ve are-german-universities-hard yazılarına bilinçli olarak link verilmedi.
 * [n] işaretleri Kaynaklar başlığının HeadingPermalink id'sine bağlanır (tez rehberindeki kalıp).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd74edd14-c2c9-4ded-8a4f-607da5efe754';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Kısa cevap: Almanya'da sınav tekrar hakkı için ülke genelinde geçerli tek bir "üç hak" kuralı yoktur. Kaç kez tekrar edebileceğinizi, hangi sürede tekrar etmeniz gerektiğini ve kalırsanız ne olacağını kendi sınav yönetmeliğiniz (Prüfungsordnung), eyaletinizin yükseköğretim yasası ve programınızın kuralları belirler. Bu rehberde Alman not sistemini, tekrar haklarını ve bir sınavdan kaldığınızda atmanız gereken adımları 10 üniversiteden gerçek örneklerle anlatıyoruz.

> **Son güncelleme:** Eylül 2026 · Sınav kuralları eyalete, üniversiteye, fakülteye ve sınav yönetmeliğine göre değişir. · **⚖️ Resmî kural** = yasa veya yönetmelikteki düzenleme, **💡 Pratik öneri** = deneyime dayalı tavsiye · Örnekler Eylül 2026'da 10 Alman üniversitesinde kontrol edildi; bu evrensel bir kural değildir.
>
> **Your Prüfungsordnung and your Prüfungsamt have the final word.**
> Türkçesi: Son sözü her zaman sınav yönetmeliğiniz ve sınav ofisiniz (Prüfungsamt) söyler.

## Almanya'da üniversite not sistemi nasıl işler

Almanya'da üniversite not sistemi Türkiye'dekinin tersine çalışır: Küçük sayı iyi, büyük sayı kötüdür. 1,0 en iyi not, 5,0 ise başarısız nottur.

**⚖️ Resmî kural:** HRK ve KMK'nın Diploma Supplement (diploma eki) şablonuna göre Alman not sistemi genellikle beş basamaktan oluşur: sehr gut (1, pekiyi), gut (2, iyi), befriedigend (3, orta), ausreichend (4, yeterli) ve nicht ausreichend (5, yetersiz). Ara notlar verilebilir; en düşük geçer not ausreichend (4) seviyesidir [\[2\]](#content-kaynaklar).

Ara basamakları KMK değil, üniversitelerin sınav yönetmelikleri belirler. İncelediğimiz 10 kurumun tamamında tipik basamaklar şunlardır: 1,0 · 1,3 · 1,7 · 2,0 · 2,3 · 2,7 · 3,0 · 3,3 · 3,7 · 4,0 · 5,0. Bu kurumların birçoğu 0,7, 4,3 ve 4,7 notlarını açıkça hariç tutar; TUM'un genel yönetmeliği (APSO) ise yalnızca 0,7 ve 5,3'ü hariç tutar [\[12\]](#content-kaynaklar).

KMK'nın 2010 tarihli ortak çerçevesine göre (bugün yerini akreditasyon çerçevesine bırakmıştır) mezuniyet notunun yanında göreli bir not, yani ECTS notu da gösterilir [\[1\]](#content-kaynaklar).

Almanca belgelerde sık göreceğiniz birkaç terim:

- **Prüfungsordnung:** Sınav yönetmeliği. Programınıza ait, yasal bağlayıcılığı olan belge.
- **Prüfungsamt:** Sınav ofisi. Kayıtları, notları ve kararları yöneten birim.
- **bestanden / nicht bestanden:** Geçti / kaldı.

## 1,0'dan 5,0'a notların anlamı

| Not | Almanca adı (Türkçesi) | Anlamı |
|---|---|---|
| 1,0 / 1,3 | sehr gut (pekiyi) | Üstün performans |
| 1,7 / 2,0 / 2,3 | gut (iyi) | Ortalamanın belirgin biçimde üstünde |
| 2,7 / 3,0 / 3,3 | befriedigend (orta) | Ortalama beklentiyi karşılıyor |
| 3,7 / 4,0 | ausreichend (yeterli) | Eksikleri var ama asgari şartları karşılıyor |
| 5,0 | nicht ausreichend (yetersiz) | Başarısız; Köln WiSo'da "mangelhaft" (zayıf) diye adlandırılır |

> Bu örnekler belirli programlara veya sınav yönetmeliklerine aittir. Kendi sınav yönetmeliğiniz (Prüfungsordnung) farklı olabilir. (These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.)

*Yönetmelikler Eylül 2026'da kontrol edildi.*

Modül notları ve genel not ise genellikle bantlarla ifade edilir. Tipik örnek (ör. TUM ve Kiel): 1,5'e kadar sehr gut; 1,6–2,5 gut; 2,6–3,5 befriedigend; 3,6–4,0 ausreichend; 4,0'ın üstü nicht ausreichend.

**⚖️ Resmî kural (ortalama hesabı):** İncelediğimiz kurumların çoğu ortalamada virgülden sonraki ilk basamağı alır, gerisini yuvarlamadan atar (TUM, RWTH, TU Berlin, KIT, Frankfurt, Köln, TH Köln, FU Berlin). Yani 2,36 ortalama 2,3 olur, 2,4 değil. LMU ve Uni Hamburg WiSo ise iki ondalık basamak tutar.

Türk notunuzu Alman sistemine çevirmek isterseniz KMK'nın yabancı diplomalar için belirlediği "modifiye Bavyera formülü" kullanılır: X = 1 + 3 × (Nmax − Nd) / (Nmax − Nmin). Burada Nmax en yüksek notu, Nmin en düşük **geçer** notu, Nd sizin notunuzu gösterir; sonuç bir ondalık basamakla ve yuvarlanmadan belirlenir [\[3\]](#content-kaynaklar). Üniversiteler bu formülü sıkça kullanır. [Not dönüştürücümüz](/tr/tools/grade-converter) size bu formüle dayalı bir fikir verir.

**💡 Pratik öneri:** Not dönüştürücüler yalnızca yol gösterir; üniversiteniz veya uni-assist kendi dönüşüm kurallarını uygulayabilir. Resmî not her zaman kurumun hesapladığı nottur.

## Almanya'da geçme notu: 4,0 ve istisnaları

Almanya'da geçme notu, incelediğimiz 10 kurumun tamamında 4,0 ya da notsuz derslerde "bestanden" (geçti) ibaresidir. Ancak "4,0 aldım, kesin geçtim" demeden önce yönetmeliğinizi okuyun; istisnalar var:

- **Alt değerlendirmeler:** RWTH Aachen Informatik'te bir modüldeki her alt değerlendirmenin ve her sınavın ayrı ayrı en az 4,0 olması gerekir [\[14\]](#content-kaynaklar). Yani modül ortalamanız 4,0'ın altında olsa bile tek bir parçadan kalmak modülü geçmenizi engelleyebilir.
- **Çoktan seçmeli sınavlar:** FU Berlin'de çoktan seçmeli sınavlar puanların %50'siyle geçilir. Göreli bir kural da vardır, ancak baraj hiçbir zaman %40'ın altına inmez [\[21\]](#content-kaynaklar).
- **Notsuz ödevler (Studienleistungen):** Bunlar yalnızca "bestanden" veya "nicht bestanden" olarak değerlendirilir; sayısal not yoktur.

**💡 Pratik öneri:** Modül tanımında (Modulhandbuch) sınavın hangi parçalardan oluştuğuna ve her parça için ayrı bir baraj olup olmadığına bakın.

## "Üç sınav hakkı" efsanesi

Türk öğrenciler arasında en sık duyulan cümle, "Almanya'da her sınav için üç hakkın var" cümlesidir. Bu genelleme yanlıştır. Almanya'da sınav tekrar hakkının sayısı yasa ile ülke genelinde sabitlenmemiştir.

İncelediğimiz 10 kurumdaki tablo şöyle:

- **2 hak:** KIT; Goethe Frankfurt WiWi'de seçmeli modüller [\[16\]](#content-kaynaklar)[\[18\]](#content-kaynaklar)
- **3 hak:** RWTH, Uni Hamburg WiSo, Köln WiSo, TH Köln, FU Berlin BWL, Frankfurt'ta zorunlu modüller [\[14\]](#content-kaynaklar)[\[17\]](#content-kaynaklar)[\[18\]](#content-kaynaklar)[\[19\]](#content-kaynaklar)[\[20\]](#content-kaynaklar)[\[21\]](#content-kaynaklar)
- **4 hak:** TU Berlin, dördüncüsü zorunlu danışmanlık görüşmesinden sonra [\[15\]](#content-kaynaklar)
- **Sınırsız ama süreye bağlı:** TUM ve LMU [\[12\]](#content-kaynaklar)[\[13\]](#content-kaynaklar)
- **Başvuruyla ek haklar:** Köln WiSo ve TH Köln [\[19\]](#content-kaynaklar)[\[20\]](#content-kaynaklar)

Yönlendirme ve temel sınavlarda (Orientierungsprüfung, Grundlagen- ve Orientierungsprüfung / GOP) hak sayısı çoğu zaman yalnızca ikidir. Tez için genellikle toplam iki deneme vardır (TU Berlin'de üç).

**⚖️ Resmî kural (eyalet yasalarından örnekler):**

- **NRW:** Yükseköğretim yasası (HG § 64), tekrar sayısını ve koşullarını sınav yönetmeliğinin düzenlemesini ister; yasada asgari bir sayı yoktur [\[4\]](#content-kaynaklar).
- **Hamburg:** HmbHG § 65'e göre öğrenim sürecindeki sınavlar en az iki kez tekrar edilebilir; tez bir kez, ikinci kez ancak gerekçeli istisnai durumlarda [\[5\]](#content-kaynaklar).
- **Berlin:** TU Berlin gibi üniversitelerin uygulamasında sınavlar ilke olarak iki kez tekrar edilebilir, zorunlu bölüm danışmanlığından (Studienfachberatung) sonra bir hak daha verilir [\[15\]](#content-kaynaklar).
- **Bavyera:** BayHIG Art. 84'e göre tekrarlar normalde altı ay içinde mümkün olmalıdır; yönetmelik serbest bir deneme hakkı (freier Prüfungsversuch) öngörebilir, ama tez için değil [\[6\]](#content-kaynaklar).

## Erstversuch, Zweitversuch, Drittversuch

Almanca yazışmalarda denemeleriniz şöyle adlandırılır:

- **Erstversuch:** İlk deneme. Sınava ilk kez girdiğiniz an.
- **Zweitversuch:** İkinci deneme, yani ilk tekrar.
- **Drittversuch:** Üçüncü deneme. Hak sayısı üç olan programlarda son şanstır; bu yüzden "üçüncü sınav hakkı" Almanya'da çoğu öğrenci için ciddi bir eşiktir.
- **Wiederholungsprüfung:** Tekrar sınavı; ikinci ve sonraki denemelerin genel adı.
- **Viertversuch / Zusatzversuch:** Dördüncü veya ek deneme; yalnızca yönetmelik öngörüyorsa vardır (ör. TU Berlin'de danışmanlık sonrası, Köln ve TH Köln'de ek haklar) [\[15\]](#content-kaynaklar)[\[19\]](#content-kaynaklar)[\[20\]](#content-kaynaklar).

**⚖️ Resmî kural (örnek):** KIT Informatik'te yalnızca iki deneme vardır. İkinci bir tekrar sadece istisnai durumlarda başvuruyla mümkündür, karar gerektiğinde rektörlük (Präsidium) düzeyine kadar çıkar; yönlendirme sınavları için bu yol yoktur [\[16\]](#content-kaynaklar).

**💡 Pratik öneri:** Bir sınav tekrarının kaçıncı deneme sayıldığını öğrenci portalınızdaki transkriptten (ör. "Versuch 2") kontrol edin. Emin değilseniz Prüfungsamt'a yazılı sorun.

## Wiederholungsprüfung (tekrar sınavı) nasıl işler

Tekrar sınavı, çoğu zaman bir sonraki sınav döneminde veya dönem başındaki telafi sınavında yapılır. İki nokta sık gözden kaçar:

**1. Kayıt:** Birçok yerde tekrar sınavı kendiliğinden gelmez; her deneme için yeniden kayıt yaptırmanız gerekir. Örneğin TUM'da her tekrar ayrı kayıt ister [\[12\]](#content-kaynaklar). Kayıt tarihlerini kaçırmak hakkınızı kaybettirebilir.

**2. Sınav biçimi değişebilir:** Tekrar sınavı her zaman ilk sınavla aynı formatta olmayabilir. Örnekler:

- Goethe Frankfurt WiWi'de sınav kurulu, yazılı tekrar sınavının yerine sözlü sınav yapılmasına karar verebilir [\[18\]](#content-kaynaklar).
- KIT'te yazılı tekrar sınavından kalırsanız otomatik olarak sözlü bir telafi sınavı yapılır [\[16\]](#content-kaynaklar).
- TU Berlin'de sınav yapan hoca, kalınan bir sınavdan sonra sözlü yeniden sınav önerebilir; bu bir hak değildir [\[15\]](#content-kaynaklar).

**⚖️ Resmî kural (Bavyera):** Tekrar sürelerini kendi kusurunuzla kaçırırsanız sınav "abgelegt und nicht bestanden" (girilmiş ve başarısız) sayılır [\[6\]](#content-kaynaklar).

## Sınavı ne zaman tekrar etmeniz gerekir

Almanya'da sınavdan kalınca ne olur sorusunun en kritik kısmı süredir. Tekrar hakkınız olsa bile süreyi kaçırmak, o hakkı fiilen yok edebilir.

**⚖️ Resmî kural (örnekler):**

- **TUM:** Normal modül sınavları sınırsız tekrar edilebilir, ama APSO § 10 dönem başına asgari kredi eşikleri koyar. Bu eşikleri tutturamazsanız sonuç kesin başarısızlıktır. Tekrar normalde yaklaşık altı ay içinde veya bir sonraki sınav tarihinde yapılır [\[12\]](#content-kaynaklar).
- **LMU Informatik:** Sınırsız deneme, ancak standart öğrenim süresi artı iki dönemin sonunda geçilmemiş sınav kesin başarısız sayılır [\[13\]](#content-kaynaklar).
- **KIT Informatik:** Tekrar, ilk başarısızlıktan sonraki dördüncü dönemin sınav döneminin sonuna kadar yapılmalıdır; aksi hâlde sınava girme hakkı kaybedilir [\[16\]](#content-kaynaklar).
- **TH Köln:** Ek deneme için sonucun açıklanmasından itibaren **bir ay** içinde başvurmanız gerekir; başvurmazsanız sınav kesin başarısız sayılır [\[20\]](#content-kaynaklar).
- **Bavyera genel:** Tekrarlar normalde altı ay içinde mümkün olmalıdır [\[6\]](#content-kaynaklar).
- **Goethe Frankfurt WiWi:** İlk tekrar dönem sonunda veya bir sonraki dönem başında; yönlendirme aşaması üç dönem içinde, derece dokuzuncu dönemin sonuna kadar tamamlanmalıdır [\[18\]](#content-kaynaklar).
- **FU Berlin BWL:** En az bir tekrar, izleyen ikinci döneme kadar sunulur [\[21\]](#content-kaynaklar).
- **RWTH Aachen:** Genel bir tekrar süresi bulamadık; sınavlar yılda en az iki kez sunulur [\[14\]](#content-kaynaklar).

**💡 Pratik öneri:** Sınav sonucunu gördüğünüz gün takviminize üç tarih yazın: tekrar sınavının kayıt dönemi, sınav tarihi ve varsa ek hak başvurusunun son günü.

## Not yükseltmek için sınav tekrarı (Notenverbesserung)

Geçtiğiniz bir sınavı daha iyi not için tekrar etmek (Notenverbesserung, not yükseltme) incelediğimiz kurumların çoğunda mümkün değildir. İstisnalar:

- **LMU Informatik:** Bir kez, bir sonraki olağan sınav tarihinde not yükseltmeye girilebilir; iyi olan sonuç sayılır [\[13\]](#content-kaynaklar).
- **TH Köln:** Derece başına dört ek hakkın ikisi, ilk denemede geçilmiş sınavların notunu yükseltmek için kullanılabilir; iyi olan not geçerlidir [\[20\]](#content-kaynaklar).
- **TUM:** Yalnızca program yönetmeliği izin veriyorsa; Informatik'te yok [\[12\]](#content-kaynaklar).

Bazı kurumlar farklı bir yol sunar: **RWTH**'de Bachelor öğrencileri standart sürede bitirirlerse 5–30 kredilik notu genel ortalamadan çıkarabilir (Informatik'te 30 krediye kadar) [\[14\]](#content-kaynaklar). **FU Berlin BWL**'de altı dönemde bitiren Bachelor öğrencileri, toplam 12 krediyi aşmayan en fazla iki notlu modülü notsuza çevirebilir [\[21\]](#content-kaynaklar).

## Freiversuch (sayılmayan deneme hakkı)

Freiversuch, kalırsanız hiç girilmemiş sayılan bir denemedir. Yani kalınan sınav, hak sayınızdan düşmez. Bu imkân yalnızca bazı yönetmeliklerde vardır ve koşulları sıkıdır.

**⚖️ Resmî kural (örnekler):**

- **LMU Informatik:** Standart öğrenim süresi içinde kalınan ilk deneme sayılmaz. GOP sınavları ve tez için geçerli değildir [\[13\]](#content-kaynaklar).
- **RWTH Aachen:** Bachelor öğrencileri, ilk üç dönemde girdikleri ve kaldıkları en fazla üç yazılı sınavın girilmemiş sayılması için **başvurabilir** [\[14\]](#content-kaynaklar). Kendiliğinden işlemez.
- **TU Berlin:** Bachelor'da ilk dönemde ilk kez kalınan modül sınavları girilmemiş sayılır [\[15\]](#content-kaynaklar).

Bavyera yasası tez için serbest deneme hakkını dışarıda bırakır [\[6\]](#content-kaynaklar).

**💡 Pratik öneri:** RWTH'deki gibi başvuru gerektiren durumlarda başvuru süresini kaçırmayın; Freiversuch'un otomatik işlediğini varsaymayın.

## Mündliche Ergänzungsprüfung (sözlü tamamlama sınavı)

Mündliche Ergänzungsprüfung, kalınan bir yazılı sınavdan sonra yapılan kısa bir sözlü sınavdır. Amaç, notu "geçer" seviyesine çıkarma şansı vermektir. Bu sınavda en iyi sonuç genellikle 4,0'dır; daha iyi not alamazsınız.

**⚖️ Resmî kural (örnekler):**

- **KIT:** Yazılı tekrar sınavından kalınırsa sözlü telafi sınavı otomatik yapılır; en iyi not 4,0 [\[16\]](#content-kaynaklar).
- **RWTH Aachen:** Yazılı bir sınavın ikinci tekrarından da kalınırsa **başvuruyla** sözlü tamamlama sınavı yapılır; sonuç yalnızca 4,0 veya 5,0 olabilir [\[14\]](#content-kaynaklar).
- **TU Berlin:** Sınav yapan hoca, kalınan herhangi bir sınavdan sonra sözlü yeniden sınav **önerebilir**; geçmek için 4,0 gerekir. Bu bir hak değildir [\[15\]](#content-kaynaklar).

## Farklı sınav türleri

Almanya'da "sınav" sadece yazılı sınav demek değildir. Sık karşılaşılan türler:

- **Klausur:** Yazılı sınav.
- **Mündliche Prüfung:** Sözlü sınav.
- **Hausarbeit / Seminararbeit:** Dönem ödevi / seminer makalesi.
- **Praktikum / Labor:** Uygulama veya laboratuvar çalışması.
- **Projekt:** Genellikle grup hâlinde yürütülen proje.

Hak sayısı konusunda önemli bir ayrım vardır: Notlu sınavlar (Prüfungsleistungen) ile notsuz ödevler (Studienleistungen). İncelediğimiz örneklerde notsuz ödevler çoğu zaman sınırsız veya birden fazla kez tekrar edilebilir; örneğin KIT, Goethe Frankfurt ve Köln'de [\[16\]](#content-kaynaklar)[\[18\]](#content-kaynaklar)[\[19\]](#content-kaynaklar). Notlu sınavlar ise yukarıdaki hak sınırlarına tabidir.

## Tez için tekrar hakkı

**⚖️ Resmî kural (örnekler):** Bachelor veya Master tezinden (Abschlussarbeit) kalırsanız genellikle yalnızca **bir** tekrar hakkınız vardır; tekrarda çoğu zaman yeni bir konu alırsınız (ör. TUM, Goethe Frankfurt) [\[12\]](#content-kaynaklar)[\[18\]](#content-kaynaklar). İstisnalar:

- **TU Berlin:** İki tekrar [\[15\]](#content-kaynaklar).
- **Uni Hamburg WiSo:** Bir tekrar; ikinci tekrar yalnızca gerekçeli istisnai durumlarda [\[17\]](#content-kaynaklar).
- **RWTH Aachen:** Bir tekrar; yeni tezin üç dönem içinde kaydedilmesi gerekir [\[14\]](#content-kaynaklar).
- **Köln WiSo:** Bir tekrar; tezde ek hak kullanılamaz [\[19\]](#content-kaynaklar).
- **TH Köln:** Tez için bir tekrar, kolokyum için de bir tekrar [\[20\]](#content-kaynaklar).

Tez sürecinin tamamı için [Almanya'da Bachelor ve Master tezi rehberimize](/tr/blog/bachelor-and-master-thesis-process-in-germany) bakın.

## Endgültig nicht bestanden nedir

"Endgültig nicht bestanden" (kesin olarak başarısız), zorunlu bir sınavda yönetmeliğinizin tanıdığı tüm denemeleri veya süreleri tükettiğiniz anlamına gelir. Bu, Almanca sınav ofisi yazışmalarındaki en ağır ifadedir.

**⚖️ Resmî kural:** Zorunlu bir modülde kesin başarısızlık genellikle o programda sınava girme hakkınızı (Prüfungsanspruch) kaybetmeniz demektir. Seçmeli modüllerde ise seçenek kaldığı sürece çoğu zaman başka bir modüle geçebilirsiniz; örneğin Hamburg WiSo, Goethe Frankfurt WiWi ve RWTH Informatik seçmelilerinde [\[14\]](#content-kaynaklar)[\[17\]](#content-kaynaklar)[\[18\]](#content-kaynaklar). Frankfurt'ta dikkat: Üçüncü bir seçmeli modülden de kesin olarak kalırsanız sınav hakkınız sona erer [\[18\]](#content-kaynaklar).

Önemli nokta: Kesin başarısızlık bir **karar** (Bescheid) ile bildirilir. Bu kararda itiraz yolları ve süreleri (Rechtsbehelfsbelehrung) yazar. Örneğin Hamburg WiSo, Goethe Frankfurt ve RWTH'de itiraz (Widerspruch) süresi bir aydır [\[14\]](#content-kaynaklar)[\[17\]](#content-kaynaklar)[\[18\]](#content-kaynaklar).

**💡 Pratik öneri:** Kararı aldığınız gün itiraz süresinin son gününü not edin. İtiraz etmeyi düşünüyorsanız [genel itiraz dilekçesi şablonumuzu](/tr/templates/widerspruch-bescheid) başlangıç noktası olarak kullanabilir, ama gerekçeyi kendi durumunuza göre yazmalısınız.

## Prüfungsanspruch'un (sınava girme hakkı) kaybı

Prüfungsanspruch, bir programda sınavlara girme ve derece alma hakkınızdır. Kesin başarısızlık veya süre aşımı bu hakkı sona erdirebilir. Örnekler:

- **KIT:** Tekrar süresini kaçırmak veya son denemede kalmak sınava girme hakkının kaybı demektir [\[16\]](#content-kaynaklar).
- **TUM:** Kredi eşiklerini tutturamamak kesin başarısızlığa yol açar [\[12\]](#content-kaynaklar).
- **LMU:** Standart süre artı iki dönemde geçilmeyen sınav kesin başarısız sayılır [\[13\]](#content-kaynaklar).

**Härtefall (hakkaniyet gerektiren zor durum)** kuralları ise programa özgüdür: Hamburg WiSo'da bu durumlar hakkında sınav kurulu başkanı karar verir [\[17\]](#content-kaynaklar); FU Berlin'de kurul, istisnai koşullar varsa sonuç açıklandıktan sonra bile kalınan **son** denemeden çekilmeye izin verebilir, bunun için üç ay içinde başvurmak gerekir [\[21\]](#content-kaynaklar).

## Üniversite değiştirmek

Kesin başarısızlıktan sonra aynı bölümü başka bir üniversitede okumak her zaman mümkün değildir; bu eyalete göre değişir. Ama "Almanya'da bir daha hiç okuyamazsınız" demek de yanlıştır.

**⚖️ Resmî kural (eyalet örnekleri):**

- **NRW (HG § 50):** Seçtiğiniz programda gerekli bir sınavdan Almanya'daki herhangi bir üniversitede kesin olarak kaldıysanız kayıt reddedilir. Benzer programlar ise yalnızca sınav yönetmeliği öngörüyorsa ("erhebliche inhaltliche Nähe", belirgin içerik yakınlığı) etkilenir [\[4\]](#content-kaynaklar).
- **Bavyera (BayHIG Art. 91):** Farklı bir programa geçmediğiniz sürece kayıt reddedilir [\[6\]](#content-kaynaklar).
- **Hamburg (HmbHG § 44):** Aynı programa kayıt olunamaz; başka bir programa ancak kalınan sınav konuları orada da zorunluysa engel çıkar [\[5\]](#content-kaynaklar).
- **Hessen (HessHG § 63):** Aynı veya içerik olarak benzer bir programa kayıt **reddedilebilir** [\[7\]](#content-kaynaklar).
- **KIT (Baden-Württemberg):** Aynı veya esas olarak aynı içerikteki benzer programlar etkilenir [\[16\]](#content-kaynaklar).
- **TU Berlin:** Başvuranlar, seçtikleri programda Almanya/AB/AEA'da gerekli sınavlardan kesin olarak kalmadıklarını beyan eder [\[15\]](#content-kaynaklar).

**Beyan ve belge yükümlülüğü:** LMU, kayıt engellerini kendiliğinden bildirmenizi ister [\[22\]](#content-kaynaklar). FU Berlin, üniversite değiştirirken bir Unbedenklichkeitsbescheinigung (engel yoktur belgesi: kesin olarak kalmadığınızı ve sınav hakkınızın devam ettiğini gösteren belge) talep eder [\[23\]](#content-kaynaklar). Goethe Frankfurt WiWi'de ise başka Alman üniversitelerinde aynı veya benzer sınavda kullandığınız başarısız denemeler de sayılır [\[18\]](#content-kaynaklar).

**💡 Pratik öneri:** Kesin başarısızlığı gizlemeye çalışmayın; kayıt beyanında yanlış bilgi vermek daha büyük sorun yaratır. Hedef üniversitenin öğrenci sekreterliğine (Studierendensekretariat) durumunuzu yazılı sorun.

## Bölüm değiştirmek

Başka bir bölüme geçmek çoğu zaman mümkündür. Engeller genellikle şu durumlarda ortaya çıkar:

- Kesin olarak kaldığınız sınav yeni programda da **zorunluysa** (ör. Hamburg) [\[5\]](#content-kaynaklar).
- Yeni program eskisine içerik olarak yakınsa ve yönetmelik bunu öngörüyorsa (ör. NRW) [\[4\]](#content-kaynaklar).

İçerik olarak uzak, farklı bir alan ise genellikle açıktır. Uygulama ağırlıklı bir Hochschule (uygulamalı bilimler üniversitesi) de alternatif olabilir; farkları [Hochschule, Universität ve FH karşılaştırmamızda](/tr/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany) anlattık. Seçenekleri görmek için [üniversiteler](/tr/universities) ve [programlar](/tr/programs) sayfalarımıza göz atabilirsiniz.

**💡 Pratik öneri:** Yeni programın sınav yönetmeliğini ve modül listesini inceleyin; kaldığınız dersin orada zorunlu olup olmadığını kontrol edin. Önceki derslerinizin tanınması (Anerkennung) için de başvurun.

## Exmatrikulation (kaydın silinmesi)

Exmatrikulation, üniversite kaydınızın silinmesidir. Yasalar bu konuda emredici bir dil kullanır: NRW HG § 51'de "ist zu exmatrikulieren" (kaydı silinir), ayrıca Bavyera Art. 94 ve Hamburg § 42 [\[4\]](#content-kaynaklar)[\[6\]](#content-kaynaklar)[\[5\]](#content-kaynaklar).

**⚖️ Resmî kural:** Buna rağmen Exmatrikulation, ayrı bir idari kararla (Bescheid) ve itiraz yolları belirtilerek uygulanır. Kararın itiraz edilebilir olduğunu bilin: Eyalet itiraz (Widerspruch) yolunu koruyorsa itiraz, aksi hâlde idare mahkemesinde dava açılır. Bavyera'da sınav kararları için itiraz veya doğrudan dava arasında seçim yapılabilir [\[6\]](#content-kaynaklar). Zamanlama örnekleri:

- **RWTH Aachen:** Karar kesinleştiği dönemin sonunda [\[14\]](#content-kaynaklar).
- **TU Berlin:** Resen (kendiliğinden başlatılan işlemle), en erken iki ay sonra; başka bir programa başvurursanız ertelenir [\[15\]](#content-kaynaklar).

Yani süreç kendiliğinden, bir gecede gerçekleşmez; arada bir karar ve itiraz imkânı vardır. Nedenler, sonuçlar ve geri dönüş yolları için [Exmatrikulation rehberimize](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) bakın.

## Oturum izniniz için ne anlama gelir

AB dışından gelen öğrenciler için en büyük endişe genellikle vizedir. Almanya'da öğrenci oturum izni § 16b AufenthG'ye dayanır.

**⚖️ Resmî kural:**

- **§ 16b(2):** Öğrenim amacına henüz ulaşılmadıysa ve makul bir süre içinde hâlâ ulaşılabilecekse izin uzatılır; yabancılar dairesi üniversitenin görüşünü alabilir [\[8\]](#content-kaynaklar).
- **§ 7(2) cümle 2:** Esaslı bir şart ortadan kalkarsa iznin süresi sonradan **kısaltılabilir**. Bu bir takdir yetkisidir, otomatik bir sonuç değildir [\[9\]](#content-kaynaklar).
- **§ 16b(6):** Öğrencinin sorumlu olmadığı nedenlerle izin geri alınacak veya kısaltılacaksa, başka bir kuruma başvurmak için dokuz aya kadar süre tanınır [\[8\]](#content-kaynaklar). Kesin başarısızlığın bu kapsama girip girmediği netleşmiş değildir.
- **BMI uygulama notları (06/2024):** Program veya öğrenim yeri değişikliği mümkündür; yeni bir oturum izni için başvurulması gerekir ve bu izne hak vardır. Öğrenim makul sürede bitirilebilmelidir (toplam 10 yıla kadar kalış) [\[10\]](#content-kaynaklar).
- **§ 20 AufenthG:** 18 aya kadar iş arama izni yalnızca öğrenimi **başarıyla** tamamladıktan sonra verilir [\[11\]](#content-kaynaklar).

Özetle: Bir sınavdan kalmak tek başına oturum izninizi sona erdirmez. Sınav başarısızlığı, öğrenimi makul sürede bitirmeniz şüpheli hâle geldiğinde veya kaydınız silindikten sonra oturum açısından önem kazanır.

**💡 Pratik öneri:** Yabancılar dairesine (Ausländerbehörde) erkenden ve yazılı olarak başvurun; somut bir plan sunun: yeni program, mesleki eğitim (Ausbildung) veya iş. Amaç değişikliği seçenekleri için [öğrenci vizesinden çalışma iznine geçiş (Zweckwechsel) rehberimize](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel), mezuniyet sonrası için [iş arama vizesi rehberimize](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates) bakın.

## Hastalık, rapor (Attest) ve sınavdan çekilme (Rücktritt)

Sınava hasta girmek, sınavdan kalmanın en yaygın nedenlerinden biridir. Oysa birçok yönetmelik gerekçesiz çekilmeye (Rücktritt) belli bir tarihe kadar izin verir.

**⚖️ Resmî kural (gerekçesiz çekilme örnekleri):**

- **RWTH Aachen:** Sınavdan 3 iş günü öncesine kadar; sonrasında hastalık için hekim raporu 3. iş gününe kadar [\[14\]](#content-kaynaklar).
- **TU Berlin:** 3 gün öncesine kadar; rapor 5 gün içinde [\[15\]](#content-kaynaklar).
- **Köln WiSo:** 2 hafta öncesine kadar [\[19\]](#content-kaynaklar).
- **TH Köln:** 1 hafta öncesine kadar [\[20\]](#content-kaynaklar).
- **KIT:** Yazılı sınavlarda bir gün öncesine kadar, sözlü sınavlarda 3 iş günü öncesine kadar [\[16\]](#content-kaynaklar).

Bu tarihten sonra yalnızca geçerli bir nedenle (ör. hastalık) ve gecikmeden bildirerek çekilebilirsiniz; genellikle hekim raporu (Attest) gerekir. **LMU**'da işveren için verilen normal iş göremezlik belgesi (Arbeitsunfähigkeitsbescheinigung) yeterli değildir; sınava girememe durumunu belirten bir hekim raporu istenir [\[13\]](#content-kaynaklar). Bazı kurullar resmî hekim veya güvenilir hekim (Vertrauensarzt) raporu isteyebilir.

**⚖️ Resmî kural:** İncelediğimiz tüm kurumlarda geçerli bir neden olmadan sınava girmemek 5,0 veya "nicht bestanden" demektir, yani bir deneme hakkınız yanar.

**💡 Pratik öneri:** [Hastalık bildirimi şablonumuz](/tr/templates/krankmeldung) genel bir bildirim içindir; Prüfungsamt'ın sınavdan çekilme formu değildir. Üniversitenizin kendi formunu kullanın. Uzun süreli hastalıkta [izin dönemi (Urlaubssemester) rehberimize](/tr/blog/urlaubssemester-taking-a-semester-off-in-germany) ve [izin dönemi dilekçe şablonumuza](/tr/templates/urlaubssemester-antrag) bakın.

## 10 üniversite karşılaştırması

| Üniversite / program örneği | Geçme notu | Hak sayısı | Tekrar süresi | Üçüncü / ek hak | Sözlü tamamlama sınavı | Freiversuch | Not yükseltme | Tez tekrarı | Kesin başarısızlığın sonucu |
|---|---|---|---|---|---|---|---|---|---|
| [TUM](/tr/universities/technische-universitat-munchen-partner-019ddbba), B.Sc. Informatik | 4,0 | Süre sınırı içinde sınırsız; GOP 2 | ~6 ay / sonraki tarih; kredi eşikleri | Sınırsız (kredi eşiğine bağlı) | Bulunamadı | Yok | Yok (Informatik) | 1, yeni konu | Karar + Exmatrikulation |
| [LMU](/tr/universities/ludwig-maximilians-universitat-munchen-q55044), B.Sc. Informatik | 4,0 | Süre sınırı içinde sınırsız; GOP 2 | Standart süre + 2 dönem | Sınırsız (süreye bağlı) | Bulunamadı | Var | Evet, bir kez | 1 | Yazılı karar, itiraz bilgisiyle |
| [RWTH Aachen](/tr/universities/rwth-aachen-university-partner-019de9ee), B.Sc. Informatik | 4,0 (her kısım ≥ 4,0) | 3 | Genel süre yok; yılda ≥ 2 sınav | 3. deneme normal hak | Başvuruyla, sonuç 4,0/5,0 | Benzeri: ilk 3 dönem, başvuruyla | Hayır (5–30 KP not çıkarma) | 1, 3 dönem içinde | Kesinleşince dönem sonunda Exmatrikulation |
| TU Berlin (AllgStuPO) | 4,0 | 3 + 1 | Genellikle bir sonraki düzenli sınav tarihi | 4. hak danışmanlık sonrası | Hoca önerebilir | 1. dönem (Bachelor) | Hayır | 2 | Resen Exmatrikulation, en erken 2 ay |
| [KIT](/tr/universities/karlsruher-institut-fur-technologie-q309988), B.Sc. Informatik | 4,0 | 2 | 4. dönem sınav dönemi sonu | Yalnızca istisnai başvuruyla | Otomatik, en fazla 4,0 | Yok | Hayır | 1 | Sınav hakkı kaybı; Exmatrikulation (LHG) |
| Uni Hamburg, WiSo B.Sc. | 4,0 | 3 | Mümkün olan ilk tarih | Härtefall: kurul başkanı | Yok | Yok | Hayır | 1 (+ istisnai 2.) | Karar; 1 ay itiraz |
| Goethe Frankfurt, WiWi B.Sc. | 4,0 | Zorunlu 3, seçmeli 2 | Dönem sonu / sonraki dönem başı | Seçmeli değiştirilebilir | Kurul kararıyla sözlü | Bu programda yok | Bu programda yok | 1, yeni konu | Karar + Exmatrikulation; 1 ay itiraz |
| Uni Köln, WiSo B.Sc. BWL | 4,0 | Modül başına 3 | Belirtilmedi | Derece başına 3 ek (+1, ≥ 140 KP) | Yok | Yok | Hayır | 1, ek hak yok | Programdan Exmatrikulation |
| TH Köln (RPO 2023) | 4,0 | 3 | Ek hak: 1 ay içinde başvuru | Derece başına 4 ek (başvuruyla) | Yok | Yok | Evet (ek haklardan 2'si) | 1 (+ kolokyum 1) | Karar + Exmatrikulation |
| FU Berlin, BWL | 4,0 (ÇS: %50) | 3 | En az bir tekrar 2. sonraki döneme kadar | Härtefall: son denemeden çekilme | Yok | Yok | Hayır (≤ 12 KP notsuza) | 1 | Karar + Exmatrikulation |

> Bu örnekler belirli programlara veya sınav yönetmeliklerine aittir. Kendi sınav yönetmeliğiniz (Prüfungsordnung) farklı olabilir. (These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.)

*Yönetmelikler Eylül 2026'da kontrol edildi.*

Kaynaklar: TUM [\[12\]](#content-kaynaklar), LMU [\[13\]](#content-kaynaklar), RWTH [\[14\]](#content-kaynaklar), TU Berlin [\[15\]](#content-kaynaklar), KIT [\[16\]](#content-kaynaklar), Hamburg [\[17\]](#content-kaynaklar), Frankfurt [\[18\]](#content-kaynaklar), Köln [\[19\]](#content-kaynaklar), TH Köln [\[20\]](#content-kaynaklar), FU Berlin [\[21\]](#content-kaynaklar). KP = kredi puanı (ECTS), ÇS = çoktan seçmeli.

## Dört öğrenci senaryosu

### Senaryo 1: İlk denemede kaldınız

- **Önce kontrol edilecek belge:** Sınav yönetmeliğinizdeki tekrar maddesi ve öğrenci portalındaki sonuç kaydı.
- **Prüfungsamt'a soru:** "Tekrar sınavına ayrıca kayıt olmam gerekiyor mu ve kayıt dönemi ne zaman?"
- **Süre:** Bir sonraki sınav dönemi; TUM'da normalde yaklaşık altı ay, KIT'te dördüncü dönemin sonu [\[12\]](#content-kaynaklar)[\[16\]](#content-kaynaklar).
- **Olası seçenekler:** Freiversuch varsa (ör. LMU, TU Berlin, RWTH'de başvuruyla) denemenin sayılmaması; sınav incelemesi (Klausureinsicht) [\[13\]](#content-kaynaklar)[\[14\]](#content-kaynaklar)[\[15\]](#content-kaynaklar).
- **Kaçınılacak hata:** "Nasılsa daha hakkım var" deyip tekrarı ertelemek.

### Senaryo 2: Drittversuch, yani son deneme önünüzde

- **Önce kontrol edilecek belge:** Son denemeden sonra sözlü tamamlama sınavı, ek hak veya Härtefall kuralı olup olmadığı.
- **Prüfungsamt'a soru:** "Bu son denemem mi? Kalırsam sözlü tamamlama sınavı veya ek hak var mı?"
- **Süre:** Çekilme son tarihi (ör. RWTH 3 iş günü, Köln 2 hafta öncesi) [\[14\]](#content-kaynaklar)[\[19\]](#content-kaynaklar).
- **Olası seçenekler:** RWTH'de başvuruyla sözlü tamamlama, TU Berlin'de danışmanlık sonrası dördüncü hak, Köln ve TH Köln'de ek haklar [\[14\]](#content-kaynaklar)[\[15\]](#content-kaynaklar)[\[19\]](#content-kaynaklar)[\[20\]](#content-kaynaklar).
- **Kaçınılacak hata:** Hasta veya hazırlıksızken sınava girmek; geçerli nedeniniz varsa zamanında ve raporla çekilin.

### Senaryo 3: Zorunlu modülden kesin olarak kaldınız

- **Önce kontrol edilecek belge:** Kesin başarısızlık kararı (Bescheid) ve içindeki itiraz bilgisi.
- **Prüfungsamt'a soru:** "Kararın kesinleşme tarihi nedir ve Exmatrikulation ne zaman uygulanır?"
- **Süre:** İtiraz süresi, örneğin Hamburg, Frankfurt ve RWTH'de bir ay [\[14\]](#content-kaynaklar)[\[17\]](#content-kaynaklar)[\[18\]](#content-kaynaklar).
- **Olası seçenekler:** İtiraz; FU Berlin'de istisnai koşullarda son denemeden çekilme (üç ay içinde) [\[21\]](#content-kaynaklar); başka bir bölüme geçiş.
- **Kaçınılacak hata:** Aynı bölüme başka eyalette kaydı sorunsuz sanmak; eyalet kurallarını kontrol edin.

### Senaryo 4: AB dışından öğrencisiniz ve bölüm değiştirmek istiyorsunuz

- **Önce kontrol edilecek belge:** Oturum izninizin geçerlilik tarihi ve yeni programın kabul koşulları.
- **Prüfungsamt'a soru:** "Unbedenklichkeitsbescheinigung veya sınav hakkı belgesi alabilir miyim?" [\[23\]](#content-kaynaklar)
- **Süre:** Yeni programın başvuru dönemi ve oturum izninizin bitiş tarihi.
- **Olası seçenekler:** BMI notlarına göre program değişikliği mümkündür; yeni oturum izni için başvurmanız gerekir [\[10\]](#content-kaynaklar).
- **Kaçınılacak hata:** Ausländerbehörde'yi son ana kadar bilgilendirmemek. Planınızı yazılı sunun.

## "Sınavdan kaldım, şimdi ne yapmalıyım" kontrol listesi

- Sonuç bildirimini ve itiraz bilgilerini (Rechtsbehelfsbelehrung) dikkatle okuyun.
- Sınav yönetmeliğinizde bunun kaçıncı deneme olduğunu kontrol edin.
- Tekrar sınavının kayıt ve sınav tarihlerini not edin.
- Freiversuch, ek hak veya sözlü tamamlama sınavı olup olmadığını araştırın.
- İmkân varsa sınav kağıdınızı inceleyin (Klausureinsicht).
- Prüfungsamt veya bölüm danışmanlığı (Studienfachberatung) ile görüşün.
- Son denemeyse danışmanlık alın, Härtefall imkânını sorun ve itiraz süresini takip edin.
- AB dışındansanız bir plan hazırlayın ve Ausländerbehörde ile yazılı iletişim kurun.
- Kendinize iyi bakın; zorlanıyorsanız [yalnızlık ve ruh sağlığı yazımıza](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany) göz atın ve üniversitenizin psikolojik danışma servisine başvurun.

## Sıkça sorulan sorular

### Almanya'da bir sınavı kaç kez tekrar edebilirim?

Ülke genelinde tek bir sayı yoktur; kendi sınav yönetmeliğiniz belirler. İncelediğimiz 10 kurumda aralık geniştir: KIT'te iki deneme, RWTH, Hamburg WiSo, Köln WiSo, TH Köln ve FU Berlin BWL'de üç, TU Berlin'de danışmanlık sonrası dört. TUM ve LMU'da deneme sayısı sınırsız ama süreye bağlıdır. Köln ve TH Köln'de ayrıca başvuruyla ek haklar vardır. Yönlendirme sınavlarında hak sayısı çoğu zaman ikidir.

### Almanya'da 4,0 geçer not mu?

İncelediğimiz kurumların tamamında geçme notu 4,0'dır (ausreichend, yeterli). Ancak bazı istisnalar vardır: RWTH Informatik'te bir modüldeki her alt değerlendirmenin ayrı ayrı en az 4,0 olması gerekir. FU Berlin'de çoktan seçmeli sınavlar puanların yüzde ellisiyle geçilir. Notsuz ödevler yalnızca "bestanden" veya "nicht bestanden" olarak değerlendirilir. Modül tanımınızı mutlaka kontrol edin.

### Sınavdan kalmak öğrenci vizemi etkiler mi?

Bir sınavdan kalmak tek başına oturum izninizi sona erdirmez. Öğrenimi makul sürede bitirmeniz şüpheli hâle gelirse veya kaydınız silinirse oturum açısından önem kazanır. Yabancılar dairesi iznin süresini kısaltabilir, ama bu bir takdir kararıdır. Program değişikliği mümkündür; yeni bir izin için başvurmanız gerekir. Ausländerbehörde ile erken ve yazılı iletişim kurun.

### Kesin olarak kaldıktan sonra başka yerde okuyabilir miyim?

Çoğu zaman evet, ama bu eyalete ve programa bağlıdır. NRW ve Bavyera'da aynı programa kayıt reddedilir; Hessen'de reddedilebilir. Hamburg'da başka bir programa ancak kalınan sınav orada da zorunluysa engel çıkar. Tamamen farklı bir bölüm genellikle mümkündür. Bazı üniversiteler beyan veya Unbedenklichkeitsbescheinigung ister; durumunuzu dürüstçe bildirin.

### Geçtiğim bir sınavı notumu yükseltmek için tekrar edebilir miyim?

İncelediğimiz kurumların çoğunda hayır. LMU Informatik'te bir kez, bir sonraki olağan sınav tarihinde mümkündür ve iyi olan not sayılır. TH Köln'de dört ek hakkın ikisi not yükseltmek için kullanılabilir. RWTH ve FU Berlin ise tekrar yerine bazı notları ortalamadan çıkarma veya notsuza çevirme imkânı sunar. Kendi yönetmeliğinizde "Notenverbesserung" ifadesini arayın.

### Freiversuch nedir?

Freiversuch, kalırsanız hiç girilmemiş sayılan bir deneme hakkıdır; kalınan sınav hak sayınızdan düşmez. Her yönetmelikte yoktur. LMU Informatik'te standart süre içindeki ilk başarısız deneme sayılmaz. RWTH'de ilk üç dönemde kalınan en fazla üç yazılı sınav için başvurabilirsiniz. TU Berlin'de ilk dönemde kalınan Bachelor sınavları sayılmaz. Tez için genellikle geçerli değildir.

## Kaynaklar

1. KMK, Ländergemeinsame Strukturvorgaben (i.d.F. 04.02.2010) — https://www.kmk.org/fileadmin/Dateien/veroeffentlichungen_beschluesse/2003/2003_10_10-Laendergemeinsame-Strukturvorgaben.pdf
2. HRK/KMK, Diploma Supplement şablonu (2015) — https://www.hrk.de/fileadmin/redaktion/hrk/02-Dokumente/02-03-Studium/02-03-01-Studium-Studienreform/Bologna_Dokumente/Diploma_Supplement_englisch_2015_aktualisiert.pdf
3. KMK, Vereinbarung über die Festsetzung der Gesamtnote bei ausländischen Hochschulzugangszeugnissen (2013) — https://www.kmk.org/fileadmin/Dateien/pdf/ZAB/Hochschulzugang_Beschluesse_der_KMK/GesNot05.pdf
4. Hochschulgesetz NRW (§§ 50, 51, 64) — https://recht.nrw.de/lrgv/gesetz/07052025-gesetz-ueber-die-hochschulen-des-landes-nordrhein-westfalen-hochschulgesetz-hg/
5. Hamburgisches Hochschulgesetz (§§ 42, 44, 65; Universität Hamburg Hukuk Fakültesi konsolide metni) — https://www.jura.uni-hamburg.de/media/service/rechtsgrundlagen/a-allgemeines/a--i--hamburgisches-hochschulgesetz--hmbhg-.pdf
6. Bayerisches Hochschulinnovationsgesetz (Art. 84, 91, 94) — https://www.gesetze-bayern.de/Content/Document/BayHIG-84
7. Hessisches Hochschulgesetz (2021) — https://www.uni-giessen.de/de/mug/1/pdf/1_31_00_1_HHG2021_DruckJLU
8. § 16b AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
9. § 7 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__7.html
10. BMI, Anwendungshinweise zum Fachkräfteeinwanderungsgesetz (Rechtsstand 01.06.2024) — https://www.bmi.bund.de/SharedDocs/downloads/DE/veroeffentlichungen/themen/migration/anwendungshinweise-fachkraefteeinwanderungsgesetz.pdf?__blob=publicationFile&v=16
11. § 20 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html
12. TUM, APSO (2024 değişiklikleriyle) — https://www.edu.sot.tum.de/fileadmin/w00bed/edu/Documents/Studium/APSO/Lesb.F._APSO_vom_18.03.2011_mit_11._AES_vom_17.12.2024.pdf
13. LMU, PStO B.Sc. Informatik (2022) — https://cms-cdn.lmu.de/media/contenthub/amtliche-veroeffentlichungen/1568-16in-ba-nf30-2022-ps00.pdf
14. RWTH Aachen, Übergreifende Prüfungsordnung (2025 değişiklikleriyle) — https://www.rwth-aachen.de/global/show_document.asp?id=aaaaaaaaddqknak
15. TU Berlin, AllgStuPO (Amtl. Mitteilungsblatt 29/2024) — https://www.static.tu.berlin/fileadmin/www/10000000/Studiengaenge/StuPOs/AllgStuPO_deu.pdf
16. KIT, SPO B.Sc. Informatik (2022) — https://www.sle.kit.edu/downloads/AmtlicheBekanntmachungen/2022_AB_034.pdf
17. Universität Hamburg, PO WiSo B.Sc. (2024) — https://www.uni-hamburg.de/campuscenter/studienorganisation/ordnungen-satzungen/pruefungsordnungen/wirtschafts-und-sozialwissenschaften/20240508-po-wiso-bsc-82.pdf
18. Goethe-Universität Frankfurt, B.Sc. Wirtschaftswissenschaften PO (2022) — https://www.uni-frankfurt.de/112161167/BA_Wirtschaftswissenschaften_2022_01_31.pdf
19. Universität zu Köln, WiSo Gemeinsame Prüfungsordnung (2025) — https://wiso.uni-koeln.de/sites/fakultaet/dokumente/PA/po/GPO_2025.pdf
20. TH Köln, Rahmenprüfungsordnungen (2023) — https://www.th-koeln.de/mam/downloads/deutsch/hochschule/amtlichemitteilungen/inkraftsetzungssatzung_rpoen_2023_final.pdf
21. FU Berlin, RSPO (2013) — https://www.fu-berlin.de/service/zuvdocs/amtsblatt/2013/ab322013.pdf ; SPO B.Sc. BWL (2017) — https://www.wiwiss.fu-berlin.de/studium-lehre/pruefungsbuero/studien--und-pruefungsordnungen/BWLab112017.pdf
22. LMU, Immatrikulationshindernisse — https://www.lmu.de/de/workspace-fuer-studierende/1x1-des-studiums/immatrikulationshindernisse-und-versagungsgruende/index.html
23. FU Berlin WiWiss, Unbedenklichkeitsbescheinigung — https://www.wiwiss.fu-berlin.de/studium-lehre/pruefungsbuero/Bescheinigungen/Unbedenklichkeitsbescheinigungen/

*Yönetmelikler Eylül 2026'da 10 Alman üniversitesinde kontrol edildi; kurallar değişebilir, kendi yönetmeliğinizin güncel sürümünü mutlaka kontrol edin. Bu yazı genel bilgi amaçlıdır, hukuki danışmanlık değildir.*
MD;

        $enBody = <<<'MD'
German universities grade from 1.0 (best) to 5.0 (fail), and 4.0 is usually the lowest passing grade. There is no single national rule that gives everyone three attempts at an exam: the number of attempts, retake deadlines and the consequences of a final fail depend on state law, your university and your program's examination regulations (Prüfungsordnung). Your Prüfungsordnung and your Prüfungsamt have the final word.

> **Last updated:** September 2026 · Exam rules vary by state, university, faculty and examination regulations. · ⚖️ = official rule (law or examination regulation), 💡 = practical advice · Examples checked across 10 German universities (September 2026); this is not a universal rule.

## How the German grading system works

If you come from a system where higher numbers are better, the German scale feels upside down: **1.0 is the top grade** and **5.0 means failed**.

**⚖️ Official rule:** The HRK/KMK Diploma Supplement template describes a scale that usually has five levels: *sehr gut* (1), *gut* (2), *befriedigend* (3), *ausreichend* (4) and *nicht ausreichend* (5). Intermediate grades may be given, and the minimum passing grade is *ausreichend* (4) [\[2\]](#content-sources).

Two more layers sit on top:

- **Intermediate steps** such as 1.3 or 2.7 are not set nationally. Each university's examination regulations define them.
- **Relative grades:** Under the KMK's former structural guidelines (now superseded by the accreditation framework), a relative or ECTS grade is shown for the final grade in addition to the 1–5 grade [\[1\]](#content-sources).

Each exam gets its own grade. Module grades and your final grade are then calculated from those results, usually weighted by credits (ECTS).

## What 1.0 to 5.0 mean

**⚖️ Official rule:** All 10 institutions we examined use the steps 1.0, 1.3, 1.7, 2.0, 2.3, 2.7, 3.0, 3.3, 3.7, 4.0 and 5.0. Several explicitly exclude 0.7, 4.3 and 4.7; TUM's general regulations (APSO) exclude only 0.7 and 5.3 [\[12\]](#content-sources).

| Grade | Label (German + gloss) | Meaning |
|---|---|---|
| 1.0 / 1.3 | *sehr gut* (very good) | Outstanding performance |
| 1.7 / 2.0 / 2.3 | *gut* (good) | Clearly above average |
| 2.7 / 3.0 / 3.3 | *befriedigend* (satisfactory) | Average |
| 3.7 / 4.0 | *ausreichend* (sufficient) | Minimum requirements met; 4.0 is the lowest pass |
| 5.0 | *nicht ausreichend* (insufficient) | Failed (Universität zu Köln WiSo: *mangelhaft*, deficient) |

> These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.

*Regulations checked September 2026.*

For **averages** (module and overall grades), typical bands, for example at TUM and Kiel, are: up to 1.5 *sehr gut*; 1.6–2.5 *gut*; 2.6–3.5 *befriedigend*; 3.6–4.0 *ausreichend*; above 4.0 *nicht ausreichend*.

**Truncation, not rounding.** Most institutions we examined keep only the first decimal of an average and cut off the rest (TUM, RWTH Aachen, TU Berlin, KIT, Frankfurt, Köln, TH Köln, FU Berlin). An average of 2.58 becomes 2.5, not 2.6. LMU and Universität Hamburg WiSo keep two decimals.

**Converting foreign grades.** For foreign school-leaving certificates, the KMK uses the "modified Bavarian formula": X = 1 + 3 × (Nmax − Nd) / (Nmax − Nmin), where Nmin is the lowest *passing* grade. The result is set to one decimal without rounding [\[3\]](#content-sources). Universities often reuse this formula.

💡 **Practical advice:** Our [grade converter](/en/tools/grade-converter) gives you an orientation value only. Grade converters are indicative. Your university or uni-assist may apply its own conversion rules.

## The passing grade

**⚖️ Official rule:** In all the institutions we examined, you pass with 4.0 (*ausreichend*) or better, or with *bestanden* (passed) for ungraded work [\[2\]](#content-sources). A 5.0 means *nicht bestanden* (not passed). Exceptions to watch:

- **Sub-assessments:** In RWTH Aachen's computer science program, every sub-assessment and every exam in a module must be at least 4.0 [\[14\]](#content-sources). A strong exam grade does not rescue a failed component.
- **Multiple choice:** At FU Berlin, multiple-choice exams pass at 50 % of the points; a relative rule may lower this, but never below 40 % [\[21\]](#content-sources).
- **Ungraded coursework:** *Studienleistungen* (coursework requirements such as exercise sheets or lab work) are only *bestanden* or *nicht bestanden*.

💡 **Practical advice:** Check the module description (*Modulbeschreibung*) to see whether each part of a module must be passed separately.

## The "three attempts" myth

You will often hear on campus and in forums that there is a universal three-attempt rule. **As a general rule, it is false.**

**⚖️ Official rule:** Germany has no national law on the number of exam attempts. The states regulate higher education, and they leave much to university regulations:

- **North Rhine-Westphalia (NRW):** The Prüfungsordnung must regulate "the number and conditions for repeating exams"; the law sets no minimum [\[4\]](#content-sources).
- **Hamburg:** Exams during your studies can be repeated at least twice; the thesis once, a second time only in justified exceptional cases [\[5\]](#content-sources).
- **Berlin:** As implemented by universities such as TU Berlin, exams can in principle be repeated twice, plus one more attempt after compulsory subject counseling (*Studienfachberatung*) [\[15\]](#content-sources).
- **Bavaria:** Repeats should normally be possible within six months. Regulations may provide a free attempt, but not for the thesis. Missing deadlines for reasons you are responsible for means "taken and not passed" [\[6\]](#content-sources).

In the 10 institutions we examined, the range was wide:

- **2 attempts:** KIT computer science; electives in Frankfurt's economics program [\[16\]](#content-sources) [\[18\]](#content-sources)
- **3 attempts:** RWTH Aachen, Hamburg WiSo, Köln WiSo, TH Köln, FU Berlin BWL, Frankfurt compulsory modules [\[14\]](#content-sources) [\[17\]](#content-sources) [\[18\]](#content-sources) [\[19\]](#content-sources) [\[20\]](#content-sources) [\[21\]](#content-sources)
- **4 attempts:** TU Berlin, the fourth after counseling [\[15\]](#content-sources)
- **Unlimited but time-limited:** TUM and LMU [\[12\]](#content-sources) [\[13\]](#content-sources)
- **Extra attempts on application:** Köln and TH Köln [\[19\]](#content-sources) [\[20\]](#content-sources)

## Erstversuch, Zweitversuch, Drittversuch

- ***Erstversuch*** – the first attempt.
- ***Zweitversuch*** – the second attempt, i.e. the first repeat.
- ***Drittversuch*** – the third attempt, or second repeat. In many programs this is the last regular attempt.
- ***Wiederholungsprüfung*** – a repeat exam in general.

Count carefully: "two repeats" means three attempts in total, while KIT's "one repeat" means two [\[16\]](#content-sources).

**⚖️ Official rule:** Orientation or foundation exams often allow only two attempts. At TUM and LMU, the orientation exams (*Grundlagen- und Orientierungsprüfung*, GOP) normally have two attempts, although normal module exams there have no fixed limit [\[12\]](#content-sources) [\[13\]](#content-sources). KIT's exceptional second repeat is not available for orientation exams [\[16\]](#content-sources).

💡 **Practical advice:** If your exam portal's attempt counter doesn't match your own count, ask the Prüfungsamt in writing before registering again.

## How a Wiederholungsprüfung works

**⚖️ Official rule:** In many places you must **register for each attempt**. At TUM, every repeat needs a new registration and is normally taken within about six months, at the next exam date [\[12\]](#content-sources). Frankfurt's economics faculty offers the first repeat at the end of the semester or the start of the next [\[18\]](#content-sources); RWTH Aachen offers exams at least twice a year [\[14\]](#content-sources).

The **format can change**:

- At Frankfurt (economics), the examination board may decide that an oral exam replaces a written repeat [\[18\]](#content-sources).
- At KIT, a failed written repeat is automatically followed by an oral follow-up exam [\[16\]](#content-sources).
- At TU Berlin, the examiner may offer an oral re-examination [\[15\]](#content-sources).

💡 **Practical advice:** Registration windows often close weeks before the exam. Put the next one in your calendar the day you get your grade.

## When you have to retake

In some programs, waiting too long means the exam counts as failed or you lose your right to be examined.

**⚖️ Official rule (examples):**

- **TUM:** Unlimited attempts, but you must reach minimum credits by set semesters; missing them leads to final failure [\[12\]](#content-sources).
- **LMU (computer science):** Exams not passed by the end of the standard period of study plus two semesters count as finally failed [\[13\]](#content-sources).
- **KIT (computer science):** Repeat by the end of the exam period of the fourth semester after the first failure, or you lose the right to be examined [\[16\]](#content-sources).
- **TH Köln:** Apply for an additional attempt within one month of the result, or the exam counts as finally failed [\[20\]](#content-sources).
- **Bavaria:** Repeats normally possible within six months [\[6\]](#content-sources).
- **Hamburg WiSo:** The repeat "should" be at the next possible date [\[17\]](#content-sources).
- **FU Berlin:** At least one more repeat by the second following semester [\[21\]](#content-sources).

💡 **Practical advice:** Postponing a repeat to "prepare better" can backfire under time limits. Check deadlines first.

## Retaking to improve a grade

Passed with 3.7 and want a better grade? **Mostly not possible** in the institutions we examined.

**⚖️ Official rule (examples):**

- **LMU (computer science):** Yes, once, at the next regular date; the better result counts [\[13\]](#content-sources).
- **TH Köln:** Two of the four additional attempts may improve passed first attempts [\[20\]](#content-sources).
- **TUM:** Only if the program regulation allows it; computer science does not [\[12\]](#content-sources).
- **RWTH, TU Berlin, KIT, Hamburg WiSo, Köln WiSo, FU Berlin BWL:** no [\[14\]](#content-sources) [\[15\]](#content-sources) [\[16\]](#content-sources) [\[17\]](#content-sources) [\[19\]](#content-sources) [\[21\]](#content-sources). Frankfurt's framework allows it, but the economics program doesn't use it [\[18\]](#content-sources).

Alternatives exist: RWTH bachelor's students finishing in the standard period may drop 5–30 credits of grades (computer science: up to 30) [\[14\]](#content-sources); FU Berlin BWL students finishing in six semesters can turn up to two graded modules (max. 12 credits) into ungraded ones [\[21\]](#content-sources); TU Berlin lets you replace failed electives [\[15\]](#content-sources).

## Freiversuch

A *Freiversuch* (free attempt) is a first attempt that doesn't count if you fail.

**⚖️ Official rule (examples):**

- **LMU (computer science):** A first failed attempt within the standard period doesn't count; not for GOP exams or the thesis [\[13\]](#content-sources).
- **RWTH Aachen:** Bachelor's students can apply for up to three failed written exams from the first three semesters to count as not taken [\[14\]](#content-sources).
- **TU Berlin:** Bachelor's module exams first failed in the first semester count as not taken [\[15\]](#content-sources).
- **Bavaria:** Regulations may provide one, but not for the thesis [\[6\]](#content-sources).

TUM, KIT, Hamburg WiSo, Köln WiSo, TH Köln and FU Berlin BWL had none in the regulations we checked [\[12\]](#content-sources) [\[16\]](#content-sources) [\[17\]](#content-sources) [\[19\]](#content-sources) [\[20\]](#content-sources) [\[21\]](#content-sources).

💡 **Practical advice:** Check the conditions: which semester, which exam type, and whether you must apply.

## Mündliche Ergänzungsprüfung

A *mündliche Ergänzungsprüfung* (oral supplementary exam) is a short oral exam after a failed written one. It can only lift you to a pass.

**⚖️ Official rule (examples):**

- **KIT (computer science):** Takes place automatically after a failed written repeat; best grade 4.0 [\[16\]](#content-sources).
- **RWTH Aachen:** On application after failing the second repeat of a written exam; result 4.0 or 5.0 [\[14\]](#content-sources).
- **TU Berlin:** The examiner may offer an oral re-examination after any failed exam; pass = 4.0. Not a right [\[15\]](#content-sources).

We didn't find it at TUM, LMU, Hamburg WiSo, Köln WiSo, TH Köln or FU Berlin BWL.

## Different exam types

- ***Klausur*** – written exam
- ***Mündliche Prüfung*** – oral exam
- ***Hausarbeit / Seminararbeit*** – term or seminar paper
- ***Praktikum / Labor*** – lab work or practical training
- ***Projekt*** – project work, often in groups

**⚖️ Official rule:** Ungraded coursework (*Studienleistungen*) can often be repeated without limit, for example at KIT, Frankfurt and Köln [\[16\]](#content-sources) [\[18\]](#content-sources) [\[19\]](#content-sources). Graded exams (*Prüfungsleistungen*) count against your attempts, and a repeat may use a different format.

## Thesis repeat rights

**⚖️ Official rule (examples):** In most institutions we examined, a failed thesis can be repeated **once**. TU Berlin allows two repeats [\[15\]](#content-sources). Hamburg allows a second only in justified exceptional cases [\[5\]](#content-sources) [\[17\]](#content-sources). RWTH requires you to register the repeat within three semesters [\[14\]](#content-sources). At TUM and Frankfurt, the repeat uses a new topic [\[12\]](#content-sources) [\[18\]](#content-sources). Köln WiSo grants no additional attempts for the thesis [\[19\]](#content-sources); TH Köln allows one repeat of the thesis and one of the colloquium [\[20\]](#content-sources).

For the full process, see our [bachelor's and master's thesis guide](/en/blog/bachelor-and-master-thesis-process-in-germany-en).

## What "endgültig nicht bestanden" means

*Endgültig nicht bestanden* means "finally failed": you have used up the attempts or time limits your Prüfungsordnung allows for a required exam.

**⚖️ Official rule:** In a **compulsory module**, this usually means losing your right to be examined in that program. In an **elective**, you can often switch while options remain, for example at Hamburg WiSo, Frankfurt and in RWTH computer science [\[14\]](#content-sources) [\[17\]](#content-sources) [\[18\]](#content-sources). At Frankfurt, a third finally failed elective ends the right to be examined [\[18\]](#content-sources).

You receive a written notice (*Bescheid*) with legal-remedy instructions (*Rechtsbehelfsbelehrung*), as at LMU [\[13\]](#content-sources). At RWTH, Hamburg WiSo and Frankfurt, an objection (*Widerspruch*) is possible within one month [\[14\]](#content-sources) [\[17\]](#content-sources) [\[18\]](#content-sources).

💡 **Practical advice:** Note the date you received the notice; objection deadlines run from then.

## Losing your Prüfungsanspruch

Your *Prüfungsanspruch* is your right to take exams in your program. Losing it means you can't finish that degree there, usually leads to exmatriculation, and may block the same or related programs elsewhere, depending on the state.

**⚖️ Official rule:** At KIT, missing the repeat deadline alone costs you this right [\[16\]](#content-sources); at TUM, missing credit thresholds does [\[12\]](#content-sources). Hardship and extra-attempt rules are program-specific: TU Berlin's fourth attempt after counseling [\[15\]](#content-sources), Köln's additional attempts [\[19\]](#content-sources), TH Köln's four extra attempts on application [\[20\]](#content-sources), hardship decisions by the board chair at Hamburg WiSo [\[17\]](#content-sources), FU Berlin's withdrawal from a failed last attempt in exceptional circumstances (apply within three months) [\[21\]](#content-sources), and KIT's second repeat on exceptional application [\[16\]](#content-sources).

## Changing university

**Final failure does not mean a Germany-wide ban on studying.** It can, however, block the same program and sometimes related ones.

**⚖️ Official rule (examples):**

- **NRW:** Enrollment is refused if you finally failed a required exam in the chosen program at a German university; related programs only if the exam regulations say so (*erhebliche inhaltliche Nähe*, considerable similarity in content) [\[4\]](#content-sources).
- **Bavaria:** Refusal after final failure unless you switch to a different program [\[6\]](#content-sources).
- **Hamburg:** Not the same program; another program only if the failed subjects are compulsory there too [\[5\]](#content-sources).
- **Hesse:** Enrollment *may* be refused for the same or a content-comparable program [\[7\]](#content-sources).
- **KIT (Baden-Württemberg):** Same or related program with essentially the same content [\[16\]](#content-sources).
- **TU Berlin:** Applicants declare they have not finally failed required exams in the chosen program (Germany/EU/EEA) [\[15\]](#content-sources).

LMU asks you to state enrollment obstacles unprompted [\[22\]](#content-sources). FU Berlin requires an *Unbedenklichkeitsbescheinigung* (certificate that you haven't finally failed and still have the right to be examined) when you change university [\[23\]](#content-sources). At Frankfurt's economics faculty, failed attempts at the same or a comparable exam at other German universities count [\[18\]](#content-sources).

💡 **Practical advice:** Answer disclosure questions accurately. To compare options, browse [universities](/en/universities) and [degree programs](/en/programs).

## Changing your subject

Switching subject is often possible, but not always.

**⚖️ Official rule:** In Hamburg, the bar extends to another program if the failed subjects are compulsory there [\[5\]](#content-sources). In NRW, related programs are affected only if the regulations say so [\[4\]](#content-sources). Hesse *may* refuse content-comparable programs [\[7\]](#content-sources).

💡 **Practical advice:** Choose a program where the failed module isn't compulsory. Changing institution type is another route; see [Hochschule vs. Universität vs. FH](/en/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany-en).

## Exmatrikulation

*Exmatrikulation* is removal from the list of enrolled students.

**⚖️ Official rule:** State laws use mandatory wording after final failure: NRW says a student "is to be exmatriculated" (*ist zu exmatrikulieren*) [\[4\]](#content-sources); Bavaria [\[6\]](#content-sources) and Hamburg [\[5\]](#content-sources) are similar. But exmatriculation is carried out by a **separate administrative decision** (*Bescheid*) with legal-remedy instructions. You can challenge it by objection where the state provides for one, otherwise at the administrative court; in Bavaria, for exam decisions, you may choose either [\[6\]](#content-sources).

Timing varies: RWTH exmatriculates at the end of the semester in which the decision becomes final [\[14\]](#content-sources); at TU Berlin it takes effect after two months at the earliest, postponed if you apply for another program [\[15\]](#content-sources).

Read our [exmatriculation guide](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en). Our [generic objection letter template](/en/templates/widerspruch-bescheid) helps you structure a Widerspruch; it is not legal advice.

## What it means for your residence permit

For non-EU students with a study permit (§ 16b of the Residence Act, AufenthG):

**⚖️ Official rule:**

- **Failing an exam does not by itself end your permit.** It matters when your progress makes finishing in a reasonable time doubtful, or after exmatriculation.
- **Extension:** Possible if the purpose of study is not yet achieved but can still be achieved within a reasonable time; the university may be consulted [\[8\]](#content-sources).
- **Shortening is discretionary:** If an essential condition ceases, the permit's validity *can* be shortened. It is a decision, not an automatic effect [\[9\]](#content-sources).
- **Program change:** Under the Federal Interior Ministry's application notes, changing program or place of study is possible. You must apply for a new residence permit and are entitled to it, if you can finish within a reasonable time (up to a total stay of 10 years) [\[10\]](#content-sources).
- **Job search only after success:** The post-study job-search permit (up to 18 months, § 20 AufenthG) requires *successful* completion [\[11\]](#content-sources).

💡 **Practical advice:** Contact the immigration office (*Ausländerbehörde*) early and in writing, with a plan: a new program, vocational training (*Ausbildung*) or a job. See our guides on [switching to a work permit](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en) and the [job-seeker visa](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en).

## Illness, medical certificates and withdrawal (Rücktritt)

**⚖️ Official rule (withdrawal without reasons, examples):** RWTH up to three working days before [\[14\]](#content-sources); TU Berlin three days [\[15\]](#content-sources); Köln WiSo two weeks [\[19\]](#content-sources); TH Köln one week [\[20\]](#content-sources); KIT written exams until the day before, oral exams three working days before [\[16\]](#content-sources).

After that, withdrawal requires a good reason, reported without delay, usually with a medical certificate: at RWTH by the third working day [\[14\]](#content-sources), at TU Berlin within five days [\[15\]](#content-sources). **At LMU, a normal employer sick note (*Arbeitsunfähigkeitsbescheinigung*) is not enough** [\[13\]](#content-sources). Boards may require an official or trust-doctor certificate. **A no-show without good reason is graded 5.0 / not passed** in all institutions we examined.

💡 **Practical advice:** Use your Prüfungsamt's own form where one exists. Our [sick notice template](/en/templates/krankmeldung) helps you notify people in writing, but it is not a Prüfungsamt withdrawal form. For longer illness, see our [Urlaubssemester guide](/en/blog/urlaubssemester-taking-a-semester-off-in-germany-en) and [leave of absence application template](/en/templates/urlaubssemester-antrag).

## 10 universities compared

| University / program example | Passing grade | Attempts | Repeat deadline | Third / additional attempt | Oral supplementary exam | Freiversuch | Grade improvement | Thesis repeats | Final-failure consequence |
|---|---|---|---|---|---|---|---|---|---|
| [TUM](/en/universities/technische-universitat-munchen-partner-019ddbba), B.Sc. Informatik | 4.0 | Unlimited within credit deadlines; GOP 2 | ~6 months / next date | n/a | Not found | No | Only if program allows (here: no) | 1, new topic | Notice + exmatriculation |
| [LMU](/en/universities/ludwig-maximilians-universitat-munchen-q55044), B.Sc. Informatik | 4.0 | Unlimited within time limits; GOP 2 | Standard period + 2 semesters | n/a | Not found | Yes | Yes, once | 1 | Written notice + legal remedies |
| [RWTH Aachen](/en/universities/rwth-aachen-university-partner-019de9ee), B.Sc. Informatik | 4.0 (each sub-assessment) | 3 | None found; exams ≥ 2×/year | 3rd is a regular attempt | On application (4.0/5.0) | Up to 3 exams, first 3 semesters | No (drop 5–30 CP) | 1, within 3 semesters | Exmatriculation end of semester |
| TU Berlin (AllgStuPO) | 4.0 | 3 + 1 | Normally the next regular exam date | 4th after counseling | Examiner may offer | 1st semester | No | 2 | Exmatriculation after ≥ 2 months |
| [KIT](/en/universities/karlsruher-institut-fur-technologie-q309988), B.Sc. Informatik | 4.0 | 2 | 4th semester after failure | Exceptional, on application | Automatic (max 4.0) | No | No | 1 | Loss of right to be examined |
| Universität Hamburg, WiSo B.Sc. | 4.0 | 3 | Next possible date | Hardship (board chair) | No | No | No | 1 (+1 exceptional) | Notice; objection 1 month |
| Goethe Frankfurt, WiWi B.Sc. | 4.0 | 3 compulsory / 2 elective | End of semester / start of next | 3rd for compulsory only | Oral may replace written repeat | Not used | Not used | 1, new topic | Notice + exmatriculation |
| Universität zu Köln, WiSo B.Sc. BWL | 4.0 | 3 per module | None stated | Up to 3 extra (+1 at ≥140 CP) | No | No | No | 1 | Exmatriculation from program |
| TH Köln (RPO 2023) | 4.0 | 3 | Extra: apply within 1 month | 4 extra on application | No | No | Yes, 2 of 4 extra | 1 (+ colloquium 1) | Notice + exmatriculation |
| FU Berlin, B.Sc. BWL | 4.0 (MC: 50 %) | 3 | By 2nd following semester | Hardship withdrawal (last attempt) | No | No | No (≤12 CP ungraded) | 1 | Notice + exmatriculation |

> These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.

*Regulations checked September 2026.*

## Four student scenarios

### You failed a first attempt

- **Document to check first:** The repeat rules in your Prüfungsordnung and your portal's attempt counter.
- **Question for the Prüfungsamt:** "Does this attempt count, or does a Freiversuch apply? How do I register for the repeat?"
- **Deadline:** The next registration window and any repeat time limit (e.g. KIT [\[16\]](#content-sources)).
- **Possible options:** Freiversuch where available (LMU, RWTH on application, TU Berlin) [\[13\]](#content-sources) [\[14\]](#content-sources) [\[15\]](#content-sources); exam review; a study group.
- **Mistake to avoid:** Postponing until you hit a time limit.

### You are facing the Drittversuch or last attempt

- **Document to check first:** Rules on the last attempt, oral supplementary exams and extra attempts.
- **Question for the Prüfungsamt:** "Is this my last regular attempt? Can I apply for an oral supplementary exam or an extra attempt?"
- **Deadline:** Registration and any application deadline (TH Köln: one month [\[20\]](#content-sources)).
- **Possible options:** RWTH oral exam on application [\[14\]](#content-sources); TU Berlin fourth attempt after counseling [\[15\]](#content-sources); Köln additional attempts [\[19\]](#content-sources).
- **Mistake to avoid:** Sitting the exam while sick instead of withdrawing properly.

### A compulsory module is finally failed

- **Document to check first:** The *Bescheid* and its legal-remedy instructions.
- **Question for the Prüfungsamt:** "Is the decision final? Is any hardship rule available? When does exmatriculation take effect?"
- **Deadline:** The objection deadline, often one month [\[14\]](#content-sources) [\[17\]](#content-sources) [\[18\]](#content-sources).
- **Possible options:** Widerspruch if you see procedural errors; FU Berlin hardship withdrawal [\[21\]](#content-sources); KIT exceptional repeat [\[16\]](#content-sources); a program where the module isn't compulsory.
- **Mistake to avoid:** Assuming you can never study in Germany again [\[4\]](#content-sources) [\[5\]](#content-sources) [\[6\]](#content-sources) [\[7\]](#content-sources).

### You're an international student changing program

- **Document to check first:** The new university's enrollment obstacles and your permit's expiry date.
- **Question for the new university:** "Do I need an Unbedenklichkeitsbescheinigung? Do my failed attempts count here?" [\[18\]](#content-sources) [\[23\]](#content-sources)
- **Deadline:** Application deadlines and your exmatriculation date.
- **Possible options:** A different subject or institution type, with a new residence permit for the new program [\[10\]](#content-sources).
- **Mistake to avoid:** Waiting for the Ausländerbehörde to act first [\[9\]](#content-sources).

## "I failed an exam — what now" checklist

- Read the notice and the legal-remedy instructions.
- Check which attempt this was in your Prüfungsordnung.
- Note the repeat and registration deadlines.
- Check for a Freiversuch, extra attempts or an oral supplementary exam.
- Review your exam (*Klausureinsicht*) if offered.
- Talk to the Prüfungsamt or *Studienfachberatung*.
- If this was your last attempt: get counseling, ask about hardship rules, note the Widerspruch deadline.
- Non-EU student: make a plan and contact the Ausländerbehörde in writing.
- Look after your health; see our guide on [loneliness and mental health](/en/blog/loneliness-mental-health-international-students-germany).

## FAQ

### How many times can I retake an exam at a German university?

It depends on your examination regulations; there is no national rule. In the 10 institutions we examined, the range was two attempts (KIT) to three (RWTH Aachen, Hamburg WiSo, Köln, TH Köln, FU Berlin) or four (TU Berlin, after counseling). TUM and LMU allow unlimited attempts at normal module exams, but within strict time limits. Köln and TH Köln add extra attempts on application. Orientation exams often allow only two.

### Is 4.0 a passing grade in Germany?

Yes, 4.0 (*ausreichend*) is usually the lowest pass, and 5.0 means failed. Exceptions exist: RWTH Aachen's computer science program requires every sub-assessment in a module to reach 4.0, FU Berlin passes multiple-choice exams at 50 % of the points, and ungraded coursework is simply passed or not passed. Several regulations exclude grades like 4.3 or 4.7.

### Does failing an exam affect my student visa?

Not by itself. Your permit can be extended as long as you can still finish within a reasonable time. It becomes an issue if your progress makes that doubtful, or after exmatriculation, and even then shortening the permit is a discretionary decision. A program change is possible with a new permit. Contact the Ausländerbehörde early and in writing.

### Can I study elsewhere after a final fail?

Often yes, but usually not the same program. Depending on the state, enrollment may be refused for the same program and sometimes for related ones; NRW, Bavaria, Hamburg and Hesse each have their own rules. Some universities require proof or a declaration. A different subject is often possible if the failed module isn't compulsory there.

### Can I retake a passed exam to improve my grade?

Usually not. LMU's computer science program allows it once, and TH Köln lets you use two of four additional attempts for improvement. RWTH Aachen instead lets bachelor's students who finish on time drop some grades, and FU Berlin BWL can turn up to two graded modules into ungraded ones.

### What is a Freiversuch?

A Freiversuch is a first attempt that doesn't count if you fail. It exists only where your regulations provide one: for example LMU computer science (within the standard period), RWTH Aachen (on application, up to three written exams in the first three semesters) and TU Berlin (first-semester exams). It usually doesn't cover the thesis.

## Sources

1. KMK, Ländergemeinsame Strukturvorgaben (as amended 04.02.2010) — https://www.kmk.org/fileadmin/Dateien/veroeffentlichungen_beschluesse/2003/2003_10_10-Laendergemeinsame-Strukturvorgaben.pdf
2. HRK/KMK, Diploma Supplement template (2015) — https://www.hrk.de/fileadmin/redaktion/hrk/02-Dokumente/02-03-Studium/02-03-01-Studium-Studienreform/Bologna_Dokumente/Diploma_Supplement_englisch_2015_aktualisiert.pdf
3. KMK, Vereinbarung über die Festsetzung der Gesamtnote bei ausländischen Hochschulzugangszeugnissen (2013) — https://www.kmk.org/fileadmin/Dateien/pdf/ZAB/Hochschulzugang_Beschluesse_der_KMK/GesNot05.pdf
4. Hochschulgesetz NRW (§§ 50, 51, 64) — https://recht.nrw.de/lrgv/gesetz/07052025-gesetz-ueber-die-hochschulen-des-landes-nordrhein-westfalen-hochschulgesetz-hg/
5. Hamburgisches Hochschulgesetz (§§ 42, 44, 65; consolidated copy, Universität Hamburg Faculty of Law) — https://www.jura.uni-hamburg.de/media/service/rechtsgrundlagen/a-allgemeines/a--i--hamburgisches-hochschulgesetz--hmbhg-.pdf
6. Bayerisches Hochschulinnovationsgesetz (Art. 84, 91, 94) — https://www.gesetze-bayern.de/Content/Document/BayHIG-84
7. Hessisches Hochschulgesetz (2021) — https://www.uni-giessen.de/de/mug/1/pdf/1_31_00_1_HHG2021_DruckJLU
8. § 16b AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
9. § 7 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__7.html
10. BMI, Anwendungshinweise zum Fachkräfteeinwanderungsgesetz (legal status 01.06.2024) — https://www.bmi.bund.de/SharedDocs/downloads/DE/veroeffentlichungen/themen/migration/anwendungshinweise-fachkraefteeinwanderungsgesetz.pdf?__blob=publicationFile&v=16
11. § 20 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html
12. TUM, APSO (as amended 2024) — https://www.edu.sot.tum.de/fileadmin/w00bed/edu/Documents/Studium/APSO/Lesb.F._APSO_vom_18.03.2011_mit_11._AES_vom_17.12.2024.pdf
13. LMU, PStO B.Sc. Informatik (2022) — https://cms-cdn.lmu.de/media/contenthub/amtliche-veroeffentlichungen/1568-16in-ba-nf30-2022-ps00.pdf
14. RWTH Aachen, Übergreifende Prüfungsordnung (as amended 2025) — https://www.rwth-aachen.de/global/show_document.asp?id=aaaaaaaaddqknak
15. TU Berlin, AllgStuPO (Amtliches Mitteilungsblatt 29/2024) — https://www.static.tu.berlin/fileadmin/www/10000000/Studiengaenge/StuPOs/AllgStuPO_deu.pdf
16. KIT, SPO B.Sc. Informatik (2022) — https://www.sle.kit.edu/downloads/AmtlicheBekanntmachungen/2022_AB_034.pdf
17. Universität Hamburg, PO WiSo B.Sc. (2024) — https://www.uni-hamburg.de/campuscenter/studienorganisation/ordnungen-satzungen/pruefungsordnungen/wirtschafts-und-sozialwissenschaften/20240508-po-wiso-bsc-82.pdf
18. Goethe-Universität Frankfurt, B.Sc. Wirtschaftswissenschaften PO (2022) — https://www.uni-frankfurt.de/112161167/BA_Wirtschaftswissenschaften_2022_01_31.pdf
19. Universität zu Köln, WiSo Gemeinsame Prüfungsordnung (2025) — https://wiso.uni-koeln.de/sites/fakultaet/dokumente/PA/po/GPO_2025.pdf
20. TH Köln, Rahmenprüfungsordnungen (2023) — https://www.th-koeln.de/mam/downloads/deutsch/hochschule/amtlichemitteilungen/inkraftsetzungssatzung_rpoen_2023_final.pdf
21. FU Berlin, RSPO (2013) — https://www.fu-berlin.de/service/zuvdocs/amtsblatt/2013/ab322013.pdf ; SPO B.Sc. BWL (2017) — https://www.wiwiss.fu-berlin.de/studium-lehre/pruefungsbuero/studien--und-pruefungsordnungen/BWLab112017.pdf
22. LMU, Immatrikulationshindernisse — https://www.lmu.de/de/workspace-fuer-studierende/1x1-des-studiums/immatrikulationshindernisse-und-versagungsgruende/index.html
23. FU Berlin WiWiss, Unbedenklichkeitsbescheinigung — https://www.wiwiss.fu-berlin.de/studium-lehre/pruefungsbuero/Bescheinigungen/Unbedenklichkeitsbescheinigungen/

Regulations checked in September 2026 across 10 German universities; rules change — check the current version of your own regulation. General information, not legal advice.
MD;

        $deBody = <<<'MD'
Eine nicht bestandene Prüfung ist im deutschen Studium fast nie das Ende: In aller Regel folgt eine Wiederholungsprüfung. Wie viele Versuche Sie haben, bis wann Sie wiederholen müssen und was bei „endgültig nicht bestanden" geschieht, legt jedoch keine bundesweite Regel fest, sondern Ihre Prüfungsordnung, das Hochschulgesetz Ihres Bundeslandes und die Vorgaben Ihres Studiengangs. Ihre Prüfungsordnung und Ihr Prüfungsamt haben das letzte Wort. *(Your Prüfungsordnung and your Prüfungsamt have the final word.)*

> **Stand: September 2026** (zuletzt aktualisiert) · Prüfungsregeln unterscheiden sich je nach Bundesland, Hochschule, Fakultät und Prüfungsordnung. · **⚖️ Regelung** = Vorgabe aus einem Gesetz oder einer Prüfungsordnung · **💡 Praxistipp** = unsere praktische Empfehlung · Beispiele geprüft an 10 deutschen Hochschulen (September 2026); das ist keine allgemeingültige Regel.

## So funktioniert das deutsche Notensystem

⚖️ **Regelung:** Nach der Vorlage für das Diploma Supplement von HRK und KMK kennt das deutsche Notensystem in der Regel fünf Stufen: sehr gut (1), gut (2), befriedigend (3), ausreichend (4) und nicht ausreichend (5). Zwischennoten sind möglich, die schlechteste bestandene Note ist „ausreichend" (4) [\[2\]](#content-quellen). Anders als in vielen anderen Ländern gilt also: **Je niedriger die Zahl, desto besser die Note.**

Zwischenstufen wie 1,3 oder 2,7 legen die Prüfungsordnungen der Hochschulen fest, nicht die KMK. Die KMK-Strukturvorgaben von 2010, inzwischen durch den Akkreditierungsrahmen abgelöst, sahen zusätzlich vor, neben der Abschlussnote eine relative Note bzw. ECTS-Note auszuweisen [\[1\]](#content-quellen).

Sie begegnen drei Ebenen: Prüfungsnote, Modulnote und Gesamtnote. Wie sie zusammengerechnet werden, steht in Ihrer Prüfungsordnung. Unbenotete Studienleistungen zählen nur als „bestanden" oder „nicht bestanden".

## Was die Noten 1,0 bis 5,0 bedeuten

In allen zehn Hochschulen unserer Stichprobe wird mit den Stufen 1,0, 1,3, 1,7, 2,0, 2,3, 2,7, 3,0, 3,3, 3,7, 4,0 und 5,0 bewertet. Mehrere Prüfungsordnungen schließen die Noten 0,7, 4,3 und 4,7 ausdrücklich aus; die TUM-Prüfungsordnung (APSO) schließt nur 0,7 und 5,3 aus [\[12\]](#content-quellen).

| Note | Bezeichnung (mit englischer Entsprechung) | Bedeutung in der Praxis |
|---|---|---|
| 1,0 / 1,3 | sehr gut (very good) | Spitzenbereich, beste Notenstufe |
| 1,7 / 2,0 / 2,3 | gut (good) | deutlich über dem Durchschnitt |
| 2,7 / 3,0 / 3,3 | befriedigend (satisfactory) | solide, mittlere Leistung |
| 3,7 / 4,0 | ausreichend (sufficient) | bestanden; 4,0 ist die Bestehensgrenze |
| 5,0 | nicht ausreichend (fail) | nicht bestanden; an der Uni Köln WiSo „mangelhaft" genannt [\[19\]](#content-quellen) |

> Diese Beispiele beziehen sich auf bestimmte Studiengänge oder Prüfungsordnungen. Ihre eigene Prüfungsordnung kann abweichen. *(These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.)*

*Regelungen geprüft im September 2026.*

Für Modul- und Gesamtnoten gelten typischerweise, zum Beispiel an der TUM [\[12\]](#content-quellen), diese Spannen: bis 1,5 „sehr gut", 1,6 bis 2,5 „gut", 2,6 bis 3,5 „befriedigend", 3,6 bis 4,0 „ausreichend", über 4,0 „nicht ausreichend".

⚖️ **Regelung zur Durchschnittsbildung:** Die meisten untersuchten Hochschulen berücksichtigen bei Durchschnittsnoten nur die erste Nachkommastelle; alle weiteren Stellen werden ohne Rundung gestrichen. So ist es etwa an TUM, RWTH, TU Berlin, KIT, Goethe-Universität Frankfurt, Universität zu Köln, TH Köln und FU Berlin geregelt. Aus 2,37 wird dort also 2,3, nicht 2,4. Die LMU und die Universität Hamburg (WiSo) rechnen dagegen mit zwei Nachkommastellen.

Für ausländische Hochschulzugangszeugnisse gilt die modifizierte bayerische Formel der KMK, die Hochschulen oft auch anderweitig nutzen [\[3\]](#content-quellen):

**X = 1 + 3 × (Nmax − Nd) / (Nmax − Nmin)**

Dabei ist Nmax die beste erreichbare Note, Nmin die niedrigste **bestandene** Note und Nd Ihre Note. Das Ergebnis wird auf eine Nachkommastelle bestimmt, ohne zu runden. Beispiel: Bei einer Skala mit 100 als Bestwert, 50 als Bestehensgrenze und Ihrer Note 80 ergibt sich 1 + 3 × 20/50 = 2,2.

💡 **Praxistipp:** Mit unserem [Notenrechner](/de/tools/grade-converter) erhalten Sie einen Orientierungswert. Notenrechner sind nur ein Richtwert: Ihre Hochschule oder uni-assist kann eigene Umrechnungsregeln anwenden.

## Die Bestehensgrenze: 4,0 und ihre Ausnahmen

⚖️ **Regelung:** In allen untersuchten Hochschulen ist eine benotete Prüfung mit 4,0 oder besser bestanden, unbenotete Leistungen mit „bestanden". Es gibt aber wichtige Ausnahmen, die Sie kennen sollten:

- **Teilleistungen:** In der Informatik an der RWTH Aachen muss jede Teilleistung und jede Prüfung eines Moduls mindestens mit 4,0 bewertet sein [\[14\]](#content-quellen). Ein guter Durchschnitt rettet eine nicht bestandene Teilprüfung dort nicht.
- **Multiple-Choice-Klausuren:** An der FU Berlin ist eine Multiple-Choice-Prüfung mit 50 % der Punkte bestanden; eine relative Bestehensregel kann gelten, die Grenze fällt aber nie unter 40 % [\[21\]](#content-quellen).
- **Unbenotete Studienleistungen:** Hier gibt es keine Note, nur „bestanden" oder „nicht bestanden".

Prüfen Sie daher, ob Ihr Modul aus mehreren Teilleistungen besteht, die jeweils einzeln bestanden werden müssen.

## Der Mythos von den „drei Versuchen"

Die verbreitete Annahme, in Deutschland gebe es grundsätzlich drei Versuche, stimmt nicht. Unsere Auswertung von zehn Hochschulen zeigt eine große Bandbreite:

- **2 Versuche:** KIT (eine Wiederholung) sowie Wahlmodule im WiWi-Bachelor der Goethe-Universität Frankfurt [\[16\]](#content-quellen)[\[18\]](#content-quellen)
- **3 Versuche:** RWTH Aachen, Universität Hamburg (WiSo), Universität zu Köln (WiSo), TH Köln, FU Berlin (BWL) sowie Pflichtmodule in Frankfurt
- **4 Versuche:** TU Berlin, wobei der vierte Versuch eine Studienfachberatung voraussetzt [\[15\]](#content-quellen)
- **Unbegrenzt, aber befristet:** TUM und LMU, wo Zeitgrenzen und Leistungsfristen statt einer festen Versuchszahl greifen [\[12\]](#content-quellen)[\[13\]](#content-quellen)
- **Zusatzversuche auf Antrag:** Universität zu Köln und TH Köln [\[19\]](#content-quellen)[\[20\]](#content-quellen)

⚖️ **Regelung auf Landesebene:** Die Hochschulgesetze setzen nur den Rahmen, je nach Bundesland verschieden:

- **Nordrhein-Westfalen:** Nach § 64 HG NRW muss die Prüfungsordnung „die Zahl und die Voraussetzungen" für Wiederholungen regeln; eine Mindestzahl nennt das Gesetz nicht [\[4\]](#content-quellen).
- **Hamburg:** Studienbegleitende Prüfungen können nach § 65 HmbHG mindestens zweimal wiederholt werden, die Abschlussarbeit einmal, ein zweites Mal nur in begründeten Ausnahmefällen [\[5\]](#content-quellen).
- **Berlin:** Nach dem Berliner Hochschulgesetz, wie es Hochschulen wie die TU Berlin umsetzen, sind Prüfungen grundsätzlich zweimal wiederholbar, dazu kommt ein weiterer Versuch nach einer verpflichtenden Studienfachberatung [\[15\]](#content-quellen).
- **Bayern:** Nach Art. 84 BayHIG soll eine Wiederholung in der Regel innerhalb von sechs Monaten möglich sein; die Prüfungsordnung kann einen freien Prüfungsversuch vorsehen, nicht aber für die Abschlussarbeit [\[6\]](#content-quellen).

## Erstversuch, Zweitversuch, Drittversuch

Der **Erstversuch** ist Ihre erste Teilnahme an einer Prüfung, der **Zweitversuch** die erste Wiederholungsprüfung. Der **Drittversuch** ist die zweite Wiederholung und an vielen untersuchten Hochschulen der letzte reguläre Versuch. Wer dort scheitert, hat „endgültig nicht bestanden", sofern die Prüfungsordnung keine weiteren Möglichkeiten vorsieht.

Orientierungs- und Grundlagenprüfungen haben oft nur zwei Versuche: An TUM und LMU gilt das für die Grundlagen- und Orientierungsprüfung (GOP) [\[12\]](#content-quellen)[\[13\]](#content-quellen). Am KIT ist eine zweite Wiederholung bei Orientierungsprüfungen ausgeschlossen [\[16\]](#content-quellen). In Frankfurt muss die Orientierungsphase im WiWi-Bachelor innerhalb von drei Semestern abgeschlossen sein [\[18\]](#content-quellen).

💡 **Praxistipp:** Notieren Sie für jedes Modul, im wievielten Versuch Sie sind, und fragen Sie im Zweifel beim Prüfungsamt nach, bevor Sie sich zum Drittversuch anmelden.

## So läuft eine Wiederholungsprüfung ab

Eine Wiederholungsprüfung findet meist zum nächsten regulären Prüfungstermin statt. Verlassen Sie sich nicht auf eine automatische Anmeldung: An der TUM etwa ist für jede Wiederholung eine eigene Anmeldung nötig [\[12\]](#content-quellen).

Auch die Prüfungsform kann sich ändern: In Frankfurt kann der Prüfungsausschuss eine schriftliche Wiederholungsprüfung durch eine mündliche Prüfung ersetzen [\[18\]](#content-quellen); am KIT und an der RWTH gibt es mündliche Ergänzungsprüfungen (siehe unten).

💡 **Praxistipp:** Nutzen Sie die Klausureinsicht, wenn sie angeboten wird. Dort sehen Sie, wo Punkte verloren gingen, und lernen gezielter.

## Wann Sie wiederholen müssen: Wiederholungsfristen

Die Wiederholungsfrist ist oft wichtiger als die Zahl der Versuche. Beispiele:

- **Bayern:** Wiederholung in der Regel innerhalb von sechs Monaten; wer Fristen selbstverschuldet versäumt, hat „abgelegt und nicht bestanden" [\[6\]](#content-quellen).
- **TUM:** Wiederholung normalerweise innerhalb von etwa sechs Monaten bzw. zum nächsten Termin; zudem schreibt § 10 APSO Mindest-Credits pro Semester vor [\[12\]](#content-quellen).
- **LMU:** Wer eine Prüfung bis zum Ende der Regelstudienzeit plus zwei Semester nicht bestanden hat, hat sie endgültig nicht bestanden [\[13\]](#content-quellen).
- **KIT:** Die Wiederholung muss bis zum Ende des Prüfungszeitraums des vierten Semesters nach dem ersten Fehlversuch abgelegt sein, sonst droht der Verlust des Prüfungsanspruchs [\[16\]](#content-quellen).
- **TH Köln:** Zusatzversuche müssen innerhalb eines Monats nach Bekanntgabe des Ergebnisses beantragt werden [\[20\]](#content-quellen).
- **FU Berlin:** Mindestens eine weitere Wiederholung ist bis zum zweiten Folgesemester vorgesehen [\[21\]](#content-quellen).
- **Goethe-Universität Frankfurt:** Die erste Wiederholung liegt am Semesterende bzw. zu Beginn des Folgesemesters; der Abschluss muss bis zum Ende des neunten Semesters erreicht sein [\[18\]](#content-quellen).

## Wiederholen, um die Note zu verbessern

An den meisten untersuchten Hochschulen können Sie eine bestandene Prüfung **nicht** wiederholen, um die Note zu verbessern. Ausnahmen sind genau geregelt:

- **LMU:** Eine Notenverbesserung ist einmal möglich, zum nächsten regulären Termin; die bessere Note zählt [\[13\]](#content-quellen).
- **TH Köln:** Zwei der vier Zusatzversuche dürfen zur Verbesserung bestandener Erstversuche genutzt werden; auch hier zählt die bessere Note [\[20\]](#content-quellen).
- **TUM:** Nur, wenn die Fachprüfungsordnung es erlaubt; im Bachelor Informatik ist das nicht der Fall [\[12\]](#content-quellen).
- **Goethe-Universität Frankfurt:** Die Rahmenordnung würde Notenverbesserung erlauben, der WiWi-Bachelor nutzt diese Möglichkeit aber nicht [\[18\]](#content-quellen).

Andere Wege: An der RWTH können Bachelorstudierende mit Abschluss in der Regelstudienzeit Noten im Umfang von 5 bis 30 CP streichen lassen (Informatik: bis 30 CP) [\[14\]](#content-quellen). An der FU Berlin können Bachelorstudierende mit Abschluss in sechs Semestern bis zu zwei benotete Module (höchstens 12 Leistungspunkte) in unbenotete umwandeln [\[21\]](#content-quellen).

## Der Freiversuch

Ein **Freiversuch** bedeutet: Ein früher, nicht bestandener Versuch gilt als nicht unternommen und wird nicht auf Ihre Versuche angerechnet. In Bayern erlaubt das Gesetz den Hochschulen, einen freien Prüfungsversuch vorzusehen, allerdings nicht für die Abschlussarbeit [\[6\]](#content-quellen). Beispiele:

- **LMU:** Ein erster nicht bestandener Versuch innerhalb der Regelstudienzeit zählt nicht; ausgenommen sind GOP und Abschlussarbeit [\[13\]](#content-quellen).
- **RWTH Aachen:** Bachelorstudierende können beantragen, dass bis zu drei nicht bestandene Klausuren aus den ersten drei Semestern als nicht abgelegt gelten [\[14\]](#content-quellen).
- **TU Berlin:** Im Bachelor gelten Modulprüfungen, die im ersten Semester erstmals nicht bestanden wurden, als nicht abgelegt [\[15\]](#content-quellen).

An TUM, KIT, Universität Hamburg, Universität zu Köln, TH Köln und FU Berlin haben wir in den untersuchten Ordnungen keinen Freiversuch gefunden; in Frankfurt erlaubt die Rahmenordnung ihn, der WiWi-Bachelor nutzt ihn nicht.

💡 **Praxistipp:** Beachten Sie die Bedingungen: An der RWTH braucht es einen Antrag, an der LMU zählt die Regelstudienzeit.

## Die mündliche Ergänzungsprüfung

Manche Prüfungsordnungen geben nach einer nicht bestandenen Klausur eine letzte mündliche Chance, meist mit höchstens 4,0.

- **KIT:** Scheitert die schriftliche Wiederholung, folgt automatisch eine mündliche Nachprüfung; die bestmögliche Note ist 4,0 [\[16\]](#content-quellen).
- **RWTH Aachen:** Nach der nicht bestandenen zweiten Wiederholung einer Klausur können Sie eine mündliche Ergänzungsprüfung beantragen; das Ergebnis lautet nur 4,0 oder 5,0 [\[14\]](#content-quellen).
- **TU Berlin:** Die Prüferin oder der Prüfer **kann** nach einer nicht bestandenen Prüfung eine mündliche Nachprüfung anbieten; bestanden wird mit 4,0. Ein Anspruch darauf besteht nicht [\[15\]](#content-quellen).

An der Universität Hamburg (WiSo), der Universität zu Köln (WiSo), der TH Köln und der FU Berlin (BWL) gibt es keine mündliche Ergänzungsprüfung; an TUM und LMU haben wir keine gefunden.

## Unterschiedliche Prüfungsformen

Die Prüfungsordnung legt fest, in welcher Form ein Modul geprüft wird: **Klausur**, **mündliche Prüfung**, **Hausarbeit/Seminararbeit**, **Praktikum/Labor** oder **Projekt**.

Unterscheiden Sie **Prüfungsleistungen** (benotet, begrenzte Versuche) von **Studienleistungen** (oft unbenotet). Studienleistungen sind häufig unbegrenzt wiederholbar, etwa am KIT, in Frankfurt und an der Universität zu Köln [\[16\]](#content-quellen)[\[18\]](#content-quellen)[\[19\]](#content-quellen). Wiederholungen können in anderer Form stattfinden, wenn die Prüfungsordnung das zulässt.

## Wiederholung der Abschlussarbeit

In den meisten untersuchten Hochschulen dürfen Sie eine nicht bestandene Abschlussarbeit **einmal** wiederholen. Besonderheiten:

- **TU Berlin:** zwei Wiederholungen [\[15\]](#content-quellen).
- **Universität Hamburg:** eine zweite Wiederholung nur in begründeten Ausnahmefällen [\[5\]](#content-quellen)[\[17\]](#content-quellen).
- **RWTH Aachen:** Die Wiederholung muss innerhalb von drei Semestern angemeldet werden [\[14\]](#content-quellen).
- **TUM und Frankfurt:** Die Wiederholung erfolgt mit neuem Thema [\[12\]](#content-quellen)[\[18\]](#content-quellen).
- **TH Köln:** Arbeit und Kolloquium können jeweils einmal wiederholt werden [\[20\]](#content-quellen).
- **Universität zu Köln:** Für die Abschlussarbeit gibt es keine Zusatzversuche [\[19\]](#content-quellen).

Ein Freiversuch ist bei der Abschlussarbeit in Bayern gesetzlich ausgeschlossen [\[6\]](#content-quellen). Mehr zu Anmeldung, Betreuung und Fristen lesen Sie in unserem [Leitfaden zur Bachelor- und Masterarbeit](/de/blog/bachelor-and-master-thesis-process-in-germany-de).

## Was „endgültig nicht bestanden" bedeutet

„Endgültig nicht bestanden" heißt: Sie haben alle Versuche oder Fristen, die Ihre Prüfungsordnung für eine erforderliche Prüfung vorsieht, ausgeschöpft, ohne zu bestehen. Die Folge hängt davon ab, welches Modul betroffen ist.

- **Pflichtmodul:** In der Regel verlieren Sie Ihren Prüfungsanspruch in diesem Studiengang.
- **Wahlmodul:** Oft können Sie ein anderes Wahlmodul belegen, solange noch Alternativen übrig sind, zum Beispiel an der Universität Hamburg (WiSo), in Frankfurt oder bei Wahlmodulen der RWTH-Informatik [\[14\]](#content-quellen)[\[17\]](#content-quellen)[\[18\]](#content-quellen). In Frankfurt gilt jedoch: Ein drittes endgültig nicht bestandenes Wahlmodul beendet den Prüfungsanspruch [\[18\]](#content-quellen).

Das Ergebnis erhalten Sie als Bescheid, an der LMU etwa schriftlich mit Rechtsbehelfsbelehrung [\[13\]](#content-quellen). Diese nennt Frist und Form, in der Sie sich wehren können.

## Wenn der Prüfungsanspruch verloren ist

Der **Verlust des Prüfungsanspruchs** bedeutet, dass Sie in einem bestimmten Studiengang keine Prüfungen mehr ablegen dürfen. Dazu kommt es nicht nur durch Fehlversuche, sondern auch durch versäumte Fristen:

- Am KIT verlieren Sie den Prüfungsanspruch, wenn Sie die Wiederholungsfrist nicht einhalten [\[16\]](#content-quellen).
- An der TUM führt das Verfehlen der Mindest-Credits nach § 10 APSO zum endgültigen Nichtbestehen [\[12\]](#content-quellen).
- In Bayern gilt eine Prüfung, deren Frist Sie aus selbst zu vertretenden Gründen versäumen, als „abgelegt und nicht bestanden" [\[6\]](#content-quellen).

⚖️ **Regelung:** Der Verlust des Prüfungsanspruchs betrifft zunächst Ihren Studiengang. Welche Folgen er für ein Studium an einer anderen Hochschule oder in einem anderen Fach hat, regelt das jeweilige Landesrecht.

## Hochschulwechsel nach endgültigem Nichtbestehen

Ein endgültiges Nichtbestehen bedeutet **kein** deutschlandweites Studienverbot, kann aber je nach Bundesland die Einschreibung in denselben oder einen verwandten Studiengang verhindern:

- **Nordrhein-Westfalen:** Die Einschreibung wird versagt, wenn Sie eine erforderliche Prüfung im gewählten Studiengang an einer Hochschule in Deutschland endgültig nicht bestanden haben. Verwandte Studiengänge sind nur betroffen, wenn die Prüfungsordnung das wegen „erheblicher inhaltlicher Nähe" vorsieht (§ 50 Abs. 1 Nr. 2 HG NRW) [\[4\]](#content-quellen).
- **Bayern:** Die Einschreibung wird nach Art. 91 BayHIG versagt, es sei denn, Sie wechseln in einen anderen Studiengang [\[6\]](#content-quellen).
- **Hamburg:** Nicht im selben Studiengang; ein anderer Studiengang ist nur ausgeschlossen, wenn die nicht bestandenen Prüfungsfächer dort ebenfalls Pflicht sind (§ 44 HmbHG) [\[5\]](#content-quellen).
- **Hessen:** Die Einschreibung **kann** für denselben oder einen inhaltlich vergleichbaren Studiengang versagt werden (§ 63 HessHG) [\[7\]](#content-quellen).
- **KIT (Baden-Württemberg):** betroffen sind derselbe oder ein verwandter Studiengang mit im Wesentlichen gleichem Inhalt.
- **TU Berlin:** Bewerberinnen und Bewerber erklären, dass sie im gewählten Studiengang keine erforderlichen Prüfungen in Deutschland, der EU oder dem EWR endgültig nicht bestanden haben.

Rechnen Sie mit Offenlegungs- und Nachweispflichten: Die LMU verlangt, Immatrikulationshindernisse unaufgefordert anzugeben [\[22\]](#content-quellen). Die FU Berlin fordert beim Wechsel eine **Unbedenklichkeitsbescheinigung**, die belegt, dass Sie nicht endgültig nicht bestanden haben und Ihr Prüfungsanspruch besteht [\[23\]](#content-quellen). Im WiWi-Bachelor in Frankfurt zählen Fehlversuche in derselben oder einer vergleichbaren Prüfung an anderen deutschen Hochschulen mit [\[18\]](#content-quellen).

💡 **Praxistipp:** Verschweigen Sie beim Wechsel keine Fehlversuche; unvollständige Angaben können Ihre Einschreibung gefährden.

## Fachwechsel als Option

Wer einen Pflichtteil endgültig nicht bestanden hat, kann in vielen Fällen ein anderes Fach studieren. Einschränkungen gibt es dort, wo die nicht bestandene Prüfung auch im neuen Studiengang Pflicht ist (Hamburg [\[5\]](#content-quellen)) oder wo der neue Studiengang eng verwandt ist und die Prüfungsordnung das vorsieht (NRW [\[4\]](#content-quellen)).

💡 **Praxistipp:** Lassen Sie sich vor dem Wechsel von der Studienfachberatung des neuen Fachs beraten und klären Sie, welche Leistungen anerkannt werden. Einen Überblick über Hochschultypen finden Sie in unserem Beitrag zu [Hochschule, Universität und FH](/de/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany-de). Passende Alternativen können Sie in unserer [Studiengangsuche](/de/programs) und der [Hochschulübersicht](/de/universities) recherchieren.

## Exmatrikulation

⚖️ **Regelung:** Die Hochschulgesetze formulieren die Exmatrikulation nach endgültigem Nichtbestehen zwingend, etwa § 51 HG NRW („ist zu exmatrikulieren") [\[4\]](#content-quellen), Art. 94 BayHIG [\[6\]](#content-quellen) und § 42 HmbHG [\[5\]](#content-quellen). Trotzdem ist sie **kein automatischer Vorgang**: Die Hochschule muss sie durch einen eigenen Verwaltungsakt, einen **Bescheid** mit Rechtsbehelfsbelehrung, aussprechen. Beispiele zum Zeitpunkt:

- **RWTH Aachen:** Exmatrikulation zum Ende des Semesters, in dem die Entscheidung bestandskräftig wird; Widerspruch innerhalb eines Monats [\[14\]](#content-quellen).
- **TU Berlin:** Exmatrikulation von Amts wegen, frühestens nach zwei Monaten; sie wird aufgeschoben, wenn Sie sich für einen anderen Studiengang bewerben [\[15\]](#content-quellen).
- **Universität zu Köln (WiSo):** „endgültig nicht bestanden mit der Folge der Exmatrikulation aus dem Studiengang" [\[19\]](#content-quellen).

Gegen den Bescheid können Sie mit **Widerspruch** vorgehen, wo das Land dieses Verfahren beibehalten hat, sonst mit Klage beim Verwaltungsgericht. In Bayern können Sie bei Prüfungsentscheidungen zwischen Widerspruch und direkter Klage wählen [\[6\]](#content-quellen). An der Universität Hamburg (WiSo) und in Frankfurt beträgt die Widerspruchsfrist einen Monat [\[17\]](#content-quellen)[\[18\]](#content-quellen).

💡 **Praxistipp:** Für ein erstes Schreiben können Sie unsere allgemeine [Vorlage für einen Widerspruch gegen einen Bescheid](/de/templates/widerspruch-bescheid) nutzen und an Ihren Fall anpassen. Mehr zu Gründen, Folgen und Rückkehr lesen Sie in unserem [Leitfaden zur Exmatrikulation](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de).

## Was das für Ihren Aufenthaltstitel bedeutet

**Eine nicht bestandene Prüfung beendet Ihren Aufenthaltstitel nicht automatisch.** Relevant wird das Prüfungsergebnis erst, wenn Zweifel entstehen, ob Sie Ihr Studium in angemessener Zeit abschließen können, oder nach einer Exmatrikulation.

⚖️ **Regelung:**

- **§ 16b Abs. 2 AufenthG:** Der Aufenthaltstitel wird verlängert, wenn der Studienzweck noch nicht erreicht ist und in angemessener Zeit erreicht werden kann; die Ausländerbehörde kann die Hochschule beteiligen [\[8\]](#content-quellen).
- **§ 7 Abs. 2 Satz 2 AufenthG:** Fällt eine wesentliche Voraussetzung weg, **kann** die Geltungsdauer nachträglich verkürzt werden. Das ist eine Ermessensentscheidung [\[9\]](#content-quellen).
- **§ 16b Abs. 6 AufenthG:** Soll der Titel aus Gründen verkürzt oder widerrufen werden, die Sie nicht zu vertreten haben, erhalten Sie bis zu neun Monate Zeit, sich bei einer anderen Hochschule zu bewerben [\[8\]](#content-quellen). Ob ein endgültiges Nichtbestehen darunter fällt, ist nicht geklärt.
- **Anwendungshinweise des BMI (Stand 06/2024):** Ein Wechsel des Studiengangs oder Studienorts ist möglich. Dafür ist ein neuer Aufenthaltstitel zu beantragen, auf den ein Anspruch besteht; das Studium muss in angemessener Zeit abschließbar sein, bis zu einem Gesamtaufenthalt von zehn Jahren [\[10\]](#content-quellen).
- **§ 20 AufenthG:** Die Aufenthaltserlaubnis zur Arbeitsplatzsuche (bis zu 18 Monate) setzt einen **erfolgreichen** Abschluss voraus [\[11\]](#content-quellen).

💡 **Praxistipp:** Wenden Sie sich frühzeitig und schriftlich an Ihre Ausländerbehörde, wenn ein endgültiges Nichtbestehen droht, und legen Sie einen Plan vor (neuer Studiengang, Ausbildung oder Arbeitsstelle). Hintergrund finden Sie in unseren Beiträgen zum [Zweckwechsel vom Studium in die Arbeit](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de) und zum [Visum zur Arbeitssuche](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de).

## Krankheit, ärztliches Attest und Rücktritt

Viele Prüfungsordnungen erlauben einen **Rücktritt ohne Angabe von Gründen** bis kurz vor der Prüfung:

- **RWTH Aachen:** bis drei Werktage vorher; ein ärztliches Attest muss bis zum dritten Werktag vorliegen [\[14\]](#content-quellen).
- **TU Berlin:** bis drei Tage vorher; ein Attest ist innerhalb von fünf Tagen einzureichen [\[15\]](#content-quellen).
- **Universität zu Köln (WiSo):** bis zwei Wochen vorher [\[19\]](#content-quellen).
- **TH Köln:** bis eine Woche vorher [\[20\]](#content-quellen).
- **KIT:** bei Klausuren bis zum Vortag, bei mündlichen Prüfungen bis drei Werktage vorher [\[16\]](#content-quellen).

Danach ist ein Rücktritt nur aus triftigem Grund möglich und unverzüglich mitzuteilen, meist mit ärztlichem Attest. An der LMU reicht eine Arbeitsunfähigkeitsbescheinigung für den Arbeitgeber **nicht** aus [\[13\]](#content-quellen). Prüfungsausschüsse können auch amtsärztliche oder vertrauensärztliche Atteste verlangen. Erscheinen Sie ohne triftigen Grund nicht, wird die Prüfung in allen untersuchten Hochschulen mit 5,0 bzw. „nicht bestanden" gewertet.

Nutzen Sie für den Rücktritt die offiziellen Formulare Ihres Prüfungsamts. Unsere [Vorlage für eine Krankmeldung](/de/templates/krankmeldung) hilft beim Formulieren, ist aber **kein Rücktrittsformular des Prüfungsamts**. Wenn Sie länger ausfallen, prüfen Sie ein Urlaubssemester, siehe unseren [Leitfaden zum Urlaubssemester](/de/blog/urlaubssemester-taking-a-semester-off-in-germany-de) und die [Vorlage für den Antrag auf ein Urlaubssemester](/de/templates/urlaubssemester-antrag).

## 10 Hochschulen im Vergleich

| Hochschule / Studiengang (Beispiel) | Bestehensgrenze | Versuche | Wiederholungsfrist | Dritter / zusätzlicher Versuch | Mündliche Ergänzungsprüfung | Freiversuch | Notenverbesserung | Wiederholung Abschlussarbeit | Folge bei endgültigem Nichtbestehen |
|---|---|---|---|---|---|---|---|---|---|
| [TUM](/de/universities/technische-universitat-munchen-partner-019ddbba), B.Sc. Informatik [\[12\]](#content-quellen) | 4,0 | unbegrenzt innerhalb der Credit-Fristen; GOP i. d. R. 2 | ca. 6 Monate / nächster Termin | entfällt (keine feste Zahl) | nicht gefunden | nein | nur wenn FPSO erlaubt (hier nein) | 1×, neues Thema | Bescheid + Exmatrikulation |
| [LMU](/de/universities/ludwig-maximilians-universitat-munchen-q55044), B.Sc. Informatik [\[13\]](#content-quellen) | 4,0 | unbegrenzt bis Regelstudienzeit + 2; GOP 2 | Regelstudienzeit + 2 Semester | entfällt (keine feste Zahl) | nicht gefunden | ja | ja, einmal | 1× | Bescheid mit Rechtsbehelfsbelehrung |
| [RWTH Aachen](/de/universities/rwth-aachen-university-partner-019de9ee), B.Sc. Informatik [\[14\]](#content-quellen) | 4,0 (jede Teilleistung) | 3 | keine allgemeine Frist; Prüfungen mind. 2× jährlich | Drittversuch regulär | auf Antrag nach 2. Wiederholung (4,0/5,0) | auf Antrag, bis 3 Klausuren (Sem. 1–3) | nein (5–30 CP streichbar) | 1×, Anmeldung binnen 3 Semestern | Exmatrikulation zum Semesterende; Widerspruch 1 Monat |
| TU Berlin, AllgStuPO [\[15\]](#content-quellen) | 4,0 | 3 + 4. nach Studienfachberatung | in der Regel nächster regulärer Termin | 4. Versuch nach Beratung | Prüfer*in kann anbieten | ja (Bachelor, 1. Semester) | nein | 2× | Exmatrikulation von Amts wegen, frühestens nach 2 Monaten |
| [KIT](/de/universities/karlsruher-institut-fur-technologie-q309988), B.Sc. Informatik [\[16\]](#content-quellen) | 4,0 | 2 | bis Ende Prüfungszeitraum des 4. Semesters nach Fehlversuch | nur ausnahmsweise auf Antrag | automatisch, max. 4,0 | nein | nein | 1× | Verlust des Prüfungsanspruchs; Exmatrikulation nach LHG |
| Universität Hamburg, WiSo [\[17\]](#content-quellen) | 4,0 | 3 | möglichst nächster Termin | Härtefall: Ausschussvorsitz entscheidet | nein | nein | nein | 1× (2. nur in Ausnahmefällen) | Bescheid; Widerspruch 1 Monat |
| Goethe-Uni Frankfurt, WiWi [\[18\]](#content-quellen) | 4,0 | Pflicht 3, Wahl 2 | Semesterende / Beginn Folgesemester | Drittversuch nur in Pflichtmodulen | mündlich statt Klausur möglich | nein (hier nicht genutzt) | nein (hier nicht genutzt) | 1×, neues Thema | Bescheid + Exmatrikulation; Widerspruch 1 Monat |
| Universität zu Köln, WiSo BWL [\[19\]](#content-quellen) | 4,0 | 3 je Modul | keine allgemeine Frist genannt | bis 3 Zusatzversuche je Bachelor (+1 ab 140 LP) | nein | nein | nein | 1×, ohne Zusatzversuche | Exmatrikulation aus dem Studiengang |
| TH Köln, Rahmen-PO [\[20\]](#content-quellen) | 4,0 | 3 | Antrag auf Zusatzversuch binnen 1 Monat | 4 Zusatzversuche auf Antrag | nein | nein | ja, 2 der 4 Zusatzversuche | 1× (+ Kolloquium 1×) | Bescheid + Exmatrikulation |
| FU Berlin, BWL [\[21\]](#content-quellen) | 4,0 (MC: 50 %, nie unter 40 %) | 3 | mind. eine Wiederholung bis 2. Folgesemester | Härtefall: Rücktritt vom letzten Versuch (Antrag binnen 3 Monaten) | nein | nein | nein (bis 2 Module ≤ 12 LP unbenotet) | 1× | Bescheid + Exmatrikulation |

> Diese Beispiele beziehen sich auf bestimmte Studiengänge oder Prüfungsordnungen. Ihre eigene Prüfungsordnung kann abweichen. *(These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.)*

*Regelungen geprüft im September 2026.*

## Vier Fallbeispiele aus dem Studium

### Erstversuch nicht bestanden

- **Zuerst prüfen:** den Notenbescheid und die Prüfungsordnung Ihres Studiengangs.
- **Frage ans Prüfungsamt:** „Wann ist der nächste Termin, und muss ich mich selbst anmelden?"
- **Frist:** Wiederholungs- und Anmeldefrist, in Bayern in der Regel innerhalb von sechs Monaten [\[6\]](#content-quellen).
- **Mögliche Optionen:** Freiversuch (z. B. LMU, RWTH, TU Berlin), Klausureinsicht.
- **Fehler vermeiden:** die Wiederholung aufschieben und eine Frist verpassen.

### Drittversuch oder letzter Versuch steht an

- **Zuerst prüfen:** ob es wirklich Ihr letzter Versuch ist und ob Zusatzversuche oder eine mündliche Ergänzungsprüfung vorgesehen sind.
- **Frage ans Prüfungsamt:** „Welche Möglichkeiten habe ich, falls ich auch diesen Versuch nicht bestehe?"
- **Frist:** Anmelde- und Rücktrittsfristen; an der TH Köln der Monat für den Antrag auf Zusatzversuch [\[20\]](#content-quellen).
- **Mögliche Optionen:** Studienfachberatung (an der TU Berlin Voraussetzung für den 4. Versuch [\[15\]](#content-quellen)), Härtefallantrag, Wechsel der Prüfungsform, falls zulässig.
- **Fehler vermeiden:** krank zur Prüfung gehen, statt rechtzeitig mit Attest zurückzutreten.

### Pflichtmodul endgültig nicht bestanden

- **Zuerst prüfen:** den Bescheid und die Rechtsbehelfsbelehrung.
- **Frage ans Prüfungsamt:** „Gibt es eine Härtefallregelung, und wann wird die Exmatrikulation wirksam?"
- **Frist:** Widerspruchs- oder Klagefrist, häufig ein Monat (z. B. RWTH, Hamburg, Frankfurt).
- **Mögliche Optionen:** Widerspruch, Härtefallantrag, Fachwechsel in einen Studiengang ohne das gescheiterte Pflichtfach.
- **Fehler vermeiden:** sich an einer anderen Hochschule einschreiben, ohne den Fehlversuch anzugeben.

### Internationale Studierende und Studiengangswechsel

- **Zuerst prüfen:** Ihren Aufenthaltstitel und die Einschreibungsregeln des neuen Bundeslandes.
- **Frage ans Prüfungsamt:** „Kann ich eine Unbedenklichkeitsbescheinigung erhalten, und welche Leistungen werden anerkannt?"
- **Frist:** Bewerbungsfristen des neuen Studiengangs und die Gültigkeit Ihres Titels.
- **Mögliche Optionen:** neuer Studiengang mit neuem Aufenthaltstitel (Anspruch nach BMI-Hinweisen [\[10\]](#content-quellen)), Ausbildung, Arbeitsstelle.
- **Fehler vermeiden:** die Ausländerbehörde erst informieren, wenn der Titel bereits ausläuft.

## Checkliste: Prüfung nicht bestanden – was jetzt

- Bescheid und Rechtsbehelfsbelehrung in Ruhe lesen.
- In der Prüfungsordnung prüfen, im wievielten Versuch Sie waren.
- Wiederholungs- und Anmeldefrist notieren.
- Freiversuch, Zusatzversuch oder mündliche Ergänzungsprüfung prüfen.
- Klausureinsicht nutzen, wenn sie angeboten wird.
- Mit Prüfungsamt und Studienfachberatung sprechen.
- Beim letzten Versuch: Beratung suchen, Härtefallantrag prüfen, Widerspruchsfrist im Blick behalten.
- Nicht-EU-Studierende: einen Plan erstellen und die Ausländerbehörde schriftlich kontaktieren.
- Auf Ihre Gesundheit achten; Unterstützung finden Sie auch in unserem Beitrag zu [Einsamkeit und mentaler Gesundheit](/de/blog/einsamkeit-mentale-gesundheit-internationale-studierende-deutschland).

## Häufige Fragen (FAQ)

### Wie oft kann ich eine Prüfung an einer deutschen Hochschule wiederholen?

Eine bundesweite Regel gibt es nicht; entscheidend ist Ihre Prüfungsordnung. In den zehn untersuchten Hochschulen reicht die Spanne von zwei Versuchen (KIT) über drei (etwa RWTH, Hamburg, FU Berlin) und vier nach Studienfachberatung (TU Berlin) bis zu unbegrenzten Versuchen innerhalb von Zeitgrenzen (TUM, LMU). Die Universität zu Köln und die TH Köln gewähren zusätzlich Zusatzversuche.

### Ist eine 4,0 bestanden?

Ja, in allen untersuchten Hochschulen ist 4,0 die Bestehensgrenze. Ausnahmen betreffen den Aufbau von Modulen: In der RWTH-Informatik muss jede Teilleistung mindestens 4,0 erreichen. An der FU Berlin gilt bei Multiple-Choice-Prüfungen eine Grenze von 50 % der Punkte, nie unter 40 %. Unbenotete Studienleistungen werden nur mit „bestanden" oder „nicht bestanden" bewertet.

### Gefährdet eine nicht bestandene Prüfung mein Visum?

Eine nicht bestandene Prüfung beendet Ihren Aufenthaltstitel nicht. Relevant wird es erst, wenn fraglich ist, ob Sie Ihr Studium in angemessener Zeit abschließen können, oder nach einer Exmatrikulation. Auch dann ist eine Verkürzung des Titels eine Ermessensentscheidung. Ein Studiengangswechsel erfordert einen neuen Aufenthaltstitel. Nehmen Sie früh schriftlich Kontakt mit der Ausländerbehörde auf.

### Kann ich nach endgültigem Nichtbestehen woanders studieren?

Oft ja, meist aber nicht im selben Studiengang. Je nach Bundesland wird die Einschreibung in denselben oder einen eng verwandten Studiengang versagt (etwa NRW, Bayern, Hamburg) oder kann versagt werden (Hessen). Ein anderes Fach ist häufig möglich. Rechnen Sie mit Offenlegungspflichten, etwa an der LMU, oder einer Unbedenklichkeitsbescheinigung wie an der FU Berlin.

### Kann ich eine bestandene Prüfung wiederholen, um die Note zu verbessern?

Meist nicht. Ausnahmen in unserer Stichprobe: An der LMU ist eine Notenverbesserung einmal zum nächsten regulären Termin möglich, an der TH Köln über zwei der vier Zusatzversuche; jeweils zählt die bessere Note. An der TUM hängt es von der Fachprüfungsordnung ab. RWTH und FU Berlin erlauben stattdessen, einzelne Noten bei Abschluss in der Regelstudienzeit zu streichen bzw. umzuwandeln.

### Was ist ein Freiversuch?

Beim Freiversuch wird ein früher Fehlversuch nicht auf Ihre Versuche angerechnet. An der LMU gilt das für den ersten Fehlversuch innerhalb der Regelstudienzeit, nicht für GOP und Abschlussarbeit. An der RWTH können Bachelorstudierende das für bis zu drei Klausuren der ersten drei Semester beantragen, an der TU Berlin gilt es für Bachelor-Modulprüfungen des ersten Semesters.

## Quellen

1. KMK, Ländergemeinsame Strukturvorgaben (i. d. F. vom 04.02.2010) — https://www.kmk.org/fileadmin/Dateien/veroeffentlichungen_beschluesse/2003/2003_10_10-Laendergemeinsame-Strukturvorgaben.pdf
2. HRK/KMK, Vorlage Diploma Supplement (2015) — https://www.hrk.de/fileadmin/redaktion/hrk/02-Dokumente/02-03-Studium/02-03-01-Studium-Studienreform/Bologna_Dokumente/Diploma_Supplement_englisch_2015_aktualisiert.pdf
3. KMK, Vereinbarung über die Festsetzung der Gesamtnote bei ausländischen Hochschulzugangszeugnissen (2013) — https://www.kmk.org/fileadmin/Dateien/pdf/ZAB/Hochschulzugang_Beschluesse_der_KMK/GesNot05.pdf
4. Hochschulgesetz NRW (§§ 50, 51, 64) — https://recht.nrw.de/lrgv/gesetz/07052025-gesetz-ueber-die-hochschulen-des-landes-nordrhein-westfalen-hochschulgesetz-hg/
5. Hamburgisches Hochschulgesetz (§§ 42, 44, 65; konsolidierte Fassung, Fakultät für Rechtswissenschaft der Universität Hamburg) — https://www.jura.uni-hamburg.de/media/service/rechtsgrundlagen/a-allgemeines/a--i--hamburgisches-hochschulgesetz--hmbhg-.pdf
6. Bayerisches Hochschulinnovationsgesetz (Art. 84, 91, 94) — https://www.gesetze-bayern.de/Content/Document/BayHIG-84
7. Hessisches Hochschulgesetz (2021) — https://www.uni-giessen.de/de/mug/1/pdf/1_31_00_1_HHG2021_DruckJLU
8. § 16b AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
9. § 7 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__7.html
10. BMI, Anwendungshinweise zum Fachkräfteeinwanderungsgesetz (Rechtsstand 01.06.2024) — https://www.bmi.bund.de/SharedDocs/downloads/DE/veroeffentlichungen/themen/migration/anwendungshinweise-fachkraefteeinwanderungsgesetz.pdf?__blob=publicationFile&v=16
11. § 20 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html
12. TUM, APSO (Lesefassung mit Änderungen bis 2024) — https://www.edu.sot.tum.de/fileadmin/w00bed/edu/Documents/Studium/APSO/Lesb.F._APSO_vom_18.03.2011_mit_11._AES_vom_17.12.2024.pdf
13. LMU, Prüfungs- und Studienordnung B.Sc. Informatik (2022) — https://cms-cdn.lmu.de/media/contenthub/amtliche-veroeffentlichungen/1568-16in-ba-nf30-2022-ps00.pdf
14. RWTH Aachen, Übergreifende Prüfungsordnung (Fassung 2025) — https://www.rwth-aachen.de/global/show_document.asp?id=aaaaaaaaddqknak
15. TU Berlin, AllgStuPO (Amtliches Mitteilungsblatt 29/2024) — https://www.static.tu.berlin/fileadmin/www/10000000/Studiengaenge/StuPOs/AllgStuPO_deu.pdf
16. KIT, SPO B.Sc. Informatik (2022) — https://www.sle.kit.edu/downloads/AmtlicheBekanntmachungen/2022_AB_034.pdf
17. Universität Hamburg, Prüfungsordnung WiSo B.Sc. (2024) — https://www.uni-hamburg.de/campuscenter/studienorganisation/ordnungen-satzungen/pruefungsordnungen/wirtschafts-und-sozialwissenschaften/20240508-po-wiso-bsc-82.pdf
18. Goethe-Universität Frankfurt, Prüfungsordnung B.Sc. Wirtschaftswissenschaften (2022) — https://www.uni-frankfurt.de/112161167/BA_Wirtschaftswissenschaften_2022_01_31.pdf
19. Universität zu Köln, WiSo Gemeinsame Prüfungsordnung (2025) — https://wiso.uni-koeln.de/sites/fakultaet/dokumente/PA/po/GPO_2025.pdf
20. TH Köln, Rahmenprüfungsordnungen (2023) — https://www.th-koeln.de/mam/downloads/deutsch/hochschule/amtlichemitteilungen/inkraftsetzungssatzung_rpoen_2023_final.pdf
21. FU Berlin, RSPO (2013) — https://www.fu-berlin.de/service/zuvdocs/amtsblatt/2013/ab322013.pdf ; SPO B.Sc. BWL (2017) — https://www.wiwiss.fu-berlin.de/studium-lehre/pruefungsbuero/studien--und-pruefungsordnungen/BWLab112017.pdf
22. LMU, Immatrikulationshindernisse und Versagungsgründe — https://www.lmu.de/de/workspace-fuer-studierende/1x1-des-studiums/immatrikulationshindernisse-und-versagungsgruende/index.html
23. FU Berlin WiWiss, Unbedenklichkeitsbescheinigung — https://www.wiwiss.fu-berlin.de/studium-lehre/pruefungsbuero/Bescheinigungen/Unbedenklichkeitsbescheinigungen/

Regelungen im September 2026 an 10 deutschen Hochschulen geprüft; Regeln ändern sich – prüfen Sie die aktuelle Fassung Ihrer eigenen Prüfungsordnung. Allgemeine Informationen, keine Rechtsberatung. *(Regulations checked in September 2026 across 10 German universities; rules change — check the current version of your own regulation. General information, not legal advice.)*
MD;

        $variants = [
            'tr' => [
                'slug' => 'german-university-grading-system-and-exam-retakes',
                'title' => 'Almanya\'da Üniversite Not Sistemi ve Sınav Tekrar Hakkı',
                'excerpt' => 'Almanya\'da ülke genelinde tek bir "üç hak" kuralı yok. 1,0–5,0 not sistemi, geçme notu, tekrar süreleri, Freiversuch, kesin başarısızlık ve oturum izni etkisi; 10 üniversite karşılaştırması ve adım adım kontrol listesi.',
                'meta_title' => 'Almanya\'da Sınav Tekrar Hakkı ve Not Sistemi (2026)',
                'meta_description' => 'Almanya\'da sınav tekrar hakkı kaç tane? 4,0 geçme notu, Drittversuch, Freiversuch, endgültig nicht bestanden ve vize etkisi; 10 üniversiteden örnekler.',
                'body' => $trBody,
            ],
            'en' => [
                'slug' => 'german-university-grading-system-and-exam-retakes-en',
                'title' => 'German University Grading System and Exam Retake Rules',
                'excerpt' => 'German universities grade from 1.0 to 5.0, and 4.0 usually passes. But there\'s no national "three attempts" rule. Here\'s how retakes, Freiversuch, final failure and your residence permit really work, with 10 universities compared.',
                'meta_title' => 'Exam Retakes & Grading at German Universities (2026)',
                'meta_description' => 'Failed an exam in Germany? How 1.0–5.0 grades work, why 3 attempts is not a national rule, retake deadlines, final failure, visa impact and 10 unis compared.',
                'body' => $enBody,
            ],
            'de' => [
                'slug' => 'german-university-grading-system-and-exam-retakes-de',
                'title' => 'Notensystem und Prüfungswiederholung an deutschen Hochschulen',
                'excerpt' => 'Keine bundesweite 3-Versuche-Regel: was 1,0 bis 5,0 bedeuten, wie Wiederholungsprüfung, Freiversuch und Notenverbesserung funktionieren und was „endgültig nicht bestanden“ für Studium und Aufenthalt heißt. Mit Vergleich von 10 Hochschulen.',
                'meta_title' => 'Prüfungswiederholung im Studium: Noten, Versuche, Fristen',
                'meta_description' => 'Prüfungswiederholung im Studium: wie viele Versuche Sie haben, ob 4,0 besteht, was Freiversuch und Drittversuch bedeuten und was bei Nichtbestehen gilt.',
                'body' => $deBody,
            ],
        ];

        // Slug başka bir yazıya aitse ezme: hiçbir şey yazmadan önce üç slug'ı da kontrol et.
        foreach ($variants as $v) {
            $foreign = Post::where('slug', $v['slug'])->where(fn ($q) => $q->whereNull('translation_group_id')->orWhere('translation_group_id', '!=', $groupId))->first();
            if ($foreign) {
                throw new RuntimeException("Not sistemi yazısı: '{$v['slug']}' slug'ı başka bir çeviri grubuna ait (#{$foreign->id}), hiçbir şey yazılmadı.");
            }
        }

        DB::transaction(function () use ($variants, $groupId, $userId, $categoryId) {
        foreach ($variants as $locale => $v) {
            // content_html + reading_minutes: Post::booted() content_md'den üretir (MarkdownRenderer + BlogAutoLinker).
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'],
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'is_published' => true,
            ];
            // Tekrar koşarsa yayın tarihi korunur; değişmeyen alanlar updated_at'a dokunmaz.
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug'], 'published_at' => now()]);
        }
        });
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'german-university-grading-system-and-exam-retakes',
            'german-university-grading-system-and-exam-retakes-en',
            'german-university-grading-system-and-exam-retakes-de',
        ])->delete();
    }
};
