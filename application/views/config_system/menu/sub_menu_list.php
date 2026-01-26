
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>config_system/menu/menu_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-reply" aria-hidden="true"></i> กลับ
            </a>
            <a href="<?php echo base_url();?>/config_system/menu/add_sub_menu_form/<?php echo $parentid;?>" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus" aria-hidden="true"></i> เพิ่มเมนูย่อย
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
                        <td><?php if($arr_menu['show_customer'] == 1){echo "<span class='counter-icon green-600'><i class='fa fa-check'></i></span>";}else{echo "<span class='counter-icon red-600'><i class='fa fa-check'></i></span>";}?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/menu/sub_menu_edit_form/<?php echo $arr_menu['menu_id'];?>/<?php echo $parentid;?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/menu/del_sub_action/<?php echo $arr_menu['menu_id'];?>/<?php echo $parentid;?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


