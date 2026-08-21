
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/machine/machine_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/machine/machine_edit";?>" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Name : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="machine_name" id='machine_name'  autocomplete="off" value="<?php echo $arr_machine['machine_name'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Width : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="machine_width" id='machine_width'  autocomplete="off" value="<?php echo $arr_machine['machine_width'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Height : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="machine_height" id='machine_height'  autocomplete="off" value="<?php echo $arr_machine['machine_height'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Length : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="machine_length" id='machine_length'  autocomplete="off" value="<?php echo $arr_machine['machine_length'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Type : </label>
                    <div class="col-md-6">
                      <input type="number" step="1" min="1" class="form-control" name="machine_type" id='machine_type'  autocomplete="off" value="<?php echo $arr_machine['machine_type'];?>">1: Transfer Robot with Sigle Silo, 2: Transfer Robot with multiple silo, 3: Ceaning robot, 4: Waste transfer
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Ground offset : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="ground_offset" id='ground_offset'  autocomplete="off" value="<?php echo $arr_machine['ground_offset'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Load capacity : </label>
                    <div class="col-md-6">
                      <input type="number" step="10" min="50" class="form-control" name="machine_load_capacity" id='machine_load_capacity'  autocomplete="off" value="<?php echo $arr_machine['machine_load_capacity'];?>"> Liters
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Unit : </label>
                    <div class="col-md-6">
                      <select class="form-control" name="machine_unit" id="machine_unit">
                        <option value="1" <?php if($arr_machine['machine_unit'] == '1'){echo "selected";}?>>kg/m</option>
                        <option value="2" <?php if($arr_machine['machine_unit'] == '2'){echo "selected";}?>>L/m</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Description : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="description" id="description" ><?php echo $arr_machine['description'];?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>automation/machine/machine_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" id="id_en" value="<?php echo $id_en;?>">
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
              <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/machine/machine_silo_add";?>" method="post">
                <div class="form-group row" style="flex-direction: row !important;">
                  <label class="col-md-2 col-form-label">Position : </label>
                  <div class="col-md-3">
                    <input type="number" class="form-control" name="machine_position" id='machine_position'  autocomplete="off">
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
                        if(!empty($arr_machine_silos)){
                        foreach($arr_machine_silos as $arr_machine_silo){
                    ?>
                      <tr>
                        <td><?php echo $arr_machine_silo['position']?></td>
                        <td class="text-nowrap">
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>automation/machine/del_silo_action/<?php echo $arr_machine_silo['machine_silo_id'];?>/<?php echo $id_en;?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


