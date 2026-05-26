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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// Common installer pages
$lang = array_merge($lang, array(
	'INSTALL_PANEL'	=> 'Kurulum Paneli',
	'SELECT_LANG'	=> 'Dil seçin',

	'STAGE_INSTALL'	=> 'phpBB kurulumu',

	// Introduction page
	'INTRODUCTION_TITLE'	=> 'Giriş',
	'INTRODUCTION_BODY'		=> 'phpBB3’e hoşgeldiniz!<br /><br />phpBB® dünyadaki açık kaynak mesaj panosu çözümlerinin en yaygın olarak kullanılanıdır. phpBB3 2000 yılında başlayan bir paket dizisinin en son bölümüdür. Bunlardan önce phpBB3 zengin özellikli, kullanıcı dostu, ve phpBB Takımı tarafından tam desteklenen bir yazılımdır. phpBB3, phpBB2’de yapılanlardan daha çok geliştirilmiştir, önceki sürümlerde bulunmayan popüler ve genellikle istenilen özellikler eklenmiştir. Umarız beklentilerinizi fazlasıyla karşılar.<br /><br />Bu kurulum sistemi phpBB3 kurulumu yaparken, geçmiş bir sürümden phpBB3’ün son sürümüne güncelleme yaparken, hem de farklı bir mesaj panosu sisteminden phpBB3’e dönüşüm yaparken (phpBB2 dahil) size rehberlik edecektir. Daha fazla bilgi için, sizi <a href="%1$s">kurulum rehberini</a> okumaya teşvik ediyoruz.<br /><br />phpBB3 lisansını okumak için ya da destek almak hakkında bilgileri ve bizim bu konuda tutumlarımızı öğrenmek için, lütfen yan menüden ilgili seçenekleri seçin. Devam etmek için, lütfen yukarıdan uygun bir sekme seçin.',

	// Support page
	'SUPPORT_TITLE'		=> 'Destek',
	'SUPPORT_BODY'		=> 'phpBB3’ün şu anki sabit sürümü için ücretsiz, tam destek sağlanacaktır. Destek, şu konuları kapsar:</p><ul><li>kurulum</li><li>ayarlar</li><li>teknik sorular</li><li>yazılımdaki olası hatalara bağlı problemler</li><li>Release Candidate (RC) sürümlerinden son sabit sürüme güncelleme</li><li>phpBB 2.0.x sürümlerinden phpBB3 sürümüne dönüştürme</li><li>diğer mesaj panosu yazılımlarından phpBB3’e dönüştürme (lütfen <a href="https://www.phpbb.com/community/viewforum.php?f=486">Dönüştürücüler Forumuna</a> bakın)</li></ul><p>Hala phpBB3’ün beta sürümlerini kullanan kullanıcıların kurulumlarını, son sürümün yeni kopyası ile değiştirmeye teşvik ediyoruz.</p><h2>Eklentiler / Stiller</h2><p>Eklentiler ile ilgili sorunlar için, lütfen uygun olan <a href="https://www.phpbb.com/community/viewforum.php?f=451">Uzantılar Forumuna</a> mesaj gönderin.<br />Stiller, temalar ve şablonlar ile ilgili sorunlar için, lütfen uygun olan <a href="https://www.phpbb.com/community/viewforum.php?f=471">Stiller Forumuna</a> mesaj gönderin.<br /><br />Eğer belirli bir pakete bağlı sorunuz varsa, lütfen direkt olarak paket için belirlenmiş başlığa mesaj gönderin.</p><h2>Destek Alma</h2><p><a href="https://www.phpbb.com/support/">Destek Bölümü</a><br /><a href="https://www.phpbb.com/support/docs/en/3.3/ug/quickstart/">Kolay Başlangıç Rehberi</a><br /><br />Son haberler ve yayınlanan sürümler ile güncel kalmak için, bizi <a href="https://www.twitter.com/phpbb/">Twitter</a> ve <a href="https://www.facebook.com/phpbb/">Facebook</a> sayfalarımızdan takip edebilirsiniz<br /><br />',

	// License
	'LICENSE_TITLE'		=> 'Genel Kamu Lisansı',

	// Install page
	'INSTALL_INTRO'			=> 'Kuruluma Hoşgeldiniz',
	'INSTALL_INTRO_BODY'	=> 'Bu seçenek ile, sunucunuza phpBB3 kurmak mümkün olacaktır.</p><p>Devam etmek için, veritabanı ayarlarınıza ihtiyacınız vardır. Eğer veritabanı ayarlarınızı bilmiyorsanız, lütfen hosting firmanız ile iletişime geçin ve onlara sorun. Bu ayarlar olmadan devam edemezsiniz. Size gerekenler:</p>
	
	<ul>
		<li>Veritabanı Tipi - kullanacağınız veritabanı.</li>
		<li>Veritabanı sunucu adı ya da DSN - veritabanı sunucusunun adresi.</li>
		<li>Veritabanı sunucu portu - veritabanı sunucusunun portu (genellikle bu gerekli değildir).</li>
		<li>Veritabanı adı - sunucudaki veritabanının adı.</li>
		<li>Veritabanı kullanıcı adı ve Veritabanı şifresi - veritabanına erişim için giriş bilgileri.</li>
	</ul>
     
	<p><strong>Not:</strong> eğer kurulum yaparken SQLite kullanacaksanız, DSN alanına veritabanı dosyanıza ulaşan tam yolu girmelisiniz ve kullanıcı adı ile şifre alanlarını boş bırakmalısınız. Güvenlik sebeplerinden dolayı, veritabanı dosyasının web üzerinden erişilebilir bir konumda bulunmadığına emin olun.</p> 
     
	<p>phpBB3 alttaki veritabanlarını destekler:</p>
	<ul>
		<li>MySQL 4.1.3 veya üzeri (MySQLi gerekli)</li>
		<li>PostgreSQL 8.3+</li>
		<li>SQLite 2.8.2+</li>
		<li>SQLite 3.6.15+</li>
		<li>MS SQL Server 2000 veya üzeri (direkt olarak veya ODBC yoluyla)</li>
		<li>MS SQL Server 2005 veya üzeri (native)</li>
		<li>Oracle</li>
	</ul>
        
	<p>Sunucunuzda sadece bu desteklenen veritabanları görüntülenecektir.',
	'ACP_LINK'	=> '<a href="%1$s">YKP sayfasına</a> git',

	'INSTALL_PHPBB_INSTALLED'		=> 'phpBB zaten kuruldu.',
	'INSTALL_PHPBB_NOT_INSTALLED'	=> 'phpBB henüz kurulmadı.',
	'INSTALL_RESTART'				=> 'Kurulumu yeniden başlat',	
));

