<div class="dashboard-nav">
    <header>
        <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
        <a href="#" class="brand-logo"><img src="<?php echo base_url()?>resources/images/logo-bny.png" width="32px"><span>&nbsp BNY</span></a></header>
        <nav class="dashboard-nav-list">
            <?php 
                if(!empty($arr_page_menus['data_menus'])){
                    foreach ($arr_page_menus['data_menus'] as $arr_page_menu){
                        if(!empty($arr_page_menu['submenus'])){
            ?>
                <div class='dashboard-nav-dropdown <?php if(in_array($menu_id_ref,array_column($arr_page_menu['submenus'], 'menu_id'))){echo "show";}?>'>
                    <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle"><i class="fas <?php echo $arr_page_menu['icon']?>"></i> <?php echo $arr_page_menu['menu_name']?> </a>
                    <?php if(!empty($arr_page_menu['submenus'])){?>
                        <div class='dashboard-nav-dropdown-menu'>
                            <?php foreach ($arr_page_menu['submenus'] as $arr_page_submenu){ ?>
                                <a href="<?php echo base_url().$arr_page_submenu['link'];?>" class="dashboard-nav-dropdown-item <?php if($arr_page_submenu['menu_id']== $menu_id_ref){echo "active";}?>"><?php echo $arr_page_submenu['menu_name']?></a>
                            <?php }?>
                        </div>
                    <?php }?>
                </div>
            <?php }else{?>
                    <a href="<?php echo base_url().$arr_page_menu['link'];?>" class="dashboard-nav-item <?php if($arr_page_menu['menu_id'] == $menu_id_ref){echo "active";}?>"><i class="fas fa-cogs"></i> <?php echo $arr_page_menu['menu_name']?> </a>
            <?php }}}?>
      <div class="nav-item-divider"></div>
      <a href="<?php echo base_url();?>users/logout" class="dashboard-nav-item"><i class="fas fa-sign-out-alt"></i> Logout </a>
    </nav>
</div>

<div class='dashboard-app'>
<header class='dashboard-toolbar'><a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a></header>