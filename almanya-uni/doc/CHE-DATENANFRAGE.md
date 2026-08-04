# CHE Hochschulranking — Datenanfrage

**Durum:** gönderilmeyi bekliyor
**Kime:** CHE Centrum für Hochschulentwicklung gGmbH — `info@che.de` (ayrıca `ranking@che.de`
ve methodik.che-ranking.de üzerindeki iletişim formu denenebilir)
**Kimden:** partnerships@applytogerman.com (admin → Mail panelinden gönderilebilir)

**Neden bu mektup:** Alan bazlı üniversite sıralamalarımız şu an üniversitenin *genel* dünya
sırasını (QS/THE/ARWU) kullanıyor. Bu, Fachhochschule'leri cezalandırıyor — dünya
sıralamalarında hiç yer almadıkları için kalite bileşeninden sıfır alıyorlar. CHE, konu
bazında **hem üniversiteleri hem FH'leri** kapsayan tek kaynak. Veri, HeyStudium'da
görüntülenebiliyor ama yalnızca belgelenmemiş bir iç uç nokta üzerinden geliyor ve
lisanslı bir ürün → resmî yoldan erişim isteniyor.

**İstenen alanlar:** kurum kimliği (tercihen HRK-Nummer — bizim `hs_nummer` alanımızla
birebir eşleşiyor), fach/konu, hochschultyp (Uni/FH), abschlussart (Bachelor/Master),
gösterge grubu (Spitzen-/Mittel-/Schlussgruppe), yıl.

---

## Konu (Betreff)

```
Anfrage zur Lizenzierung von CHE-Ranking-Daten für eine Studienorientierungsplattform
```

## Metin

```
Sehr geehrte Damen und Herren,

wir betreiben ApplyToGerman (applytogerman.com), eine mehrsprachige
Studienorientierungsplattform, die internationale Studieninteressierte — mit einem
Schwerpunkt auf Bewerberinnen und Bewerbern aus der Türkei — bei der Auswahl eines
Studienstandorts in Deutschland unterstützt. Unsere Datenbank umfasst derzeit rund
480 Hochschulen und etwa 7.000 Studiengänge; die Inhalte werden auf Türkisch, Englisch
und Deutsch bereitgestellt.

Wir stellen unseren Nutzerinnen und Nutzern fachbezogene Hochschulübersichten zur
Verfügung. Bislang stützen sich diese auf die Gesamtplatzierung der Hochschule in den
internationalen Rankings (QS, THE, ARWU). Dieses Vorgehen benachteiligt systematisch die
Hochschulen für angewandte Wissenschaften: Da sie in den globalen Rankings praktisch
nicht vertreten sind, erhalten sie in unserer Bewertung keinen Qualitätswert — obwohl
sie gerade in den ingenieurwissenschaftlichen Fächern für unsere Zielgruppe besonders
relevant sind.

Das CHE Hochschulranking ist unseres Wissens die einzige Quelle, die fachbezogen sowohl
Universitäten als auch Fachhochschulen abbildet. Wir möchten daher anfragen, unter
welchen Bedingungen eine Lizenzierung der Ranking-Daten für unsere Plattform möglich ist.

Konkret wären für uns folgende Angaben relevant:

  - Hochschulkennung (bevorzugt die HRK-Nummer)
  - Fach sowie Hochschultyp (Universität / Fachhochschule) und Abschlussart
  - Zuordnung zur Spitzen-, Mittel- oder Schlussgruppe je Indikator
  - Erhebungsjahr

Selbstverständlich würden wir die Daten ausschließlich mit klarer Quellenangabe und
Verlinkung auf das CHE Hochschulranking bzw. HeyStudium verwenden. Eine vollständige
Wiedergabe der Ranking-Tabellen ist ausdrücklich nicht beabsichtigt; die Angaben sollen
als ein Kriterium unter mehreren in unsere fachbezogenen Übersichten einfließen, jeweils
mit Hinweis auf die Methodik des CHE.

Gerne würden wir erfahren:

  1. ob eine Lizenzierung für diesen Anwendungsfall grundsätzlich möglich ist,
  2. in welchem Format die Daten bereitgestellt werden können (Datei, Schnittstelle),
  3. welche Konditionen und Aktualisierungsintervalle vorgesehen sind.

Für Rückfragen stehen wir jederzeit zur Verfügung und würden uns über eine Rückmeldung
sehr freuen.

Mit freundlichen Grüßen

Halil Kurucu
Gründer, ApplyToGerman
partnerships@applytogerman.com
https://applytogerman.com
```

---

## Cevap gelirse

Veri şeması hazır: `university_subject_ranks` tablosu (bkz. ilgili migration). CHE'nin üç
grubu `tier` alanına (`top`/`mid`/`bottom`), bantlı sayısal sıralar `rank_low`/`rank_high`
alanlarına yazılır. Eşleştirme `hs_nummer` ↔ HRK-Nummer üzerinden yapılır; ad eşleştirmesine
gerek yok.

## Cevap gelmezse / lisans uygun değilse

Alternatif: ShanghaiRanking GRAS ve QS konu sıralamaları (serbestçe görüntülenebiliyor,
tek tek konumlar kaynak gösterilerek kullanılabilir). Fachhochschule kapsamazlar, yani
asıl açığı kapatmazlar — ama TU/üniversite tarafını belirgin biçimde iyileştirirler.
