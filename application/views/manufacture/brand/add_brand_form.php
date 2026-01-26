<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่ม Brand</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."manufacture/brand/brand_add";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อ Brand:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="material_brand_name" id='material_brand_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>manufacture/brand/brand_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="from_pop" value="<?php echo $from_pop;?>">
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