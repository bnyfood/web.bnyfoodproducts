<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/robot/robot_add_form" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus"></i> เพิ่ม Robot
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
                      <th>Name</th>
                      <th>width</th>
                      <th>height</th>
                      <th>length</th>
                      <th>type</th>
                      <th>Wheel radius</th>
                      <th>Wheel thickness</th>
                      <th>Fontwheel gap</th>
                      <th>Sidewheel gap</th>
                      <th>Load capacity(Liters)</th>
                      <th>No. of silo(s)</th>
                      <th>Description</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_robots)){
                        foreach($arr_robots as $arr_robot){
                    ?>
                      <tr>
                        <td><?php echo $arr_robot['robot_name']?></td>
                        <td><?php echo $arr_robot['robot_width']?></td>
                        <td><?php echo $arr_robot['robot_height']?></td>
                        <td><?php echo $arr_robot['robot_length']?></td>
                        <td><?php echo $arr_robot['robot_type']?></td>
                        <td><?php echo $arr_robot['wheel_radius']?></td>
                        <td><?php echo $arr_robot['wheel_thickness']?></td>
                        <td><?php echo $arr_robot['fontwheel_gap']?></td>
                        <td><?php echo $arr_robot['sidewheel_gap']?></td>
                        <td><?php echo $arr_robot['robot_load_capacity']?></td>
                        <td><?php echo count($arr_robot['robot_silo']);?></td>
                        <td><?php echo $arr_robot['description']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>automation/robot/robot_edit_form/<?php echo $arr_robot['robotID'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>automation/robot/del_action/<?php echo $arr_robot['robotID'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


