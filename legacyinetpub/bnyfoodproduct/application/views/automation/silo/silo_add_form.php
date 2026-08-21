
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/silo/silo_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/silo/silo_add";?>" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Name : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="silo_name" id='silo_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Width : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="silo_width" id='silo_width'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Height : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="silo_height" id='silo_height'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Length : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="silo_length" id='silo_length'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Type : </label>
                    <div class="col-md-6">
                      <input type="number" step="1" min="1" class="form-control" name="silo_type" id='silo_type'  autocomplete="off">1: Transfer Robot with Sigle Silo, 2: Transfer Robot with multiple silo, 3: Ceaning robot, 4: Waste transfer
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Ground offset : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="ground_offset" id='ground_offset'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Load capacity : </label>
                    <div class="col-md-6">
                      <input type="number" step="10" min="50" class="form-control" name="silo_load_capacity" id='silo_load_capacity'  autocomplete="off"> Liters
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Unit : </label>
                    <div class="col-md-6">
                      <select class="form-control" name="silo_unit" id="silo_unit">
                        <option value="1">kg/m</option>
                        <option value="2">L/m</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Description : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="description" id="description" ></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Status : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="status_name" id='status_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>automation/silo/silo_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>  
            </div>
          </div>
        </div>
</div>


