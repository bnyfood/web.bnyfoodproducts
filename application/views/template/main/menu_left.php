<div class="dashboard-nav">
    <header>
        <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
        <a href="#" class="brand-logo"><img src="<?php echo base_url()?>resources/images/logo-bny.png" width="32px"><span>&nbsp BNY</span></a></header>
        <nav class="dashboard-nav-list">
          <?php if(!empty($arr_page_menus)){ ?>
              <?php foreach ($arr_page_menus as $arr_page_menu){ ?>
                  <?php
                      $icon_class = !empty($arr_page_menu['icon']) ? $arr_page_menu['icon'] : 'fas';
                      $has_submenus = !empty($arr_page_menu['submenus']);
                      $is_menu_active = (!empty($menu_id_ref) && $arr_page_menu['menu_id'] == $menu_id_ref);
                      $is_child_active = false;
                      if($has_submenus){
                          foreach ($arr_page_menu['submenus'] as $arr_page_submenu){
                              if(!empty($menu_id_ref) && $arr_page_submenu['menu_id'] == $menu_id_ref){
                                  $is_child_active = true;
                              }
                              if(!empty($arr_page_submenu['lv3_submenus'])){
                                  foreach ($arr_page_submenu['lv3_submenus'] as $arr_page_submenu_lv3){
                                      if(!empty($menu_id_ref) && $arr_page_submenu_lv3['menu_id'] == $menu_id_ref){
                                          $is_child_active = true;
                                      }
                                  }
                              }
                          }
                      }
                      $menu_link = !empty($arr_page_menu['link']) ? base_url().$arr_page_menu['link'] : '#';
                  ?>
                  <?php if($has_submenus){ ?>
                      <div class="dashboard-nav-dropdown <?php if($is_menu_active || $is_child_active){echo 'show';} ?>">
                          <a href="<?php echo $menu_link; ?>" class="dashboard-nav-item dashboard-nav-dropdown-toggle">
                                              <i class="<?php echo $icon_class; ?>"></i> <?php echo htmlspecialchars(menu_label($arr_page_menu), ENT_QUOTES, 'UTF-8'); ?><?php echo menu_chat_badge(isset($arr_page_menu['menu_id']) ? $arr_page_menu['menu_id'] : 0); ?><?php echo menu_alert_badge(isset($arr_page_menu['menu_id']) ? $arr_page_menu['menu_id'] : 0); ?>
                                          </a>
                                          <div class="dashboard-nav-dropdown-menu">
                                              <?php foreach ($arr_page_menu['submenus'] as $arr_page_submenu){ ?>
                                                  <?php
                                                      $has_lv3 = !empty($arr_page_submenu['lv3_submenus']);
                                                      $is_sub_active = (!empty($menu_id_ref) && $arr_page_submenu['menu_id'] == $menu_id_ref);
                                                      $is_lv3_active = false;
                                                      if($has_lv3){
                                                          foreach ($arr_page_submenu['lv3_submenus'] as $arr_page_submenu_lv3){
                                                              if(!empty($menu_id_ref) && $arr_page_submenu_lv3['menu_id'] == $menu_id_ref){
                                                                  $is_lv3_active = true;
                                                              }
                                                          }
                                                      }
                                                      $raw_sub_link = isset($arr_page_submenu['link']) ? trim((string)$arr_page_submenu['link']) : '';
                                                      $is_domains_item = isset($arr_page_submenu['menu_name'])
                                                          && stripos(trim($arr_page_submenu['menu_name']), 'Domain') !== false;
                                                      if ($is_domains_item) {
                                                          $submenu_link = base_url().'webs/domains/domains_list';
                                                      } elseif ($raw_sub_link === '' || $raw_sub_link === '#' || $raw_sub_link === '#!') {
                                                          $submenu_link = '#';
                                                      } else {
                                                          $submenu_link = base_url().$raw_sub_link;
                                                      }
                                                  ?>
                                                  <?php if($has_lv3){ ?>
                                                      <div class="dashboard-nav-dropdown <?php if($is_sub_active || $is_lv3_active){echo 'show';} ?>">
                                                          <a href="<?php echo $submenu_link; ?>" class="dashboard-nav-dropdown-item dashboard-nav-dropdown-toggle">
                                                              <?php echo htmlspecialchars(menu_label($arr_page_submenu), ENT_QUOTES, 'UTF-8'); ?><?php echo menu_chat_badge(isset($arr_page_submenu['menu_id']) ? $arr_page_submenu['menu_id'] : 0); ?><?php echo menu_alert_badge(isset($arr_page_submenu['menu_id']) ? $arr_page_submenu['menu_id'] : 0); ?>
                                                          </a>
                                                          <div class="dashboard-nav-dropdown-menu">
                                                              <?php foreach ($arr_page_submenu['lv3_submenus'] as $arr_page_submenu_lv3){ ?>
                                                                  <?php
                                                                      $raw_lv3 = isset($arr_page_submenu_lv3['link']) ? trim((string)$arr_page_submenu_lv3['link']) : '';
                                                                      $lv3_name = isset($arr_page_submenu_lv3['menu_name']) ? trim($arr_page_submenu_lv3['menu_name']) : '';
                                                                      if (strcasecmp($lv3_name, 'Category') === 0 && ($raw_lv3 === '' || $raw_lv3 === '#' || $raw_lv3 === '#!')) {
                                                                          $lv3_link = base_url().'webs/products/category/manage';
                                                                      } elseif ($raw_lv3 === '' || $raw_lv3 === '#' || $raw_lv3 === '#!') {
                                                                          $lv3_link = '#';
                                                                      } else {
                                                                          $lv3_link = base_url().$raw_lv3;
                                                                      }
                                                                  ?>
                                                                  <a href="<?php echo $lv3_link; ?>" class="dashboard-nav-dropdown-item <?php if(!empty($menu_id_ref) && $arr_page_submenu_lv3['menu_id'] == $menu_id_ref){echo 'active';} ?>" style="margin-left: 10px;">
                                                                      <?php echo htmlspecialchars(menu_label($arr_page_submenu_lv3), ENT_QUOTES, 'UTF-8'); ?>
                                                                  </a>
                                                              <?php } ?>
                                                          </div>
                                                      </div>
                                                  <?php }else{ ?>
                                                      <a href="<?php echo $submenu_link; ?>" class="dashboard-nav-dropdown-item <?php if($is_sub_active){echo 'active';} ?>">
                                                          <?php echo htmlspecialchars(menu_label($arr_page_submenu), ENT_QUOTES, 'UTF-8'); ?><?php echo menu_chat_badge(isset($arr_page_submenu['menu_id']) ? $arr_page_submenu['menu_id'] : 0); ?><?php echo menu_alert_badge(isset($arr_page_submenu['menu_id']) ? $arr_page_submenu['menu_id'] : 0); ?>
                                                      </a>
                                                  <?php } ?>
                                              <?php } ?>
                                          </div>
                                      </div>
                                  <?php }else{ ?>
                                      <a href="<?php echo $menu_link; ?>" class="dashboard-nav-item <?php if($is_menu_active){echo 'active';} ?>">
                                          <i class="<?php echo $icon_class; ?>"></i> <?php echo htmlspecialchars(menu_label($arr_page_menu), ENT_QUOTES, 'UTF-8'); ?><?php echo menu_chat_badge(isset($arr_page_menu['menu_id']) ? $arr_page_menu['menu_id'] : 0); ?><?php echo menu_alert_badge(isset($arr_page_menu['menu_id']) ? $arr_page_menu['menu_id'] : 0); ?>
                                      </a>
                                  <?php } ?>
                              <?php } ?>
                          <?php } ?>
          <div class="nav-item-divider"></div>
          <a href="<?php echo base_url();?>users/logout" class="dashboard-nav-item"><i class="fas fa-sign-out-alt"></i> <?php echo htmlspecialchars(alang('logout', 'Logout'), ENT_QUOTES, 'UTF-8'); ?> </a>
    </nav>
    </nav>
    <div class="nav-scroll-hint nav-scroll-hint-up" aria-hidden="true"><i class="fas fa-chevron-up"></i></div>
    <div class="nav-scroll-hint nav-scroll-hint-down" aria-hidden="true"><i class="fas fa-chevron-down"></i></div>
