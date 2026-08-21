<div class="site-menubar">
  <ul class="site-menu">
    <?php
      if(!empty($arr_page_menus)){
        foreach ($arr_page_menus as $arr_page_menu){ ?>
          <?php if(!empty($arr_page_menu['submenus'])){?>
            <li class="site-menu-item has-sub <?php if(in_array($menu_id_ref,$arr_page_submenus)){echo "active";}?> ">
          <?php }else{ ?>  
            <li class="site-menu-item has-sub <?php if($arr_page_menu['menu_id'] == $menu_id_ref){echo "active";}?> ">
          <?php }?>  
            <a href="<?php echo base_url().$arr_page_menu['link'];?>">
                <i class="site-menu-icon <?php echo $arr_page_menu['icon']?>" aria-hidden="true"></i>
                <span class="site-menu-title"><?php echo $arr_page_menu['menu_name']?></span>
                <?php if(!empty($arr_page_menu['submenus'])){?>
                  <span class="site-menu-arrow"></span>
                <?php }?>
            </a>
            <?php if(!empty($arr_page_menu['submenus'])){?>
            <ul class="site-menu-sub">
              <?php foreach ($arr_page_menu['submenus'] as $arr_page_submenu){ ?>
              <li class="site-menu-item <?php if($arr_page_submenu['menu_id'] == $menu_id_ref){echo "active";}?> ">
                <a class="animsition-link" href="<?php echo base_url().$arr_page_submenu['link'];?>">
                  <span class="site-menu-title"><?php echo $arr_page_submenu['menu_name']?></span>
                  <?php if(!empty($arr_page_submenu['lv3_submenus'])){?>
                  <span class="site-menu-arrow"></span>
                <?php }?>
                </a>
                <?php if(!empty($arr_page_submenu['lv3_submenus'])){?>
                  <ul class="site-menu-sub">
                    <?php foreach ($arr_page_submenu['lv3_submenus'] as $arr_page_submenu_lv3){ ?>
                    <li class="site-menu-item <?php if($arr_page_submenu_lv3['menu_id'] == $menu_id_ref){echo "active";}?> ">
                      <a class="animsition-link" href="<?php echo base_url().$arr_page_submenu_lv3['link'];?>">
                        <span class="site-menu-title"><?php echo $arr_page_submenu_lv3['menu_name']?></span>
                      </a>
                    </li>
                    <?php }?>
                  </ul>
                <?php }?>
              </li>
              <?php }?>
            </ul>
          <?php }?>
          </li>
  <?php }}?>
  </ul>
</div>    
  <div class="site-gridmenu">
    <div>
      <div>
        <ul>
          <li>
            <a href="apps/mailbox/mailbox.html">
              <i class="icon wb-envelope"></i>
              <span>Mailbox</span>
            </a>
          </li>
          <li>
            <a href="apps/calendar/calendar.html">
              <i class="icon wb-calendar"></i>
              <span>Calendar</span>
            </a>
          </li>
          <li>
            <a href="apps/contacts/contacts.html">
              <i class="icon wb-user"></i>
              <span>Contacts</span>
            </a>
          </li>
          <li>
            <a href="apps/media/overview.html">
              <i class="icon wb-camera"></i>
              <span>Media</span>
            </a>
          </li>
          <li>
            <a href="apps/documents/categories.html">
              <i class="icon wb-order"></i>
              <span>Documents</span>
            </a>
          </li>
          <li>
            <a href="apps/projects/projects.html">
              <i class="icon wb-image"></i>
              <span>Project</span>
            </a>
          </li>
          <li>
            <a href="apps/forum/forum.html">
              <i class="icon wb-chat-group"></i>
              <span>Forum</span>
            </a>
          </li>
          <li>
            <a href="index.html">
              <i class="icon wb-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
</div>