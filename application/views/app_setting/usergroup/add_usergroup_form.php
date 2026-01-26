
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>config_system/usergroup/usergroup_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."config_system/usergroup/usergroup_add";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อกลุ่ม : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="group_name" id='group_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_add" id="is_add" value="1">
                    <label class="form-check-label" for="flexCheckDefault">
                      Add
                    </label> 
                  </div>
                  <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_edit" id="is_edit" value="1">
                    <label class="form-check-label" for="flexCheckDefault">
                      Edit
                    </label> 
                  </div>
                  <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_del" id="is_del" value="1">
                    <label class="form-check-label" for="flexCheckDefault">
                      Delete
                    </label> 
                  </div>
                  <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_read" id="is_read" value="1">
                    <label class="form-check-label" for="flexCheckDefault">
                      Read
                    </label> 
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/usergroup/usergroup_list" id="addToTable" class="btn btn-outline btn-primary" >
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


