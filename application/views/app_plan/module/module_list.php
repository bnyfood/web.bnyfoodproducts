<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>app_plan/module/add_module_form" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus"></i> เพิ่ม Module
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <table class="table table-striped">
                  <thead >
                    <tr>
                      <th>Module Name</th>
                      <th>Menu</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_modules)){
                        foreach($arr_modules as $arr_module){
                    ?>
                      <tr>
                        <td><?php echo $arr_module['module_name']?></td>
                        <td>
                          <?php
                            if(!empty($arr_module['arr_menus'])){
                              foreach($arr_module['arr_menus'] as $arr_menu){
                                echo $arr_menu['menu_name']."<br>";
                                if(!empty($arr_menu['submenus'])){
                                  foreach($arr_menu['submenus'] as $submenu){
                                    echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$submenu['menu_name']."<br>";
                                  }
                                }
                              }
                            }
                          ?>
                        </td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>app_plan/module/edit_module_form/<?php echo $arr_module['bny_module_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>app_plan/module/del_action/<?php echo $arr_module['bny_module_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php }}?>
                  </tbody>
                </table>
              </div>  
            </div>
          </div>
        </div>
</div>


