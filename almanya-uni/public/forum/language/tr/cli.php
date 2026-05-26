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

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'CLI_APCU_CACHE_NOTICE'				=> 'APCu önbelleğinin Yönetim Kontrol Paneli aracılığıyla temizlenmesi gerekir.',

	'CLI_CONFIG_CANNOT_CACHED'			=> 'Verimli bir önbellek olması için yapılandırma seçeneği değişikliklerinin çok sık olmasını isterseniz bu seçeneği ayarlayın.',
	'CLI_CONFIG_CURRENT'				=> 'Şu anki yapılandırma değeri, belirli boolean değerleri için 0 ve 1 kullanın',
	'CLI_CONFIG_DELETE_SUCCESS'			=> '%s yapılandırması başarıyla silindi.',
	'CLI_CONFIG_NEW'					=> 'Yeni yapılandırma değeri, belirli boolean değerleri için 0 ve 1 kullanın',
	'CLI_CONFIG_NOT_EXISTS'				=> '%s yapılandırması mevcut değil',
	'CLI_CONFIG_OPTION_NAME'			=> 'Yapılandırma seçeneği’nin adı',
	'CLI_CONFIG_PRINT_WITHOUT_NEWLINE'	=> 'Eğer değer en sonda yeni bir satır olmadan yazdırılmış olması gerekiyorsa bu seçeneği ayarlayın.',
	'CLI_CONFIG_INCREMENT_BY'			=> 'Arttırılacak miktar',
	'CLI_CONFIG_INCREMENT_SUCCESS'		=> '%s yapılandırması başarıyla arttırıldı',
	'CLI_CONFIG_SET_FAILURE'			=> '%s yapılandırması ayarlanamamıyor',
	'CLI_CONFIG_SET_SUCCESS'			=> '%s yapılandırması başarıyla ayarlandı',

	'CLI_DESCRIPTION_CRON_LIST'					=> 'Hazır ve hazır olmayan kron işlerinin bir listesini yazdırır.',
	'CLI_DESCRIPTION_CRON_RUN'					=> 'Tüm hazır kron görevlerini çalıştırır.',
	'CLI_DESCRIPTION_CRON_RUN_ARGUMENT_1'		=> 'Çalıştırılacak görevin adı',
	'CLI_DESCRIPTION_DB_LIST'					=> 'Kurulmuş ve mevcut tüm migrasyonları listele.',	
	'CLI_DESCRIPTION_DB_MIGRATE'				=> 'Migrasyonları uygulayarak veritabanını günceller.',
	'CLI_DESCRIPTION_DB_REVERT'					=> 'Bir migrasyona geri dön.',	
	'CLI_DESCRIPTION_DELETE_CONFIG'				=> 'Bir yapılandırma seçeneğini siler',
	'CLI_DESCRIPTION_DISABLE_EXTENSION'			=> 'Belirtilen eklentiyi devre dışı bırakır.',
	'CLI_DESCRIPTION_ENABLE_EXTENSION'			=> 'Belirtilen eklentiyi etkinleştirir.',
	'CLI_DESCRIPTION_FIND_MIGRATIONS'			=> 'Bağlı olmayan migrasyonları bulur.',
	'CLI_DESCRIPTION_FIX_LEFT_RIGHT_IDS'		=> 'Forumların ve modüllerin ağaç yapısını onarır.',	
	'CLI_DESCRIPTION_GET_CONFIG'				=> 'Bir yapılandırma seçeneğinin değerini alır',
	'CLI_DESCRIPTION_INCREMENT_CONFIG'			=> 'Bir yapılandırma seçeneğinin tamsayı değerini arttırır',
	'CLI_DESCRIPTION_LIST_EXTENSIONS'			=> 'Dosya sistemi üzerinde ve veritabanı içerisindeki tüm eklentileri listeler.',

	'CLI_DESCRIPTION_OPTION_ENV'				=> 'Ortam adı.',
	'CLI_DESCRIPTION_OPTION_SAFE_MODE'			=> 'Güvenli Mod’da çalıştır (eklentiler olmadan).',
	'CLI_DESCRIPTION_OPTION_SHELL'				=> 'Shell’i başlat.',

	'CLI_DESCRIPTION_PURGE_EXTENSION'			=> 'Belirtilen eklentiyi temizler.',

	'CLI_DESCRIPTION_REPARSER_LIST'						=> 'Yeniden ayrıştırma olabilmesi için metin türlerini listeler.',
	'CLI_DESCRIPTION_REPARSER_AVAILABLE'				=> 'Kullanılabilir yeniden ayrıştırıcılar:',
	'CLI_DESCRIPTION_REPARSER_REPARSE'					=> 'Geçerli text_formatter servisi ile depolanan metni yeniden ayrıştırır.',
	'CLI_DESCRIPTION_REPARSER_REPARSE_ARG_1'			=> 'Yeniden ayrıştırma için metin türü. Her şeyi yeniden ayrıştırmak için boş bırakın.',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_DRY_RUN'		=> 'Herhangi bir değişikliği kaydetmeyin; sadece olacak şeyleri yazdırın',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_FORCE_BBCODE'	=> 'Tüm BBCodeları istisnasız yeniden ayrıştırın. Daha önce devre dışı bırakılan BBCodeların yeniden işleneceğini, etkinleştirileceğini ve tamamının oluşturulacağını unutmayın.',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RANGE_MIN'	=> 'İşlem için en düşük kayıt ID numarası',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RANGE_MAX'	=> 'İşlem için en yüksek kayıt ID numarası',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RANGE_SIZE'	=> 'Bir kerede işlemek için yaklaşık kayıt sayısı',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RESUME'		=> 'Son uygulamanın durdurulduğu yerden yeniden ayrıştırmaya başla',	

	'CLI_DESCRIPTION_SET_ATOMIC_CONFIG'			=> 'Eğer sadece eski eşleşen değer geçerliyse bir yapılandırma seçeneğinin değerini ayarlar',
	'CLI_DESCRIPTION_SET_CONFIG'				=> 'Bir yapılandırma seçeneğinin değerini ayarlar',

	'CLI_DESCRIPTION_THUMBNAIL_DELETE'		=> 'Varolan tüm küçük resimleri sil.',
	'CLI_DESCRIPTION_THUMBNAIL_GENERATE'	=> 'Eksik olan tüm küçük resimleri oluştur.',
	'CLI_DESCRIPTION_THUMBNAIL_RECREATE'	=> 'Tüm küçük resimleri yeniden oluştur.',

	'CLI_DESCRIPTION_UPDATE_CHECK'					=> 'Mesaj panosunun güncel olup olmadığını kontrol edin',
	'CLI_DESCRIPTION_UPDATE_CHECK_ARGUMENT_1'		=> 'Kontrol edilecek eklentinin adı (eğer hepsini kontrol etmek istiyorsanız, tüm eklentileri seçin)',
	'CLI_DESCRIPTION_UPDATE_CHECK_OPTION_CACHE'		=> 'Kontrol komutunu önbellekle çalıştır.',
	'CLI_DESCRIPTION_UPDATE_CHECK_OPTION_STABILITY'	=> 'Sadece kararlı ya da kararlı olmayan sürümleri kontrol etmek için komutu çalıştır.',

	'CLI_DESCRIPTION_UPDATE_HASH_BCRYPT'		=> 'bcrypt ile hash olması için güncel olmayan şifre hashlerini günceller.',

	'CLI_ERROR_INVALID_STABILITY' => '"%s" "kararlı" ya da "kararlı olmayan" olarak ayarlanmalıdır.',
	
	'CLI_DESCRIPTION_USER_ACTIVATE'				=> 'Bir kullanıcı hesabını aktifleştir (ya da deaktif et).',
	'CLI_DESCRIPTION_USER_ACTIVATE_USERNAME'	=> 'Aktifleştirilecek hesabın kullanıcı adı.',
	'CLI_DESCRIPTION_USER_ACTIVATE_DEACTIVATE'	=> 'Kullanıcı’nın hesabını deaktif et',
	'CLI_DESCRIPTION_USER_ACTIVATE_ACTIVE'		=> 'Kullanıcı zaten aktif.',
	'CLI_DESCRIPTION_USER_ACTIVATE_INACTIVE'	=> 'Kullanıcı zaten aktif değil.',
	'CLI_DESCRIPTION_USER_ADD'					=> 'Yeni bir kullanıcı ekle.',
	'CLI_DESCRIPTION_USER_ADD_OPTION_USERNAME'	=> 'Yeni kullanıcının kullanıcı adı',
	'CLI_DESCRIPTION_USER_ADD_OPTION_PASSWORD'	=> 'Yeni kullanıcının şifresi',
	'CLI_DESCRIPTION_USER_ADD_OPTION_EMAIL'		=> 'Yeni kullanıcının e-posta adresi',
	'CLI_DESCRIPTION_USER_ADD_OPTION_NOTIFY'	=> 'Yeni kullanıcıya hesap aktivasyon e-postası gönder (varsayılan olarak gönderilmez)',
	'CLI_DESCRIPTION_USER_DELETE'				=> 'Bir kullanıcı hesabını sil.',
	'CLI_DESCRIPTION_USER_DELETE_USERNAME'		=> 'Silinecek kullanıcının kullanıcı adı',
	'CLI_DESCRIPTION_USER_DELETE_ID'			=> 'Kullanıcı hesaplarını ID numarasına göre sil.',
	'CLI_DESCRIPTION_USER_DELETE_ID_OPTION_ID'	=> 'Silinecek kullanıcıların kullanıcı ID numaraları',	
	'CLI_DESCRIPTION_USER_DELETE_OPTION_POSTS'	=> 'Bu kullanıcı tarafından gönderilen tüm mesajları sil. Bu seçenek seçilmezse, kullanıcı’nın mesajları tutulacaktır.',
	'CLI_DESCRIPTION_USER_RECLEAN'				=> 'Kullanıcı adlarını yeniden temizle.',

	'CLI_EXTENSION_DISABLE_FAILURE'		=> '%s eklentisi devre dışı bırakılamadı',
	'CLI_EXTENSION_DISABLE_SUCCESS'		=> '%s eklentisi başarıyla devre dışı bırakıldı',
	'CLI_EXTENSION_DISABLED'			=> '%s eklentisi etkin değil',	
	'CLI_EXTENSION_ENABLE_FAILURE'		=> '%s eklentisi etkinleştirilemedi',
	'CLI_EXTENSION_ENABLE_SUCCESS'		=> '%s eklentisi başarıyla etkinleştirildi',
	'CLI_EXTENSION_ENABLED'				=> '%s eklentisi zaten etkin',
	'CLI_EXTENSION_NOT_EXIST'			=> '%s eklentisi mevcut değil',	
	'CLI_EXTENSION_NAME'				=> 'Eklentinin adı',
	'CLI_EXTENSION_PURGE_FAILURE'		=> '%s eklentisi temizlenemedi',
	'CLI_EXTENSION_PURGE_SUCCESS'		=> '%s eklentisi başarıyla temizlendi',
	'CLI_EXTENSION_UPDATE_FAILURE'		=> '%s eklentisi güncellenemedi',
	'CLI_EXTENSION_UPDATE_SUCCESS'		=> '%s eklentisi başarıyla güncellendi',
	'CLI_EXTENSION_NOT_FOUND'			=> 'Hiç bir eklenti bulunamadı.',
	'CLI_EXTENSION_NOT_ENABLEABLE'		=> '%s eklentisi etkinleştirilebilir değil.',	
	'CLI_EXTENSIONS_AVAILABLE'			=> 'Mevcut',
	'CLI_EXTENSIONS_DISABLED'			=> 'Devre dışı bırakıldı',
	'CLI_EXTENSIONS_ENABLED'			=> 'Etkinleştirildi',

	'CLI_FIXUP_FIX_LEFT_RIGHT_IDS_SUCCESS'		=> 'Forumların ve modüllerin ağaç yapısı başarıyla onarıldı.',
	'CLI_FIXUP_UPDATE_HASH_BCRYPT_SUCCESS'		=> 'Güncel olmayan şifreler bcrypt ile hashlenerek başarıyla güncellendi.',

	'CLI_MIGRATION_NAME'					=> 'Ad alanı içeren migrasyon adı (sorunları önlemek için ters slaş (eğik çizgi) yerine düz slaş kullanın).',
	'CLI_MIGRATIONS_AVAILABLE'				=> 'Mevcut migrasyonlar',
	'CLI_MIGRATIONS_INSTALLED'				=> 'Kurulmuş migrasyonlar',
	'CLI_MIGRATIONS_ONLY_AVAILABLE'		    => 'Sadece mevcut migrasyonları göster',
	'CLI_MIGRATIONS_EMPTY'                  => 'Hiç bir migrasyon yok.',

	'CLI_REPARSER_REPARSE_REPARSING'		=> '%1$s yeniden ayrıştırılıyor (%2$d..%3$d aralığı)',
	'CLI_REPARSER_REPARSE_REPARSING_START'	=> '%s yeniden ayrıştırılıyor...',
	'CLI_REPARSER_REPARSE_SUCCESS'			=> 'Yeniden ayrıştırma başarıyla tamamlandı',

	// In all the case %1$s is the logical name of the file and %2$s the real name on the filesystem
	// eg: big_image.png (2_a51529ae7932008cf8454a95af84cacd) generated.
	'CLI_THUMBNAIL_DELETED'		=> '%1$s (%2$s) silindi.',
	'CLI_THUMBNAIL_DELETING'	=> 'Küçük resimler siliniyor',
	'CLI_THUMBNAIL_SKIPPED'		=> '%1$s (%2$s) atlandı.',
	'CLI_THUMBNAIL_GENERATED'	=> '%1$s (%2$s) oluşturuldu.',
	'CLI_THUMBNAIL_GENERATING'	=> 'Küçük resimler oluşturuluyor',
	'CLI_THUMBNAIL_GENERATING_DONE'	=> 'Tüm küçük resimler yeniden oluşturuldu.',
	'CLI_THUMBNAIL_DELETING_DONE'	=> 'Tüm küçük resimler silindi.',

	'CLI_THUMBNAIL_NOTHING_TO_GENERATE'	=> 'Oluşturulacak hiç bir küçük resim yok.',
	'CLI_THUMBNAIL_NOTHING_TO_DELETE'	=> 'Silinecek hiç bir küçük resim yok.',

	'CLI_USER_ADD_SUCCESS'			=> '%s kullanıcısı başarıyla eklendi.',
	'CLI_USER_DELETE_CONFIRM'		=> '‘%s’ kullanıcısını silmek istediğinize emin misiniz? [e/H]',
	'CLI_USER_DELETE_ID_CONFIRM'	=> '‘%s’ kullanıcı ID numarasını silmek istediğinize emin misiniz? [e/H]',
	'CLI_USER_DELETE_ID_SUCCESS'	=> 'Kullanıcı ID numaraları başarıyla silindi.',
	'CLI_USER_DELETE_ID_START'		=> 'ID numarasına göre kullanıcılar siliniyor',
	'CLI_USER_DELETE_NONE'			=> 'ID numarasına göre hiç bir kullanıcı silinemedi.',
	'CLI_USER_RECLEAN_START'		=> 'Kullanıcı adları yeniden temizleniyor',
	'CLI_USER_RECLEAN_DONE'			=> [
		0	=> 'Yeniden temizlenme tamamlandı. Temizlenmesi gereken herhangi bir kullanıcı yok.',
		1	=> 'Yeniden temizlenme tamamlandı. %d kullanıcı temizlendi.',
	],	
));