// Requirements translation
$lang = array_merge($lang, array(
	// Filesystem requirements
	'FILE_NOT_EXISTS'						=> 'Dosya mevcut değil',
	'FILE_NOT_EXISTS_EXPLAIN'				=> 'phpBB’nin kurulabilmesi için %1$s dosyasının mevcut olması gerekiyor.',
	'FILE_NOT_EXISTS_EXPLAIN_OPTIONAL'		=> 'İyi bir forum kullanıcı deneyimi için %1$s dosyasının mevcut olması önerilir.',
	'FILE_NOT_WRITABLE'						=> 'Dosya yazılabilir değil',
	'FILE_NOT_WRITABLE_EXPLAIN'				=> 'phpBB’nin kurulabilmesi için %1$s dosyasının yazılabilir olması gerekiyor.',
	'FILE_NOT_WRITABLE_EXPLAIN_OPTIONAL'	=> 'İyi bir forum kullanıcı deneyimi için %1$s dosyasının yazılabilir olması önerilir.',

	'DIRECTORY_NOT_EXISTS'						=> 'Dizin mevcut değil',
	'DIRECTORY_NOT_EXISTS_EXPLAIN'				=> 'phpBB’nin kurulabilmesi için %1$s dizininin mevcut olması gerekiyor.',
	'DIRECTORY_NOT_EXISTS_EXPLAIN_OPTIONAL'		=> 'İyi bir forum kullanıcı deneyimi için %1$s dizininin mevcut olması önerilir.',
	'DIRECTORY_NOT_WRITABLE'					=> 'Dizin yazılabilir değil',
	'DIRECTORY_NOT_WRITABLE_EXPLAIN'			=> 'phpBB’nin kurulabilmesi için %1$s dizininin yazılabilir olması gerekiyor.',
	'DIRECTORY_NOT_WRITABLE_EXPLAIN_OPTIONAL'	=> 'İyi bir forum kullanıcı deneyimi için %1$s dizininin yazılabilir olması önerilir.',

	// Server requirements
	'PHP_VERSION_REQD'					=> 'PHP sürümü',
	'PHP_VERSION_REQD_EXPLAIN'			=> 'phpBB, PHP 7.2.0 ya da üst sürümünü gerektirir.',
	'PHP_GETIMAGESIZE_SUPPORT'			=> 'PHP getimagesize() fonksiyonu gerekmektedir',
	'PHP_GETIMAGESIZE_SUPPORT_EXPLAIN'	=> 'phpBB’nin düzgün bir şekilde çalışması için, getimagesize fonksiyonu mevcut olmalıdır.',
	'PCRE_UTF_SUPPORT'					=> 'PCRE UTF-8 desteği',
	'PCRE_UTF_SUPPORT_EXPLAIN'			=> 'Eğer PHP kurulumunuz PCRE uzantısı içinde UTF-8 desteği ile derlenmediyse phpBB çalışmayacaktır.',
	'PHP_JSON_SUPPORT'					=> 'PHP JSON desteği',
	'PHP_JSON_SUPPORT_EXPLAIN'			=> 'phpBB’nin düzgün bir şekilde çalışması için, PHP JSON eklentisinin mevcut olması gereklidir.',
	'PHP_MBSTRING_SUPPORT'				=> 'PHP mbstring desteği',
	'PHP_MBSTRING_SUPPORT_EXPLAIN'		=> 'phpBB’nin düzgün bir şekilde çalışması için, PHP mbstring eklentisinin mevcut olması gereklidir.',	
	'PHP_XML_SUPPORT'					=> 'PHP XML/DOM desteği',
	'PHP_XML_SUPPORT_EXPLAIN'			=> 'phpBB’nin düzgün bir şekilde çalışması için, PHP XML/DOM eklentisinin mevcut olması gereklidir.',
	'PHP_SUPPORTED_DB'					=> 'Desteklenen veritabanları',
	'PHP_SUPPORTED_DB_EXPLAIN'			=> 'PHP için uyumlu en düşük bir veritabanı desteğine sahip olmalısınız. Eğer mevcut görünen veritabanı modülleri yoksa hosting sağlayıcınız ile iletişime geçin ya da konu ile ilgili tavsiyeler için PHP kurulum dokümanını inceleyin.',

	'RETEST_REQUIREMENTS'	=> 'Yeniden test gereksinimleri',

	'STAGE_REQUIREMENTS'	=> 'Kontrol gereksinimleri',
));

// General error messages
$lang = array_merge($lang, array(
	'INST_ERR_MISSING_DATA'		=> 'Bu bölümdeki tüm alanları doldurmalısınız.',

	'TIMEOUT_DETECTED_TITLE'	=> 'Kurulumcu bir zaman aşımı tespit etti',
	'TIMEOUT_DETECTED_MESSAGE'	=> 'Kurulumcu bir zaman aşımı tespit etti. Sayfayı yenileyebilirsiniz, ancak bu işlem veri bozulmasına yol açabilir. Zaman aşımı ayarlarınızı artırmanızı ya da Komut Satırı Arabirimi (CLI) kullanmayı denemenizi öneririz.',
));

