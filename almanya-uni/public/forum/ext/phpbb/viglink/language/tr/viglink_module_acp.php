<?php
/**
 *
 * VigLink extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//
$lang = array_merge($lang, array(
	'ACP_VIGLINK_SETTINGS'			=> 'VigLink ayarları',
	'ACP_VIGLINK_SETTINGS_EXPLAIN'	=> 'VigLink, kullanıcı deneyiminde herhangi bir değişiklik yapmadan forumunuzun kullanıcıları tarafından gönderilen bağlantılardan birbirinden ayrı bir şekilde para kazandıran üçüncü parti bir hizmettir. Kullanıcılar giden bağlantılardaki ürünlere ya da hizmetlere tıkladığında veya bir şey satın aldığında, satıcılar ya da tüccarlar VigLink’e bir komisyon öder ve bu komisyonun bir kısmı phpBB projesine bağışlanır. VigLink’i etkinleştirmeyi seçerek ve phpBB projesine gelirleri bağışlayarak, açık kaynak organizasyonumunuzu destekleyebilir ve finansal güvenliğimizin devam etmesini sağlayabilirsiniz.',
	'ACP_VIGLINK_SETTINGS_CHANGE'	=> 'Bu ayarları istediğiniz zaman “<a href="%1$s">VigLink ayarları</a>” panelinden değiştirebilirsiniz.',
	'ACP_VIGLINK_SUPPORT_EXPLAIN'	=> 'Gönder butonuna tıklayarak alttaki tercih ettiğiniz ayarları gönderdikten sonra bu sayfaya artık yönlendirilmeyeceksiniz.',	
	'ACP_VIGLINK_ENABLE'			=> 'VigLink’i etkinleştir',
	'ACP_VIGLINK_ENABLE_EXPLAIN'	=> 'VigLink hizmetlerinin kullanımını etkinleştirir.',
	'ACP_VIGLINK_EARNINGS'			=> 'Kendi kazançlarınızı talep edin (isteğe bağlı)',
	'ACP_VIGLINK_EARNINGS_EXPLAIN'  => 'VigLink Convert hesabına kaydolarak kendi kazançlarınızı talep edebilirsiniz.',
	'ACP_VIGLINK_DISABLED_PHPBB'	=> 'VigLink hizmetleri phpBB tarafından devre dışı bırakıldı.',
	'ACP_VIGLINK_CLAIM'				=> 'Kazançlarınızı talep edin',
	'ACP_VIGLINK_CLAIM_EXPLAIN'		=> 'VigLink para kazandıran bağlantılardan forumunuzun kazançlarını phpBB projesine bağışlamak yerine kendinize talep edebilirsiniz. Hesap ayarlarınızı yönetmek için, “Convert account” bağlantısına tıklayarak “VigLink Convert” hesabına kaydolun.',
	'ACP_VIGLINK_CONVERT_ACCOUNT'	=> 'Convert hesabı',
	'ACP_VIGLINK_NO_CONVERT_LINK'	=> 'VigLink convert hesabı bağlantısı alınamadı.',
));
