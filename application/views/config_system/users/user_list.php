<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config Employee</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <h4 class="example-title">พนักงาน</h4>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-15">
                    <a href="<?php echo base_url();?>config_system/users/add_user_form" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มพนักงาน
                    </a>
                  </div>
                </div>
              </div>
            <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มพนักงานสำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มพนักงานไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไขพนักงานสำเร็จ
              </div>
            <?php }?>  
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ชื่อพนักงาน</th>
                      <th>ชื่อบริษัท</th>
                      <th>ที่อยู่</th>
                      <th>โทรศัพท์</th>
                      <th>Email</th>
                      <th class="text-nowrap">จัดการ</th>
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
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/users/user_edit_form/<?php echo $arr_user['BNYCustomerID'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/users/del_action/<?php echo $arr_user['BNYCustomerID'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php }}?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div> 
</div>       