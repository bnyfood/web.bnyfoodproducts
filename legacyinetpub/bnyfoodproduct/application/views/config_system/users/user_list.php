<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config Employee</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="user_search_form" id="user_search_form" action="<?php echo base_url()."config_system/users/user_list_search";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
              <div class="panel-body">
                <h4 class="example-title">ค้นหา</h4>
                <div class="row">
                  <div class="col-md-12">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                      <input type="text" class="form-control" id="user_search" name="user_search" placeholder="Search..." value="<?php echo $data_search['user_search']?>" style="width:20%;">
                      <button type="submit" class="btn btn-primary">ค้นหา</button>
                      <a href="<?php echo base_url();?>config_system/users/user_list" id="addToTable" class="btn btn-default">ทั้งหมด</a>
                      <a href="<?php echo base_url();?>config_system/users/add_user_form" id="addToTable" class="btn btn-outline btn-primary" >
                        <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มพนักงาน
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="example-wrap">
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
          <div class="example table-responsive" id="highlighting">
            <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
              <thead>
                <tr>
                  <th>ชื่อพนักงาน
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('Name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('Name','desc',1)"></i>
                  </th>
                  <th>ชื่อบริษัท
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('CompanyName','asc',2)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('CompanyName','desc',3)"></i>
                  </th>
                  <th>ที่อยู่</th>
                  <th>โทรศัพท์
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('Mobile','asc',4)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('Mobile','desc',5)"></i>
                  </th>
                  <th>Email
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('email','asc',6)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('email','desc',7)"></i>
                  </th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
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
                <input type="hidden" name="offset" id="offset" value="0">
                <input type="hidden" name="sortby" id="sortby" value="<?php echo $data_search['sortby']?>">
                <input type="hidden" name="sorttype" id="sorttype" value="<?php echo $data_search['sorttype']?>">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       