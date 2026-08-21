
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/map/map_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="atm_map_add_form" id="atm_map_add_form" action="<?php echo base_url()."automation/map/map_add";?>" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Picture : </label>
                    <div class="col-md-6">
                      <input type="file" id="upload_file_map" name="upload_file_map" />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Width : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="map_width" id='map_width'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>automation/map/map_list" id="addToTable" class="btn btn-outline btn-primary" >
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


