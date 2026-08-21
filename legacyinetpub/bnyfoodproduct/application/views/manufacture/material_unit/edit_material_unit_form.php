<div class="page">
  <div class="page-header">
    <h1 class="page-title">แก้ไข Material Unit</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:40px;">
        <form role="form" name="material_unit_edit_form" id="material_unit_edit_form" action="<?php echo base_url()."manufacture/material_unit/material_unit_edit";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูล Material Unit</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Material Unit:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="material_unit" id='material_unit'  autocomplete="off" value="<?php echo $arr_unit['material_unit']?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>manufacture/material_unit/material_unit_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" id="id_en" value="<?php echo $id_en?>">
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
