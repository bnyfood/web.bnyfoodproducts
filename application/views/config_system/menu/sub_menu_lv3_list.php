<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config Menu</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <h4 class="example-title">Menu</h4>
              <div class="row">
              <div class="col-md-6">
                <div class="mb-15">
                  <a href="<?php echo base_url();?>/config_system/menu/add_sub_menu_lv3_form/<?php echo $parentid;?>/<?php echo $id_lv2_en;?>" id="addToTable" class="btn btn-outline btn-primary" >
                    <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มเมนูย่อย
                  </a>
                </div>
              </div>
            </div>
            <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มเมนูสำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มเมนูไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไขเมนูสำเร็จ
              </div>
            <?php }?>  
              <div class="example table-responsive">
                <table class="table table-bordered">
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
                        <td><?php if($arr_menu['show_customer'] == 1){echo "<span class='counter-icon green-600'><i class='icon wb-check' aria-hidden='true'></i></span>";}else{echo "<span class='counter-icon red-600'><i class='icon wb-close' aria-hidden='true'></i></span>";}?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/menu/sub_menu_lv3_edit_form/<?php echo $arr_menu['menu_id'];?>/<?php echo $parentid;?>/<?php echo $id_lv2_en;?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/menu/del_sub_lv3_action/<?php echo $arr_menu['menu_id'];?>/<?php echo $parentid;?>/<?php echo $id_lv2_en;?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php }}?>
                  </tbody>
                </table>
                <a href="<?php echo base_url();?>config_system/menu/sub_menu_list/<?php echo $parentid;?>" id="addToTable" class="btn btn-outline btn-primary" >
                  <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                </a>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div> 
</div>       