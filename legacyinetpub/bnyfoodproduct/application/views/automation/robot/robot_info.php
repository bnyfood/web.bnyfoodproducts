<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/robot/robot_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
            <a href="<?php echo base_url();?>automation/robot/robot_edit_form/<?php echo $id_en;?>" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-edit"></i> แก้ไข Robot
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <table class="table table-striped">
                    <?php 
                        if(!empty($arr_robot)){
                    ?>
                      <tr>
                        <td>Name : </td>
                        <td><?php echo $arr_robot['robot_name']?></td>
                        <td>Width : </td>
                        <td><?php echo $arr_robot['robot_width']?></td>
                      </tr>
                      <tr>
                        <td>Height : </td>
                        <td><?php echo $arr_robot['robot_height']?></td>
                        <td>Length : </td>
                        <td><?php echo $arr_robot['robot_length']?></td>
                      </tr>
                      <tr>
                        <td>Type : </td>
                        <td><?php echo $arr_robot['robot_type']?></td>
                        <td>Wheel radius : </td>
                        <td><?php echo $arr_robot['wheel_radius']?></td>
                      </tr>
                      <tr>
                        <td>Wheel thickness : </td>
                        <td><?php echo $arr_robot['wheel_thickness']?></td>
                        <td>Fontwheel gap : </td>
                        <td><?php echo $arr_robot['fontwheel_gap']?></td>
                      </tr>
                      <tr>
                        <td>Sidewheel gap : </td>
                        <td><?php echo $arr_robot['sidewheel_gap']?></td>
                        <td>Robot load capacity : </td>
                        <td><?php echo $arr_robot['robot_load_capacity']?></td>
                      </tr>
                      <tr>
                        <td>Description : </td>
                        <td colspan="3"><?php echo $arr_robot['description']?></td>
                      </tr>
                    <?php }?>
                </table>
              </div>  
            </div>
          </div>
        </div>
        <div class="row">
          <div class='card'>
            <div class='card-body'>
              <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/robot/robot_silo_add";?>" method="post" enctype="multipart/form-data">
                <div class="form-group row" style="flex-direction: row !important;">
                  <label class="col-md-2 col-form-label">Position : </label>
                  <div class="col-md-3">
                    <input type="number" class="form-control" name="robot_position" id='robot_position'  autocomplete="off">
                  </div>
                  <div class="col-md-3">
                    <input type="hidden" name="id_en" value="<?php echo $id_en;?>">
                  <button type="submit" class="btn btn-primary"><i class="icon wb-plus" aria-hidden="true">เพิ่ม Silo</button>
                  </div>
                </div>
              </form>

              <div class="table-responsive-sm">
                <table class="table table-striped">
                  <thead >
                    <tr>
                      <th>Position</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_robot_silos)){
                        foreach($arr_robot_silos as $arr_robot_silo){
                    ?>
                      <tr>
                        <td><?php echo $arr_robot_silo['position']?></td>
                        <td class="text-nowrap">
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>automation/robot/del_silo_action/<?php echo $arr_robot_silo['robot_silo_id'];?>/<?php echo $id_en;?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


