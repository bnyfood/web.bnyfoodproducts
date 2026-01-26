<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่มเมนูย่อย</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-md-12 col-lg-6">
            <!-- Example Basic Form Without Label -->
            <div class="example-wrap">
              <h4 class="example-title">Menu</h4>
              <div class="example">
                <form role="form" name="sub_menu_add_form" id="sub_menu_add_form" action="<?php echo base_url()."config_system/menu/sub_menu_lv3_add";?>" method="post">
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
                      <input type="checkbox" name="show_customer" id="show_customer" value="1" >
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/menu/sub_menu_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="parentid_en" value="<?php echo $parentid_en;?>">
                    <input type="hidden" name="id_lv2_en" value="<?php echo $id_lv2_en;?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       