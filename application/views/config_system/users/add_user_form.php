<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่มพนักงาน</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."config_system/users/user_add";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูลพนักงาน</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อพนักงาน : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="text_name" id='text_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อบริษัท : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="CompanyName" id='CompanyName'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">เบอร์โทร1 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Mobile" id='Mobile'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">เบอร์โทร2 : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Mobile2" id='Mobile2'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Line : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Line" id='Line'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Tax : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Tax" id='Tax'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Email : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="txt_email" id='txt_email'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Password: </label>
                    <div class="col-md-6">
                      <input type="password" class="form-control" name="txt_password" id='register_password'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Confirm Password: </label>
                    <div class="col-md-6">
                      <input type="password" class="form-control" name="txt_repassword" id='txt_repassword'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/users/user_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">
            <div class="example-wrap">
              <h4 class="example-title">ที่อยู่</h4>
              <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ที่อยู่ : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="address1"></textarea>
                    </div>
                  </div>
                  
                  
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">จังหวัด : </label>
                    <div class="col-md-6">
                      <select class="form-control" name="province_sel" id="province_sel">
                        <option value="">กรุณาเลือก</option> 
                        <?php foreach($arr_provinces as $arr_province){?>
                          <option value="<?php echo $arr_province['ProvinceID']?>"><?php echo $arr_province['NameInThai']?></option>
                        <?php }?>  
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">อำเภอ : </label>
                    <div class="col-md-6">
                      <select class="form-control" name="district_sel" id="district_sel">
                        <option value="">กรุณาเลือก</option> 
                    </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">แขวง/ตำบล:</label>
                    <div class="col-md-6">
                      <select class="form-control" name="subdistrict_sel" id="subdistrict_sel">
                        <option value="">กรุณาเลือก</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รหัสไปษณีย์: </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="Zip" id='Zip' autocomplete="off">
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