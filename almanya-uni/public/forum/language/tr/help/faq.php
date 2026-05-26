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
	'HELP_FAQ_ATTACHMENTS_ALLOWED_ANSWER'	=> 'Her mesaj panosu yöneticisi bazı dosya eki tiplerine izin verebilir ya da izin vermeyebilir. Eğer nelerin yüklenmesine izin verildiğine dair şüpheleriniz varsa, yardım için mesaj panosu yöneticisiyle iletişime geçin.',
	'HELP_FAQ_ATTACHMENTS_ALLOWED_QUESTION'	=> 'Bu mesaj panosunda hangi dosya eklerine izin veriliyor?',
	'HELP_FAQ_ATTACHMENTS_OWN_ANSWER'	=> 'Yüklediğiniz dosya eklerinin listesini bulmak için, Kullanıcı Kontrol Panelinize gidin ve dosya ekleri bölümüne giden bağlantıları izleyin.',
	'HELP_FAQ_ATTACHMENTS_OWN_QUESTION'	=> 'Tüm dosya eklerimi nasıl bulurum?',

	'HELP_FAQ_BLOCK_ATTACHMENTS'	=> 'Dosya ekleri',
	'HELP_FAQ_BLOCK_BOOKMARKS'	=> 'Abonelikler ve Yer imleri',
	'HELP_FAQ_BLOCK_FORMATTING'	=> 'Biçimlendirme ve Başlık Tipleri',
	'HELP_FAQ_BLOCK_FRIENDS'	=> 'Arkadaşlar ve Engellenenler',
	'HELP_FAQ_BLOCK_GROUPS'	=> 'Kullanıcı Seviyeleri ve Grupları',
	'HELP_FAQ_BLOCK_ISSUES'	=> 'phpBB Konuları',
	'HELP_FAQ_BLOCK_LOGIN'	=> 'Giriş ve Kayıt sorunları',
	'HELP_FAQ_BLOCK_PMS'	=> 'Özel Mesajlar',
	'HELP_FAQ_BLOCK_POSTING'	=> 'Mesaj Gönderme Sorunları',
	'HELP_FAQ_BLOCK_SEARCH'	=> 'Forumlarda Arama',
	'HELP_FAQ_BLOCK_USERSETTINGS'	=> 'Kullanıcı Seçenekleri ve ayarları',

	'HELP_FAQ_BOOKMARKS_DIFFERENCE_ANSWER'	=> 'phpBB 3.0’da, başlık yer imleri bir web tarayıcısındaki yer imleri gibi çalışıyordu. Yer imlerine eklenen başlıklar güncellendiği zaman hiç bir uyarı alamıyordunuz. phpBB 3.1 itibariyle, yer imleri tıpkı bir başlığa abone olmak gibi çalışıyor. Yer imlerindeki bir başlık güncellendiği zaman artık bildirim alabiliyorsunuz. Aboneliklerde ise, farklı olarak, mesaj panosu genelinde bir başlık ya da forum güncellendiğinde bildirim alacaksınız. Yer imleri ve abonelikler için bildirim seçeneklerini Kullanıcı Kontrol Panelindeki “Mesaj panosu tercihleri” bölümünden ayarlayabilirsiniz.',
	'HELP_FAQ_BOOKMARKS_DIFFERENCE_QUESTION'	=> 'Yer imi ve abonelik arasındaki fark nedir?',
	'HELP_FAQ_BOOKMARKS_FORUM_ANSWER'	=> 'Belirli bir foruma abone olmak için, ilgili foruma giriş yaptığınızda sayfanın alt kısmında bulunan “Foruma abone ol” bağlantısına tıklayabilirsiniz.',
	'HELP_FAQ_BOOKMARKS_FORUM_QUESTION'	=> 'Belirli bir foruma nasıl abone olurum?',
	'HELP_FAQ_BOOKMARKS_REMOVE_ANSWER'	=> 'Aboneliklerinizi, Kullanıcı Kontrol Panelinize giderek ve abonelikleriniz için bağlantıları takip ederek silebilirsiniz.',
	'HELP_FAQ_BOOKMARKS_REMOVE_QUESTION'	=> 'Aboneliklerimi nasıl silerim?',
	'HELP_FAQ_BOOKMARKS_TOPIC_ANSWER'	=> 'Belirli bir başlığı yer imlerine eklemek ya da bu başlığa abone olmak için bir başlıktaki üst ve alt kısımlarda bulunan “Başlık araçları” menüsünden uygun bağlantıyı tıklayabilirsiniz.<br />Ayrıca bir başlığa cevap yazarken “Mesaja cevap geldiğinde bana bildir” seçeneğini işaretleyerek başlığa abone olabilirsiniz.',
	'HELP_FAQ_BOOKMARKS_TOPIC_QUESTION'	=> 'Belirli başlıkları nasıl yer imlerine eklerim ya da bu başlıklara nasıl abone olurum?',

	'HELP_FAQ_FORMATTING_ANNOUNCEMENT_ANSWER'	=> 'Duyurular çoğu zaman önemli bilgileri içerir. Mesaj panosundaki duyuruları mümkün olan en kısa zamanda okumalısınız. Duyurular, ilgili forumdaki başlıkların en üst tarafında görüntülenir. Global duyurularda olduğu gibi, duyuru gönderebilmeniz için mesaj panosu yöneticisi tarafından size yetki verilmiş olması gerekir.',
	'HELP_FAQ_FORMATTING_ANNOUNCEMENT_QUESTION'	=> 'Duyurular nedir?',
	'HELP_FAQ_FORMATTING_BBOCDE_ANSWER'	=> 'BBCode HTML’in özel bir uygulamasıdır. Foruma yazdığınız mesajlarda BBCode kullanabilme imkanını mesaj panosu yöneticisi belirler. Ayrıca mesaj gönderme formundaki seçenekler sayesinde dilediğiniz mesajlarda BBCode’u iptal etmeniz mümkündür. BBCode, HTML’e benzer tarzdadır fakat etiketler &lt; ve &gt; yerine köşeli parantez içine alınır: [ ve ]. Ayrıca neyin nasıl görüntüleneceği daha iyi kontrol edilebilir. BBCode hakkında daha geniş bilgiler için, mesaj gönderme sayfasından ulaşabileceğiniz rehbere bakınız.',
	'HELP_FAQ_FORMATTING_BBOCDE_QUESTION'	=> 'BBCode nedir?',
	'HELP_FAQ_FORMATTING_GLOBAL_ANNOUNCE_ANSWER'	=> 'Global duyurular önemli bilgiler içerir ve onları mümkün olan en kısa zamanda okumalısınız. Global duyurular her forumun başında görünecektir ve ayrıca Kullanıcı Kontrol Panelinizden görebilirsiniz. Gerekli izinlere sahipseniz sizde bir global duyuru gönderebilirsiniz, bu izinler yönetici tarafından ayarlanır.',
	'HELP_FAQ_FORMATTING_GLOBAL_ANNOUNCE_QUESTION'	=> 'Global duyurular nedir?',
	'HELP_FAQ_FORMATTING_HTML_ANSWER'	=> 'Hayır. Bu mesaj panosunda HTML mesajları göndermek ve farklı HTML kodları kullanmak mümkün değildir. Daha fazla biçimlendirme için HTML yerine BBCode kullanarak uygulayabilirsiniz.',
	'HELP_FAQ_FORMATTING_HTML_QUESTION'	=> 'HTML kullanabilir miyim?',
	'HELP_FAQ_FORMATTING_ICONS_ANSWER'	=> 'Mesajlara, içeriğini işaret edecek başlık ikon resimleri tanımlanabilir. Başlık ikonlarının kullanımı yönetici tarafından ayarlanan izinlere bağlıdır.',
	'HELP_FAQ_FORMATTING_ICONS_QUESTION'	=> 'Başlık ikonları nedir?',
	'HELP_FAQ_FORMATTING_IMAGES_ANSWER'	=> 'Evet, resimler mesajınızda gösterilebilir. Eğer yönetim dosya eklerine izin verdiyse, resimleri mesaj panosuna yükleyebilirsiniz. Aksi takdirde, genel erişilebilir bir web sunucusunda depolanan bir resime bağlantı vermelisiniz, örn. http://www.ornek.com/benim-resmim.gif. Kendi bilgisayarınızda saklanan resimlere bağlantı veremezsiniz (bilgisayarınız genel erişilebilir bir sunucu olmadığı sürece). Ayrıca kimlik doğrulama mekanizmaları ardında depolanan resimlere bağlantı veremezsiniz, ör. hotmail ya da yahoo e-posta kutularındaki resimler, şifre korumları siteler, v.b. Resmi görüntülemek için BBCode [img] etiketini kullanın.',
	'HELP_FAQ_FORMATTING_IMAGES_QUESTION'	=> 'Resim gönderebilir miyim?',
	'HELP_FAQ_FORMATTING_LOCKED_ANSWER'	=> 'Kilitli başlıklar, kullanıcıların ilgili başlığa artık cevap yazamaması için belirlenir. Bir başlık kilitlendiğinde içerdikleri anketlerde otomatik olarak sona erer. Başlıklar, bir çok nedenlerden dolayı forum moderatörü ya da mesaj panosu yöneticisi tarafından kilitlenmiş olabilir. Ayrıca mesaj panosu yöneticisi tarafından verilen izinlere bağlı olarak kendi başlıklarınızı kilitleyebilirsiniz.',
	'HELP_FAQ_FORMATTING_LOCKED_QUESTION'	=> 'Kilitli başlıklar nedir?',
	'HELP_FAQ_FORMATTING_SMILIES_ANSWER'	=> 'İfadeler ya da Duygusal Simgeler, belirli duygu ifadelerini vermek için kullanılan küçük resimler halindeki kısa kodlardır. Örn. :) mutlu, :( ise üzgün anlamındadır. Kullanabileceğiniz ifadelerin tam listesini mesaj gönderme formunda görebilirsiniz. İfadeleri aşırı derecede kullanmamaya özen gösterin, onlar metin yoksa okunmaz hale gelebilir ve bir moderatör mesajınızı düzenlemeye ya da silmeye karar verebilir. Mesaj panosu yöneticisi ayrıca bir mesajınızda kullanabileceğiniz en fazla ifade sınırını ayarlamış olabilir.',
	'HELP_FAQ_FORMATTING_SMILIES_QUESTION'	=> 'İfadeler nedir?',
	'HELP_FAQ_FORMATTING_STICKIES_ANSWER'	=> 'Sabit başlıklar, ilgili forumun ilk sayfasında, duyuruların hemen altında görülür. Çoğu zaman önemli bilgileri içerirler, mümkünse okumanızı öneririz. Duyurular için geçerli olduğu gibi, herhangi bir foruma sabit başlık göndermek için gereken yetkileri mesaj panosu yöneticisi belirler.',
	'HELP_FAQ_FORMATTING_STICKIES_QUESTION'	=> 'Sabit başlıklar nedir?',

	'HELP_FAQ_FRIENDS_BASIC_ANSWER'	=> 'Bu listeleri kullanarak mesaj panosunun diğer üyelerini organize edebilirsiniz. Arkadaşlar listenize eklenen üyeler, onlara özel mesajlar göndermeye ve çevrimiçi durumlarını görüntülemeye kolay erişim sağlamak için Kullanıcı Kontrol Panelinizde listelenecektir. Tema uygun desteği sağlıyorsa, bu kullanıcılardan gelen mesajlar ayrıca detaylı ve belirgin olarak görünebilir. Eğer engellenenler listenize bir kullanıcı eklediyseniz onlar tarafından oluşturulan mesajlar varsayılan olarak gizlenecektir.',
	'HELP_FAQ_FRIENDS_BASIC_QUESTION'	=> 'Arkadaşlarım ve Engellenenler listesi nedir?',
	'HELP_FAQ_FRIENDS_MANAGE_ANSWER'	=> 'Listenize kullanıcıları iki yolla ekleyebilirsiniz. Her kullanıcı’nın profilinde, onları Arkadaşlar ya da Engellenenler listenize eklemek için bir bağlantı olacaktır. Alternatif olarak Kullanıcı Kontrol Paneliniz’den, direkt olarak kullanıcıların üye adlarını girerek ekleyebilirsiniz. Ayrıca aynı sayfayı kullanarak kullanıcıları listenizden silebilirsiniz.',
	'HELP_FAQ_FRIENDS_MANAGE_QUESTION'	=> 'Kullanıcıları Arkadaşlarım veya Engellenenler listesine nasıl ekleyebilirim / silebilirim?',

	'HELP_FAQ_GROUPS_ADMINISTRATORS_ANSWER'	=> 'Yöneticiler, mesaj panosunun her bölümünde en çok yetkiye sahip olan üyelerdir. Bu üyeler, mesaj panosunun her türlü işlevini kontrol edebilir: izin verme, yetkilendirme, kullanıcı yasaklama, kullanıcı grupları oluşturma, moderatör yetkilerini verme vs. Ayrıca onlar mesaj panosu kurucusu tarafından verilen ayarlara bağlı olarak bütün forumlarda tam moderatör yetkilerine sahip olabilirler.',
	'HELP_FAQ_GROUPS_ADMINISTRATORS_QUESTION'	=> 'Yöneticiler nedir?',
	'HELP_FAQ_GROUPS_COLORS_ANSWER'	=> 'Mesaj panosu yöneticisi bir kullanıcı grubunun üyelerine bir renk belirler, ve bu grubun üyelerinin kolayca tanınması mümkün olur.',
	'HELP_FAQ_GROUPS_COLORS_QUESTION'	=> 'Neden bazı kullanıcı grupları farklı renkte görünüyor?',
	'HELP_FAQ_GROUPS_DEFAULT_ANSWER'	=> 'Eğer bir kullanıcı grubundan daha fazlasının üyesi iseniz, varsayılan olarak kullanmak için belirlenen grubunuzun rengi ve rütbesi gösterilir. Mesaj panosu yöneticisi, Kullanıcı Kontrol Panelinizden varsayılan kullanıcı grubunuzu değiştirmenize izin vermiş olabilir.',
	'HELP_FAQ_GROUPS_DEFAULT_QUESTION'	=> '“Varsayılan kullanıcı grubu” nedir?',
	'HELP_FAQ_GROUPS_MODERATORS_ANSWER'	=> 'Moderatörler (ya da onların grupları), günlük olarak forumun çalışmasını kontrol eden şahıslar veya gruplardır. Başlıkları değiştirme ve silme yetkisine sahip olabilirler. Ayrıca moderatör oldukları forumdaki başlıkları kilitleyebilir, taşıyabilir, silebilir ve bölebilirler. Genelde moderatörlerin görevi, off-topic, yani başlık konusuyla ilgisi olmayan yanıtların veya hakaret ve saldırı niteliğinde mesajların gönderilmesini önlemektir.',
	'HELP_FAQ_GROUPS_MODERATORS_QUESTION'	=> 'Moderatörler nedir?',
	'HELP_FAQ_GROUPS_TEAM_ANSWER'	=> 'Bu sayfa mesaj panosu yönetiminin bir listesini size belirtir, mesaj panosu yöneticileri ile moderatörlerinin bilgilerini ve diğer detaylarla onların yönettikleri forumları içerir.',
	'HELP_FAQ_GROUPS_TEAM_QUESTION'	=> '“Takım” bağlantısı nedir?',
	'HELP_FAQ_GROUPS_USERGROUPS_ANSWER'	=> 'Kullanıcı grupları, mesaj panosu yöneticilerinin kullanıcıları grup halinde ayırabilmesi için öngörülen bir yöntemdir. Her kullanıcı (çoğu mesaj panolarından farklı olarak) bir çok gruba üye olabilir ve her gruba ayrı ayrı izinler tanımlanabilir. Bu şekilde mesaj panosu yöneticileri belirli kullanıcılara rahatlıkla moderatör yetkilerini veya özel forumlara erişme gibi yetkiler verebilir.',
	'HELP_FAQ_GROUPS_USERGROUPS_JOIN_ANSWER'	=> 'Bir kullanıcı grubuna katılabilmek için, Kullanıcı Kontrol Panelinizden “Kullanıcı grupları” bağlantısına tıklayın; oradan tüm kullanıcı gruplarını görüntüleyebilirsiniz. Grupların tümü erişime açık olmayabilir. Bazılarına katılmak için onay gerekebilir ve bazıları kapalı ya da gizli üyeliklere sahip olabilir. Eğer grup açık ise, ilgili bağlantıya tıklayarak katılabilirsiniz. Eğer bir gruba katılmak için onay gerekiyorsa ilgili bağlantıya tıklayarak istek yapabilirsiniz. İsteğinizin kullanıcı grubu lideri tarafından onaylanması gerek, onlar size neden gruba katılmak istediğinizi sorabilirler. İsteğiniz reddedilirse grup liderini rahatsız etmeyin; bunun çeşitli nedenleri olsa gerek.',
	'HELP_FAQ_GROUPS_USERGROUPS_JOIN_QUESTION'	=> 'Bir kullanıcı grubuna nasıl katılabilirim?',
	'HELP_FAQ_GROUPS_USERGROUPS_LEAD_ANSWER'	=> 'Kullanıcı grupları bir yönetici tarafından oluşturulur, genellikle bir kullanıcı grubu lideri belirlenir. Eğer yeni bir kullanıcı grubu oluşturmak istiyorsanız, ilk önce bir yöneticiyle iletişime geçmelisiniz; bir özel mesaj göndermeyi deneyin.',
	'HELP_FAQ_GROUPS_USERGROUPS_LEAD_QUESTION'	=> 'Bir kullanıcı grubunun lideri olmak için ne yapmam gerek?',
	'HELP_FAQ_GROUPS_USERGROUPS_QUESTION'	=> 'Kullanıcı grupları nedir?',

	'HELP_FAQ_ISSUES_ADMIN_ANSWER'	=> 'Mesaj panosunun tüm kullanıcıları, “Bize ulaşın” formunu (eğer bu özellik mesaj panosu yöneticisi tarafından etkinleştirilmişse) kullanabilir.<br />Mesaj panosunun üyeleri, bir mesaj panosu yöneticisi ile iletişime geçmek için ayrıca “Takım” bağlantısını da kullanabilir.',
	'HELP_FAQ_ISSUES_ADMIN_QUESTION'	=> 'Bir mesaj panosu yöneticisiyle nasıl iletişime geçebilirim?',
	'HELP_FAQ_ISSUES_FEATURE_ANSWER'	=> 'Bu yazılım phpBB Limited tarafından yazılmış ve lisanslanmıştır. Eğer herhangi bir özelliğin eklenmesi gerektiğini düşünüyorsanız lütfen <a href="https://www.phpbb.com/ideas/">phpBB Fikirleri Merkezi</a>’ni ziyaret edin, oradan mevcut fikirlere oy verebilir ya da yeni özellikler önerebilirsiniz.',
	'HELP_FAQ_ISSUES_FEATURE_QUESTION'	=> 'Aradığım X özellik neden yok?',
	'HELP_FAQ_ISSUES_LEGAL_ANSWER'	=> '“Takım” sayfasında listelenmiş yöneticilerin herhangi biri ile şikayetleriniz için iletişime geçebilirsiniz. Eğer onlardan cevap alamıyorsanız, o zaman alan adının sahibi ile (bir <a href="http://www.google.com/search?q=whois">whois sorgulaması</a> yaparak alan adı sahibine ulaşılabilir) ya da, eğer bu mesaj panosu ücretsiz bir serviste çalışıyorsa (ör. Yahoo!, free.fr, f2s.com, v.b.), bu servisin yönetimi veya suistimal konularla ilgilenen bölümüyle iletişime geçmelisiniz. Not: phpBB Limited, bu mesaj panosunun nasıl, nerede ve kimler tarafından kullanıldığı konusunda bir bilgisi olmadığı için <strong>kesinlikle yargılanamaz</strong> ve her ne olursa olsun sorumlu tutulamaz. phpBB.com sitesiyle veya phpBB yazılımıyla <strong>doğrudan ilgisi olmayan</strong> herhangi bir hukuki konuda (ihtiyati tedbir, mali sorumluluk, iftira vs.) phpBB Limited ile iletişime geçmeyin. Bu yazılımın herhangi <strong>üçüncü şahıslar tarafından kullanımıyla ilgili</strong> phpBB Limited’e e-posta gönderirseniz, ya çok kısa bir cevap alırsınız ya da hiç bir cevap alamazsınız.',
	'HELP_FAQ_ISSUES_LEGAL_QUESTION'	=> 'Bu mesaj panosuyla ilgili hukuki sorunlar için ve/veya suistimal durumlarda kime başvurabilirim?',
	'HELP_FAQ_ISSUES_WHOIS_PHPBB_ANSWER'	=> 'Bu yazılım (değiştirilmemiş haliyle) <a href="https://www.phpbb.com/">phpBB Limited</a> tarafından telif hakkıyla üretilmiş ve genel dağıtıma çıkarılmıştır. Bu yazılım, GNU General Public License (Genel Kamu Lisansı), sürüm 2 (GPL-2.0) altında yapılmıştır ve serbestçe dağıtılabilir. Daha fazla detay için <a href="https://www.phpbb.com/about/">phpBB Hakkında</a> sayfasına bakın.',
	'HELP_FAQ_ISSUES_WHOIS_PHPBB_QUESTION'	=> 'Bu mesaj panosunu kim yazdı?',

	'HELP_FAQ_LOGIN_AUTO_LOGOUT_ANSWER'	=> 'Eğer giriş yaparken <em>Beni hatırla</em> kutucuğunu işaretlemezseniz, mesaj panosu sadece belirli bir zaman için sizi giriş yapmış şekilde tutacaktır. Bu, hesabınızın başka biri tarafından kötüye kullanımını önlemek içindir. Sürekli giriş yapmış olarak kalmak için, giriş sırasında <em>Beni hatırla</em> kutucuğunu işaretleyin. Ancak bu işlem başkalarıyla paylaşılan bir bilgisayarlardan, örneğin; kütüphane, internet kafe, üniversite bilgisayar laboratuarı, v.b. gibi yerlerden mesaj panosuna erişim yaptığınızda önerilmez. Eğer giriş sırasında <em>Beni hatırla</em> kutucuğunu göremiyorsanız, bunun anlamı bir mesaj panosu yöneticisinin bu özelliği devre dışı bırakmış olmasıdır.',
	'HELP_FAQ_LOGIN_AUTO_LOGOUT_QUESTION'	=> 'Neden otomatik olarak çıkışım yapılıyor?',
	'HELP_FAQ_LOGIN_CANNOT_LOGIN_ANSWER'	=> 'Bunun meydana gelmesinde çeşitli sebepler vardır. Öncelikle, kullanıcı adınız ve şifrenizi doğru olarak girdiğinizden emin olmalısınız. Eğer kullanıcı adı ve şifrenizin doğruluğundan eminseniz, bir mesaj panosu yöneticisi ile iletişime geçerek mesaj panosundan yasaklanıp yasaklanmadığınızdan emin olun. Eğer sorun bu da değilse web sitesi sahibi mesaj panosu için yanlış ayar yapmış olabilir ve bunu düzeltmesi gerekebilir.',
	'HELP_FAQ_LOGIN_CANNOT_LOGIN_ANYMORE_ANSWER'	=> 'Bazı sebeplerden dolayı bir yönetici hesabınızı deaktif etmiş ya da silmiş olabilir. Ayrıca, bazı mesaj panoları veritabanını azaltmak için uzun bir süredir mesaj göndermeyen kullanıcıları periyodik aralıklarla silerler. Eğer bu olmuşsa, tekrar kayıt olmayı deneyin ve tartışmalara katılın.',
	'HELP_FAQ_LOGIN_CANNOT_LOGIN_ANYMORE_QUESTION'	=> 'Daha önce kayıt olmuştum ama artık giriş yapamıyorum?!',
	'HELP_FAQ_LOGIN_CANNOT_LOGIN_QUESTION'	=> 'Neden giriş yapamıyorum?',
	'HELP_FAQ_LOGIN_CANNOT_REGISTER_ANSWER'	=> 'Bir mesaj panosu yöneticisi yeni ziyaretçilerin kayıt olmasını önlemek amacıyla kayıt işlemini devre dışı bırakmış olabilir. Ayrıca mesaj panosu yöneticisi IP adresinizi yasaklamış ya da kullanıcı adınızı kullanmanıza izin vermemiş olabilir. Yardım için bir mesaj panosu yöneticisiyle iletişime geçin.',
	'HELP_FAQ_LOGIN_CANNOT_REGISTER_QUESTION'	=> 'Neden kayıt olamıyorum?',
	'HELP_FAQ_LOGIN_COPPA_ANSWER'	=> 'COPPA ya da Child Online Privacy and Protection Act of 1998, Birleşik Devletlerde web sitelerinin 13 yaşından küçüklerin ebeveynlerinden potansiyel bilgi toplayabilmek için yazılı izin almayı gerekli tutan bir kanundur, ya da başka bir deyişle yasal veli/vasi onay şeklidir, veliler 13 yaşından küçüklerden kişisel kimlik bilgilerinin toplanması için izin verirler. Eğer bu uygulama ile kayıt olmak ya da bu uygulama ile bir web sitesine kayıt olmak size güvenilir gelmiyorsa, yardım için bir yasal danışman ile iletişime geçin. Not: phpBB Limited ya da bu mesaj panosunun sahibi yasal destek sağlamaz, ve “Bu mesaj panosu ile ilgili kötü niyetli ve/veya hukuki konularda kime başvurabilirim?” sorusu hariç, yasayı ilgilendiren herhangi bir konuda iletişim noktası olarak gösterilemez.',
	'HELP_FAQ_LOGIN_COPPA_QUESTION'	=> 'COPPA nedir?',
	'HELP_FAQ_LOGIN_DELETE_COOKIES_ANSWER'	=> '“Çerezleri sil” özelliği, phpBB tarafından oluşturulan ve mesaj panosuna girişiniz, doğrulanmanız için tutulan çerezleri silmeye yarar. Çerezler ayrıca, eğer bir mesaj panosu yöneticisi tarafından ayarlandıysa, okuma takibi gibi özellikler sağlar. Eğer giriş ya da çıkış problemleri yaşıyorsanız, mesaj panosu çerezlerini silmek size yardımcı olabilir.',
	'HELP_FAQ_LOGIN_DELETE_COOKIES_QUESTION'	=> '“Çerezleri sil” nedir?',
	'HELP_FAQ_LOGIN_LOST_PASSWORD_ANSWER'	=> 'Panik yapmayın! Şifreniz geri getirelemese de, kolayca sıfırlanabilir. Giriş sayfasını ziyaret edin ve <em>Şifremi unuttum</em> bağlantısına tıklayın. Buradaki talimatları takip ederek kısa bir süre içerisinde yeniden giriş yapabilirsiniz.<br />Yine de, eğer şifenizi sıfırlayamazsanız, bir mesaj panosu yöneticisi ile iletişime geçin.',
	'HELP_FAQ_LOGIN_LOST_PASSWORD_QUESTION'	=> 'Şifremi kaybettim!',
	'HELP_FAQ_LOGIN_REGISTER_ANSWER'	=> 'Kayıt olmanıza gerek olmayabilirdi aslında. Mesaj gönderebilmek için kayıt işleminin şart olması, mesaj panosu yöneticisinin (yönetici) kararına bağlıdır. Ayrıca kayıt olunca bazı özel imkanlara ulaşabilirsiniz. Örneğin mesajlarınızın yanında kendinize ait küçük bir resim (avatar) gösterme, özel mesaj gönderme, tanıdığınız kullanıcılara e-posta gönderme veya kullanıcı gruplarına katılma imkanlarına misafir kullanıcılar sahip değildir. Kayıt işlemi çok basit olduğu için kayıt olmanız önerilir.',
	'HELP_FAQ_LOGIN_REGISTER_CONFIRM_ANSWER'	=> 'Öncelikle, kullanıcı adınızı ve şifrenizi kontrol edin. Eğer onlar doğruysa, o zaman şu iki durumdan biri meydana gelmiş olabilir. Eğer COPPA desteği açıksa ve kayıt sırasında 13 yaşından küçük olduğunuzu belirttiyseniz, size tarif edilen işlemleri uygulamanız gerekmektedir. Bazı mesaj panoları yeni kayıtlarda ayrıca aktivasyon istemektedir, giriş yapmadan önce bu aktivasyonun kendiniz ya da bir yönetici tarafından yapılması gerekmektedir; bu bilgi kayıt sırasında gösterilmiştir. Eğer size bir e-posta gönderildiyse, açıklamaları takip edin. Eğer bir e-posta almadıysanız, yanlış bir e-posta adresi belirtmiş olabilirsiniz ya da e-posta, bir spam filtresi tarafından spam olarak seçilmiş olabilir. Eğer doğru e-posta adresi belirttiğinize eminseniz, bir yönetici ile iletişime geçmeyi deneyin.',
	'HELP_FAQ_LOGIN_REGISTER_CONFIRM_QUESTION'	=> 'Kayıt oldum ama giriş yapamıyorum!',
	'HELP_FAQ_LOGIN_REGISTER_QUESTION'	=> 'Neden kayıt olmam gerekiyor?',

	'HELP_FAQ_PMS_CANNOT_SEND_ANSWER'	=> 'Bunun üç sebebi olabilir; henüz kayıt olmamış veya giriş yapmamışsınız, veya mesaj panosu yöneticisi bütün mesaj panosu için özel mesajları iptal etmiş. Üçüncü olanak ise: mesaj panosu yöneticisi sizin bu imkanı kullanmanızı önlemiş olabilir, bu durumda kendisine nedenini sormanız gerekir.',
	'HELP_FAQ_PMS_CANNOT_SEND_QUESTION'	=> 'Özel mesaj gönderemiyorum!',
	'HELP_FAQ_PMS_SPAM_ANSWER'	=> 'Bunu duyduğumuz için üzgünüz. Aslında, bu mesaj panosunun sunduğu e-posta gönderme işlevi spamdan korunmak için birçok önlemi almış durumda. Aldığınız spam e-postanın bir kopyasını mesaj panosu yöneticisine gönderin. Özellikle aldığınız e-posta’nın başlık kısmını (to (kime), subject (konu) vs.) iletmeyi unutmayın, bu kısımda e-postayı gönderen kullanıcı hakkında bilgiler bulunur. Mesaj panosu yöneticileri bu bilgilerle meseleyi takip edebilir.',
	'HELP_FAQ_PMS_SPAM_QUESTION'	=> 'Bu mesaj panosunda herhangi birinden spam e-posta aldım!',
	'HELP_FAQ_PMS_UNWANTED_ANSWER'	=> 'Kullanıcı Kontrol Panelinizdeki mesaj kuralları özelliğini kullanarak bir kullanıcıdan gelen özel mesajları otomatik olarak silebilirsiniz. Eğer belirli bir kullanıcıdan küfürlü ya da kötü niyetli özel mesajlar alıyorsanız, bu mesajları moderatörlere bildirin; onlar özel mesaj gönderen bir kullanıcıyı önleme yetkisine sahiptir.',
	'HELP_FAQ_PMS_UNWANTED_QUESTION'	=> 'İstemediğim özel mesajları almaya devam ediyorum!',

	'HELP_FAQ_POSTING_BUMP_ANSWER'	=> '“Başlığı darbele” bağlantısını görüp tıkladığınız zaman, başlığı “darbeleyerek” forumun ilk sayfasında en üst sıraya çıkarabilirsiniz. Fakat, eğer bu bağlantıyı göremiyorsanız, o zaman başlık darbeleme özelliği kapatılmış olabilir ya da darbelemeler arası izin verilen zamana henüz ulaşılmamıştır. Ayrıca cevap gelene kadar başlığın basit bir şekilde darbelenmesi mümkündür. Buna rağmen, mesaj panosu kurallarını takip ettiğinize emin olun.',
	'HELP_FAQ_POSTING_BUMP_QUESTION'	=> 'Başlığımı nasıl darbelerim?',
	'HELP_FAQ_POSTING_CREATE_ANSWER'	=> 'Bir foruma yeni bir başlık göndermek için, "Yeni Başlık" bağlantısına, bir başlığa cevap göndermek içinse, "Cevap Gönder" bağlantısına tıklayın. Bir mesaj göndermeden önce kayıt olmanız gerekebilir. Forum ve başlık ekranlarının alt kısımlarında her forum için mevcut olan izinlerin bir listesini görebilirsiniz. Örneğin: Yeni başlıklar gönderebilirsiniz, Dosya ekleri gönderebilirsiniz, v.b.',
	'HELP_FAQ_POSTING_CREATE_QUESTION'	=> 'Yeni bir başlık nasıl oluşturabilirim ya da bir mesaja nasıl cevap gönderebilirim?',
	'HELP_FAQ_POSTING_DRAFT_ANSWER'	=> 'Bu buton sonraki bir tarihte tamamlamak ve göndermek için başlığınızı taslak olarak kaydetmeye olanak sağlar. Kaydedilen bir taslağı yeniden yüklemek için, Kullanıcı Kontrol Panelini ziyaret edin.',
	'HELP_FAQ_POSTING_DRAFT_QUESTION'	=> 'Başlığa mesaj gönderilirken görülen “Kaydet” butonu nedir?',
	'HELP_FAQ_POSTING_EDIT_DELETE_ANSWER'	=> 'Mesaj panosu yöneticisi veya moderatör olmadığınız sürece, sadece kendinize ait mesajları düzenleyebilir veya silebilirsiniz. Gönderdiğiniz bir mesajı düzenle butonuna tıklayarak düzenleyebilirsiniz (bu imkan bazen sadece belirli bir süre için mevcuttur). Eğer mesajınıza birileri cevap göndermişse, başlığa döndüğünüzde mesajınızın altında metni kaç defa düzenlediğinizi gösteren kısa bir yazı göreceksiniz. Mesajınıza henüz cevap verilmemişse, bu not görülmez. Ayrıca mesajınız mesaj panosu yöneticileri veya moderatörler tarafından düzenlenince de bu metin görünmez. Buna rağmen mesajı neden düzenlediklerine dair kendilerine has bir not bırakabilirler. Not: Normal kullanıcılar herhangi birinden cevap geldikten sonra bir mesajı silemezler.',
	'HELP_FAQ_POSTING_EDIT_DELETE_QUESTION'	=> 'Bir mesajı nasıl düzenleyebilir ya da silebilirim?',
	'HELP_FAQ_POSTING_FORUM_RESTRICTED_ANSWER'	=> 'Bazı forumlar sadece belirli kullanıcılara veya kullanıcı gruplarına açık olabilir. Mesajları okumak, görüntülemek, göndermek ya da diğer işlemler için özel yetki gerekebilir. Size erişim verilebilmesi için bir moderatör ya da mesaj panosu yöneticisiyle iletişime geçin.',
	'HELP_FAQ_POSTING_FORUM_RESTRICTED_QUESTION'	=> 'Neden bir foruma erişimim yok?',
	'HELP_FAQ_POSTING_NO_ATTACHMENTS_ANSWER'	=> 'Her forumda, her grupta, veya her kullanıcı temelinde dosya eki izinleri vardır. Mesaj panosu yöneticisi mesaj gönderdiğiniz belirli forum için eklenen dosya eklerine izin vermemiş olabilir, ya da muhtemelen sadece bazı gruplar dosya eki gönderebiliyordur. Eğer dosya eki eklemenin sizin için neden kapalı olduğu hakkında bir şüpheniz varsa mesaj panosu yöneticiyle iletişime geçin.',
	'HELP_FAQ_POSTING_NO_ATTACHMENTS_QUESTION'	=> 'Neden dosya ekleri ekleyemiyorum?',
	'HELP_FAQ_POSTING_POLL_ADD_ANSWER'	=> 'Anket seçenekleri için sınır, mesaj panosu yöneticisi tarafından ayarlanır. Eğer anketiniz için izin verilen miktardan daha fazla seçenek eklemeniz gerekiyorsa, mesaj panosu yöneticisi ile iletişime geçin.',
	'HELP_FAQ_POSTING_POLL_ADD_QUESTION'	=> 'Neden daha fazla anket seçeneği ekleyemiyorum?',
	'HELP_FAQ_POSTING_POLL_CREATE_ANSWER'	=> 'Yeni bir başlık gönderirken (veya bir başlığın ilk mesajını düzenlerken (bu tabiki sahip olduğunuz izne bağlıdır)), mesaj gönderme formunun altında “Anket oluştur” sekmesini göreceksiniz (böyle bir formu göremiyorsanız, anket oluşturma yetkiniz yok demektir). Anket için “Anket sorusu” kısmına bir başlık girmelisiniz ve sonra “Anket seçenekleri” alanına, her satıra ayrı bir seçenek olacak şekilde en az iki seçenek girmelisiniz (bu sınır mesaj panosu yönetici tarafından ayarlanır). Ayrıca kullanıcıların oylama sırasında seçebilecekleri seçeneklerin sayısını “Her kullanıcı için seçenek” bölümünün altından ayarlayabilirsiniz, anket için gün cinsinden bir zaman sınırı belirleyebilirsiniz (sınırsız sürede olması için 0 yazın) ve son olarak eğer kullanıcıların kendi oylarını değiştirme izni varsa oy verdikleri seçeneği değiştirebilirler.',
	'HELP_FAQ_POSTING_POLL_CREATE_QUESTION'	=> 'Nasıl bir anket oluştururum?',
	'HELP_FAQ_POSTING_POLL_EDIT_ANSWER'	=> 'Anketlerde, mesajlar gibi sadece gönderen kullanıcı, bir moderatör veya bir yönetici tarafından değiştirilebilir. Bir anketi değiştirmek için, başlığın ilk mesajını tıklayın; ilgili anket daima bu mesaja bağlıdır. Ankete henüz katılan olmadıysa, hazırlayan kullanıcı tarafından değiştirilebilir veya silinebilir. Fakat, eğer üyeler ankete katılmışsa, sadece forum ve mesaj panosu yöneticileri tarafından değiştirilebilir veya silinebilir. Böylece bir süre sonra şıkları değiştirip anket sonuçlarını saptırma olanağı kalmaz.',
	'HELP_FAQ_POSTING_POLL_EDIT_QUESTION'	=> 'Bir anketi nasıl değiştirir veya silerim?',
	'HELP_FAQ_POSTING_QUEUE_ANSWER'	=> 'Mesaj panosu yöneticisi, foruma mesaj göndermek için ilk önce mesajların incelenmesi gerektiğine karar vermiş olabilir. Ayrıca yönetici, sizi bir kullanıcı grubuna yerleştirmiş olabilir ve bu grubun mesajları gönderilmeden önce incelenmesi gerekiyor olabilir. Daha fazla bilgi için lütfen mesaj panosu yöneticisiyle iletişime geçin.',
	'HELP_FAQ_POSTING_QUEUE_QUESTION'	=> 'Neden mesajımın onaylanması gerekiyor?',
	'HELP_FAQ_POSTING_REPORT_ANSWER'	=> 'Eğer mesaj panosu yöneticimi buna izin veriyorsa, bildiri yapmak istediğiniz mesaja gidin ve orada mesaj bildirileri için bir buton göreceksiniz. Bu butona tıklayarak mesaj bildirisi için zorunlu adımlara ulaşacaksınız.',
	'HELP_FAQ_POSTING_REPORT_QUESTION'	=> 'Bir moderatöre mesajları nasıl bildirebilirim?',
	'HELP_FAQ_POSTING_SIGNATURE_ANSWER'	=> 'Herhangi bir mesaja imzanızı ekleyebilmek için öncelikle Kullanıcı Kontrol Panelinizden bir imza oluşturmanız gerekmektedir. Daha sonra mesaj gönderme formunun alt kısmındaki <em>Bir imza ekle</em> seçeneğini seçip mesajınıza imzanızı ekleyebilirsiniz. Ayrıca Kullanıcı Kontrol Panelindeki uygun seçeneği işaretleyerek tüm mesajlarınıza varsayılan olarak bir imza ekleyebilirsiniz. Buna rağmen dilediğiniz her mesaj için imzanızın eklenmesini önleyebilirsiniz, bunu yapmak içinse mesaj gönderme formunda imza ekleme seçeneğinin işaretini kaldırmanız yeterlidir.',
	'HELP_FAQ_POSTING_SIGNATURE_QUESTION'	=> 'Mesajıma bir imza nasıl eklerim?',
	'HELP_FAQ_POSTING_WARNING_ANSWER'	=> 'Her mesaj panosu yöneticisi, mesaj panoları için kendi kurallarını belirlemiştir. Eğer bir kural ihlalinde bulunduysanız, uyarı alabilirsiniz. Not: Bu durum, mesaj panosu yöneticisi’nin kararındadır ve phpBB Limited verilen bu uyarı ile ilgili herhangi bir şey yapamaz. Eğer neden bir uyarı aldığınızı bilmiyorsanız, mesaj panosu yöneticisi ile iletişime geçin.',
	'HELP_FAQ_POSTING_WARNING_QUESTION'	=> 'Neden bir uyarı aldım?',

	'HELP_FAQ_SEARCH_BLANK_ANSWER'	=> 'Arama sorgunuzla ilgili geri dönen çok fazla sonuç olduğu için web sunucusu meşgul duruma geçmiş olabilir. Gelişmiş aramayı kullanın ve terimler içinde kullanılacak daha fazla özellik ile aranacak forumları belirleyin.',
	'HELP_FAQ_SEARCH_BLANK_QUESTION'	=> 'Neden arama yaptığımda boş bir sayfa çıkıyor!?',
	'HELP_FAQ_SEARCH_FORUM_ANSWER'	=> 'Ana sayfa görüntülenirken, forum görüntülenirken ya da başlık görüntülenirken yerleşik arama kutusunun içerisine aranacak terimi girerek arama yapabilirsiniz. Gelişmiş arama yapmak için forumda tüm sayfalarda bulunan “Arama” bağlantısına tıklayabilirsiniz. Arama sayfasına erişiminiz kullandığınız temaya bağlı olarak değişebilir.',
	'HELP_FAQ_SEARCH_FORUM_QUESTION'	=> 'Bir forumda ya da forumlarda nasıl arama yapabilirim?',
	'HELP_FAQ_SEARCH_MEMBERS_ANSWER'	=> '“Üyeler” sayfasına gidin ve “Bir üye bul” bağlantısına tıklayın.',
	'HELP_FAQ_SEARCH_MEMBERS_QUESTION'	=> 'Üyeler için nasıl arama yaparım?',
	'HELP_FAQ_SEARCH_NO_RESULT_ANSWER'	=> 'Yaptığınız arama, çok belirsiz ve phpBB tarafından indekslenmeyen çok fazla genel terim içeriyor olabilir. Gelişmiş Arama içerisindeki daha fazla özellik ve mevcut seçenekleri kullanarak arama yapabilirsiniz.',
	'HELP_FAQ_SEARCH_NO_RESULT_QUESTION'	=> 'Neden arama yaptığımda sonuç çıkmıyor?',
	'HELP_FAQ_SEARCH_OWN_ANSWER'	=> 'Kendi mesajlarınızı, Kullanıcı Kontrol Panelinden “Mesajlarımı göster” bağlantısına tıklayarak ya da kendi profil sayfanızdaki “Kullanıcı’nın mesajlarını ara” bağlantısına tıklayarak ya da mesaj panosunun üstündeki “Hızlı Bağlantılar” menüsüne tıklayarak bulabilirsiniz. Başlıklarınızı aramak için, Gelişmiş arama sayfasını kullanın ve uygun olan seçenekleri doldurun.',
	'HELP_FAQ_SEARCH_OWN_QUESTION'	=> 'Kendi mesajlarımı ve başlıklarımı nasıl bulabilirim?',

	'HELP_FAQ_USERSETTINGS_AVATAR_ANSWER'	=> 'Mesajları görüntülerken kullanıcı adı ile birlikte iki tane resim görüntülenebilir. Bunlardan bir tanesi rütbeniz ile ilişkilendirilmiş; genellikle yıldız, blok ya da nokta şeklinde; mesaj panosundaki durumunuzu veya gönderdiğiniz mesaj sayısına göre değişkenlik gösteren bir resim olabilir. Diğeri ise, çoğunlukla büyük boyda, her kullanıcı için kişisel ya da benzersiz, avatar olarak bilinen bir resimdir.',
	'HELP_FAQ_USERSETTINGS_AVATAR_DISPLAY_ANSWER'	=> 'Kullanıcı Kontrol Panelinizde, “Profil” bölümünden şu dört farklı metottan birisini kullanarak avatar ekleyebilirsiniz: Gravatar, Galeri, Uzak Avatar ya da Avatar Yükleme. Avatar kullanma imkanı ve avatar kullanımında seçilebilecek yollar mesaj panosu yöneticisinin kararına bağlıdır. Eğer avatarları kullanamıyorsanız, bir mesaj panosu yöneticisi ile iletişime geçin.',
	'HELP_FAQ_USERSETTINGS_AVATAR_DISPLAY_QUESTION'	=> 'Bir avatarı nasıl gösterebilirim?',
	'HELP_FAQ_USERSETTINGS_AVATAR_QUESTION'	=> 'Kullanıcı adımın yanındaki resim nedir?',
	'HELP_FAQ_USERSETTINGS_CHANGE_SETTINGS_ANSWER'	=> 'Eğer kayıtlı bir kullanıcı iseniz, tüm ayarlarınız mesaj panosu veritabanında saklanır. Ayarlarınızı değiştirmek için Kullanıcı Kontrol Panelinizi ziyaret edin; genellikle sayfaların üst kısmında, kullanıcı adınızın üzerine tıkladığınızda bir bağlantı bulunur. Bu sistem size tüm ayarlarınızı ve tercihlerinizi değiştirme izni verecektir.',
	'HELP_FAQ_USERSETTINGS_CHANGE_SETTINGS_QUESTION'	=> 'Ayarlarımı nasıl değiştirebilirim?',
	'HELP_FAQ_USERSETTINGS_EMAIL_LOGIN_ANSWER'	=> 'E-posta formunu kullanarak sadece kayıtlı kullanıcılar e-posta gönderebilir (eğer yönetici bu özelliği aktifleştirdiyse). Bunun sebebi, e-posta sisteminin anonim kullanıcılar tarafından suistimal edilmesini önlemektir.',
	'HELP_FAQ_USERSETTINGS_EMAIL_LOGIN_QUESTION'	=> 'Bir kullanıcıya ait e-posta bağlantısını tıklayınca neden giriş yapmam isteniyor?',
	'HELP_FAQ_USERSETTINGS_HIDE_ONLINE_ANSWER'	=> 'Kullanıcı Kontrol Panelinizden, “Mesaj panosu tercihleri” bölümüne tıkladığınızda, <em>Çevrimiçi durumumu gizle</em> seçeneğini bulacaksınız. Bu seçeneği aktifleştirdiğinizde kullanıcı adınız, çevrimiçi kullanıcılar listesinde sadece yöneticiler, moderatörler ve kendiniz tarafından görüntülenecektir. Böylece gizli bir kullanıcı olarak sayılacaksınız.',
	'HELP_FAQ_USERSETTINGS_HIDE_ONLINE_QUESTION'	=> 'Kullanıcı adımın çevrimiçi kullanıcılar listesinde görüntülenmesini nasıl önleyebilirim?',
	'HELP_FAQ_USERSETTINGS_LANGUAGE_ANSWER'	=> 'Mesaj panosu yöneticisi konuştuğunuz dili destekleyen paketi kurmamıştır, ya da hiç kimse bu mesaj panosunu konuştuğunuz dile henüz çevirmemiştir. Bir mesaj panosu yöneticisine başvurup, ihtiyacınız olan dil paketini kurmasını rica edin. Eğer dil paketi mevcut değilse, yeni bir çeviri oluşturmakta özgürsünüz. Daha fazla bilgi <a href="https://www.phpbb.com/">phpBB</a>&reg; websitesinde bulunabilir.',
	'HELP_FAQ_USERSETTINGS_LANGUAGE_QUESTION'	=> 'Konuştuğum dil listede yok!',
	'HELP_FAQ_USERSETTINGS_RANK_ANSWER'	=> 'Genelde kullanıcı rütbenizi doğrudan değiştirmeniz mümkün değildir (kullanıcı rütbesi, gönderdiğiniz mesajın yanında bulunan isminizin altında ve kullanıcı profili sayfasında görülür). Çoğu mesaj panosunda kullanıcı rütbeleri, gönderilen mesajların sayısını veya yetkili üyeleri belirlemek için kullanılır, örn. yöneticiler veya mesaj panosu yöneticileri özel bir rütbeye sahip olabilir. Lütfen gereksiz yere mesaj gönderipte rütbenizi yükseltmeye çalışmayın, elde edeceğiniz tek sonuç, yöneticilerin mesajlarınızın sayısını düşürmesi olacaktır.',
	'HELP_FAQ_USERSETTINGS_RANK_QUESTION'	=> 'Rütbem nedir ve onu nasıl değiştirebilirim?',
	'HELP_FAQ_USERSETTINGS_SERVERTIME_ANSWER'	=> 'Eğer doğru zaman dilimini seçtiğinize eminseniz ve zaman hala yanlışsa, sunucu saatinde kayıtlı zaman yanlış olabilir. Lütfen problemin düzeltilmesi için bir yöneticiyi haberdar edin.',
	'HELP_FAQ_USERSETTINGS_SERVERTIME_QUESTION'	=> 'Değişik bir zaman dilimi seçtim ama saatler hala yanlış!',
	'HELP_FAQ_USERSETTINGS_TIMEZONE_ANSWER'	=> 'Gösterilen zaman, sizin bulunduğunuz yerden farklı bir zaman dilimindeyse bu olabilir. Bu durumu düzeltmek için, Kullanıcı Kontrol Panelinizi ziyaret edin ve ayrıntılı alandan uygun zaman diliminize göre değiştirin, ör. Londra, Paris, New York, Sydney, gibi. Not: Bu zaman dilimi değişikliklerini ve diğer bir çok ayarları sadece kayıtlı kullanıcılar yapabilir. Eğer kayıtlı değilseniz, şimdi kaydolmanın tam zamanı.',
	'HELP_FAQ_USERSETTINGS_TIMEZONE_QUESTION'	=> 'Gösterilen zamanlar yanlış!',
));