// Data obtaining translations
$lang = array_merge($lang, array(
	'STAGE_OBTAIN_DATA'	=> 'Kurulum verisi ayarı',

	//
	// Admin data
	//
	'STAGE_ADMINISTRATOR'	=> 'Yönetici ayrıntıları',

	// Form labels
	'ADMIN_CONFIG'				=> 'Yönetici ayarları',
	'ADMIN_PASSWORD'			=> 'Yönetici şifresi',
	'ADMIN_PASSWORD_CONFIRM'	=> 'Yönetici şifresini doğrula',
	'ADMIN_PASSWORD_EXPLAIN'	=> 'Lütfen 6 ve 30 karakter arası uzunlukta bir şifre girin.',
	'ADMIN_USERNAME'			=> 'Yönetici kullanıcı adı',
	'ADMIN_USERNAME_EXPLAIN'	=> 'Lütfen 3 ve 20 karakter arası uzunlukta bir kullanıcı adı girin.',

	// Errors
	'INST_ERR_EMAIL_INVALID'		=> 'Girdiğiniz e-posta adresi geçersiz.',
	'INST_ERR_PASSWORD_MISMATCH' 	=> 'Girdiğiniz şifreler uyuşmuyor.',
	'INST_ERR_PASSWORD_TOO_LONG' 	=> 'Girdiğiniz şifre çok uzun. En fazla uzunluk 30 karakter olmalıdır.',
	'INST_ERR_PASSWORD_TOO_SHORT' 	=> 'Girdiğiniz şifre çok kısa. En az uzunluk 6 karakter olmalıdır.',
	'INST_ERR_USER_TOO_LONG'		=> 'Girdiğiniz kullanıcı adı çok uzun. En fazla 20 karakter uzunluğunda olmalıdır.',
	'INST_ERR_USER_TOO_SHORT'		=> 'Girdiğiniz kullanıcı adı çok kısa. En az 3 karakter uzunluğunda olmalıdır.',

	//
	// Board data
	//
	// Form labels
	'BOARD_CONFIG'		=> 'Mesaj panosu yapılandırması',
	'DEFAULT_LANGUAGE'	=> 'Varsayılan dil',
	'BOARD_NAME'		=> 'Mesaj panosu adı',
	'BOARD_DESCRIPTION'	=> 'Mesaj panosunun kısa açıklaması',

	//
	// Database data
	//
	'STAGE_DATABASE'	=> 'Veritabanı yapılandırması',

	// Form labels
	'DB_CONFIG'				=> 'Veritabanı ayarları',
	'DBMS'					=> 'Veritabanı tipi',
	'DB_HOST'				=> 'Veritabanı sunucusu ana makine adı veya VKA',
	'DB_HOST_EXPLAIN'		=> 'Veri Kaynak Adı için VKA (DSN) belirtilmelidir ve bu sadece ODBC kurulumlarında bulunur. PostgreSQL’da, yerel sunucuya UNIX domain socket yoluyla bağlanmak için localhost, TCP yoluyla bağlanmak içinse 127.0.0.1 kullanılır. SQLite için, veritabanı dosyanıza giden tam yolu girin.',
	'DB_PORT'				=> 'Veritabanı sunucu portu',
	'DB_PORT_EXPLAIN'		=> 'Sunucu, bildiğiniz standart bir portta çalışmıyorsa burayı boş bırakın.',
	'DB_PASSWORD'			=> 'Veritabanı şifresi',
	'DB_NAME'				=> 'Veritabanı adı',
	'DB_USERNAME'			=> 'Veritabanı kullanıcı adı',
	'DATABASE_VERSION'		=> 'Veritabanı sürümü',
	'TABLE_PREFIX'			=> 'Veritabanındaki tablolar için önek',
	'TABLE_PREFIX_EXPLAIN'	=> 'Önek, bir harf ile başlamalıdır ve sadece harfler, sayılar ve altçizgiler içermelidir.',

	// Database options
	'DB_OPTION_MSSQL_ODBC'	=> 'ODBC aracılığıyla MSSQL Sunucusu 2000+',
	'DB_OPTION_MSSQLNATIVE'	=> 'MSSQL Sunucusu 2005+ [ Native ]',
	'DB_OPTION_MYSQLI'		=> 'MySQLi Eklentisi ile MySQL',
	'DB_OPTION_ORACLE'		=> 'Oracle',
	'DB_OPTION_POSTGRES'	=> 'PostgreSQL',
	'DB_OPTION_SQLITE3'		=> 'SQLite 3',

	// Errors
	'INST_ERR_DB'					=> 'Veritabanı kurulum hatası',	
	'INST_ERR_NO_DB'				=> 'Seçilen veritabanı tipi için PHP modülü yüklenmedi.',
	'INST_ERR_DB_INVALID_PREFIX'	=> 'Girdiğiniz önek geçersiz. Önek, bir harf ile başlamalı ve sadece harfler, sayılar ve altçizgiler içermelidir.',
	'INST_ERR_PREFIX_TOO_LONG'		=> 'Belirttiğiniz tablo öneki çok uzun. En fazla %d karakter uzunluğunda olmalıdır.',
	'INST_ERR_DB_NO_NAME'			=> 'Hiç bir veritabanı adı belirtilmedi.',
	'INST_ERR_DB_FORUM_PATH'		=> 'Mesaj panonuzun dizin ağacı içerisinde veritabanı dosyası belirlendi. Web üzerinden erişim konumunda olmayan bir yere bu dosyayı yerleştirmelisiniz.',
	'INST_ERR_DB_CONNECT'			=> 'Veritabanına bağlanılamıyor, hata mesajı için alta bakın.',
	'INST_ERR_DB_NO_WRITABLE'		=> 'Hem veritabanı hem de onu içeren dizin yazılabilir olmalıdır.',	
	'INST_ERR_DB_NO_ERROR'			=> 'Hiç bir hata mesajı verilmedi.',
	'INST_ERR_PREFIX'				=> 'Belirttiğiniz önek ile başlayan tablolar zaten var, lütfen alternatif bir tane seçin.',
	'INST_ERR_DB_NO_MYSQLI'			=> 'Bu makinede kurulu olan MySQL sürümü seçtiğiniz “MySQLi Eklentisi ile MySQL” seçeneği ile uyuşmuyor. Lütfen bunun yerine “MySQL” seçeneğini seçerek deneyin.',
	'INST_ERR_DB_NO_SQLITE3'		=> 'Kurulu olan SQLite eklentisinin sürümü çok eski, en düşük 3.6.15 sürümüne güncellenmelidir.',
	'INST_ERR_DB_NO_ORACLE'			=> 'Bu makinede kurulu olan Oracle’nin sürümünde <var>NLS_CHARACTERSET</var> parametresini <var>UTF8</var>’e göre ayarlamanız gerekiyor. Kurulumunuzu 9.2+ sürümüne güncelleyin veya parametreyi değiştirin.',
	'INST_ERR_DB_NO_POSTGRES'		=> 'Seçtiğiniz veritabanı <var>UNICODE</var> veya <var>UTF8</var> kodlaması içerisinde oluşturulamadı. <var>UNICODE</var> veya <var>UTF8</var> kodlaması ile oluşturulmuş bir veritabanı ile kurmayı deneyin.',
	'INST_SCHEMA_FILE_NOT_WRITABLE'	=> 'Şema dosyası yazılabilir değil',	

	//
	// Email data
	//
	'EMAIL_CONFIG'	=> 'E-posta yapılandırması',

	// Package info
	'PACKAGE_VERSION'					=> 'Paket sürüm kuruldu',
	'UPDATE_INCOMPLETE'				=> 'phpBB kurulumunuz doğru şekilde güncellenmedi.',
	'UPDATE_INCOMPLETE_MORE'		=> 'Lütfen bu hatayı düzeltmek için alttaki bilgileri okuyun.',
	'UPDATE_INCOMPLETE_EXPLAIN'		=> '<h1>Tamamlanmayan güncelleme</h1>

		<p>phpBB kurulumunuzun son güncellemesinin tamamlanmadığını farkettik. <a href="%1$s" title="%1$s">Veritabanı güncelleyici</a> sayfasını ziyaret edin, <em>Sadece veritabanını güncelle</em> seçeneğini seçin ve <strong>Gönder</strong> butonuna tıklayın. Veritabanınızı başarıyla güncelledikten sonra "install"-dizinini silmeyi unutmayın.</p>',

	//
	// Server data
	//
	// Form labels
	'UPGRADE_INSTRUCTIONS'			=> 'Yeni sürüm <strong>%1$s</strong> yayınlandı. Bu sürüm hakkında daha fazla bilgi almak ve nasıl güncelleme yapacağınızı öğrenmek için lütfen <a href="%2$s" title="%2$s"><strong>sürüm duyurusu</strong></a> sayfasını ziyaret edin.',	
	'SERVER_CONFIG'				=> 'Sunucu yapılandırması',
	'SCRIPT_PATH'				=> 'Komut yolu',
	'SCRIPT_PATH_EXPLAIN'		=> 'Alan adına göre phpBB’nin ilgili konum yolu, ör: <samp>/phpBB3</samp>.',
));

