
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/windows_and_door/data_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/windows_and_door/add_data";?>" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Room1 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="room1" id='room1'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Room2 : </label>
                    <div class="col-md-6">
                      <input type="text"  class="form-control" name="room2" id='room2'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Type : </label>
                    <div class="col-md-6">
                      <input type="number" step="1" min="1" class="form-control" name="type" id='type'  autocomplete="off">1: Transfer Robot with Sigle Silo, 2: Transfer Robot with multiple silo, 3: Ceaning robot, 4: Waste transfer
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Ground offset : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.0001" min="0.01" class="form-control" name="ground_offset" id='ground_offset'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">X : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="x" id='x'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Y : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="y" id='y'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">R : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="r" id='r'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Ceta : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="ceta" id='ceta'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Heading : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="heading" id='heading'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Width : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="width" id='width'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Height : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="height" id='height'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Thickness : </label>
                    <div class="col-md-6">
                      <input type="number" step="0.01" min="0.1" class="form-control" name="thickness" id='thickness'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Description : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="description" id="description" ></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>automation/windows_and_door/data_list" id="addToTable" class="btn btn-outline btn-primary" >
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


