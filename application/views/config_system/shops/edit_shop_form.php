<div class="page">
  <div class="page-header">
    <h1 class="page-title">แก้ไขร้านค้า</h1>
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
              <h4 class="example-title">Shop</h4>
              <div class="example">
                <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."config_system/shops/shop_edit";?>" method="post">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อร้านค้า : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="ShopName" id='ShopName'  autocomplete="off" value="<?php echo $arr_shop['ShopName']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Domain : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="domain" id='domain'  autocomplete="off" value="<?php echo $arr_shop['domain']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">URL : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="URL_home" id='URL_home'  autocomplete="off" value="<?php echo $arr_shop['URL_home']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">IP : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="ip" id='ip'  autocomplete="off" value="<?php echo $arr_shop['ip']?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/shops/shops_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" id="id_en" value="<?php echo $arr_shop['ShopID']?>">
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