<div class="page">
  <div class="page-header">
    <h1 class="page-title">แก้ไข Supplier</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_edit_form" id="user_edit_form" action="<?php echo base_url()."supplier/supplier_edit";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูล Supplier</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อ Supplier:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="supplier_name" id='supplier_name'  autocomplete="off" value="<?php echo $arr_sipplier['supplier_name'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">TAXID : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="supplier_tax" id='supplier_tax'  autocomplete="off" value="<?php echo $arr_sipplier['supplier_tax'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Vat : </label>
                    <div class="col-md-6">
                      <div class="float-left mr-20">
                        <input type="checkbox" id="supplier_vat" name="supplier_vat" data-plugin="switchery" value="1" <?php if($arr_sipplier['supplier_vat'] == 1){echo "checked";}?>/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 form-control-label">บุคคล/บริษัท: </label>
                        <div class="col-md-9">
                          <div class="radio-custom radio-default radio-inline">
                            <input type="radio" id="supplier_person" name="supplier_person" value="1" <?php if($arr_sipplier['supplier_person'] == 1){echo "checked";}?>/>
                            <label for="inputHorizontalMale">บุคคล</label>
                          </div>
                          <div class="radio-custom radio-default radio-inline">
                            <input type="radio" id="supplier_person" name="supplier_person" value="2" <?php if($arr_sipplier['supplier_person'] == 2){echo "checked";}?>/>
                            <label for="inputHorizontalFemale">บริษัท</label>
                          </div>
                        </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">สำนักงานใหญ่ : </label>
                    <div class="col-md-2">
                        <input type="checkbox" id="supplier_headoffice" name="supplier_headoffice" data-plugin="switchery" value="1" <?php if($arr_sipplier['supplier_headoffice'] == 1){echo "checked";}?> />
                    </div>
                    <?php 
                      $css_branch = "none";
                      if($arr_sipplier['supplier_headoffice'] == 0){
                          $css_branch = "contents";
                        }

                    ?>
                      <div id="branchid" style="display:<?php echo $css_branch;?>">
                        <label class="col-md-2 col-form-label">รหัสสาขา : </label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="supplier_branchid" id='supplier_branchid'  autocomplete="off" value="<?php echo $arr_sipplier['supplier_branchid'];?>">
                        </div>
                      </div>

                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">เบอร์โทร1 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="phoneno1" id='phoneno1'  autocomplete="off" value="<?php echo $arr_sipplier['phoneno1'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">เบอร์โทร2 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="phoneno2" id='phoneno2'  autocomplete="off" value="<?php echo $arr_sipplier['phoneno2'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Line : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="supplier_line" id='supplier_line'  autocomplete="off" value="<?php echo $arr_sipplier['supplier_line'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Email : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="supplier_email" id='supplier_email'  autocomplete="off" value="<?php echo $arr_sipplier['supplier_email'];?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Discription : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="supplier_discription"><?php echo $arr_sipplier['supplier_discription'];?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>supplier/supplier_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" value="<?php echo $id_en;?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">
            <div class="example-wrap">
              <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ที่อยู่ : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="supplier_address"><?php echo $arr_sipplier['supplier_address'];?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">จังหวัด : </label>
                    <div class="col-md-6">
                      <select class="form-control form-control-sm" name="province_sel" id="province_sel">
                        <option value="">กรุณาเลือก</option>
                        <?php foreach($arr_provinces as $arr_province){?>
                          <option value="<?php echo $arr_province['ProvinceID']?>" <?php if($arr_sipplier['supplier_province'] == $arr_province['ProvinceID']){echo "selected";}?>><?php echo $arr_province['NameInThai']?></option>
                        <?php }?>  
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">อำเภอ : </label>
                    <div class="col-md-6">
                      <select class="form-control form-control-sm" name="district_sel" id="district_sel">
                        <option value="">กรุณาเลือก</option> 
                        <?php foreach($arr_districts as $arr_district){?>
                          <option value="<?php echo $arr_district['DistrictId']?>" <?php if($arr_sipplier['supplier_district'] == $arr_district['DistrictId']){echo "selected";}?>><?php echo $arr_district['NameInThai']?></option>
                        <?php }?> 
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">แขวง/ตำบล:</label>
                    <div class="col-md-6">
                      <select class="form-control form-control-sm" name="subdistrict_sel" id="subdistrict_sel">
                        <option value="">กรุณาเลือก</option>
                        <?php foreach($arr_subdistricts as $arr_subdistrict){?>
                          <option value="<?php echo $arr_subdistrict['SubdistrictsId']?>" <?php if($arr_sipplier['supplier_subdistrict'] == $arr_subdistrict['SubdistrictsId']){echo "selected";}?>><?php echo $arr_subdistrict['NameInThai']?></option>
                        <?php }?> 
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รหัสไปษณีย์: </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="Zip" id='Zip' autocomplete="off" value="<?php echo $arr_sipplier['supplier_zip'];?>">
                    </div>
                  </div>
                  <div class="form-group row" id="zip_search" style="display: none">
                    <div class="col-lg-12">
                      <div class="example table-responsive">
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>จังหวัด</th>
                              <th>อำเภอ</th>
                              <th>แขวง/ตำบล</th>
                              <th>รหัสไปษณีย์</th>
                            </tr>
                          </thead>
                          <tbody id="province-list">
                            <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
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