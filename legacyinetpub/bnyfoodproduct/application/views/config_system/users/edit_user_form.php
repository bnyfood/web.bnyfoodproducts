<div class="page">
  <div class="page-header">
    <h1 class="page-title">แก้ไขพนักงาน</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_edit_form" id="user_edit_form" action="<?php echo base_url()."config_system/users/user_edit";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">พนักงาน</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อพนักงาน : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="text_name" id='text_name'  autocomplete="off" value="<?php echo $arr_user['Name']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">เบอร์โทร1 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Mobile" id='Mobile'  autocomplete="off" value="<?php echo $arr_user['Mobile']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ที่อยู่1 : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="address1"><?php echo $arr_user['address1']?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">อำเภอ : </label>
                    <div class="col-md-6">
                      <select class="form-control form-control-sm" name="district_sel" id="district_sel">
                        <option value="">กรุณาเลือก</option> 
                        <?php foreach($arr_districts as $arr_district){?>
                          <option value="<?php echo $arr_district['DistrictId']?>" <?php if($arr_user['DistrictId'] == $arr_district['DistrictId']){echo "selected";}?>><?php echo $arr_district['NameInThai']?></option>
                        <?php }?> 
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รหัสไปษณีย์: </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="Zip" id='Zip'  autocomplete="off" value="<?php echo $arr_user['Zip']?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/users/user_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" value="<?php echo $arr_user['BNYCustomerID']?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">
            <div class="example-wrap">
              <h4 class="example-title">&nbsp</h4>
              <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อบริษัท : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="CompanyName" id='CompanyName'  autocomplete="off" value="<?php echo $arr_user['CompanyName']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">เบอร์โทร2 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Mobile2" id='Mobile2'  autocomplete="off" value="<?php echo $arr_user['Mobile2']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Line : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Line" id='Line'  autocomplete="off" value="<?php echo $arr_user['Line']?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">จังหวัด : </label>
                    <div class="col-md-6">
                      <select class="form-control form-control-sm" name="province_sel" id="province_sel">
                        <option value="">กรุณาเลือก</option>
                        <?php foreach($arr_provinces as $arr_province){?>
                          <option value="<?php echo $arr_province['ProvinceID']?>" <?php if($arr_user['ProvinceID'] == $arr_province['ProvinceID']){echo "selected";}?>><?php echo $arr_province['NameInThai']?></option>
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
                          <option value="<?php echo $arr_subdistrict['SubdistrictsId']?>" <?php if($arr_user['SubdistrictsId'] == $arr_subdistrict['SubdistrictsId']){echo "selected";}?>><?php echo $arr_subdistrict['NameInThai']?></option>
                        <?php }?> 
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Tax : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Tax" id='Tax'  autocomplete="off" value="<?php echo $arr_user['Tax']?>">
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