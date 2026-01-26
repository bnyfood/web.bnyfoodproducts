<div class="page">
  <div class="page-header">
    <h1 class="page-title">แก้ไข Controller</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."config_system/page_permission_group/edit_action";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Controller:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="controller" id='controller'  autocomplete="off" value="<?php echo $arr_data['controller']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Group:</label>
                    <div class="col-md-6">
                      <select class="form-control" name="usergroup_id" id="usergroup_id">
                        <?php foreach($arr_usergroups as $arr_usergroup){?>
                          <option value="<?php echo $arr_usergroup['usergroup_id']?>" <?php if($arr_data['group_id']==$arr_usergroup['usergroup_id']){echo "selected";}?>  ><?php echo $arr_usergroup['group_name']?></option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/page_permission_group/page_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" value="<?php echo $id_en;?>">
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