// Additional help for commands.
$lang = array_merge($lang, array(
	'CLI_HELP_CRON_RUN'			=> $lang['CLI_DESCRIPTION_CRON_RUN'] . ' İsteğe bağlı olarak sadece belirli cron görevini çalıştırmak için bir cron görev adı belirtebilirsiniz.',
	'CLI_HELP_USER_ACTIVATE'	=> 'Bir kullanıcı hesabını aktifleştirin, ya da bir hesabı <info>--deaktifleştir</info> seçeneğini kullanarak deaktifleştirin.
İsteğe bağlı olarak kullanıcıya bir aktivasyon e-postası göndermek için, <info>--send-email</info> seçeneğini kullanın.',
	'CLI_HELP_USER_ADD'			=> '<info>%command.name%</info> komutu yeni bir kullanıcı ekler:
Eğer bu komut seçenekler olmadan çalıştırılırsa, onları girmeniz istenecektir.
İsteğe bağlı olarak yeni kullanıcıya bir e-posta göndermek için, <info>--send-email</info> seçeneğini kullanın.',
	'CLI_HELP_USER_RECLEAN'		=> 'Kullanıcıları yeniden temizleme işlemiyle saklanan tüm kullanıcı adları kontrol edilecek ve ayrıca temiz sürümlerin saklandığına emin olunacaktır. Temizlenen kullanıcı adları büyük küçük harf kuralına uygunlaştırılır, NFC standartlarında normalleştirilir ve ASCII biçime dönüştürülür.',	
));
