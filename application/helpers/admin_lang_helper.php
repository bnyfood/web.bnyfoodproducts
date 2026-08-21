<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin UI language (Thai / English).
 * Cookie + session; used across entire backend chrome and bilingual fields.
 */
if ( ! function_exists('admin_lang'))
{
	function admin_lang()
	{
		$CI =& get_instance();
		$lang = $CI->session->userdata(SESSION_PREFIX.'admin_lang');
		if ($lang !== 'th' && $lang !== 'en') {
			$cookie = isset($_COOKIE['bny_admin_lang']) ? strtolower((string)$_COOKIE['bny_admin_lang']) : '';
			$lang = ($cookie === 'th' || $cookie === 'en') ? $cookie : 'th';
			$CI->session->set_userdata(SESSION_PREFIX.'admin_lang', $lang);
		}
		return $lang;
	}
}

if ( ! function_exists('set_admin_lang'))
{
	function set_admin_lang($lang)
	{
		$lang = ($lang === 'en') ? 'en' : 'th';
		$CI =& get_instance();
		$CI->session->set_userdata(SESSION_PREFIX.'admin_lang', $lang);
		setcookie('bny_admin_lang', $lang, time() + 365 * 24 * 60 * 60, '/');
		$_COOKIE['bny_admin_lang'] = $lang;
		return $lang;
	}
}

if ( ! function_exists('alang'))
{
	/**
	 * Translate UI string key from language files (admin_lang.php).
	 */
	function alang($line, $default = null)
	{
		$CI =& get_instance();
		$CI->lang->load('admin', ($CI->config->item('language') ?: 'thai'));
		$t = $CI->lang->line($line, false);
		if ($t === false || $t === null || $t === '') {
			return ($default !== null) ? $default : $line;
		}
		return $t;
	}
}

if ( ! function_exists('menu_label'))
{
	/**
	 * Pick menu display name by current admin language.
	 * @param array $row menu row with menu_name / menu_name_en
	 */
	function menu_label($row)
	{
		if (!is_array($row)) {
			return '';
		}
		$th = isset($row['menu_name']) ? trim((string)$row['menu_name']) : '';
		$en = isset($row['menu_name_en']) ? trim((string)$row['menu_name_en']) : '';
		if (admin_lang() === 'en') {
			$label = ($en !== '') ? $en : $th;
		} else {
			$label = ($th !== '') ? $th : $en;
		}
		$id = isset($row['menu_id']) ? (int)$row['menu_id'] : 0;
		if (defined('MENU_SALES_CHAT') && $id === MENU_SALES_CHAT) {
			return (admin_lang() === 'en') ? 'Chat' : 'แชท';
		}
		if (defined('MENU_ALERTS') && $id === MENU_ALERTS) {
			return (admin_lang() === 'en') ? 'Alerts' : 'อะเลิร์ท';
		}
		if (defined('MENU_ALERTS_TOKEN') && $id === MENU_ALERTS_TOKEN) {
			return 'Token expire';
		}
		if (defined('MENU_ALERTS_STATUS') && $id === MENU_ALERTS_STATUS) {
			return 'Status change';
		}
		return $label;
	}
}

if ( ! function_exists('menu_chat_badge'))
{
	function menu_chat_badge($menu_id)
	{
		$id = (int)$menu_id;
		if (defined('MENU_SALES') && $id === MENU_SALES) {
			return ' <span class="chat-unreplied-badge" data-chat-badge="all"></span>';
		}
		if (defined('MENU_SALES_CHAT') && $id === MENU_SALES_CHAT) {
			return ' <span class="chat-unreplied-badge" data-chat-badge="chat"></span>';
		}
		return '';
	}
}

if ( ! function_exists('menu_alert_badge'))
{
	function menu_alert_badge($menu_id)
	{
		$id = (int)$menu_id;
		if (defined('MENU_ALERTS') && $id === MENU_ALERTS) {
			return ' <span class="chat-unreplied-badge" data-alert-badge="topics"></span>';
		}
		if (defined('MENU_ALERTS_TOKEN') && $id === MENU_ALERTS_TOKEN) {
			return ' <span class="chat-unreplied-badge" data-alert-badge="token"></span>';
		}
		if (defined('MENU_ALERTS_STATUS') && $id === MENU_ALERTS_STATUS) {
			return ' <span class="chat-unreplied-badge" data-alert-badge="status"></span>';
		}
		return '';
	}
}

if ( ! function_exists('menu_has_link'))
{
	function menu_has_link($row)
	{
		if (!is_array($row)) {
			return false;
		}
		$link = isset($row['link']) ? trim((string)$row['link']) : '';
		return ($link !== '' && $link !== '#' && $link !== '#!');
	}
}

if ( ! function_exists('menu_trail'))
{
	/**
	 * Path from root menu to the current menu_id (lv1 > lv2 > lv3).
	 */
	function menu_trail($menus, $menu_id_ref)
	{
		$trail = array();
		if (empty($menus) || !is_array($menus) || $menu_id_ref === null || $menu_id_ref === '') {
			return $trail;
		}
		foreach ($menus as $lv1) {
			if (!is_array($lv1)) {
				continue;
			}
			if (isset($lv1['menu_id']) && (string)$lv1['menu_id'] === (string)$menu_id_ref) {
				return array($lv1);
			}
			if (empty($lv1['submenus']) || !is_array($lv1['submenus'])) {
				continue;
			}
			foreach ($lv1['submenus'] as $lv2) {
				if (!is_array($lv2)) {
					continue;
				}
				if (isset($lv2['menu_id']) && (string)$lv2['menu_id'] === (string)$menu_id_ref) {
					return array($lv1, $lv2);
				}
				if (empty($lv2['lv3_submenus']) || !is_array($lv2['lv3_submenus'])) {
					continue;
				}
				foreach ($lv2['lv3_submenus'] as $lv3) {
					if (!is_array($lv3)) {
						continue;
					}
					if (isset($lv3['menu_id']) && (string)$lv3['menu_id'] === (string)$menu_id_ref) {
						return array($lv1, $lv2, $lv3);
					}
				}
			}
		}
		return $trail;
	}
}

if ( ! function_exists('bilingual'))
{
	/**
	 * Pick bilingual master-data field (e.g. name_th / name_en, Title_th / Title_en).
	 * Falls back to base field name if present (legacy single column).
	 */
	function bilingual($row, $base_field)
	{
		if (!is_array($row)) {
			return '';
		}
		$th_key = $base_field.'_th';
		$en_key = $base_field.'_en';
		$th = isset($row[$th_key]) ? trim((string)$row[$th_key]) : '';
		$en = isset($row[$en_key]) ? trim((string)$row[$en_key]) : '';
		$base = isset($row[$base_field]) ? trim((string)$row[$base_field]) : '';

		if (admin_lang() === 'en') {
			if ($en !== '') return $en;
			if ($base !== '') return $base;
			return $th;
		}
		if ($th !== '') return $th;
		if ($base !== '') return $base;
		return $en;
	}
}