// Default database schema entries... 
$lang = array_merge($lang, array( 
   'CONFIG_BOARD_EMAIL_SIG'        => 'Teşekkürler, Yönetim',
   'CONFIG_SITE_DESC'             => 'Mesaj panonuzu tanımlamak için kısa bir yazı',
   'CONFIG_SITENAME'              => 'siteadresiniz.com',
     
   'DEFAULT_INSTALL_POST'        => '<t>Bu, phpBB3 kurulumunuzun içerisindeki örnek bir mesajdır. Her şey çalışıyor görünüyor. Eğer isterseniz bu mesajı silebilirsiniz ve mesaj panonuzu ayarlamaya devam edebilirsiniz. Kurulum işlemi sırasında öntanımlı kullanıcı grupları, yöneticiler, botlar, global moderatörler, misafirler, kayıtlı kullanıcılar ve kayıtlı COPPA kullanıcıları için ayrılan izinler ilk kategoriniz ve ilk forumunuz için tanımlanmıştır. Ayrıca eğer ilk kategorinizi ve ilk forumunuzu silmeyi tercih ederseniz, oluşturacağınız tüm yeni kategoriler ve forumlar için tüm bu kullanıcı gruplarına izinleri atamayı unutmayın. İlk kategorinizi ve ilk forumunuzu yeniden adlandırmanız ve oluşturacağınız yeni kategoriler ve yeni forumlar için izinleri buradan kopyalamanız önerilir. İyi eğlenceler!</t>',
     
   'FORUMS_FIRST_CATEGORY'      => 'İlk kategoriniz',
   'FORUMS_TEST_FORUM_DESC'   => 'İlk forumunuzun açıklaması.',
   'FORUMS_TEST_FORUM_TITLE'     => 'İlk forumunuz',
     
   'RANKS_SITE_ADMIN_TITLE'        => 'Mesaj Panosu Yöneticisi',
   'REPORT_WAREZ'               => 'Mesaj illegal ya da korsan yazılım bağlantıları içeriyor.',
   'REPORT_SPAM'               => 'Bildirilen mesaj bir web sitesinin ya da diğer bir ürünün reklamını yapma amacında.',
   'REPORT_OFF_TOPIC'            => 'Bildirilen mesaj konu dışı.',
   'REPORT_OTHER'               => 'Bildirilen mesaj diğer kategorilerden herhangi birine uymuyor, lütfen daha fazla bilgi alanını kullanın.', 
     
   'SMILIES_ARROW'            => 'Ok', 
   'SMILIES_CONFUSED'         => 'Şaşkın', 
   'SMILIES_COOL'        => 'Soğukkanlı', 
   'SMILIES_CRYING'           => 'Ağlıyor veya Çok Üzgün', 
   'SMILIES_EMARRASSED'      => 'Utangaç', 
   'SMILIES_EVIL'           => 'Kötü veya Çok Kızgın', 
   'SMILIES_EXCLAMATION'      => 'Haykırma', 
   'SMILIES_GEEK'              => 'Garip', 
   'SMILIES_IDEA'              => 'Fikir', 
   'SMILIES_LAUGHING'          => 'Gülüyor', 
   'SMILIES_MAD'               => 'Kızgın', 
   'SMILIES_MR_GREEN'          => 'Bay Yeşil', 
   'SMILIES_NEUTRAL'           => 'Tarafsız', 
   'SMILIES_QUESTION'          => 'Soru', 
   'SMILIES_RAZZ'              => 'Alaycı', 
   'SMILIES_ROLLING_EYES'      => 'Dönen Gözler', 
   'SMILIES_SAD'               => 'Üzgün', 
   'SMILIES_SHOCKED'           => 'Şok Olmuş', 
   'SMILIES_SMILE'             => 'Gülümseme', 
   'SMILIES_SURPRISED'         => 'Sürpriz', 
   'SMILIES_TWISTED_EVIL'     => 'Çok Kötü', 
   'SMILIES_UBER_GEEK'       => 'Daha Garip', 
   'SMILIES_VERY_HAPPY'      => 'Çok Mutlu', 
   'SMILIES_WINK'             => 'Göz Kırpıyor', 
     
   'TOPICS_TOPIC_TITLE'     => 'phpBB3’e hoşgeldiniz', 
));

// Common navigation items' translation
$lang = array_merge($lang, array(
	'MENU_OVERVIEW'		=> 'Genel bakış',
	'MENU_INTRO'		=> 'Giriş',
	'MENU_LICENSE'		=> 'Lisans',
	'MENU_SUPPORT'		=> 'Destek',
));

// Task names
$lang = array_merge($lang, array(
	// Install filesystem
	'TASK_CREATE_CONFIG_FILE'	=> 'Yapılandırma dosyası oluşturuluyor',

	// Install database
	'TASK_ADD_CONFIG_SETTINGS'			=> 'Yapılandırma ayarları ekleniyor',
	'TASK_ADD_DEFAULT_DATA'				=> 'Varsayılan ayarlar veritabanına ekleniyor',
	'TASK_CREATE_DATABASE_SCHEMA_FILE'	=> 'Veritabanı şeması oluşturuluyor',
	'TASK_SETUP_DATABASE'				=> 'Veritabanı ayarlanıyor',
	'TASK_CREATE_TABLES'				=> 'Tablolar oluşturuluyor',

	// Install data
	'TASK_ADD_BOTS'				=> 'Botlar kaydediliyor',
	'TASK_ADD_LANGUAGES'		=> 'Mevcut diller kuruluyor',
	'TASK_ADD_MODULES'			=> 'Modüller kuruluyor',
	'TASK_CREATE_SEARCH_INDEX'	=> 'Arama indeksi oluşturuluyor',

	// Install finish tasks
	'TASK_INSTALL_EXTENSIONS'	=> 'Paket eklentiler kuruluyor',
	'TASK_NOTIFY_USER'			=> 'Bildirim e-postası gönderiliyor',
	'TASK_POPULATE_MIGRATIONS'	=> 'Migrasyonlar yerleştiriliyor',

	// Installer general progress messages
	'INSTALLER_FINISHED'	=> 'Kurulumcu başarıyla tamamlandı',
));

// Installer's general messages
$lang = array_merge($lang, array(
	'MODULE_NOT_FOUND'				=> 'Modül bulunamadı',
	'MODULE_NOT_FOUND_DESCRIPTION'	=> 'Bir modül bulunamadı, çünkü %s servisi tanımlı değil.',

	'TASK_NOT_FOUND'				=> 'Görev bulunamadı',
	'TASK_NOT_FOUND_DESCRIPTION'	=> 'Bir görev bulunamadı, çünkü %s servisi tanımlı değil.',

	'SKIP_MODULE'	=> '“%s” modulünü atla',
	'SKIP_TASK'		=> '“%s” görevini atla',

	'TASK_SERVICE_INSTALLER_MISSING'	=> 'Tüm kurulumcu görev servisleri “installer” ile başlamalıdır',
	'TASK_CLASS_NOT_FOUND'				=> 'Kurulumcu görev servisi tanımı geçersiz. Servis adı “%1$s” olarak verildi, bunun için beklenen sınıf alan adı “%2$s” dir. Daha fazla bilgi için lütfen task_interface dokümanına bakın.',

	'INSTALLER_CONFIG_NOT_WRITABLE'	=> 'Kurulumcu yapılandırma dosyası yazılabilir değil.',
));

// CLI messages
$lang = array_merge($lang, array(
	'CLI_INSTALL_BOARD'				=> 'phpBB’yi kur',
	'CLI_UPDATE_BOARD'				=> 'phpBB’yi güncelle',
	'CLI_INSTALL_SHOW_CONFIG'		=> 'Kullanılacak yapılandırmayı göster',
	'CLI_INSTALL_VALIDATE_CONFIG'	=> 'Bir yapılandırma dosyasını doğrula',
	'CLI_CONFIG_FILE'				=> 'Kullanılacak yapılandırma dosyası',
	'MISSING_FILE'					=> '%1$s dosyasına erişilemiyor',
	'MISSING_DATA'					=> 'Yapılandırma dosyası eksik veri ya da geçersiz ayarlar içeriyor olabilir.',
	'INVALID_YAML_FILE'				=> '%1$s YAML dosyası ayrıştırılamıyor',
	'CONFIGURATION_VALID'			=> 'Yapılandırma dosyası geçerli',
));

