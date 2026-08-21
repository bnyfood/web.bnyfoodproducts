
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>config_system/menu/sub_menu_list/<?php echo $parentid;?>" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-reply" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="sub_menu_add_form" id="sub_menu_add_form" action="<?php echo base_url()."config_system/menu/sub_menu_edit";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อเมนู : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="menu_name" id='menu_name'  autocomplete="off" value="<?php echo $arr_menu['menu_name']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Link : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="link" id='link'  autocomplete="off" value="<?php echo $arr_menu['link']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Icon : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="icon" id='icon'  autocomplete="off" value="<?php echo $arr_menu['icon']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ลำดับ : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="sort" id='sort'  autocomplete="off" value="<?php echo $arr_menu['sort']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">แสดงที่ลูกค้า : </label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="show_customer" id="show_customer" <?php if($arr_menu['show_customer'] == 1){echo "checked";}?>>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/menu/sub_menu_list/<?php echo $parentid;?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="fa fa-reply" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" value="<?php echo $arr_menu['menu_id']?>">
                    <input type="hidden" name="parentid" value="<?php echo $parentid?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>  
            </div>
          </div>
        </div>
</div>


