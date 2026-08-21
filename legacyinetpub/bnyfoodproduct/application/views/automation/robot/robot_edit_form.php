
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/robot/robot_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/robot/robot_edit";?>" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Name : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="robot_name" id='robot_name'  autocomplete="off" value="<?php echo $arr_robot['robot_name']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Width : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="robot_width" id='robot_width'  autocomplete="off" value="<?php echo $arr_robot['robot_width']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Height : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="robot_height" id='robot_height'  autocomplete="off" value="<?php echo $arr_robot['robot_height']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Length : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="robot_length" id='robot_length'  autocomplete="off" value="<?php echo $arr_robot['robot_length']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Type : </label>
                    <div class="col-md-6">
                      <input type="number" step="1" min="1" class="form-control" name="robot_type" id='robot_type'  autocomplete="off" value="<?php echo $arr_robot['robot_type']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Wheel radius : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="wheel_radius" id='wheel_radius'  autocomplete="off" value="<?php echo $arr_robot['wheel_radius']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Wheel thickness : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="wheel_thickness" id='wheel_thickness'  autocomplete="off" value="<?php echo $arr_robot['wheel_thickness']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Fontwheel gap : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="fontwheel_gap" id='fontwheel_gap'  autocomplete="off" value="<?php echo $arr_robot['fontwheel_gap']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Sidewheel gap : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="sidewheel_gap" id='sidewheel_gap'  autocomplete="off" value="<?php echo $arr_robot['sidewheel_gap']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Load capacity : </label>
                    <div class="col-md-6">
                      <input type="number" step="10" min="50" class="form-control" name="robot_load_capacity" id='robot_load_capacity'  autocomplete="off" value="<?php echo $arr_robot['robot_load_capacity']?>">Liters
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Description : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="description" id="description" ><?php echo $arr_robot['description']?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <input type="hidden" name="id_en" value="<?php echo $id_en;?>">
                    <a href="<?php echo base_url();?>automation/robot/robot_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
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