// Common updater messages
$lang = array_merge($lang, array(
	'UPDATE_INSTALLATION'			=> 'phpBB kurulumunu güncelle',
	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Bu seçenek ile, phpBB kurulumunuzu son sürüme güncellemek mümkündür.<br />İşlem sırasında tüm dosyalarınızın bütünlüğü kontrol edilecektir. Güncellemeden önce tüm farklılıkları ve dosyaları gözden geçirebilirsiniz.<br /><br />Dosya güncellemesi iki farklı yolla yapılabilir.</p><h2>Elle Güncelleme</h2><p>Bu seçenek ile dosya değişikliklerinizi kaybetmediğinize emin olmak için sadece değişecek dosyaları indirerek kişisel olarak ayarlayıp yapabilirsiniz. Bu paketi indirdikten sonra phpBB ana dizininizin altındaki doğru pozisyonlara elle yükleme yapmalısınız. İşlem tamamlandıktan sonra, dosyaları doğru yerlerine taşıyıp taşımadığınızı görmek için tekrar dosya kontrol aşamasını yapabilirsiniz.</p><h2>FTP ile Gelişmiş Güncelleme</h2><p>Bu metot ilkine benzer fakat değişecek dosyaları indirmenize ve onları kendinizin güncellemesine gerek yoktur. Bu işlem sizin için yapılacaktır. Bu metotu kullanmak için size FTP giriş bilgileriniz sorulacağından, bu bilgileri bilmeniz gerekmektedir. İşlem bittikten sonra herşeyin doğru olarak güncellendiğine emin olmak için tekrar dosya kontrol aşamasına yönlendirileceksiniz.<br /><br />',
	'UPDATE_INSTRUCTIONS'			=> '

		<h1>Sürüm duyurusu</h1>

		<p>Güncelleme işleminize başlamadan önce lütfen son sürüm için sürüm duyurusunu okuyun. Bu duyuru yararlı bilgiler içerebilir. Sürüm duyurusu ayrıca tüm indirme bağlantılarını ve değişiklik kayıtlarını içermektedir.</p>

		<br />

		<h1>Tam Paket ile kurulumunuz nasıl güncelleştirilir?</h1>

		<p>Kurulumunuzu güncellemenin önerilen yolu Tam Paketi kullanmaktır. Eğer kurulumunuzdaki çekirdek phpBB dosyaları değiştirildiyse, bu değişiklikleri kaybetmemek için gelişmiş güncelleme paketini kullanmak isteyebilirsiniz. Ayrıca kurulumunuzu INSTALL.html belgesinde listelenen diğer yöntemleri kullanarak da güncelleyebilirsiniz. Tam paketi kullanarak phpBB3’ü güncellemek için adımlar:</p>

		<ol style="margin-left: 20px; font-size: 1.1em;">
			<li><strong class="error">Tüm mesaj panosu dosyalarının ve veritabanının yedeğini alın.</strong></li>
			<li><a href="https://www.phpbb.com/downloads/" title="https://www.phpbb.com/downloads/">phpBB.com indirme sayfasına</a> gidin ve son yayınlanan "Full Package" arşivini indirin.</li>
			<li>Arşiv dosyasını açın.</li>
			<li><code class="inline">config.php</code> dosyası ile <code class="inline">/images</code>, <code class="inline">/store</code> ve <code class="inline">/files</code> klasörlerini <em>paketin içerisinden</em> (sitenizden değil) kaldırın (silin).</li>
			<li>Yönetim Kontrol Paneli -> Mesaj Panosu Ayarları sayfasına gidin ve varsayılan stilin prosilver olarak ayarlandığına emin olun. Eğer başka bir stil ayarlıysa, prosilver stilini ayarlayın.</li>
			<li>Sunucunuzda, mesaj panonuzun olduğu ana dizinden <code class="inline">/vendor</code> ve <code class="inline">/cache</code> klasörlerini silin.</li>
			<li>FTP ya da SSH programı yardımıyla kalan dosya ve klasörleri (yani, phpBB3 klasörünün kalan İÇERİĞİNİ) sunucunuzdaki mesaj panosu kurulumunuzun ana dizinine yükleyin, varolan dosyaların üzerine yazın. (Not: Yeni phpBB3 içeriğini yüklerken <code class="inline">/ext</code> klasörüzdeki eklentileri silmemeye dikkat edin.)</li>
			<li><strong><a href="%1$s" title="%1$s">Şimdi tarayıcınız ile kurulum dizinine giderek güncelleme işlemini başlatın</a>.</strong></li>
			<li>Veritabanını güncellemek için adımları izleyin ve bu işlemin tamamlanmasına izin verin.</li>
			<li>FTP ya da SSH programı yardımıyla mesaj panosu kurulumunuzun ana dizininden <code class="inline">/install</code> klasörünü silin.<br><br></li>
		</ol>

		<p>Artık tüm kullanıcılarınız ve mesajlarınızı içeren yeni bir güncel mesaj panonuz var. Ayrıca alttaki işlemleri yapmayı da unutmayın:</p>
		<ul style="margin-left: 20px; font-size: 1.1em;">
			<li>Dil dosyanızı güncelleyin</li>
			<li>Stilinizi güncelleyin<br><br></li>
		</ul>
		
		<h1>Gelişmiş Güncelleme Paketi ile kurulumunuz nasıl güncelleştirilir?</h1>

		<p>Gelişmiş güncelleme paketi sadece uzman kullanıcılar için ve kurulumunuzdaki çekirdek phpBB dosyalarında değişiklik yapıldığı durumlarda önerilir. Ayrıca kurulumunuzu INSTALL.html belgesi içerisinde belirtilen yöntemleri kullanarak da güncelleyebilirsiniz. Gelişmiş güncelleme paketi kullanarak phpBB3’ü güncellemek için adımlar:</p>

		<ol style="margin-left: 20px; font-size: 1.1em;">
			<li><a href="https://www.phpbb.com/downloads/" title="https://www.phpbb.com/downloads/">phpBB.com indirme sayfasına</a> gidin ve "Gelişmiş Güncelleme Paketi" arşivini indirin.</li>
			<li>Arşiv dosyasını açın.</li>
			<li>phpBB ana dizininize (config.php dosyanızın bulunduğu dizin) sıkıştırılmamış "install" ve "vendor" klasörlerinin tamamını yükleyin.<br><br></li>
		</ol>

		<p>Mesaj panonuz, install klasörünün yükleme işlemi sırasında ve işlem bittikten sonra normal kullanıcılar için çevrimdışı olacaktır.<br /><br />
		<strong><a href="%1$s" title="%1$s">Şimdi tarayıcınız ile kurulum dizinine giderek güncelleme işlemini başlatın</a>.</strong><br />
		<br />
		Güncelleme işlemi sırasında size rehberlik edilecektir. Güncelleme tamamlandığında ise bildiri alacaksınız.
		</p>
	',	
));

// Updater forms
$lang = array_merge($lang, array(
	// Updater types
	'UPDATE_TYPE'			=> 'Güncelleme tipi',

	'UPDATE_TYPE_ALL'		=> 'Dosya sistemini ve veritabanını güncelle',
	'UPDATE_TYPE_DB_ONLY'	=> 'Sadece veritabanını güncelle',

	// File updater methods
	'UPDATE_FILE_METHOD_TITLE'		=> 'Dosya güncelleyici metotlar',

	'UPDATE_FILE_METHOD'			=> 'Dosya güncelleyici metotu',
	'UPDATE_FILE_METHOD_DOWNLOAD'	=> 'Düzenlenmiş dosyaları bir arşiv halinde indir',
	'UPDATE_FILE_METHOD_FTP'		=> 'Dosyaları FTP yoluyla güncelle (Otomatik)',
	'UPDATE_FILE_METHOD_FILESYSTEM'	=> 'Dosyaları doğrudan dosya erişimi yoluyla güncelle (Otomatik)',

	// File updater archives
	'SELECT_DOWNLOAD_FORMAT'	=> 'Arşiv indirme formatını seçin',

	// FTP settings
	'FTP_SETTINGS'			=> 'FTP ayarları',
));

