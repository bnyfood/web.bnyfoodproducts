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
                              <i class="<?php echo $icon_class; ?>"></i> <?php echo $arr_page_menu['menu_name']; ?>
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
                                      $submenu_link = !empty($arr_page_submenu['link']) ? base_url().$arr_page_submenu['link'] : '#';
                                  ?>
                                  <?php if($has_lv3){ ?>
                                      <div class="dashboard-nav-dropdown <?php if($is_sub_active || $is_lv3_active){echo 'show';} ?>">
                                          <a href="<?php echo $submenu_link; ?>" class="dashboard-nav-dropdown-item dashboard-nav-dropdown-toggle">
                                              <?php echo $arr_page_submenu['menu_name']; ?>
                                          </a>
                                          <div class="dashboard-nav-dropdown-menu">
                                              <?php foreach ($arr_page_submenu['lv3_submenus'] as $arr_page_submenu_lv3){ ?>
                                                  <?php
                                                      $lv3_link = !empty($arr_page_submenu_lv3['link']) ? base_url().$arr_page_submenu_lv3['link'] : '#';
                                                  ?>
                                                  <a href="<?php echo $lv3_link; ?>" class="dashboard-nav-dropdown-item <?php if(!empty($menu_id_ref) && $arr_page_submenu_lv3['menu_id'] == $menu_id_ref){echo 'active';} ?>" style="margin-left: 10px;">
                                                      <?php echo $arr_page_submenu_lv3['menu_name']; ?>
                                                  </a>
                                              <?php } ?>
                                          </div>
                                      </div>
                                  <?php }else{ ?>
                                      <a href="<?php echo $submenu_link; ?>" class="dashboard-nav-dropdown-item <?php if($is_sub_active){echo 'active';} ?>">
                                          <?php echo $arr_page_submenu['menu_name']; ?>
                                      </a>
                                  <?php } ?>
                              <?php } ?>
                          </div>
                      </div>
                  <?php }else{ ?>
                      <a href="<?php echo $menu_link; ?>" class="dashboard-nav-item <?php if($is_menu_active){echo 'active';} ?>">
                          <i class="<?php echo $icon_class; ?>"></i> <?php echo $arr_page_menu['menu_name']; ?>
                      </a>
                  <?php } ?>
              <?php } ?>
          <?php } ?>
          <div class="nav-item-divider"></div>
          <a href="<?php echo base_url();?>users/logout" class="dashboard-nav-item"><i class="fas fa-sign-out-alt"></i> Logout </a>
    </nav>
    </nav>
</div>
<div class='dashboard-app'>
<header class='dashboard-toolbar'><a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a></header>