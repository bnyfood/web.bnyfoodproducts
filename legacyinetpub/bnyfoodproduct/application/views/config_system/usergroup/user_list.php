<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config Customer</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <h4 class="example-title">Customer</h4>
              <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."config_system/usergroup/usergroup_map";?>" method="post">
                <div class="row">
                  <div class="col-md-3">
                    <div class="mb-15">
                      <select class="form-control form-control-sm" name="usergroup_sel" id="usergroup_sel">
                          <option value="">กรุณาเลือก</option> 
                          <?php foreach($arr_usergroups as $arr_usergroup){?>
                            <option value="<?php echo $arr_usergroup['usergroup_id']?>"><?php echo $arr_usergroup['group_name']?></option>
                          <?php }?>  
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="mb-15">
                      <select class="form-control form-control-sm" name="user_sel" id="user_sel">
                        <option value="">กรุณาเลือก</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-15">
                      <input type="hidden" name="id_en" value="<?php echo $id_en?>">
                      <button type="submit" class="btn btn-primary"><i class="icon wb-plus" aria-hidden="true"></i> เพิ่มเข้ากลุ่ม</button>
                    </div> 
                  </div>
                </div>
              </form>
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ชื่อลูกค้า</th>
                      <th>ชื่อบริษัท</th>
                      <th>ที่อยู่</th>
                      <th>โทรศัพท์</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_users)){
                        foreach($arr_users as $arr_user){
                    ?>
                      <tr>
                        <td><?php echo $arr_user['Name']?></td>
                        <td><?php echo $arr_user['CompanyName']?></td>
                        <td><?php echo $arr_user['address1']?></td>
                        <td><?php echo $arr_user['Mobile']?></td>
                        <td><?php echo $arr_user['email']?></td>
                      </tr>
                    <?php }}?>
                  </tbody>
                </table>
                <a href="<?php echo base_url();?>config_system/usergroup/usergroup_list" id="addToTable" class="btn btn-outline btn-primary" >
                  <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                </a>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div> 
</div>       