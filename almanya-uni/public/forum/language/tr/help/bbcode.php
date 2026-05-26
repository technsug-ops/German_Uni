<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'HELP_BBCODE_BLOCK_IMAGES'	=> 'Mesajlarda resim görüntüleme',
	'HELP_BBCODE_BLOCK_INTRO'	=> 'Giriş',
	'HELP_BBCODE_BLOCK_LINKS'	=> 'Bağlantı Oluşturma',
	'HELP_BBCODE_BLOCK_LISTS'	=> 'Listeler oluşturma',
	'HELP_BBCODE_BLOCK_OTHERS'	=> 'Diğer konular',
	'HELP_BBCODE_BLOCK_QUOTES'	=> 'Alıntılama ve eşaralıklı yazıtipi',
	'HELP_BBCODE_BLOCK_TEXT'	=> 'Metin Biçimlendirme',

	'HELP_BBCODE_IMAGES_ATTACHMENT_ANSWER'	=> 'Dosya ekleri, yeni <strong>[attachment=][/attachment]</strong> BBCode etiketlerini kullanarak bir mesajın herhangi bir bölümüne yerleştirilebilir, tabi ki eğer dosya eki özelliği mesaj panosu yöneticisi tarafından açıldıysa ve dosya ekleri oluşturmak için uygun izinleriniz verildiyse... Satır içi dosya ekleri yerleştirmek için mesaj gönderme ekranında aşağı açılır bir kutu (sırasıyla bir buton) bulunur.',
	'HELP_BBCODE_IMAGES_ATTACHMENT_QUESTION'	=> 'Bir mesaja dosya ekleri eklemek',
	'HELP_BBCODE_IMAGES_BASIC_ANSWER'	=> 'phpBB mesajlarınıza resimler eklemek için bir BBCode etiketi içerir. Bu etiketi kullanırken iki önemli noktayı dikkate almanız gerekir: ilk olarak bir çok kullanıcı mesajlarda çok sayıda resmin görüntülenmesini hoş karşılamaz. İkinci önemli nokta ise göstermek istediğiniz resim internet üzerinde mevcut olmalıdır (eğer bir web sunucusu çalıştırmıyorsanız bu resmin kendi bilgisayarınızda bulunması yeterli değildir). Bir resmi görüntülemek için, resmin adresini <strong>[img][/img]</strong> etiketleri içine almalısınız. Örneğin:<br /><br /><strong>[img]</strong>https://www.phpbb.com/theme/images/logos/blue/160x52.png<strong>[/img]</strong><br /><br />Bir önceki konuda da belirtildiği gibi resmi dilerseniz <strong>[url][/url]</strong> etiketleri içine alabilirsiniz. Örneğin:<br /><br /><strong>[url=https://www.phpbb.com/][img]</strong>https://www.phpbb.com/theme/images/logos/blue/160x52.png<strong>[/img][/url]</strong><br /><br />oluşturulacak olan:<br /><br /><a href="https://www.phpbb.com/"><img src="https://www.phpbb.com/theme/images/logos/blue/160x52.png" alt="" /></a>',
	'HELP_BBCODE_IMAGES_BASIC_QUESTION'	=> 'Bir mesaja resim eklemek',

	'HELP_BBCODE_INTRO_BBCODE_ANSWER'	=> 'BBCode HTML’in özel bir uygulamasıdır. Foruma yazdığınız mesajlarda BBCode kullanabilme imkanı yönetici tarafından belirlenir. Ayrıca mesaj gönderme formundaki seçenekler sayesinde dilediğiniz mesajlarda BBCode’u iptal etmeniz mümkündür. BBCode, HTML’ye benzer tarzdadır fakat etiketler &lt; ve &gt; yerine köşeli parantez içine alınır. Ayrıca nelerin nasıl görüntülendiği daha iyi kontrol edilebilir. Mesajlarınıza BBCode eklemek için mesaj gövdesi üzerinde bulunan araç çubuğunu kullanmanız işi çok daha kolaylaştırır (araç çubuğu görünümü kullandığınız temaya bağlıdır). Ayrıca alttaki rehberi faydalı bulabilirsiniz.',
	'HELP_BBCODE_INTRO_BBCODE_QUESTION'	=> 'BBCode nedir?',

	'HELP_BBCODE_LINKS_BASIC_ANSWER'	=> 'phpBB, daha yaygın bir şekilde URLler olarak bilinen URIleri (Uniform Resource Indicators), BBCode yoluyla oluşturmayı destekler.<ul><li>Birinci yöntem <strong>[url=][/url]</strong> etiketiyledir. = işaretinin arkasına yazılanlar bağlantı olarak çalışır. Örneğin phpBB.com’a bağlantı vermek için şu şekilde yazın:<br /><br /><strong>[url=https://www.phpbb.com/]</strong>phpBB’yi ziyaret edin!<strong>[/url]</strong><br /><br />Sonuç olarak şu bağlantıyı göreceksiniz: <a href="https://www.phpbb.com/">phpBB’yi ziyaret edin!</a> Not: Bu bağlantı kullanıcıların tarayıcı ayarlarına bağlı olarak aynı pencerede veya yeni bir pencerede açılır.</li><li>Bağlantı adresinin gösterilmesini istiyorsanız, şu şekilde de yazabilirsiniz:<br /><br /><strong>[url]</strong>https://www.phpbb.com/<strong>[/url]</strong><br /><br />Sonuç olarak şu bağlantıyı göreceksiniz: <a href="https://www.phpbb.com/">https://www.phpbb.com/</a></li><li>Ayrıca, phpBB <i>Sihirli Bağlantılar</i> diye tabir edilen bir özelliğe sahiptir. Bu özellik sayesinde, kurallara uygun bir şekilde yazılan her bağlantı adresi otomatik olarak bağlantıya çevrilir. Bunun için herhangi bir etiket, hatta http:// yazmanıza bile gerek kalmaz. Örneğin mesajınızın içeriğine www.phpbb.com yazıp, mesajınızı görüntülediğinizde otomatik olarak <a href="http://www.phpbb.com/">www.phpbb.com</a> şeklinde görüntülenir.</li><li>Aynı şey e-posta adreslerine de uygulanır. Dilerseniz özel olarak bir adres belirleyebilirsiniz, örneğin:<br /><br /><strong>[email]</strong>no.one@domain.adr<strong>[/email]</strong><br /><br />yazılınca şu şekilde görüntülenir: <a href="mailto:no.one@domain.adr">no.one@domain.adr</a> Ya da basit olarak no.one@domain.adr yazabilirsiniz ve mesajınız görüntülendiğinde bu kısım otomatik olarak bağlantıya çevrilir.</li></ul>Bütün BBCode etiketleri gibi, bağlantı adreslerini de diğer etiketlerin içine alabilirsiniz, örn. <strong>[img][/img]</strong> (bir sonraki konuya bakın), <strong>[b][/b]</strong>, v.b. Biçimlendirme etiketlerinde olduğu gibi, etiketlerin düzgün bir şekilde sırasıyla kapatılmasını kendiniz sağlamalısınız, örn.:<br /><br /><strong>[url=https://www.phpbb.com/][img]</strong>https://www.phpbb.com/theme/images/logos/blue/160x52.png<strong>[/url][/img]</strong><br /><br />doğru <span style="text-decoration: underline">değildir</span> ve hatta mesajınızın silinmesine bile yol açabilir, bu yüzden dikkatli olmanız gerekir.',
	'HELP_BBCODE_LINKS_BASIC_QUESTION'	=> 'Ayrı bir siteye bağlantı verme',

	'HELP_BBCODE_LISTS_ORDERER_ANSWER'	=> 'İkinci liste türü olan sıralı liste, size her maddeden önce yazılacak işaretin kontrolünü verir. Sıralı bir liste oluştururken, <strong>[list=1][/list]</strong> etiketlerini kullandığınızda rakamlı bir liste oluşturabilirsiniz ya da alternatif olarak alfabetik bir liste oluşturmak için <strong>[list=a][/list]</strong> etiketlerini kullanabilirsiniz. Sırasız listelerde olduğu gibi, maddeler <strong>[*]</strong> etiketi kullanılarak belirlenir. Örneğin:<br /><br /><strong>[list=1]</strong><br /><strong>[*]</strong>Mağazaya git<br /><strong>[*]</strong>Yeni bir bilgisayar al<br /><strong>[*]</strong>Eve götür<br /><strong>[/list]</strong><br /><br />şu şekilde görüntülenir:<ol style="list-style-type: decimal;"><li>Mağazaya git</li><li>Yeni bilgisayar al</li><li>Eve götür</li></ol>Ayrıca alfabetik bir liste oluşturmak isterseniz:<br /><br /><strong>[list=a]</strong><br /><strong>[*]</strong>Birinci seçenek<br /><strong>[*]</strong>İkinci seçenek<br /><strong>[*]</strong>Üçüncü seçenek<br /><strong>[/list]</strong><br /><br />Sonuç:<ol style="list-style-type: lower-alpha"><li>Birinci seçenek</li><li>İkinci seçenek</li><li>Üçüncü seçenek</li></ol>',
	'HELP_BBCODE_LISTS_ORDERER_QUESTION'	=> 'Sıralı bir liste oluşturma',
	'HELP_BBCODE_LISTS_UNORDERER_ANSWER'	=> 'BBCode sıralı ve sırasız olmak üzere iki türlü liste destekler. Bu listeler aslında HTML eşdeğerleriyle aynıdır. Sırasız liste, her satırı bir madde imiyle beraber satır başını biraz girintilenmiş olarak görüntüler. Sırasız bir liste oluşturmak için <strong>[list][/list]</strong> etiketlerini kullanabilirsiniz. Ayrıca her maddeyi <strong>[*]</strong> etiketiyle belirlemelisiniz. Örneğin sevdiğiniz renklerin bir listesini şu şekilde hazırlayabilirsiniz:<br /><br /><strong>[list]</strong><br /><strong>[*]</strong>Kırmızı<br /><strong>[*]</strong>Mavi<br /><strong>[*]</strong>Sarı<br /><strong>[/list]</strong><br /><br />Sonuçta şu şekilde bir liste oluşturulacak:<ul><li>Kırmızı</li><li>Mavi</li><li>Sarı</li></ul>',
	'HELP_BBCODE_LISTS_UNORDERER_QUESTION'	=> 'Sırasız bir liste oluşturma',

	'HELP_BBCODE_OTHERS_CUSTOM_ANSWER'	=> 'Eğer bu mesaj panosunda bir yöneticiyseniz ve uygun izinleriniz varsa, Özel BBCode’lar bölümünden daha fazla BBCode ekleyebilirsiniz.',
	'HELP_BBCODE_OTHERS_CUSTOM_QUESTION'	=> 'Kendi etiketlerimi ekleyebilir miyim?',

	'HELP_BBCODE_QUOTES_CODE_ANSWER'	=> 'Bir programlama dilinde yazılmış kaynak yazılım veya eş aralıklı yazı tipi (örn. Courier) gerektiren herhangi bir metni görüntülemek için, ilgili kısmı <strong>[code][/code]</strong> etiketleri içine almalısınız. Örn.: <br /><br /><strong>[code]</strong>echo &quot;Bu bir örnek koddur&quot;;<strong>[/code]</strong><br /><br /><strong>[code][/code]</strong> etiketleri arasına yazılan tüm biçimleme etiketleri (örn. [i], [b] gibi) iptal edilir.',
	'HELP_BBCODE_QUOTES_CODE_QUESTION'	=> 'Kaynak yazılım veya eşaralıklı yazıtipiyle görüntüleme',
	'HELP_BBCODE_QUOTES_TEXT_ANSWER'	=> 'Bir metinden alıntı yapmanın iki ayrı yöntemi vardır: kaynak vererek veya vermeyerek.<ul><li>Bir mesaja cevap vermek için Alıntı özelliğini kullanırsanız, orijinal mesajın kendi mesajınıza <strong>[quote=&quot;&quot;][/quote]</strong> etiketleri arasında eklendiğini göreceksiniz. Bu yöntem, bir şahsı veya seçeceğiniz herhangi başka bir yeri kaynak vererek yanıt yazmanızı sağlar. Örneğin Ali isminde bir şahsın yazdıklarını iktibas etmek için şu şekilde yazmanız gerek: <br /><br /><strong>[quote=&quot;Ali&quot;]</strong>Ali’nin yazdığı yazılar...<strong>[/quote]</strong><br /><br />Sonuçta iktibas edilen kısmın önüne otomatik olarak &quot;Ali demiş ki:&quot; yazılır. Alıntı yaptığınız şahsın ismini alıntılama işaretleri &quot;&quot; arasına almayı unutmayın, alıntılama işaretlerini kullanmanız <strong>şarttır</strong>.</li><li>İkinci yöntem, kaynak vermeden alıntı yapmanızı sağlar. İlgili bölümü <strong>[quote][/quote]</strong> etiketleri içine almanız yeterli. Mesajı görüntülediğiniz zaman metin, bir alıntı bloğunun içerisinde gösterilecektir.</li></ul>',
	'HELP_BBCODE_QUOTES_TEXT_QUESTION'	=> 'Alıntı ile cevap yazma',

	'HELP_BBCODE_TEXT_BASIC_ANSWER'	=> 'BBCode, metnin temel biçimlemesini kolayca değiştirmenizi sağlayan etiketlere sahiptir. Bunu gerçekleştirmek için şu yöntemler kullanılır: <ul><li>Metnin belirli bir kısmını kalın harflerle görüntülemek için <strong>[b][/b]</strong> etiketleri içine alın, örn. <br /><br /><strong>[b]</strong>Merhaba<strong>[/b]</strong><br /><br />yazılınca <strong>Merhaba</strong> olarak görüntülenir.</li><li>Altı çizili yazılar için <strong>[u][/u]</strong> kullanın, örn.: <br /><br /><strong>[u]</strong>Günaydın<strong>[/u]</strong><br /><br />yazılınca <span style="text-decoration: underline">Günaydın</span> olarak görüntülenir.</li><li>Metni italik yazmak için <strong>[i][/i]</strong> kullanın, örn. <br /><br />Bu <strong>[i]</strong>Mükemmel!<strong>[/i]</strong><br /><br />yazılınca sonuç Bu <i>Mükemmel!</i> olur.</li></ul>',
	'HELP_BBCODE_TEXT_BASIC_QUESTION'	=> 'Kalın, italik veya altı çizili yazılar nasıl oluşturulur?',
	'HELP_BBCODE_TEXT_COLOR_ANSWER'	=> 'Yazıların renk veya boyutunu değiştirmek için alttaki etiketler kullanılabilir. Elde edilen sonuç, kullanılan tarayıcı ve bilgisayar sistemine göre değişebilir, aklınızda bulunsun: <ul><li>Yazıların rengi, metni <strong>[color=][/color]</strong> etiketleri içine alarak değiştirilir. Belirli İngilizce renk isimlerini (örn. red, blue, yellow vs.) veya alternatif olarak 16 tabanlı sayı sisteminde kodlanmış üç rakamlı renk kodunu yazabilirsiniz (örn. #FFFFFF, #000000). Metni, örneğin kırmızı harflerle yazmak için:<br /><br /><strong>[color=red]</strong>Hello!<strong>[/color]</strong><br /><br />veya<br /><br /><strong>[color=#FF0000]</strong>Hello!<strong>[/color]</strong><br /><br />aynı şekilde görüntülenir: <span style="color:red">Hello!</span></li><li>Karakterlerin boyutunu benzer şekilde <strong>[size=][/size]</strong> kullanarak değiştirebilirsiniz. Bu etiket, kullanıcının seçtiği tema’ya bağlıdır. Karakterlerin yüzde olarak boyutunu yazmanız önerilir. Bu rakam varsayılan olarak 20 (çok küçük) ile başlayıp, en fazla 200 (çok büyük) değerindedir. Örnek:<br /><br /><strong>[size=30]</strong>KÜÇÜK<strong>[/size]</strong><br /><br />genelde şu sonucu verir: <span style="font-size:30%;">KÜÇÜK</span><br /><br />öte yandan:<br /><br /><strong>[size=200]</strong>BÜYÜK!<strong>[/size]</strong><br /><br /><span style="font-size:200%;">BÜYÜK!</span> sonucunu verir.</li></ul>',
	'HELP_BBCODE_TEXT_COLOR_QUESTION'	=> 'Yazı rengi ya da boyutu nasıl değiştirilir?',
	'HELP_BBCODE_TEXT_COMBINE_ANSWER'	=> 'Evet, mesela dikkat çekmek için şöyle yazabilirsiniz:<br /><br /><strong>[size=150][color=red][b]</strong>DİKKAT!<strong>[/b][/color][/size]</strong><br /><br />Bu yazı şu şekilde görüntülenir: <span style="color:red;font-size:200%;"><strong>DİKKAT!</strong></span><br /><br />Uzun metinleri bu şekilde yazmamanızı öneririz! Unutmayın ki, etiketlerin düzgün bir şekilde kapatılmasını temin etmek, metni gönderen kişi olarak sizin görevinizdir. Örneğin bu şekilde yazmak yanlıştır: <br /><br /><strong>[b][u]</strong>Etiketler hatalı kapatılmış<strong>[/b][/u]</strong>',
	'HELP_BBCODE_TEXT_COMBINE_QUESTION'	=> 'Biçimlendirme etiketlerini birleştirebilir miyim?',
));
