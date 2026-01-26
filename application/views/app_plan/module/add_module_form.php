
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
                <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."app_plan/module/module_add";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Module Name : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="module_name" id='module_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Menu : </label>
                    <div class="col-md-6">
                        <?php if(!empty($arr_menus)){?>
                            <?php foreach($arr_menus as $arr_menu){?>
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="menu_id[]"  id="menu_id" value="<?php echo $arr_menu['menu_id']?>">
                                  <label class="form-check-label" for="flexCheckDefault">
                                  <?php echo $arr_menu['menu_name'];?>
                                  </label>
                              </div>
                              <?php if(!empty($arr_menu['submenus'])){?>
                                <?php foreach($arr_menu['submenus'] as $submenus){?>
                                  <div class="form-check" style="padding: 0px 0px 0px 50px;">
                                  <input class="form-check-input" type="checkbox" name="menu_id[]"  id="menu_id" value="<?php echo $submenus['menu_id']?>">
                                  <label class="form-check-label" for="flexCheckDefault">
                                  <?php echo $submenus['menu_name'];?>
                                  </label>
                              </div>
                        <?php }}}}?>
                    </div>
                  </div>
                  <div class="form-group">
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


