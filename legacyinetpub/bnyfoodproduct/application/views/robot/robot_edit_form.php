<div class="page">
  <div class="page-header">
    <h1 class="page-title">Edit Robot</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-md-12 col-lg-6">
            <!-- Example Basic Form Without Label -->
            <div class="example-wrap">
              <h4 class="example-title">Robot</h4>
              <div class="example">
                <form role="form" name="menu_add_form" id="menu_add_form" action="<?php echo base_url()."robot/robot_edit";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อ : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="name" id='name'  autocomplete="off" value="<?php echo $arr_robot['name'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Wide : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="wide" id='wide'  autocomplete="off" value="<?php echo $arr_robot['robot_width'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Length : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="length" id='length'  autocomplete="off" value="<?php echo $arr_robot['length'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Height : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="height" id='height'  autocomplete="off" value="<?php echo $arr_robot['height'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Type : </label>
                    <div class="col-md-6">
                      <select class="form-control" name="type" id="type">
                        <option value="">กรุณาเลือก</option> 
                          <option value="1" <?php if($arr_robot['type'] == 1){echo "selected";}?> >Dry</option>
                          <option value="2" <?php if($arr_robot['type'] == 2){echo "selected";}?> >Wet</option>
                      </select>  
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Wheel Radius : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="wheelRadius" id='wheelRadius'  autocomplete="off" value="<?php echo $arr_robot['wheelRadius'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Batterry Level : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="batterryLevel" id='batterryLevel'  autocomplete="off" value="<?php echo $arr_robot['batterryLevel'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Drivewheel gap : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="drivewheelgap" id='drivewheelgap'  autocomplete="off" value="<?php echo $arr_robot['drivewheelgap'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Fontwheel gap : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="fontwheel_gap" id='fontwheel_gap'  autocomplete="off" value="<?php echo $arr_robot['fontwheel_gap'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Wheel dept : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="wheel_dept" id='wheel_dept'  autocomplete="off" value="<?php echo $arr_robot['wheel_dept'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Robot dept : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="robot_dept" id='robot_dept'  autocomplete="off" value="<?php echo $arr_robot['robot_dept'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Color : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="robot_color" id='robot_color'  autocomplete="off" value="<?php echo $arr_robot['robot_color'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Charting : </label>
                    <div class="col-md-6">
                      <input type="checkbox" name="robot_charting" id='robot_charting' value="1"  autocomplete="off" <?php if($arr_robot['robot_charting'] == 1){echo "checked";}?>>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Active : </label>
                    <div class="col-md-6">
                      <input type="checkbox" name="robot_active" id='robot_active' value="1"  autocomplete="off" <?php if($arr_robot['robot_active'] == 1){echo "checked";}?>>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>robot/robot_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" value="<?php echo $id_en?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       