// Requirements messages
$lang = array_merge($lang, array(
	'UPDATE_FILES_NOT_FOUND'	=> 'Geçerli bir güncelleme dizini bulunamadı, lütfen uygun dosyaları yüklediğinize emin olun.',

	'NO_UPDATE_FILES_UP_TO_DATE'	=> 'phpBB sürümünüz güncel. Güncelleme aracını çalıştırmanıza gerek yok. Eğer dilerseniz doğru güncelleme dosyalarını yüklediğinize emin olmak için dosyalarınızda bir bütünlük kontrolü yapabilirsiniz.',
	'OLD_UPDATE_FILES'              => 'Güncelleştirme dosyaları güncel değil. phpBB %1$s sürümünden phpBB %2$s sürümüne güncelleme yapmak için güncelleme dosyaları bulundu fakat phpBB’nin son sürümü %3$s.',
	'INCOMPATIBLE_UPDATE_FILES'		=> 'Kurulu olan sürümünüz ile uyuşmayan güncelleme dosyaları bulundu. Kurulu olan sürümünüz %1$s ve güncelleme dosyası phpBB %2$s sürümünden %3$s sürümüne güncelleme yapmak içindir.',
));

// Update files
$lang = array_merge($lang, array(
	'STAGE_UPDATE_FILES'		=> 'Dosyaları güncelle',

	// Check files
	'UPDATE_CHECK_FILES'	=> 'Güncelleme için dosyaları kontrol et',

	// Update file differ
	'FILE_DIFFER_ERROR_FILE_CANNOT_BE_READ'	=> 'Karşılaştırma için %s dosyası açılamadı.',

	'UPDATE_FILE_DIFF'		=> 'Değiştirilmiş dosyalar karşılaştırılıyor',
	'ALL_FILES_DIFFED'		=> 'Tüm düzenlenmiş dosyalar karşılaştırıldı.',

	// File status
	'UPDATE_CONTINUE_FILE_UPDATE'	=> 'Dosyaları güncelle',

	'DOWNLOAD'							=> 'İndir',
	'DOWNLOAD_CONFLICTS'            	=> 'Birleştirilen uyuşmazlıklar arşivini indir',
	'DOWNLOAD_CONFLICTS_EXPLAIN'      	=> 'Ayırt edilen uyuşmazlıklar için &lt;&lt;&lt; ara',
	'DOWNLOAD_UPDATE_METHOD'			=> 'Düzenlenmiş dosyalar arşivini indir',
	'DOWNLOAD_UPDATE_METHOD_EXPLAIN'	=> 'İndirme işlemini tamamladıktan sonra arşivi açın. Arşivin içerisinde phpBB ana dizininize yüklemeniz gereken değiştirilmiş dosyaları bulacaksınız. Lütfen dosyaları ait olan yerlerine yükleyin. Tüm dosyaları yükledikten sonra, güncelleme işlemiyle devam edebilirsiniz.',

	'FILE_ALREADY_UP_TO_DATE'		=> 'Dosya zaten güncel.',
	'FILE_DIFF_NOT_ALLOWED'   		=> 'Dosya, karşılaştırma yapmaya izinli değil.',
	'FILE_USED'						=> 'Bilgi kullanım yeri',			// Single file
	'FILES_CONFLICT'				=> 'Uyuşmaz dosyalar',
	'FILES_CONFLICT_EXPLAIN'		=> 'Aşağıdaki dosyalarda siz ya da başka bir yönetici tarafından daha önce değişiklik yapılmıştır ve eski phpBB sürümünün orijinal dosyaları ile aynı değildir (Bu durum genelde MOD/Eklenti kurulumlarında ortaya çıkar). phpBB, bu dosyalar eğer birleşmeyi denerlerse uyuşmazlıkların meydana geleceğini belirledi. Lütfen uyuşmazlıkları inceleyin ve elle onları düzeltmeyi deneyin ya da size sunulan bir birleştirme metotu seçerek güncellemeye devam edin. Eğer uyuşmazlıkları elle çözdüyseniz, dosyalarda değişiklik yaptıktan sonra tekrar dosya kontrolü yapın. Ayrıca her dosya için sunulan birleştirme metotları arasında seçim yapabilirsiniz. Bu metotlardan birincisini seçerseniz eski dosyanızdaki uyuşmaz satırlar silinerek yeni bir dosya oluşturulacaktır, diğerini seçtiğinizde ise yeni dosya için yapılacak değişiklikler uygulanmayacaktır.',
	'FILES_DELETED'					=> 'Silinen dosyalar',
	'FILES_DELETED_EXPLAIN'			=> 'Aşağıdaki dosyalar yeni sürümde mevcut değildir. Bu dosyaların kurulumunuzdan silinmiş olması gerekmektedir.',	
	'FILES_MODIFIED'				=> 'Düzenlenmiş dosyalar',
	'FILES_MODIFIED_EXPLAIN'		=> 'Aşağıdaki dosyalarda siz ya da başka bir yönetici tarafından daha önce değişiklik yapılmıştır ve eski phpBB sürümünün orijinal dosyaları ile aynı değildir (Bu durum genelde MOD/Eklenti kurulumlarında ortaya çıkar). Yaptığınız bu değişiklikler ile yeni dosya için yapılacak değişiklikler birleştirilerek güncel bir dosya oluşturulacaktır.',
	'FILES_NEW'						=> 'Yeni dosyalar',
	'FILES_NEW_EXPLAIN'				=> 'Aşağıdaki dosyalar şu anki kurulumunuz içerisinde mevcut değil. Bu dosyalar kurulumunuza eklenecektir.',
	'FILES_NEW_CONFLICT'			=> 'Yeni uyuşmayan dosyalar',
	'FILES_NEW_CONFLICT_EXPLAIN'	=> 'Aşağıdaki dosyalar son sürüm içerisindeki yeni dosyalardır, fakat aynı konumda ve aynı isimde zaten bir dosyanın var olduğu belirlendi. Yeni dosya, bu dosyanın üzerine yazılarak değiştirilecektir.',
	'FILES_NOT_MODIFIED'			=> 'Düzenlenmemiş dosyalar',
	'FILES_NOT_MODIFIED_EXPLAIN'	=> 'Aşağıdaki dosyalarda siz ya da başka bir yönetici tarafından daha önce hiç bir değişiklik yapılmamıştır ve güncellemek istediğiniz eski phpBB sürümünün orijinal dosyaları ile aynıdır.',
	'FILES_UP_TO_DATE'				=> 'Zaten güncellenmiş dosyalar',
	'FILES_UP_TO_DATE_EXPLAIN'		=> 'Aşağıdaki dosyalar zaten güncel ve güncelleme yapılmalarına gerek yok.',
	'FILES_VERSION'					=> 'Dosya Sürümü',
	'TOGGLE_DISPLAY'				=> 'Dosya listesini göster/gizle',

	// File updater
	'UPDATE_UPDATING_FILES'	=> 'Dosyalar güncelleniyor',

	'UPDATE_FILE_UPDATER_HAS_FAILED'	=> '“%1$s“ dosya güncelleyicisi başarısız oldu. Kurulumcu “%2$s“ sürümüne geri dönüş yapmayı deneyecek.',
	'UPDATE_FILE_UPDATERS_HAVE_FAILED'	=> 'Dosya güncelleyicisi başarısız. Hiç bir geri dönüş metotu mevcut değil.',

	'UPDATE_CONTINUE_UPDATE_PROCESS'	=> 'Güncelleme işlemine devam et',
	'UPDATE_RECHECK_UPDATE_FILES'		=> 'Dosyaları tekrar kontrol et',
));

// Update database
$lang = array_merge($lang, array(
	'STAGE_UPDATE_DATABASE'		=> 'Veritabanını güncelle',

	'INLINE_UPDATE_SUCCESSFUL'		=> 'Veritabanı başarıyla güncellendi.',

	'TASK_UPDATE_EXTENSIONS'	=> 'Eklentiler güncelleniyor',
));

