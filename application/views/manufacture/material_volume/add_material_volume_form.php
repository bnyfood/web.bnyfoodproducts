<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่ม Material Volume</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."manufacture/material_volume/material_volume_add";?>" method="post" enctype="multipart/form-data">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <div class="example">

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label"> Material : </label>
                    <div class="col-md-12">

                        Material Search : 
                        <div class="form-row flex-nowrap">
                          <div class="col-md-4">
                            <input type="text" class="form-control" id="material_search" name="material_search" >
                          </div>
                          <div class="col-md-3">
                            <input type="button" class="btn btn-primary" name="btn_material_search" id="btn_material_search" value="Search">
                            <input type="button" class="btn btn-primary" name="btn_material_search_all" id="btn_material_search_all" value="All">
                          </div>
                        </div>

                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Material Name</th>
                              <th>Supplier Name</th>
                              <th class="text-nowrap">ตัวเลือก</th>
                            </tr>
                          </thead>
                          <tbody id="material-list">
                          </tbody>
                        </table>
                    

                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Volumn Type : </label>
                    <div class="col-md-12">
                      <input type="radio" name="vt_type" id="vt_type" value="1" checked>Search
                      <input type="radio" name="vt_type" id="vt_type" value="2">Add New Volumn Type
                      <div id="vt_search">

                        Volumn Type Search : 
                        <div class="form-row flex-nowrap">
                          <div class="col-md-4">
                            <input type="text" class="form-control" id="material_volume_search" name="material_volume_search" >
                          </div>
                          <div class="col-md-3">
                            <input type="button" class="btn btn-primary" name="btn_material_volume_search" id="btn_material_volume_search" value="Search">
                            <input type="button" class="btn btn-primary" name="btn_material_volume_search_all" id="btn_material_volume_search_all" value="All">
                          </div>
                        </div>

                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Volumn Type Name</th>
                              <th class="text-nowrap">ตัวเลือก</th>
                            </tr>
                          </thead>
                          <tbody id="vt-list">
                          </tbody>
                        </table>
                      </div>
                      <div id="vt_add_form" style="display: none;">

                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">Volumn Type Name : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="web_material_volume_type" id='web_material_volume_type'  autocomplete="off">
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Unit : </label>
                    <div class="col-md-12">
                      <input type="radio" name="unit_type" id="unit_type" value="1" checked>Search
                      <input type="radio" name="unit_type" id="unit_type" value="2">Add New Unit
                      <div id="unit_search">

                        Unit Search : 
                        <div class="form-row flex-nowrap">
                          <div class="col-md-4">
                            <input type="text" class="form-control" id="material_unit_search" name="material_unit_search" >
                          </div>
                          <div class="col-md-3">
                            <input type="button" class="btn btn-primary" name="btn_material_unit_search" id="btn_material_unit_search" value="Search">
                            <input type="button" class="btn btn-primary" name="btn_material_unit_search_all" id="btn_material_unit_search_all" value="All">
                          </div>
                        </div>

                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Unit Name</th>
                              <th class="text-nowrap">ตัวเลือก</th>
                            </tr>
                          </thead>
                          <tbody id="unit-list">
                          </tbody>
                        </table>
                      </div>
                      <div id="unit_add_form" style="display: none;">

                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">Unit Name : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="material_unit" id='material_unit'  autocomplete="off">
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Volume : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="web_material_volume" id='web_material_volume'  autocomplete="off">
                    </div>
                  </div>

                  
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/users/user_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </div>
            </div>
          </div>
          </div>
        </form>
      </div>
    </div>
  </div> 
</div>      


