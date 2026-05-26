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
	$lang = [];
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

$lang = array_merge($lang, [
	'ACP_STYLES_EXPLAIN'						=> 'Buradan mesaj panonuzda mevcut olan stilleri yönetebilirsiniz.<br>Not: phpBB’nin varsayılan ve birincil ana stili olduğundan “<strong>prosilver</strong>” stilini kaldıramaz veya silemezsiniz.',

	'CANNOT_BE_INSTALLED'						=> 'Kurulum yapılamadı',
	'CONFIRM_UNINSTALL_STYLES'					=> 'Seçilen stillerin kurulumlarını kaldırmak istediğinize emin misiniz?',
	'COPYRIGHT'									=> 'Telif hakkı',

	'DEACTIVATE_DEFAULT'						=> 'Varsayılan stili deaktif edemezsiniz.',
	'DELETE_FROM_FS'							=> 'Dosya sisteminden sil',
	'DELETE_STYLE_FILES_FAILED'					=> '"%s" stili için dosyalar silinirken hata oluştu.',
	'DELETE_STYLE_FILES_SUCCESS'				=> '"%s" stili için dosyalar silindi.',
	'DETAILS'									=> 'Detaylar',

	'INHERITING_FROM'							=> 'Şuradan kalıt al',
	'INSTALL_STYLE'								=> 'Stili kur',
	'INSTALL_STYLES'							=> 'Stilleri kur',
	'INSTALL_STYLES_EXPLAIN'					=> 'Buradan yeni stiller kurabilirsiniz.<br>Eğer aşağıdaki listede belirli bir stili bulamazsanız, stilin zaten kurulmuş olup olmadığını kontrol edin. Eğer stil kurulmamışsa, stil dosyalarının düzgün olarak yüklenip yüklenmediğini kontrol edin.',
	'INVALID_STYLE_ID'							=> 'Geçersiz stil ID numarası.',

	'NO_MATCHING_STYLES_FOUND'					=> 'Sorgunuza uyan bir stil bulunamadı.',
	'NO_UNINSTALLED_STYLE'						=> 'Hiç bir kaldırılmış stil tespit edilmedi.',

	'PURGED_CACHE'								=> 'Önbellek temizlendi.',	

	'REQUIRES_STYLE'							=> 'Bu stil, "%s" stilinin kurulu olmasını gerektiriyor.',	

	'STYLE_ACTIVATE'							=> 'Aktifleştir',
	'STYLE_ACTIVE'								=> 'Aktif',
	'STYLE_DEACTIVATE'							=> 'Deaktifleştir',
	'STYLE_DEFAULT'								=> 'Varsayılan stil yap',
	'STYLE_DEFAULT_CHANGE_INACTIVE'				=> 'Stili varsayılan stil yapmadan önce aktifleştirmelisiniz.',
	'STYLE_ERR_INVALID_PARENT'					=> 'Geçersiz ana stil.',
	'STYLE_ERR_NAME_EXIST'						=> 'Bu adı kullanan bir stil zaten mevcut.',
	'STYLE_ERR_STYLE_NAME'						=> 'Bu stil için bir ad belirtmelisiniz.',
	'STYLE_INSTALLED'							=> '"%s" stili kuruldu.',	
	'STYLE_INSTALLED_RETURN_INSTALLED_STYLES'	=> 'Kurulmuş stiller listesine dön',
	'STYLE_INSTALLED_RETURN_UNINSTALLED_STYLES'	=> 'Daha fazla stil kur',
	'STYLE_NAME'								=> 'Stil adı',
	'STYLE_NAME_RESERVED'						=> '"%s" stili, stil adı daha önceden rezerve edildiği için kurulamadı.',
	'STYLE_NOT_INSTALLED'						=> '"%s" stili kurulu değil.',
	'STYLE_PATH'								=> 'Stil yolu',
	'STYLE_UNINSTALL'							=> 'Kurulumu kaldır',
	'STYLE_UNINSTALL_DEPENDENT'					=> '"%s" stili kaldırılamıyor çünkü bu stil bir ya da daha fazla alt stillere sahip.',
	'STYLE_UNINSTALLED'							=> '"%s" stili başarıyla kaldırıldı.',
	'STYLE_PHPBB_VERSION'						=> 'phpBB Sürümü',
	'STYLE_USED_BY'								=> 'Kullananlar (robotlar dahil)',
	'STYLE_VERSION'								=> 'Stil sürümü',	

	'UNINSTALL_PROSILVER'						=> '“prosilver” stilini kaldıramazsınız.',
	'UNINSTALL_DEFAULT'							=> 'Varsayılan stili kaldıramazsınız.',	

	'BROWSE_STYLES_DATABASE'					=> 'Stil veritabanına göz at',
]);