// Converter
$lang = array_merge($lang, array(
	// Common converter messages
	'CONVERT_NOT_EXIST'			=> 'Belirtilen dönüştürücü bulunmuyor.',
	'DEV_NO_TEST_FILE'			=> 'Dönüştürücü içerisindeki test_file değişkeni için hiç bir değer belirtilmedi. Eğer siz bu dönüştürücünün bir kullanıcıysanız, bu hatayı göremezsiniz, lütfen dönüştürücü yapımcısına bu mesajı bildirin. Eğer siz bir dönüştürücü yapımcısıysanız, doğrulamaya izin verilen yol hangi kaynak mesaj panosu içerisinde bulunuyorsa bir dosyanın adını belirlemelisiniz.',
	'COULD_NOT_FIND_PATH'		=> 'Önceki mesaj panonuzun yolu bulunamıyor. Lütfen ayarlarınızı kontrol edip tekrar deneyin.<br />» %s kaynak yolu olarak belirtildi.',
	'CONFIG_PHPBB_EMPTY'        => '“%s” için phpBB3 yapılandırma değişkeni boş.',

	'MAKE_FOLDER_WRITABLE'		=> 'Lütfen bu dosyanın mevcut olduğuna ve web sunucusu tarafından yazılabilir olduğuna emin olup tekrar deneyin:<br />»<strong>%s</strong>.',
	'MAKE_FOLDERS_WRITABLE'		=> 'Lütfen bu dosyaların mevcut olduğuna ve web sunucusu tarafından yazılabilir olduğuna emin olup tekrar deneyin:<br />»<strong>%s</strong>.',

	'INSTALL_TEST'				=> 'Tekrar test et',

	'NO_TABLES_FOUND'			=> 'Hiç bir tablo bulunmadı.',
	'TABLES_MISSING'			=> 'Bu tablolar bulunamıyor<br />» <strong>%s</strong>.',
	'CHECK_TABLE_PREFIX'		=> 'Lütfen tablo önekini kontrol edin ve tekrar deneyin.',

	// Conversion in progress
	'CATEGORY'					=> 'Kategori',
	'CONTINUE_CONVERT'			=> 'Dönüştürmeye devam et',
	'CONTINUE_CONVERT_BODY'     => 'Önceki bir dönüştürme denemesi belirlendi. Şimdi, yeni bir dönüştürme başlatmak ya da dönüştürmeye devam etmek arasında bir seçim yapabilirsiniz.',
	'CONVERT_NEW_CONVERSION'    => 'Yeni dönüştürme',
	'CONTINUE_OLD_CONVERSION'   => 'Önceki başlatılan dönüştürmeye devam et',
	'POST_ID'					=> 'Mesaj ID numarası',

	// Start conversion
	'SUB_INTRO'					=> 'Giriş',
	'CONVERT_INTRO'				=> 'phpBB Birleştirme Dönüştürücü Sistemine hoşgeldiniz',
	'CONVERT_INTRO_BODY'		=> 'Buradan, diğer (kurulmuş) mesaj panosu sistemlerinden veri transfer edebilirsiniz. Alttaki listede geçerli tüm dönüştürme modülleri mevcuttur. Eğer alttaki listede dönüştürme yapmak istediğiniz mesaj panosu yazılımı gösterilmemişse, daha fazla dönüştürme modülleri için lütfen web sitemizi kontrol edin, bu modüller için mevcut bir indirme dosyası bulunabilir.',
	'AVAILABLE_CONVERTORS'		=> 'Mevcut dönüştürücüler',
	'NO_CONVERTORS'				=> 'Kullanılacak mevcut hiç bir dönüştürücü yok.',
	'CONVERT_OPTIONS'         	=> 'Ayarlar',
	'SOFTWARE'					=> 'Mesaj panosu yazılımı',
	'VERSION'					=> 'Sürüm',
	'CONVERT'					=> 'Dönüştür',

	// Settings
	'STAGE_SETTINGS'			=> 'Ayarlar',
	'TABLE_PREFIX_SAME'			=> 'Dönüştüreceğiniz yazılım tarafından kullanılan bir tablo öneki olmalıdır.<br />» Belirtilen tablo öneki %s.',
	'DEFAULT_PREFIX_IS'			=> 'Dönüştürücü, belirtilen önek ile tabloları bulamadı. Lütfen dönüştüreceğiniz mesaj panosundan mesaj panosu için doğru bilgileri girdiğinize emin olun. %1$s için varsayılan tablo öneki <strong>%2$s</strong>.',
	'SPECIFY_OPTIONS'			=> 'Dönüştürme seçeneklerini belirle',
	'FORUM_PATH'				=> 'Mesaj panosu yolu',
	'FORUM_PATH_EXPLAIN'		=> 'Bu, <strong>şu anki phpBB kurulumunuzun ana dizininden</strong> önceki mesaj panonuza giden diskteki <strong>bağıl</strong> yoldur.',
	'REFRESH_PAGE'				=> 'Dönüştürmeye devam etmek için sayfayı yenile',
 	'REFRESH_PAGE_EXPLAIN'		=> 'Eğer evet olarak ayarlanırsa, dönüştürmeye devam etmek için bir adım tamamlandıktan sonra dönüştürücü sayfayı yenileyecektir. Eğer bu, önceden meydana gelen herhangi bir hatayı saptamak için ve test amaçlı ilk kez yapacağınız bir dönüştürme ise, bunu Hayır olarak ayarlamanızı öneririz.',

	// Conversion
	'STAGE_IN_PROGRESS'			=> 'Dönüştürme işlemi devam ediyor',

	'AUTHOR_NOTES'				=> 'Yapımcı notları<br />» %s',
	'STARTING_CONVERT'			=> 'Dönüştürme işlemine başlanıyor',
	'CONFIG_CONVERT'			=> 'Yapılandırma dönüştürülüyor',
	'DONE'						=> 'Bitti',
	'PREPROCESS_STEP'			=> 'Fonksiyonların/sorguların ön-işlemi yapılıyor',
	'FILLING_TABLE'				=> 'Doldurulan tablo <strong>%s</strong>',
	'FILLING_TABLES'			=> 'Tablolar dolduruluyor',
	'DB_ERR_INSERT'				=> '<code>INSERT</code> sorgusu işlemi sırasında hata oluştu.',
	'DB_ERR_LAST'				=> '<var>query_last</var> işlemi sırasında hata oluştu.',
	'DB_ERR_QUERY_FIRST'		=> '<var>query_first</var> uygulaması sırasında hata oluştu.',
	'DB_ERR_QUERY_FIRST_TABLE'	=> '<var>query_first</var> uygulaması sırasında hata oluştu, %s (“%s”).',
	'DB_ERR_SELECT'				=> '<code>SELECT</code> sorgusu çalıştırılması sırasında hata oluştu.',
	'STEP_PERCENT_COMPLETED'	=> 'Adım <strong>%d</strong> / <strong>%d</strong>',
	'FINAL_STEP'				=> 'Final adımı işlemi',
	'SYNC_FORUMS'				=> 'Forumların eşleştirilmesine başlanıyor',
	'SYNC_POST_COUNT'         	=> 'post_counts senkronize ediliyor',
	'SYNC_POST_COUNT_ID'      	=> 'post_counts %1$s <var>girdisinden</var> %2$s <var>girdisine</var> senkronize ediliyor.',
	'SYNC_TOPICS'				=> 'Başlıkların eşleştirilmesine başlanıyor',
	'SYNC_TOPIC_ID'				=> '%1$s <var>topic_id</var> değerinden %2$s değerine başlıklar senkronize ediliyor.',
	'PROCESS_LAST'					=> 'Son komutlar işleniyor',
	'UPDATE_TOPICS_POSTED'   	=> 'Başlıklara gönderilen mesaj bilgileri oluşturuluyor',
	'UPDATE_TOPICS_POSTED_ERR'  => 'Başlıklara gönderilen mesaj bilgileri oluşturulurken bir hata meydana geldi. Dönüştürme işlemi tamamlandıktan sonra YKP içerisinden bu adımı tekrar deneyebilirsiniz.',
	'CONTINUE_LAST'				=> 'Son komutlara devam et',
	'CLEAN_VERIFY'				=> 'Final yapısı temizleniyor ve doğrulanıyor',
	'NOT_UNDERSTAND'			=> '%s #%d anlaşılmıyor, tablo %s (“%s”)',
	'NAMING_CONFLICT'			=> 'Adlandırma uyuşmazlığı: %s ve %s farklı adlar<br /><br />%s',

	// Finish conversion
	'CONVERT_COMPLETE'			=> 'Dönüştürme tamamlandı',
	'CONVERT_COMPLETE_EXPLAIN'	=> 'Mesaj panonuzu phpBB 3.3’e başarıyla dönüştürdünüz. Şimdi giriş yapabilir ve <a href="../">mesaj panonuza erişebilirsiniz</a>. Lütfen install dizinini silerek mesaj panonuzu aktif etmeden önce, ayarların doğru şekilde transfer edildiğine emin olun. Unutmayın, phpBB kullanımı için çevrimiçi yardım <a href="https://www.phpbb.com/support/docs/en/3.3/ug/">Dokümantasyon</a> ve <a href="https://www.phpbb.com/community/viewforum.php?f=661">destek forumlarında</a> mevcuttur.',

	'COLLIDING_CLEAN_USERNAME'			=> '<strong>%s</strong> şunun için temiz kullanıcı adıdır:',
	'COLLIDING_USER'					=> '» kullanıcı id numarası: <strong>%d</strong> kullanıcı adı: <strong>%s</strong> (%d posts)',
	'COLLIDING_USERNAMES_FOUND'			=> 'Eski mesaj panonuzda çakışan kullanıcı adları bulundu. Dönüştürmeyi tamamlamak için lütfen bu kullanıcıları silin veya yeniden adlandırın; böylece eski mesaj panonuzda her temiz kullanıcı adı için yalnızca bir kullanıcı olacaktır.',
	'CONV_ERR_FATAL'					=> 'Önemli dönüştürme hatası',
	'CONV_ERROR_ATTACH_FTP_DIR'			=> 'Eski mesaj panonuzda dosya ekleri için FTP yüklemesi açık. Lütfen FTP yükleme seçeneğini kapatın ve geçerli bir dizin belirlendiğine emin olun, daha sonra tüm dosya eki dosyalarını bu yeni web erişilebilirliği olan dizine kopyalayın. Bunu yaptıktan sonra, dönüştürücüyü yeniden başlatın.',
	'CONV_ERROR_CONFIG_EMPTY'			=> 'Dönüştürme için hiç bir konfigürasyon bilgisi mevcut değil.',
	'CONV_ERROR_FORUM_ACCESS'			=> 'Forum erişim bilgilerine ulaşılamıyor.',
	'CONV_ERROR_GET_CATEGORIES'			=> 'Kategorilere ulaşılamıyor.',
	'CONV_ERROR_GET_CONFIG'				=> 'Mesaj panosu ayarlarınıza erişilemiyor.',
	'CONV_ERROR_COULD_NOT_READ'			=> '“%s” dosyasına erişilemiyor/okunamıyor.',
	'CONV_ERROR_GROUP_ACCESS'			=> 'Grup doğrulama bilgilerine ulaşılamıyor.',
	'CONV_ERROR_INCONSISTENT_GROUPS'	=> 'add_bots() içerisindeki gruplar tablosunda uyuşmazlık tespit edildi - eğer bunu elle yaptıysanız tüm özel grupları eklemelisiniz.',
	'CONV_ERROR_INSERT_BOT'				=> 'Kullanıcılar tablosuna bot eklenemiyor.',
	'CONV_ERROR_INSERT_BOTGROUP'		=> 'Botlar tablosuna bot eklenemiyor.',
	'CONV_ERROR_INSERT_USER_GROUP'		=> 'user_group tablosuna kullanıcı eklenemiyor.',
	'CONV_ERROR_MESSAGE_PARSER'			=> 'Mesaj ayrıştırıcı hatası',
	'CONV_ERROR_NO_AVATAR_PATH'			=> 'Geliştirici için not: %s kullanmak için $convertor[\'avatar_path\'] belirlemelisiniz.',
	'CONV_ERROR_NO_FORUM_PATH'			=> 'Kaynak mesaj panosuna bağlı yol belirlenmedi.',
	'CONV_ERROR_NO_GALLERY_PATH'		=> 'Geliştirici için not: %s kullanmak için $convertor[\'avatar_gallery_path\'] belirlemelisiniz.',
	'CONV_ERROR_NO_GROUP'				=> '%2$s içerisinde “%1$s” grubu bulunamadı.',
	'CONV_ERROR_NO_RANKS_PATH'			=> 'Geliştirici için not: %s kullanmak için $convertor[\'ranks_path\'] belirlemelisiniz.',
	'CONV_ERROR_NO_SMILIES_PATH'		=> 'Geliştirici için not: %s kullanmak için $convertor[\'smilies_path\'] belirlemelisiniz.',
	'CONV_ERROR_NO_UPLOAD_DIR'			=> 'Geliştirici için not: %s kullanmak için $convertor[\'upload_path\'] belirlemelisiniz.',
	'CONV_ERROR_PERM_SETTING'			=> 'İzin ayarı eklenemiyor/güncellenemiyor.',
	'CONV_ERROR_PM_COUNT'				=> 'Özel mesaj sayaç klasörü seçilemiyor.',
	'CONV_ERROR_REPLACE_CATEGORY'		=> 'Eski kategori yerine yeni forum eklenemiyor.',
	'CONV_ERROR_REPLACE_FORUM'			=> 'Eski forum yerine yeni forum eklenemiyor.',
	'CONV_ERROR_USER_ACCESS'			=> 'Kullanıcı doğrulama bilgilerine ulaşılamıyor.',
	'CONV_ERROR_WRONG_GROUP'			=> '%2$s içerisinde tanımlanan “%1$s” grubu yanlış.', 
	'CONV_OPTIONS_BODY'            		=> 'Bu sayfa kaynak mesaj panosuna erişim için istenilen verileri toplar. Önceki mesaj panonuzun veritabanı bilgilerini girin; dönüştürücü, altta verilen veritabanı içerisinde herhangi bir şeyi değiştirmeyecektir. Tutarlı bir dönüştürme izni için kaynak mesaj panosu kapalı olmalıdır.',
	'CONV_SAVED_MESSAGES'         		=> 'Kaydedilen mesajlar',

	'PRE_CONVERT_COMPLETE'			=> 'Tüm ön-dönüştürme adımları başarıyla tamamlandı. Şimdi gerçek dönüştürme işlemine başlayabilirsiniz. Not: Yapılması gerekli diğer şeyleri elle ayarlayabilirsiniz. Dönüştürmeden sonra, özellikle tanımlı izinleri kontrol edin, eğer arama indeksiniz dönüştürülmediyse yeniden oluşturun ve ayrıca dosyaların (örneğin, avatarlar ve ifadeler) doğru şekilde kopyalandığına emin olun.',
));
