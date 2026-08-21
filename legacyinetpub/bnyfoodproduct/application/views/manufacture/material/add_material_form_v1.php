<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่ม Material</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."manufacture/material/material_add";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูล Material</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อ Material:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="material_name" id='material_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ประเภทวัตถุดิบ : </label>
                    <div class="form-group col-md-6">
                      <select class="form-control" name="web_category_id" id="web_category_id">
                        <option value="">กรุณาเลือก</option> 
                        <?php 
                          foreach($arr_list_cats as $arr_list_cat){
                            $blank = "";
                            for($i=0;$i<=$arr_list_cat['level']*2;$i++){
                              $blank.= "&nbsp";
                            }
                          ?>
                          <option value="<?php echo $arr_list_cat['web_category_id']?>"  ><?php echo $blank.$arr_list_cat['Title']?></option>
                        <?php }?> 
                      </select>  
                    </div>
                    <div class="form-group col-md-1">
                      <button type="button" id="manage_cat"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Supplier : </label>
                    <div class="form-group col-md-6">
                      <select class="form-control" name="web_supplier_id" id="web_supplier_id">
                        <option value="">กรุณาเลือก</option> 
                        <?php 
                          foreach($arr_suppliers as $arr_supplier){
                        ?>
                        <option value="<?php echo $arr_supplier['web_supplier_id']?>" ><?php echo $arr_supplier['supplier_name']?></option>
                        <?php }?> 
                      </select>  
                    </div>
                    <div class="form-group col-md-1">
                      <input type="hidden" name="ran_num_sup" id="ran_num_sup" value="<?php echo $ran_num_sup;?>">
                      <button type="button" id="manage_supplier"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                      <button type="button" id="add_supplier" class="btn btn-primary"><i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม </button>
                    </div>

                    <div class="card-block">
                      <ul class="list-group list-group-full" id="supplier-list">

                      </ul>
                    </div>

                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ยี่ห้อ : </label>
                    <div class="form-group col-md-6">
                      <select class="form-control" name="web_material_brand_id" id="web_material_brand_id">
                        <option value="">กรุณาเลือก</option> 
                        <?php 
                          foreach($arr_brands as $arr_brand){
                        ?>
                        <option value="<?php echo $arr_brand['web_material_brand_id']?>" ><?php echo $arr_brand['material_brand_name']?></option>
                        <?php }?> 
                      </select>  
                    </div>
                    <div class="form-group col-md-1">
                      <button type="button" id="manage_brand"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>manufacture/material/material_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">
            <div class="form-group row">
              <label class="col-md-2 col-form-label">ขนาด : </label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="material_size" id='material_size'  autocomplete="off">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-2 col-form-label">ราคา : </label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="material_unit_price" id='material_unit_price'  autocomplete="off">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-2 col-form-label">รายละเอียด : </label>
              <div class="col-md-6">
                <textarea class="form-control" name="description"></textarea>
              </div>
            </div>
          </div>
          </div>
        </form>
      </div>
    </div>
  </div> 
</div>       