
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>app_plan/module/module_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-plus" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."app_plan/module/module_edit";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Module Name : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="module_name" id='module_name'  autocomplete="off" value="<?php echo $arr_module['module_name']?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <input type="hidden" name="id_en" value="<?php echo $id_en;?>">
                    <a href="<?php echo base_url();?>app_plan/module/module_list" id="addToTable" class="btn btn-outline btn-primary" >
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


