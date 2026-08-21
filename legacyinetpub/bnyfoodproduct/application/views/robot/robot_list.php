<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config Robot</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <h4 class="example-title">Robot</h4>
              <div class="row">
              <div class="col-md-6">
                <div class="mb-15">
                  <a href="<?php echo base_url();?>robot/robot_add_form" id="addToTable" class="btn btn-outline btn-primary" >
                    <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Robot
                  </a>
                  <a href="<?php echo base_url();?>robot/robot_path" id="addToTable" class="btn btn-outline btn-primary" >
                     Robot path
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
                      <th>ชื่อ</th>
                      <th>Wide</th>
                      <th>Length</th>
                      <th>Height</th>
                      <th>Type</th>
                      <th>Wheel Radius</th>
                      <th>Batterry Level</th>
                      <th>Color</th>
                      <th>Charting</th>
                      <th>Active</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_robots)){
                        foreach($arr_robots as $arr_robot){
                    ?>
                      <tr>
                        <td><?php echo $arr_robot['name']?></td>
                        <td><?php echo $arr_robot['robot_width']?></td>
                        <td><?php echo $arr_robot['length']?></td>
                        <td><?php echo $arr_robot['height']?></td>
                        <td><?php if($arr_robot['type'] == 1){echo "Dry";}else{echo "Wet";}?></td>
                        <td><?php echo $arr_robot['wheelRadius']?></td>
                        <td><?php echo $arr_robot['batterryLevel']?></td>
                        <td><?php echo $arr_robot['robot_color']?></td>
                        <td><?php echo $arr_robot['robot_charting']?></td>
                        <td><?php echo $arr_robot['robot_active']?></td>

                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>robot/robot_edit_form/<?php echo $arr_robot['robotID'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>robot/del_action/<?php echo $arr_robot['robotID'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                         <!--<a href="<?php echo base_url();?>robot/robot_path/<?php echo $arr_robot['robotID'];?>" data-toggle="tooltip" data-original-title="Manage"> 
                            <i class="icon wb-menu" aria-hidden="true"></i>-->
                          </a>
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
    </div>
  </div> 
</div>       