</div>
<div class='dashboard-app'>
<header class='dashboard-toolbar'>
  <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
  <?php
    $sess_remaining = 0;
    if (isset($this->auth_bl) && is_object($this->auth_bl)) {
      $sess_remaining = (int)$this->auth_bl->get_session_remaining();
    }
    if ($sess_remaining <= 0) {
      $expire_at = (int)$this->session->userdata(SESSION_PREFIX.'session_expire_at');
      if ($expire_at > 0) {
        $sess_remaining = max(0, $expire_at - time());
      }
    }
    $sess_email_hint = (string)$this->session->userdata(SESSION_PREFIX.'email');
    $cur_lang = function_exists('admin_lang') ? admin_lang() : 'th';
  ?>
  <div class="session-toolbar" id="session_toolbar" data-remaining="<?php echo (int)$sess_remaining; ?>">
    <span class="session-toolbar-label"><?php echo htmlspecialchars(alang('login', 'Login'), ENT_QUOTES, 'UTF-8'); ?></span>
    <span class="session-toolbar-sep">|</span>
    <span class="session-toolbar-time" id="session_countdown">--:--:--</span>
    <span class="session-toolbar-sep">|</span>
    <a href="#!" class="session-toolbar-refresh" id="session_refresh_btn" title="<?php echo htmlspecialchars(alang('extend_api_token', 'Extend API login token'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(alang('refresh_session', 'Refresh session'), ENT_QUOTES, 'UTF-8'); ?></a>
    <span class="session-toolbar-sep">|</span>
    <span class="session-toolbar-lang" title="<?php echo htmlspecialchars(alang('language', 'Language'), ENT_QUOTES, 'UTF-8'); ?>">
      <a href="<?php echo base_url();?>users/set_admin_lang/th" class="session-toolbar-lang-btn <?php echo ($cur_lang === 'th') ? 'is-active' : ''; ?>">TH</a>
      <span class="session-toolbar-lang-sep">/</span>
      <a href="<?php echo base_url();?>users/set_admin_lang/en" class="session-toolbar-lang-btn <?php echo ($cur_lang === 'en') ? 'is-active' : ''; ?>">EN</a>
    </span>
    <span class="session-toolbar-sep">|</span>
    <a href="<?php echo base_url();?>users/logout" class="session-toolbar-logout" title="<?php echo htmlspecialchars(alang('logout', 'Logout'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(alang('logout', 'Logout'), ENT_QUOTES, 'UTF-8'); ?></a>
  </div>
</header>
