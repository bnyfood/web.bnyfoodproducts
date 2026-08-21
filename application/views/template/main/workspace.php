<?php
$ws_breadcrumb = isset($ws_breadcrumb) && is_array($ws_breadcrumb) ? $ws_breadcrumb : array();
$ws_lv1 = !empty($ws_breadcrumb[0]) ? $ws_breadcrumb[0] : array();
$ws_current = !empty($ws_breadcrumb) ? $ws_breadcrumb[count($ws_breadcrumb) - 1] : array();
$ws_crumb_path = array();
foreach ($ws_breadcrumb as $ws_crumb) {
	$ws_crumb_label = menu_label($ws_crumb);
	if ($ws_crumb_label !== '') {
		$ws_crumb_path[] = $ws_crumb_label;
	}
}
$ws_ui_lang = admin_lang();
$ws_cfg = array(
	'base' => base_url(),
	'pageUrl' => current_url(),
	'wsId' => isset($ws_lv1['menu_id']) ? (string)$ws_lv1['menu_id'] : '',
	'wsName' => menu_label($ws_lv1),
	'menuId' => isset($menu_id_ref) ? (string)$menu_id_ref : '',
	'menuTitle' => menu_label($ws_current),
	'crumbPath' => $ws_crumb_path,
	'lang' => $ws_ui_lang,
	'i18n' => ($ws_ui_lang === 'en')
		? array(
			'close_tab' => 'Close tab',
			'close_all_tabs' => 'Close all tabs',
			'docs_in' => 'Documents in',
			'docs_this_page' => 'This page'
		)
		: array(
			'close_tab' => 'ปิดแถบ',
			'close_all_tabs' => 'ปิดทุกแถบ',
			'docs_in' => 'เอกสารใน',
			'docs_this_page' => 'หน้านี้'
		)
);
?>
<div class="bny-ws" id="bny_workspace">
  <div class="bny-ws-other" id="bny_ws_other" aria-label="open documents from other menus">
    <div class="bny-ws-level1" id="bny_ws_level1"></div>
    <div class="bny-doc-tabs bny-ws-other-docs" id="bny_ws_other_docs"></div>
  </div>
  <?php if (!empty($ws_breadcrumb)) { ?>
  <nav class="bny-ws-crumb" aria-label="breadcrumb">
    <?php foreach ($ws_breadcrumb as $i => $crumb) {
      $label = htmlspecialchars(menu_label($crumb), ENT_QUOTES, 'UTF-8');
      $is_last = ($i === count($ws_breadcrumb) - 1);
      if ($i > 0) { echo '<span class="bny-ws-crumb-sep">›</span>'; }
      if (!$is_last && menu_has_link($crumb)) { ?>
        <a class="bny-ws-crumb-link" href="<?php echo base_url().$crumb['link']; ?>"><?php echo $label; ?></a>
      <?php } else { ?>
        <span class="bny-ws-crumb-item<?php echo $is_last ? ' is-current' : ''; ?>"><?php echo $label; ?></span>
      <?php }
    } ?>
  </nav>
  <?php } ?>
  <div class="bny-ws-page">
<script type="application/json" id="bny_ws_cfg"><?php echo json_encode($ws_cfg); ?></script>
