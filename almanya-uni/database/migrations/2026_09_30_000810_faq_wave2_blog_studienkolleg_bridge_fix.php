<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * FAQ Parity Batch B Wave 2 — ek P0 blog kaynağı: studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how
 * (TR/EN/DE). Chatbot smoke testinde (30.09.2026) kaynak gösterildi. Yalnız F1/F4/F5 ile çelişen bölümler düzeltilir:
 * "Kimler Studienkolleg'e gitmeli" listesi (kayıt şartı, "yerleşmesiz → Studienkolleg yolu açık", meslek lisesi yanlışı),
 * "çoğu Türk lise mezunu Studienkolleg'den geçer" özeti / girişi / sonucu, "YKS'de herhangi bir 4 yıllık programa
 * yerleşme yeterli", "FSP tüm üniversitelere başvuru hakkı", T-Kurs'a biyoloji, "uni-assist posta ile orijinal belge
 * ister"; excerpt (+ EN/DE meta_description) cümlesi. Başlık, slug, diğer bölümler değişmez.
 * Doğru (anabin Türkiye kuralları, DAAD kabul veritabanı, KMK Rahmenordnung 2006, uni-assist; 29–30.09.2026):
 * giriş hakkı yolu lise türüne değil diploma + YKS (SAY/SÖZ/EA/DİL > 180) + yerleşme / Türkiye'deki öğrenime bağlı;
 * yerleşmesiz → resmî genel kural yok; FH-Studienkolleg FSP'si üniversite girişi vermez; T-Kurs biyoloji hariç;
 * uni-assist başvurusu tamamen dijital.
 *
 * Motor 2026_09_30_000600 ile aynı (canlı blok metni → md satırı/penceresi; replace / delete / subs / probe; fieldsub).
 * Ek: fieldsub {null_ok}: alan boşsa dokunulmaz (meta_description boşsa fallback excerpt'tir ve o da düzeltilir).
 * Ön kontrol yazmadan önce tüm kayıtlarda yapılır; kayıt içi / kayıtlar arası kısmi durum, eşleşmeyen blok, eksik kayıt
 * → RuntimeException, hiçbir şey yazılmaz. Tek transaction. İkinci çalıştırma no-op. PHPUnit altında (boş test DB'si)
 * sorun varsa sessizce çıkar.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "records": [
  {
   "table": "posts",
   "slug": "studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Studienkolleg, Almanya'daki üniversiteye giriş yeterliliğine sahip olmayan yabancı öğrencilerin, Alman yükseköğretim sistemine uyum sağlamaları ve gerekli akademik seviyeye ulaşmaları için tasarlanmış bir yıllık (iki sömestr) hazırlık kursudur. Almanya'daki lise eğitimi (Abitur) genellikle 12 veya 13 yıl sürerken, Türkiye'deki lise eğitimi 12 yıldır. Bu süre farkı ve eğitim sistemlerinin farklılıkları nedeniyle, Türk lise diploması genellikle Almanya'daki üniversitelere doğrudan kabul için yeterli görülmez. Studienkolleg, bu farkı kapatarak öğrencilere Alman üniversite sistemine giriş hakkı kazandırır.",
      "replace": "Studienkolleg, Almanya'da üniversiteye doğrudan giriş hakkı (HZB) olmayan yabancı adayları üniversiteye hazırlayan, iki sömestrlik bir hazırlık programıdır. Türk lise diploması **tek başına** Almanya'da üniversiteye giriş hakkı vermez; anabin'deki Türkiye kuralları diplomayı YKS sonucu, yerleşme ve varsa Türkiye'deki öğrenimle birlikte değerlendirir. Bu yüzden bazı adaylar doğrudan (alana bağlı) başvurabilir, bazıları için Studienkolleg gerekir. Studienkolleg, sonunda yapılan Feststellungsprüfung ile üniversiteye giriş yolunu açar."
     },
     {
      "line": "Türkiye'den Almanya'da lisans eğitimi almak isteyen birçok öğrenci, lise diplomalarının Almanya'da doğrudan üniversiteye giriş için yeterli olup olmadığını merak eder. Bu durum, öğrencinin lise türüne ve YKS sonucuna göre değişiklik gösterir. İşte genel hatlarıyla kimlerin Studienkolleg'e gitmesi gerektiği:",
      "replace": "Studienkolleg'e ihtiyacın olup olmadığı lise türüne değil, **giriş hakkı yoluna** bağlıdır. anabin'in güncel değerlendirmesine göre özetle:\n\n- **12 yıllık lise + YKS'de SAY, SÖZ, EA ya da DİL puan türünde 180 puanın üzerinde + fakültede en az 4 yıllık lisans programına yerleşme:** yerleştiğin alanda ve yakın alanlarda doğrudan başvuru; Studienkolleg gerekmez. Türkiye'de kayıt yaptırman gerekmez.\n- **Üniversiteye bağlı bir yüksekokulda 4 yıllık programa yerleşme:** alana bağlı doğrudan giriş, yalnızca Fachhochschule'lere.\n- **Meslek lisesi + 180 puanın üzerinde:** alana yönelik Studienkolleg; Türkiye'de başarıyla tamamlanmış bir yıllık lisans öğrenimiyle alana bağlı doğrudan giriş.\n- **MYO'da yalnızca bir yıl:** Studienkolleg yolu, yalnızca Fachhochschule'ler için. **Tamamlanmış önlisans + TYT'de 150 puanın üzerinde:** alana bağlı doğrudan giriş.\n- **Yerleşmen yoksa ya da eşiğin altındaysan:** resmî kaynaklar genel bir yol tanımlamıyor; otomatik Studienkolleg ya da otomatik doğrudan kabul yok. Kendi durumunu üniversiteye ya da uni-assist'e sor.\n- **Uluslararası Bakalorya (IB) veya A-Level:** değerlendirme ders ve not şartlarına bağlıdır; üniversitenin şartlarını kontrol et.\n\nH+/H- kodları okul diplomalarını değil, kurumları gösterir. Ayrıntılar ve kaynaklar: [anabin rehberi](/tr/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma)",
      "probe": "Studienkolleg'e ihtiyacın olup olmadığı lise türüne değil, **giriş hakkı yoluna** bağlıdır. anabin'in güncel değerlendirmesine göre özetle:"
     },
     {
      "line": "Genel Lise Mezunları (Anadolu Lisesi, Fen Lisesi, Sosyal Bilimler Lisesi vb.): YKS'de 4 Yıllık Bir Bölüme Yerleşmiş ve Kayıt Yaptırmış Olanlar: Eğer Türkiye'de YKS'de 4 yıllık bir lisans programına yerleştiyseniz ve bu bölüme kayıt yaptırdıysanız (yani üniversite öğrencisi statüsündeyseniz), Almanya'da Studienkolleg'e başvurabilirsiniz. Bu durumda, Türkiye'de yerleştiğiniz bölümle ilgili bir Studienkolleg kursunu (örneğin, mühendislik için T-Kurs, tıp için M-Kurs) seçmeniz gerekecektir.",
      "delete": true
     },
     {
      "line": "YKS'de 4 Yıllık Bir Bölüme Yerleşmiş Ancak Kayıt Yaptırmamış Olanlar: Bu durumda da genellikle Studienkolleg'e başvurma hakkınız bulunur. Ancak bazı üniversiteler kayıt şartı arayabilir.",
      "delete": true
     },
     {
      "line": "YKS'de 2 Yıllık Bir Bölüme Yerleşmiş veya Hiç Yerleşememiş Olanlar: Bu kategoriye giren öğrenciler için genellikle Studienkolleg yolu açıktır. Ancak yine de YKS'de belirli bir puan barajını aşmış olmanız beklenir. Her üniversitenin veya Studienkolleg'in kendi kabul şartları olabileceği için, hedeflediğiniz kurumun güncel koşullarını kontrol etmek hayati önem taşır.",
      "delete": true
     },
     {
      "line": "Meslek Lisesi Mezunları: Meslek lisesi mezunları genellikle doğrudan Studienkolleg'e başvuru hakkına sahip değildir. Ancak, meslek lisesi diploması ile Türkiye'de 4 yıllık bir lisans programına yerleşmiş ve kayıt yaptırmışlarsa, bu durumda Studienkolleg'e başvurma şansları olabilir. Bu durum, Almanya'daki üniversitelerin ve Studienkolleg'lerin değerlendirmesine bağlıdır. Genellikle, meslek lisesi mezunlarının Almanya'da üniversite eğitimi alabilmeleri için Türkiye'de kendi alanlarında 4 yıllık bir lisans programını tamamlamış olmaları ve ardından Almanya'da benzer bir alanda yüksek lisans yapmayı hedeflemeleri daha yaygın bir yoldur. Lisans için ise, ek koşullar (örneğin, YKS'de belirli bir başarı) aranabilir.",
      "delete": true
     },
     {
      "line": "Uluslararası Bakalorya (IB) veya A-Level Diploması Olanlar: Bu tür uluslararası geçerliliği olan diplomalara sahip öğrenciler, genellikle doğrudan Alman üniversitelerine başvurabilirler ve Studienkolleg'e gitmelerine gerek kalmaz. Ancak, diploma notları ve ders içerikleri Almanya'daki kabul kriterlerini karşılamalıdır.",
      "delete": true
     },
     {
      "line": "Özetle: Türkiye'den lise mezunu olup Almanya'da lisans eğitimi almak isteyen çoğu öğrencinin yolu Studienkolleg'den geçmektedir. En kesin bilgiyi almak için, Almanya'daki üniversitelerin ve Studienkolleg'lerin resmi web sitelerindeki \"Admission Requirements for International Students\" (Uluslararası Öğrenciler İçin Kabul Şartları) bölümlerini incelemeniz veya doğrudan ilgili kuruma danışmanız şiddetle tavsiye edilir.",
      "replace": "**Özetle:** Türk lise mezunları için genel bir \"herkes Studienkolleg'e gider\" kuralı yoktur; yol, diploma türüne, YKS sonucuna, yerleşmeye ve Türkiye'deki öğrenime göre belirlenir. Kesin kararı başvurduğun üniversite (ya da eyaletin yetkili makamı) verir; bu yüzden hedeflediğin üniversitenin ve Studienkolleg'in \"Admission Requirements for International Students\" sayfalarını mutlaka kontrol et."
     },
     {
      "line": "T-Kurs (Technischer Kurs): Mühendislik bilimleri, matematik, fen bilimleri (fizik, kimya, biyoloji) ve bazı teknik bölümler için. Dersler: Almanca, Matematik, Fizik, Kimya veya Bilişim.",
      "subs": [
       {
        "old": "(fizik, kimya, biyoloji)",
        "new": "(fizik, kimya; biyoloji hariç)"
       }
      ]
     },
     {
      "line": "YKS Sonucu: Türkiye'de 4 yıllık bir lisans programına yerleşmiş olmanız veya belirli bir YKS puanına sahip olmanız istenebilir. Bu, en önemli sorun noktası'lerden biridir ve her üniversite/Studienkolleg için farklılık gösterebilir. Genel olarak, YKS'de herhangi bir 4 yıllık lisans programına yerleşmiş olmak yeterli kabul edilir. Ancak, yerleştiğiniz bölümün Almanya'da okumak istediğiniz bölümle ilgili olması veya Studienkolleg kursunuzla uyumlu olması beklenir.",
      "replace": "**YKS Sonucu ve Yerleşme:** Studienkolleg'e mi yoksa doğrudan üniversiteye mi başvurabileceğini, lise diplomanla birlikte YKS puan türün, puanın (180'in üzerinde) ve yerleştiğin programın türü belirler. Yerleştiğin alan, başvurabileceğin alanları ya da Studienkolleg kursunu etkiler. Yerleşme yoksa resmî kaynaklarda genel bir yol tanımlanmamıştır; hedef kurumun şartlarını kontrol et."
     },
     {
      "line": "Bir yılın sonunda, \"Feststellungsprüfung\" (FSP) adı verilen bitirme sınavına girersiniz. FSP, Studienkolleg'de gördüğünüz derslerden yapılır ve bu sınavı başarıyla geçmek, Almanya'daki tüm üniversitelere lisans eğitimi için başvurma hakkı kazandırır. FSP notunuz, üniversite başvurularınızda önemli bir rol oynayacaktır.",
      "replace": "Bir yılın sonunda \"Feststellungsprüfung\" (FSP) adı verilen bitirme sınavına girersin. FSP, Almanca ve kursunun iki ana dersinden yapılır. Sınavı geçmek, kursunun hazırladığı alanlarda lisans başvurusu yapma imkânı verir; bir Fachhochschule Studienkolleg'inde verilen FSP üniversitelere giriş hakkı sağlamaz. FSP geçmek bir üniversite kontenjanı garantisi değildir; FSP notun başvurularda önemli rol oynar."
     },
     {
      "line": "Evet, Uni-Assist bazı durumlarda belgelerin noter onaylı kopyalarının veya belirli evrakların orijinallerinin posta yoluyla gönderilmesini isteyebilir. Bu durumda, belgelerinizi (diploma, transkript, dil sertifikası vb.) noter onaylı kopyalarıyla birlikte, güvenilir ve takip numarası olan bir kargo firması (DHL, UPS, FedEx gibi) aracılığıyla göndermeniz en güvenli yoldur. Asla orijinal belgelerinizi göndermeden önce kendinize birer kopyasını almayı unutmayın. Kargo takip numarası sayesinde gönderinizin durumunu izleyebilir ve kaybolma riskini minimize edebilirsiniz. Uni-Assist, belgelerinizi değerlendirdikten sonra genellikle geri göndermez, bu yüzden orijinal belgelerinizi göndermeden önce çok iyi düşünmeli ve sadece istendiği durumlarda göndermelisiniz.",
      "replace": "uni-assist'e göre başvuru **tamamen dijitaldir**: belgelerini My assist hesabına yüklersin; uni-assist'e posta ile belge göndermen gerekmez. Resmî belgelerinin (imzalı ve mühürlü ya da doğrulama kodlu) taramalarını, orijinal dilde ve Almanca ya da İngilizce onaylı tercümesiyle yükle. Bir üniversite kayıt aşamasında onaylı kopyaları ayrıca isteyebilir; bu, üniversiteye özgüdür ve üniversitenin talimatlarına göre yapılır. Emin değilsen uni-assist'in iletişim formundan sor."
     },
     {
      "line": "Almanya'da üniversite hayalinizi gerçekleştirmek için Studienkolleg, birçok Türk öğrenci için kaçınılmaz ve değerli bir adımdır. Bu süreç, sadece akademik bir hazırlık değil, aynı zamanda Almanya'daki yaşama ve kültüre adaptasyon için de önemli bir fırsattır. Lise türünüz ve YKS sonucunuz ne olursa olsun, doğru araştırma, zamanında Studienkolleg başvuru ve iyi bir hazırlık ile bu köprüyü başarıyla geçebilir ve Almanya'daki akademik yolculuğunuza başlayabilirsiniz.",
      "replace": "Studienkolleg, doğrudan giriş hakkı olmayan pek çok aday için Almanya'da üniversiteye giden değerli bir köprüdür; ama herkes için zorunlu değildir. Önce kendi giriş hakkı yolunu (lise türü, YKS puan türü ve puanı, yerleşme, Türkiye'deki öğrenim) kontrol et; ardından doğrudan başvuru mu yoksa Studienkolleg mi gerektiğine göre planını yap. Bu süreç, sadece akademik bir hazırlık değil, aynı zamanda Almanya'daki yaşama ve kültüre uyum için de önemli bir fırsattır."
     }
    ]
   },
   "fieldsub": {
    "excerpt": [
     {
      "old": "Studienkolleg, Türk lise mezunları için bu hayalin ilk ve en önemli adımı olabilir.",
      "new": "Studienkolleg'e ihtiyacın olup olmadığı lise türüne değil, giriş hakkı yoluna bağlıdır."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Do you dream of studying at a university in Germany? For Turkish high school graduates, attending a Studienkolleg can be the first and most important step to making that dream a reality. In this comprehensive guide, you'll find everything you need to know about the Studienkolleg application process, who should apply, and all the important details to keep in mind.",
      "replace": "Do you dream of studying at a university in Germany? Whether you need a Studienkolleg depends on your **access route** — your school certificate together with your YKS result, placement or previous study — not simply on having a Turkish high school diploma. In this guide, you'll find how the Studienkolleg works, who it is for and how to apply."
     },
     {
      "line": "Studienkolleg is a one-year (two-semester) preparatory course designed for international students who do not yet meet the university entrance qualifications for Germany. Its purpose is to help them adapt to the German higher education system and achieve the necessary academic level. While high school education in Germany (leading to the Abitur) typically lasts 12 or 13 years, high school in Turkey is 12 years. Due to this difference in duration and varying educational systems, a Turkish high school diploma is often not considered sufficient for direct admission to German universities. Studienkolleg closes this gap, granting students the right to enter the German university system.",
      "replace": "Studienkolleg is a two-semester preparatory programme for international applicants who do not have direct access to higher education (HZB) in Germany. A Turkish high school diploma does **not on its own** give access to German universities: anabin's rules for Türkiye assess it together with the YKS result, the placement and any study in Türkiye. That is why some applicants can apply directly (subject-restricted) while others need a Studienkolleg. The Studienkolleg opens the route to university through the Feststellungsprüfung at the end."
     },
     {
      "line": "Many students from Turkey who want to pursue a bachelor's degree in Germany wonder if their high school diploma is sufficient for direct university admission. This depends on your high school type and your YKS results. Here's a general overview of who typically needs to attend a Studienkolleg:",
      "replace": "Whether you need a Studienkolleg depends on your **access route**, not on your type of high school. Based on anabin's current assessment:\n\n- **12-year Lise + more than 180 points in YKS (SAY, SÖZ, EA or DIL) + placement in a bachelor's programme of at least 4 years at a faculty:** direct, subject-restricted access for the placed subject and related subjects; no Studienkolleg. You do not need to have enrolled in Türkiye.\n- **Placement in a 4-year programme at a university-affiliated Yüksekokul:** direct subject-restricted access, Fachhochschulen only.\n- **Vocational high school (Meslek Lisesi) + more than 180 points:** subject-oriented Studienkolleg; after one successfully completed year of bachelor's study in Türkiye, direct subject-restricted access.\n- **Only one year at an MYO:** Studienkolleg route, Fachhochschulen only. **Completed Önlisans + more than 150 points in TYT:** direct subject-restricted access.\n- **No placement, or below the threshold:** official sources define no general route; there is no automatic Studienkolleg and no automatic direct admission. Ask the university or uni-assist about your case.\n- **International Baccalaureate (IB) or A-Level:** the assessment depends on subject and grade requirements; check the university's requirements.\n\nH+/H- codes describe institutions, not school certificates. Details and sources: [anabin guide](/en/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma-en)",
      "probe": "Whether you need a Studienkolleg depends on your **access route**, not on your type of high school. Based on anabin's current assessment:"
     },
     {
      "line": "General High School Graduates (Anadolu Lisesi, Fen Lisesi, Sosyal Bilimler Lisesi, etc.): If you were placed into a 4-year bachelor's program in Turkey via YKS and enrolled: If you were placed into a 4-year bachelor's program through YKS in Turkey and have officially enrolled (meaning you have student status), you can apply to a Studienkolleg in Germany. In this case, you'll need to choose a Studienkolleg course related to the program you were placed into in Turkey (for example, a T-Kurs for engineering, an M-Kurs for medicine).",
      "delete": true
     },
     {
      "line": "If you were placed into a 4-year bachelor's program in Turkey via YKS but did not enroll: You generally still have the right to apply to a Studienkolleg. However, some universities might require proof of enrollment.",
      "delete": true
     },
     {
      "line": "If you were placed into a 2-year associate's program via YKS or were not placed at all: For students in this category, the Studienkolleg path is usually open. However, you might still be expected to have exceeded a certain score threshold in YKS. Since each university or Studienkolleg can have its own admission requirements, checking the current conditions of your target institution is crucial.",
      "delete": true
     },
     {
      "line": "Vocational High School Graduates: Vocational high school graduates generally do not have the right to apply directly to a Studienkolleg. However, if they have a vocational high school diploma and were placed into and enrolled in a 4-year bachelor's program in Turkey, they might have a chance to apply to a Studienkolleg. This depends on the assessment of German universities and Studienkollegs. Generally, for vocational high school graduates to study at a university in Germany, it's more common for them to complete a 4-year bachelor's program in their field in Turkey and then aim for a master's degree in a similar field in Germany. For bachelor's degrees, additional conditions (e.g., a certain success in YKS) may be required.",
      "delete": true
     },
     {
      "line": "Students with an International Baccalaureate (IB) or A-Level Diploma: Students with these types of internationally recognized diplomas can usually apply directly to German universities and do not need to attend a Studienkolleg. However, their diploma grades and course content must meet German admission criteria.",
      "delete": true
     },
     {
      "line": "In summary: For most high school graduates from Turkey who want to pursue a bachelor's degree in Germany, the path goes through a Studienkolleg. To get the most accurate information, we strongly recommend checking the \"Admission Requirements for International Students\" sections on the official websites of German universities and Studienkollegs, or contacting the relevant institution directly.",
      "replace": "**In summary:** there is no general \"all Turkish high school graduates go to a Studienkolleg\" rule; your route depends on your type of certificate, your YKS result, your placement and any study in Türkiye. The final decision lies with the university you apply to (or the competent authority of the federal state), so always check the \"Admission Requirements for International Students\" pages of your target university and Studienkolleg."
     },
     {
      "line": "T-Kurs (Technischer Kurs): For engineering sciences, mathematics, natural sciences (physics, chemistry, biology), and some technical fields. Subjects: German, Mathematics, Physics, Chemistry or Computer Science.",
      "subs": [
       {
        "old": "(physics, chemistry, biology)",
        "new": "(physics, chemistry; not biology)"
       }
      ]
     },
     {
      "line": "YKS Result: You might be required to have been placed into a 4-year bachelor's program in Turkey or have a specific YKS score. This is one of the most critical points and can vary for each university/Studienkolleg. Generally, having been placed into any 4-year bachelor's program via YKS is considered sufficient. However, your placed program is expected to be related to the program you want to study in Germany or compatible with your Studienkolleg course.",
      "replace": "**YKS result and placement:** your YKS score type, your score (above 180) and the type of programme you were placed in, together with your high school diploma, determine whether you apply to a Studienkolleg or directly to a university. The subject you were placed in affects which subjects or which Studienkolleg course are open to you. Without a placement, official sources define no general route; check the requirements of your target institution."
     },
     {
      "line": "At the end of the year, you'll take the \"Feststellungsprüfung\" (FSP), the final assessment exam. The FSP covers the subjects you studied at the Studienkolleg, and successfully passing this exam grants you the right to apply for a bachelor's degree at any university in Germany. Your FSP grade will play a significant role in your university applications.",
      "replace": "At the end of the year, you take the \"Feststellungsprüfung\" (FSP), the final assessment exam, in German and two core subjects of your course. Passing it lets you apply for bachelor's programmes in the fields your course prepares you for; an FSP from a Fachhochschule Studienkolleg does not give access to universities. Passing the FSP does not guarantee a study place, and your FSP grade plays a significant role in your applications."
     },
     {
      "line": "Yes, Uni-Assist may sometimes request notarized copies of documents or certain original documents to be sent via postal mail. In this case, the safest way is to send your documents (diploma, transcript, language certificate, etc.) along with notarized copies, using a reliable courier service with a tracking number (such as DHL, UPS, FedEx). Never forget to make copies of your original documents before sending them. With a cargo tracking number, you can monitor the status of your shipment and minimize the risk of loss. Uni-Assist typically does not return your documents after evaluation, so you should think very carefully before sending original documents and only do so when specifically requested.",
      "replace": "According to uni-assist, the application is **fully digital**: you upload your documents in your My assist account and do not need to send documents to uni-assist by post. Upload scans of your official certificates (signed and stamped, or with a verification code) in the original language together with a certified translation into German or English. A university may ask for certified copies separately at enrolment; that is university-specific and follows the university's instructions. If in doubt, ask uni-assist via its contact form."
     },
     {
      "line": "For many Turkish students, Studienkolleg is an unavoidable and valuable step to realize their dream of studying at a university in Germany. This process is not just academic preparation but also an important opportunity to adapt to life and culture in Germany. Regardless of your high school type or YKS result, with thorough research, timely Studienkolleg application, and good preparation, you can successfully cross this bridge and begin your academic journey in Germany.",
      "replace": "For many applicants without direct access, the Studienkolleg is a valuable bridge to university in Germany — but it is not mandatory for everyone. First check your own access route (type of high school, YKS score type and score, placement, study in Türkiye), then plan either a direct application or a Studienkolleg accordingly. The process is not just academic preparation but also an important opportunity to adapt to life and culture in Germany."
     }
    ]
   },
   "fieldsub": {
    "excerpt": [
     {
      "old": "For many Turkish high school graduates, Studienkolleg is the crucial first step to bridge the academic gap and prepare for university life.",
      "new": "Whether you need a Studienkolleg depends on your access route, not on your type of high school."
     }
    ],
    "meta_description": [
     {
      "old": "For many Turkish high school graduates, Studienkolleg is the crucial first step to bridge the academic gap and prepare for university life.",
      "new": "Whether you need a Studienkolleg depends on your access route, not on your type of high school.",
      "null_ok": true
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Hast du den Traum, an einer Universität in Deutschland zu studieren? Für türkische Abiturienten kann das Studienkolleg der erste und wichtigste Schritt sein, um diesen Traum zu verwirklichen. In diesem umfassenden Leitfaden findest du alles über die Studienkolleg-Bewerbung, für wen sie gedacht ist und welche Details du beachten solltest.",
      "replace": "Hast du den Traum, an einer Universität in Deutschland zu studieren? Ob du ein Studienkolleg brauchst, hängt von deinem **Zugangsweg** ab – deinem Schulabschluss zusammen mit YKS-Ergebnis, Zuweisung oder bisherigem Studium – und nicht einfach davon, dass du ein türkisches Lise-Diplom hast. In diesem Leitfaden erfährst du, wie das Studienkolleg funktioniert, für wen es gedacht ist und wie du dich bewirbst."
     },
     {
      "line": "Das Studienkolleg ist ein einjähriger (zwei Semester) Vorbereitungskurs für ausländische Studierende, die noch nicht die Hochschulzugangsberechtigung für Deutschland besitzen. Es soll ihnen helfen, sich an das deutsche Hochschulsystem anzupassen und das erforderliche akademische Niveau zu erreichen. Während die gymnasiale Oberstufe in Deutschland (die zum Abitur führt) in der Regel 12 oder 13 Jahre dauert, beträgt die gymnasiale Ausbildung in der Türkei 12 Jahre. Aufgrund dieser Zeitdifferenz und der unterschiedlichen Bildungssysteme wird ein türkisches Abiturzeugnis oft nicht als ausreichend für die direkte Zulassung zu deutschen Universitäten angesehen. Das Studienkolleg schließt diese Lücke und verschafft den Studierenden das Recht, in das deutsche Universitätssystem einzutreten.",
      "replace": "Das Studienkolleg ist ein zweisemestriges Vorbereitungsprogramm für internationale Bewerber, die keine direkte Hochschulzugangsberechtigung (HZB) für Deutschland haben. Ein türkisches Lise-Diplom eröffnet **allein** keinen Hochschulzugang: Die anabin-Regeln für die Türkei bewerten es zusammen mit dem YKS-Ergebnis, der Zuweisung und einem eventuellen Studium in der Türkei. Deshalb können sich manche Bewerber direkt (fachgebunden) bewerben, während andere ein Studienkolleg brauchen. Das Studienkolleg eröffnet den Weg zur Hochschule über die Feststellungsprüfung am Ende."
     },
     {
      "line": "Viele Studierende aus der Türkei, die ein Bachelorstudium in Deutschland absolvieren möchten, fragen sich, ob ihr Abiturzeugnis für die direkte Hochschulzulassung in Deutschland ausreicht. Dies hängt vom Schultyp des Studierenden und dem YKS-Ergebnis ab. Hier ist ein allgemeiner Überblick darüber, wer ein Studienkolleg besuchen sollte:",
      "replace": "Ob du ein Studienkolleg brauchst, hängt von deinem **Zugangsweg** ab, nicht von der Art deines Lise. Nach der aktuellen Bewertung in anabin:\n\n- **12-jähriges Lise + mehr als 180 Punkte in der YKS (SAY, SÖZ, EA oder DIL) + Zuweisung in einen mindestens 4-jährigen Bachelorstudiengang an einer Fakultät:** fachgebundener direkter Zugang für das zugewiesene Fach und benachbarte Fächer; kein Studienkolleg. Eine Einschreibung in der Türkei ist nicht nötig.\n- **Zuweisung in einen 4-jährigen Studiengang an einer universitätsangehörigen Yüksekokul:** fachgebundener direkter Zugang, nur zu Fachhochschulen.\n- **Meslek Lisesi (Berufsgymnasium) + mehr als 180 Punkte:** fachbezogenes Studienkolleg; nach einem erfolgreich abgeschlossenen Studienjahr in der Türkei fachgebundener direkter Zugang.\n- **Nur ein Jahr an einer MYO:** Studienkolleg-Weg, nur für Fachhochschulen. **Abgeschlossenes Önlisans + mehr als 150 Punkte im TYT:** fachgebundener direkter Zugang.\n- **Keine Zuweisung oder unter der Schwelle:** Offizielle Quellen legen keinen allgemeinen Weg fest; es gibt weder ein automatisches Studienkolleg noch eine automatische Direktzulassung. Frag die Hochschule oder uni-assist nach deinem Fall.\n- **International Baccalaureate (IB) oder A-Level:** Die Bewertung hängt von Fächer- und Notenanforderungen ab; prüfe die Voraussetzungen der Hochschule.\n\nH+/H- beschreiben Hochschulen, keine Schulabschlüsse. Details und Quellen: [anabin-Leitfaden](/de/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma-de)",
      "probe": "Ob du ein Studienkolleg brauchst, hängt von deinem **Zugangsweg** ab, nicht von der Art deines Lise. Nach der aktuellen Bewertung in anabin:"
     },
     {
      "line": "Absolventen allgemeiner Gymnasien (Anadolu Lisesi, Fen Lisesi, Sosyal Bilimler Lisesi etc.): Wenn du über die YKS einen Platz in einem vierjährigen Bachelorstudiengang in der Türkei erhalten und dich eingeschrieben hast: Wenn du über die YKS einen Platz in einem vierjährigen Bachelorstudiengang in der Türkei erhalten und dich in diesen Studiengang eingeschrieben hast (d.h. du hast den Status eines Universitätsstudierenden), kannst du dich für ein Studienkolleg in Deutschland bewerben. In diesem Fall musst du einen Studienkolleg-Kurs wählen, der mit dem Studiengang in der Türkei, für den du dich qualifiziert hast, zusammenhängt (zum Beispiel einen T-Kurs für Ingenieurwissenschaften, einen M-Kurs für Medizin).",
      "delete": true
     },
     {
      "line": "Wenn du über die YKS einen Platz in einem vierjährigen Bachelorstudiengang erhalten, dich aber nicht eingeschrieben hast: Auch in diesem Fall hast du in der Regel das Recht, dich für ein Studienkolleg zu bewerben. Einige Universitäten können jedoch eine Einschreibung verlangen.",
      "delete": true
     },
     {
      "line": "Wenn du über die YKS einen Platz in einem zweijährigen Studiengang erhalten hast oder gar keinen Platz bekommen hast: Für Studierende dieser Kategorie steht der Weg über das Studienkolleg in der Regel offen. Es wird jedoch erwartet, dass du eine bestimmte Punkteschwelle in der YKS überschritten hast. Da jede Universität oder jedes Studienkolleg eigene Zulassungsvoraussetzungen haben kann, ist es von entscheidender Bedeutung, die aktuellen Bedingungen deiner Zielinstitution zu überprüfen.",
      "delete": true
     },
     {
      "line": "Absolventen von Berufsfachschulen: Absolventen von Berufsfachschulen haben in der Regel kein direktes Bewerbungsrecht für ein Studienkolleg. Wenn sie jedoch mit ihrem Berufsfachschulabschluss in der Türkei einen vierjährigen Bachelorstudiengang belegt und sich eingeschrieben haben, können sie sich für ein Studienkolleg bewerben. Dies hängt von der Bewertung der deutschen Universitäten und Studienkollegs ab. In der Regel ist es für Absolventen von Berufsfachschulen üblicher, in der Türkei einen vierjährigen Bachelorstudiengang in ihrem Fachgebiet abzuschließen und anschließend in Deutschland ein Masterstudium in einem ähnlichen Bereich anzustreben. Für ein Bachelorstudium können zusätzliche Bedingungen (z. B. ein bestimmter Erfolg in der YKS) erforderlich sein.",
      "delete": true
     },
     {
      "line": "Studierende mit International Baccalaureate (IB) oder A-Level Diplom: Studierende mit solchen international anerkannten Diplomen können sich in der Regel direkt an deutschen Universitäten bewerben und müssen kein Studienkolleg besuchen. Allerdings müssen ihre Abschlussnoten und Kursinhalte die deutschen Zulassungskriterien erfüllen.",
      "delete": true
     },
     {
      "line": "Zusammenfassend: Für die meisten Abiturienten aus der Türkei, die ein Bachelorstudium in Deutschland absolvieren möchten, führt der Weg über ein Studienkolleg. Um die genauesten Informationen zu erhalten, raten wir dir dringend, die Abschnitte „Admission Requirements for International Students“ (Zulassungsvoraussetzungen für internationale Studierende) auf den offiziellen Websites der deutschen Universitäten und Studienkollegs zu prüfen oder dich direkt an die zuständige Institution zu wenden.",
      "replace": "**Zusammenfassend:** Es gibt keine allgemeine Regel, dass alle türkischen Lise-Absolventen ein Studienkolleg besuchen müssen; dein Weg hängt von der Art deines Abschlusses, deinem YKS-Ergebnis, deiner Zuweisung und einem eventuellen Studium in der Türkei ab. Die endgültige Entscheidung trifft die Hochschule, bei der du dich bewirbst (oder die zuständige Stelle des Landes); prüfe daher immer die Seiten „Admission Requirements for International Students“ deiner Wunschhochschule und deines Studienkollegs."
     },
     {
      "line": "T-Kurs (Technischer Kurs): Für Ingenieurwissenschaften, Mathematik, Naturwissenschaften (Physik, Chemie, Biologie) und einige technische Studiengänge. Fächer: Deutsch, Mathematik, Physik, Chemie oder Informatik.",
      "subs": [
       {
        "old": "(Physik, Chemie, Biologie)",
        "new": "(Physik, Chemie; ohne Biologie)"
       }
      ]
     },
     {
      "line": "YKS-Ergebnis: Es kann sein, dass du einen Platz in einem vierjährigen Bachelorstudiengang in der Türkei erhalten oder eine bestimmte YKS-Punktzahl erreicht haben musst. Dies ist einer der wichtigsten Punkte und kann für jede Universität/jedes Studienkolleg unterschiedlich sein. Im Allgemeinen gilt der Nachweis eines Platzes in einem beliebigen vierjährigen Bachelorstudiengang über die YKS als ausreichend. Es wird jedoch erwartet, dass der Studiengang, für den du dich qualifiziert hast, mit deinem gewünschten Studiengang in Deutschland oder deinem Studienkolleg-Kurs kompatibel ist.",
      "replace": "**YKS-Ergebnis und Zuweisung:** Deine YKS-Punkteart, deine Punktzahl (über 180) und die Art des Studiengangs, dem du zugewiesen wurdest, bestimmen zusammen mit deinem Lise-Diplom, ob du dich für ein Studienkolleg oder direkt an einer Hochschule bewirbst. Das zugewiesene Fach beeinflusst, welche Fächer oder welcher Studienkolleg-Kurs dir offenstehen. Ohne Zuweisung legen offizielle Quellen keinen allgemeinen Weg fest; prüfe die Voraussetzungen deiner Zielinstitution."
     },
     {
      "line": "Am Ende des Jahres legst du die „Feststellungsprüfung“ (FSP) ab. Die FSP wird in den Fächern durchgeführt, die du am Studienkolleg belegt hast, und das erfolgreiche Bestehen dieser Prüfung berechtigt dich, dich für ein Bachelorstudium an allen Universitäten in Deutschland zu bewerben. Deine FSP-Note spielt eine wichtige Rolle bei deinen Hochschulbewerbungen.",
      "replace": "Am Ende des Jahres legst du die „Feststellungsprüfung“ (FSP) ab – in Deutsch und zwei Kernfächern deines Kurses. Mit bestandener Prüfung kannst du dich für Bachelorstudiengänge in den Fächern bewerben, auf die dein Kurs vorbereitet; eine FSP an einem Studienkolleg an Fachhochschulen eröffnet keinen Zugang zu Universitäten. Die bestandene FSP garantiert keinen Studienplatz, und deine FSP-Note spielt eine wichtige Rolle bei deinen Bewerbungen."
     },
     {
      "line": "Ja, Uni-Assist kann in einigen Fällen notariell beglaubigte Kopien von Dokumenten oder bestimmte Originaldokumente per Post anfordern. In diesem Fall ist der sicherste Weg, deine Unterlagen (Abiturzeugnis, Transkript, Sprachzertifikat etc.) zusammen mit notariell beglaubigten Kopien über einen zuverlässigen Kurierdienst mit Sendungsverfolgung (wie DHL, UPS, FedEx) zu versenden. Vergiss niemals, Kopien deiner Originaldokumente anzufertigen, bevor du sie versendest. Mit einer Sendungsverfolgungsnummer kannst du den Status deiner Sendung überwachen und das Risiko eines Verlusts minimieren. Uni-Assist sendet deine Dokumente nach der Prüfung in der Regel nicht zurück, daher solltest du sehr genau überlegen, bevor du Originaldokumente versendest, und dies nur tun, wenn es ausdrücklich verlangt wird.",
      "replace": "Laut uni-assist läuft die Bewerbung **vollständig digital**: Du lädst deine Unterlagen in deinem My-assist-Konto hoch und musst keine Dokumente per Post an uni-assist schicken. Lade Scans deiner offiziellen Zeugnisse (mit Unterschrift und Stempel oder Verifizierungscode) in der Originalsprache zusammen mit einer beglaubigten Übersetzung ins Deutsche oder Englische hoch. Eine Hochschule kann bei der Einschreibung gesondert beglaubigte Kopien verlangen; das ist hochschulspezifisch und richtet sich nach ihren Vorgaben. Frag im Zweifel über das Kontaktformular von uni-assist nach."
     },
     {
      "line": "Für viele türkische Studierende ist das Studienkolleg ein unvermeidlicher und wertvoller Schritt, um ihren Traum vom Studium an einer Universität in Deutschland zu verwirklichen. Dieser Prozess ist nicht nur eine akademische Vorbereitung, sondern auch eine wichtige Gelegenheit, sich an das Leben und die Kultur in Deutschland anzupassen. Unabhängig von deinem Schultyp oder deinem YKS-Ergebnis kannst du mit gründlicher Recherche, einer rechtzeitigen Studienkolleg-Bewerbung und einer guten Vorbereitung diese Brücke erfolgreich überqueren und deine akademische Reise in Deutschland beginnen.",
      "replace": "Für viele Bewerber ohne direkten Hochschulzugang ist das Studienkolleg eine wertvolle Brücke zum Studium in Deutschland – aber es ist nicht für alle Pflicht. Prüfe zuerst deinen eigenen Zugangsweg (Art des Lise, YKS-Punkteart und Punktzahl, Zuweisung, Studium in der Türkei) und plane dann entweder eine Direktbewerbung oder ein Studienkolleg. Dieser Prozess ist nicht nur eine akademische Vorbereitung, sondern auch eine wichtige Gelegenheit, sich an das Leben und die Kultur in Deutschland anzupassen."
     }
    ]
   },
   "fieldsub": {
    "excerpt": [
     {
      "old": "Für viele türkische Abiturienten ist das Studienkolleg der erste und wichtigste Schritt, um die akademischen Voraussetzungen zu schaffen.",
      "new": "Ob du ein Studienkolleg brauchst, hängt von deinem Zugangsweg ab, nicht von der Art deines Lise."
     }
    ],
    "meta_description": [
     {
      "old": "Für viele türkische Abiturienten ist das Studienkolleg der erste und wichtigste Schritt, um die akademischen Voraussetzungen zu schaffen.",
      "new": "Ob du ein Studienkolleg brauchst, hängt von deinem Zugangsweg ab, nicht von der Art deines Lise.",
      "null_ok": true
     }
    ]
   }
  }
 ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $problems = [];
        $writes = [];
        $done = [];
        foreach ($spec['records'] as $r) {
            $label = "{$r['table']}:{$r['slug']}".(isset($r['locale']) ? "/{$r['locale']}" : '');
            $q = DB::table($r['table'])->where('slug', $r['slug']);
            if (isset($r['locale'])) {
                $q->where('locale', $r['locale']);
            }
            $rows = $q->get();
            if ($rows->count() !== 1) {
                $problems[] = "{$label}: kayıt sayısı {$rows->count()}";
                continue;
            }
            $row = (array) $rows->first();
            if (isset($r['cluster'])) {
                $group = DB::table('faqs')->where('slug', $r['cluster'])->where('locale', 'tr')->value('translation_group_id');
                if (! $group || $row['translation_group_id'] !== $group) {
                    $problems[] = "{$label}: TR kaydıyla aynı çeviri kümesinde değil";
                    continue;
                }
            }
            $pending = 0;
            $applied = 0;
            $upd = [];

            foreach ($r['md'] ?? [] as $col => $edits) {
                $lines = explode("\n", (string) $row[$col]);
                $changed = false;
                $units = null;
                foreach ($edits as $e) {
                    // $e = {line: canlı bloğun düz metni, subs: [{old,new}]} — blok düz metinle bulunur, değişiklik
                    // yalnız ham markdown'daki alt dizede yapılır (link/biçim korunur). Blok = tek satır, art arda
                    // satırlardan oluşan paragraf (yumuşak satır sonu) ya da tablo hücresi.
                    // {line, replace}: bütün blok yeni markdown ile değişir (önek korunur).
                    // {line, delete}: blok tamamen kaldırılır (tam bir kez eşleşmeli). {probe}: çok paragraflı yeni metnin
                    // uygulanmış olduğunu tanımak için yeni metnin ilk satırı.
                    $del = ! empty($e['delete']);
                    $whole = isset($e['replace']) || $del;
                    $after = $del ? null : ($whole ? $e['replace'] : $e['line']);
                    foreach ($whole ? [] : $e['subs'] as $s) {
                        $after = str_replace($s['old'], $s['new'], $after);
                    }
                    $units ??= $this->units($lines);
                    $old = array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($e['line'])));
                    $new = $del ? [] : array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($e['probe'] ?? $after)));
                    $rawOk = $old !== [] && array_reduce($old, fn ($ok, $u) => $ok && ($whole
                        ? $u[0] !== 'cell'
                        : array_reduce($e['subs'], fn ($o, $s) => $o && $this->unitHas($lines, $u, $s['old']), true)), true);
                    if (count($old) >= 1 && count($new) === 0 && $rawOk && (! $del || count($old) === 1)) {
                        $pending++;
                        foreach ($old as [$kind, $start, $len]) {
                            if ($whole) {
                                $cr = str_ends_with($lines[$start], "\r") ? "\r" : '';
                                // ^\s* (0–3 değil): girintili alt liste maddesinin ("    *   ") öneki de korunur
                                preg_match('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', $lines[$start], $m);
                                $lines[$start] = $del ? "\0" : $m[0].$e['replace'].$cr;
                                for ($i = $start + 1; $i < $start + $len; $i++) {
                                    $lines[$i] = "\0";   // paragrafın diğer satırları (sonda silinir)
                                }
                            } else {
                                for ($i = $start; $i < $start + $len; $i++) {
                                    foreach ($e['subs'] as $s) {
                                        $lines[$i] = str_replace($s['old'], $s['new'], $lines[$i]);
                                    }
                                }
                            }
                        }
                        $changed = true;
                        $units = null;
                    } elseif (count($old) === 0 && ($del || count($new) >= 1)) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} blok eşleşmedi (eski ".count($old).', yeni '.count($new).', ham alt dize '.($rawOk ? 'var' : 'yok').') «'.mb_substr($e['line'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = implode("\n", array_values(array_filter($lines, fn ($l) => $l !== "\0")));
                }
            }

            foreach ($r['fieldsub'] ?? [] as $col => $subs) {
                $cur = (string) $row[$col];
                foreach ($subs as $s) {
                    if (! empty($s['null_ok']) && trim($cur) === '') {
                        continue;   // boş alan: fallback (excerpt) zaten düzeltiliyor — dokunma
                    }
                    if (substr_count($cur, $s['old']) === 1 && ! str_contains($cur, $s['new'])) {
                        $pending++;
                        $cur = str_replace($s['old'], $s['new'], $cur);
                        $upd[$col] = $cur;
                    } elseif (! str_contains($cur, $s['old']) && str_contains($cur, $s['new'])) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi «".mb_substr($s['old'], 0, 60).'»';
                    }
                }
            }

            foreach ($r['rewrite'] ?? [] as $col => $rw) {
                // Tamamen eski kurala dayalı SSS cevabı: eski metin parmak izleriyle doğrulanır, yeni metin bütün yazılır.
                $cur = (string) $row[$col];
                $plain = $this->norm(str_replace("\n", ' ', $cur));
                $fp = array_reduce($rw['fingerprints'], fn ($ok, $f) => $ok && str_contains($plain, $this->norm($f)), true);
                if ($cur === $rw['new']) {
                    $applied++;
                } elseif ($fp) {
                    $pending++;
                    $upd[$col] = $rw['new'];
                } else {
                    $problems[] = "{$label}: {$col} eski metin parmak izleri bulunamadı";
                }
            }

            foreach ($r['fields'] ?? [] as $col => $v) {
                $cur = (string) $row[$col];
                if ($cur === $v['new']) {
                    $applied++;
                } elseif ($cur === $v['old']) {
                    $pending++;
                    $upd[$col] = $v['new'];
                } else {
                    $problems[] = "{$label}: {$col} beklenen eski değerde değil";
                }
            }

            foreach ($r['json'] ?? [] as $col => $subs) {
                $data = json_decode((string) $row[$col], true);
                if (! is_array($data)) {
                    $problems[] = "{$label}: {$col} JSON okunamadı";
                    continue;
                }
                $changed = false;
                $orig = $data;   // durum her alt dize için ORİJİNAL veriye göre belirlenir
                foreach ($subs as $s) {
                    $o = $this->countSub($orig, $s['old']);
                    $n = $this->countSub($orig, $s['new']);
                    if ($o >= 1 && $n === 0) {
                        $pending++;
                        $data = $this->replaceSub($data, $s['old'], $s['new']);
                        $changed = true;
                    } elseif ($o === 0 && $n >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi (eski {$o}, yeni {$n}) «".mb_substr($s['old'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            if ($pending > 0 && $applied > 0) {
                $problems[] = "{$label}: kısmen uygulanmış ({$applied} uygulanmış, {$pending} bekleyen)";
            } elseif ($pending > 0) {
                $writes[] = [$r, $row['id'], $upd];
            } else {
                $done[] = $label;
            }
        }
        if (! $problems && $writes && $done) {
            $problems[] = 'kayıtlar arası kısmi durum: '.count($done).' kayıt uygulanmış, '.count($writes).' bekleyen ('.implode(', ', array_slice($done, 0, 5)).')';
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu kayıtların çoğu orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('Wave 2 blog fix (studienkolleg-your-bridge): ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
        }
        if (! $writes) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as [$r, $id, $upd]) {
                if ($r['table'] === 'posts') {
                    $m = \App\Models\Post::findOrFail($id);   // booted(): content_html + reading_minutes
                } elseif ($r['table'] === 'faqs') {
                    $m = \App\Models\Faq::findOrFail($id);    // booted(): answer_html
                } else {
                    DB::table($r['table'])->where('id', $id)->update($upd + ['updated_at' => now()]);
                    continue;
                }
                foreach ($upd as $col => $val) {
                    $m->{$col} = $val;
                }
                $m->save();
            }
        });
    }

    private function countSub($v, string $needle): int
    {
        if (is_array($v)) {
            return array_sum(array_map(fn ($x) => $this->countSub($x, $needle), $v));
        }

        return is_string($v) ? substr_count($v, $needle) : 0;
    }

    private function replaceSub($v, string $old, string $new)
    {
        if (is_array($v)) {
            return array_map(fn ($x) => $this->replaceSub($x, $old, $new), $v);
        }

        return is_string($v) ? str_replace($old, $new, $v) : $v;
    }

    /**
     * Markdown satırlarından sayfadaki bloklara karşılık gelen birimler: [tür, başlangıç, satır sayısı, düz metin].
     * tür: line (tek satır), win (boş satırla kesilmeyen art arda satırlar = yumuşak satır sonlu paragraf),
     * cell (tablo satırındaki hücre).
     */
    private function units(array $lines): array
    {
        $u = [];
        $n = count($lines);
        $blank = fn ($l) => $l === "\0" || trim($l) === '';
        for ($i = 0; $i < $n; $i++) {
            if ($blank($lines[$i])) {
                continue;
            }
            $u[] = ['line', $i, 1, $this->norm($lines[$i])];
            if (str_starts_with(ltrim($lines[$i]), '|')) {
                foreach (explode('|', trim(trim($lines[$i]), '|')) as $cell) {
                    $u[] = ['cell', $i, 1, $this->norm($cell)];
                }
            }
            $txt = $lines[$i];
            $quote = str_starts_with(ltrim($lines[$i]), '>');
            for ($j = $i + 1; $j < min($n, $i + 15) && ! $blank($lines[$j]); $j++) {
                // alıntı devamı ("> …", ">"): eşleştirmede öneki yok say (sayfada tek blockquote bloğu)
                $txt .= ' '.($quote ? preg_replace('/^\s{0,3}(?:>\s?)+/u', '', $lines[$j]) : $lines[$j]);
                $u[] = ['win', $i, $j - $i + 1, $this->norm($txt)];
            }
        }

        return $u;
    }

    private function unitHas(array $lines, array $u, string $needle): bool
    {
        for ($i = $u[1]; $i < $u[1] + $u[2]; $i++) {
            if (str_contains($lines[$i], $needle)) {
                return true;
            }
        }

        return false;
    }

    /** Markdown satırının görünen düz metni (canlı sayfadaki blok metniyle karşılaştırmak için). */
    private function norm(string $s): string
    {
        $s = preg_replace('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', '', $s);
        $s = preg_replace('/!\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        for ($i = 0; $i < 3; $i++) {
            $s = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        }
        $s = preg_replace('/<[^>]+>/u', '', $s);
        $s = str_replace(['**', '__', '*', '`'], '', $s);
        $s = preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!>|"\'])/u', '$1', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};
