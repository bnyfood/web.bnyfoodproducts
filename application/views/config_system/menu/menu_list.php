
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>config_system/menu/add_menu_form" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus" aria-hidden="true"></i> เพิ่มเมนู
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>ชื่อเมนู</th>
                      <th>Link</th>
                      <th>Icon</th>
                      <th>ลำดับ</th>
                      <th>เพิ่ม/ลด เมนูย่อย</th>
                      <th>แสดงที่ลูกค้า</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_menus)){
                        foreach($arr_menus as $arr_menu){
                    ?>
                      <tr>
                        <td><?php echo $arr_menu['menu_name']?></td>
                        <td><?php echo $arr_menu['link']?></td>
                        <td><?php echo $arr_menu['icon']?></td>
                        <td><?php echo $arr_menu['sort']?></td>
                        <td>
                          <a href="<?php echo base_url();?>config_system/menu/sub_menu_list/<?php echo $arr_menu['menu_id'];?>" > เมนูย่อย </a>
                        </td>
                        <td>
                          <?php if($arr_menu['show_customer'] == 1){echo "<span class='counter-icon green-600'><i class='fa fa-check' ></i></span>";}else{echo "<span class='counter-icon red-600'><i class='fa fa-close' ></i></span>";}?>
                        </td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/menu/menu_edit_form/<?php echo $arr_menu['menu_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm " data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/menu/del_action/<?php echo $arr_menu['menu_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


