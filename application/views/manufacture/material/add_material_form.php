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
                    <label class="col-md-2 col-form-label">หน่วยย่อย : </label>
                    <div class="col-md-12">
                      <input type="radio" name="sub_unit" id="sub_unit" value="2" checked>ไม่มี
                      <input type="radio" name="sub_unit" id="sub_unit" value="1" >มี
                      <div id="subunit_name">
                        <div class="form-group row">
                          <label class="col-md-2 col-form-label">ชื่อ Material:</label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="material_name" id='material_name'  autocomplete="off">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-2 col-form-label">SKU:</label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="material_sku" id='material_sku'  autocomplete="off">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-2 col-form-label">ขนาดบรรจุ : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="material_size" id='material_size'  autocomplete="off">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-2 col-form-label">หน่วย : </label>
                          <div class="form-group col-md-6">
                            <select class="form-control" name="material_unit" id="material_unit">
                              <option value="">กรุณาเลือก</option> 
                              <?php 
                                foreach($arr_units as $arr_unit){
                              ?>
                              <option value="<?php echo $arr_unit['web_material_unit_id']?>" ><?php echo $arr_unit['material_unit']?></option>
                              <?php }?> 
                            </select>  
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-md-2 col-form-label">ชนิดบรรจุ : </label>
                            <div class="form-group col-md-6">
                             <select class="form-control" name="main_web_material_subunit_type_id" id="main_web_material_subunit_type_id">
                              <option value="">กรุณาเลือก</option> 
                              <?php 
                                foreach($arr_unit_types as $arr_unit_type){
                              ?>
                              <option value="<?php echo $arr_unit_type['web_material_unit_type_id']?>" ><?php echo $arr_unit_type['unit_type_name']?></option>
                              <?php }?> 
                             </select> 
                            </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-md-2 col-form-label">ความหนาแน่น : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="material_density" id='material_density'  autocomplete="off">
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
                          <!--<div class="form-group col-md-1">
                            <button type="button" id="manage_cat"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                          </div> !-->
                        </div>

                      </div>
                      <div id="subunit_search" style="display: none">

                        <div class="form-row flex-nowrap">
                          <div class="form-group col-md-4">
                             <select class="form-control" name="web_material_subunit_type_id" id="web_material_subunit_type_id">
                              <option value="">กรุณาเลือก</option> 
                              <?php 
                                foreach($arr_unit_types as $arr_unit_type){
                              ?>
                              <option value="<?php echo $arr_unit_type['web_material_unit_type_id']?>" ><?php echo $arr_unit_type['unit_type_name']?></option>
                              <?php }?> 
                            </select> 
                          </div>
                        </div>
                        
                        <div class="form-row flex-nowrap">
                          <div class="col-md-4">
                            <input type="text" class="form-control" id="sub_unit_txt_search" name="sub_unit_txt_search" >
                          </div>
                          <div class="col-md-3">
                            <input type="button" class="btn btn-primary" name="btn_sub_unit_search" id="btn_sub_unit_search" value="Search">
                            <input type="button" class="btn btn-primary" name="btn_sub_unit_search_all" id="btn_sub_unit_search_all" value="All">
                          </div>
                        </div>
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>ชื่อสินค้า</th>
                              <th>ยี่ห้อ</th>
                              <th>ชนิดบรรจุ</th>
                              <th>ปริมาณหลัก</th>
                              <th>SKU</th>
                              <th class="text-nowrap">ตัวเลือก</th>
                            </tr>
                          </thead>
                          <tbody id="sub_unit-list">
                          </tbody>
                        </table>

                        <div class="form-row flex-nowrap">
                          <label class="col-md-2 col-form-label">SKU : </label>
                          <div class="col-md-1" id="newsku">
                            
                          </div>
                        </div>

                        <div class="form-row flex-nowrap">
                          <label class="col-md-2 col-form-label">จำนวนหลัก : </label>
                          <div class="col-md-1">
                            <input type="number" class="form-control" name="subunit_qty" id='subunit_qty'  autocomplete="off">
                          </div>
                        </div>
                      </div>
                      <div id="vt_add_form" style="display: none;">
                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">Volumn Type Name : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="web_material_volume_type" id='web_material_volume_type'  autocomplete="off">
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                  

                  

                  


                  <div class="form-group">
                    <a href="<?php echo base_url();?>manufacture/material/material_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <input type="hidden" name="supprice" id="supprice">
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">

            <div class="form-group row">
                          <label class="col-md-2 col-form-label">Supplier : </label>
                          <div class="form-group col-md-6">
                            <div class="example-wrap m-md-0">
                              <div class="example">
                                <select class="form-control" name="web_supplier_id[]" id="web_supplier_id" multiple="multiple" data-plugin="multiSelect">
                                  <optgroup label="Supplier">
                                    <?php 
                                      foreach($arr_suppliers as $arr_supplier){
                                    ?>
                                    <option value="<?php echo $arr_supplier['web_supplier_id']?>" ><?php echo $arr_supplier['supplier_name']?></option>
                                    <?php }?> 
                                  </optgroup>
                                </select>
                              </div>
                            </div>
                          </div>
                          <input type="hidden" name="ran_num_sup" id="ran_num_sup" value="<?php echo $ran_num_sup;?>">
                          <!--<div class="form-group col-md-1">
                            <button type="button" id="manage_supplier"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                          </div>!-->

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
                          <!--<div class="form-group col-md-1">
                            <button type="button" id="manage_brand"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                          </div>!-->
                        </div>

                        <div class="form-group row" id="zip_search">
                          <div class="col-lg-12">
                            <table class="table table-bordered">
                              <thead>
                                <tr>
                                  <th>Supplier</th>
                                  <th>ราคา</th>
                                </tr>
                              </thead>
                              <tbody id="supplier_price">
                                <tr>
                                  <td></td>
                                  <td></td>
                                </tr>
                              </tbody>
                            </table>
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