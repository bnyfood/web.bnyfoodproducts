<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่มสิทธื์</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."config_system/page_permission/page_permission_add";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Controller : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="controller" id='controller'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">User type : </label>
                    <div class="col-md-6">
                      <select class="form-control" name="user_level" id="user_level">
                        <option value="">กรุณาเลือก</option> 
                        <option value="1">User</option> 
                        <option value="2">Super User</option> 
                        <option value="3">Admin</option> 
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/page_permission/page_permission_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">
            <div class="example-wrap">
              <h4 class="example-title">สิทธิ์</h4>
                <div class="example">
                  <div class="form-group row">
                    <legend class="col-md-3 col-form-label">Add: </legend>
                    <div class="col-md-9 d-flex align-items-center">
                      <div class="radio-custom radio-default radio-inline">
                        <input type="radio" id="user_add" name="user_add" value="0" checked/>
                        <label for="inputHorizontalMale">No</label>
                      </div>
                      <div class="radio-custom radio-default radio-inline">
                        <input type="radio" id="user_add" name="user_add" value="1"
                        />
                        <label for="inputHorizontalFemale">Yes</label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <legend class="col-md-3 col-form-label">Edit: </legend>
                    <div class="col-md-9 d-flex align-items-center">
                      <div class="radio-custom radio-default radio-inline">
                        <input type="radio" id="user_edit" name="user_edit" value="0" checked />
                        <label for="inputHorizontalMale">No</label>
                      </div>
                      <div class="radio-custom radio-default radio-inline">
                        <input type="radio" id="user_edit" name="user_edit" value="1"
                        />
                        <label for="inputHorizontalFemale">Yes</label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <legend class="col-md-3 col-form-label">Delete: </legend>
                    <div class="col-md-9 d-flex align-items-center">
                      <div class="radio-custom radio-default radio-inline">
                        <input type="radio" id="user_delete" name="user_delete" value="0" checked/>
                        <label for="inputHorizontalMale">No</label>
                      </div>
                      <div class="radio-custom radio-default radio-inline">
                        <input type="radio" id="user_delete" name="user_delete" value="1" 
                        />
                        <label for="inputHorizontalFemale">Yes</label>
                      </div>
                    </div>
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