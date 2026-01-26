
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>config_system/menu/sub_menu_list/<?php echo $parentid_en;?>" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-reply" aria-hidden="true"></i> กลับ
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <form role="form" name="sub_menu_add_form" id="sub_menu_add_form" action="<?php echo base_url()."config_system/menu/sub_menu_add";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อเมนู : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="menu_name" id='menu_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Link : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="link" id='link'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Icon : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="icon" id='icon'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ลำดับ : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="sort" id='sort'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">แสดงที่ลูกค้า : </label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="show_customer" id="show_customer">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/menu/sub_menu_list/<?php echo $parentid_en;?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="fa fa-reply" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="parentid_en" value="<?php echo $parentid_en;?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>  
            </div>
          </div>
        </div>
</div>


