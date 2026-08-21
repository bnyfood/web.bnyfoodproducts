<div class="page">
  <div class="page-header">
    <h1 class="page-title">Page permission</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-15">
                    <a href="<?php echo base_url();?>config_system/page_permission/add_permission_form" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มสิทธิ์
                    </a>
                  </div>
                </div>
              </div>
            <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มสำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไขสำเร็จ
              </div>
            <?php }?>  
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Controller</th>
                      <th>User Type</th>
                      <th>Add</th>
                      <th>Edit</th>
                      <th>Delete</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_levels)){
                        foreach($arr_levels as $arr_level){
                    ?>
                      <tr>
                        <td><?php echo $arr_level['controller']?></td>
                        <td>
                          <?php if($arr_level['user_level'] == 1){
                            echo "User";
                           }elseif($arr_level['user_level'] == 2){
                            echo "Super User";
                           }elseif($arr_level['user_level'] == 3){
                            echo "Admin";
                           }
                          ?>
                        </td>
                        <td>
                          <?php if($arr_level['user_add'] == 1){?>
                            <i class="icon wb-check ml-10 green-600" aria-hidden="true" data-toggle="tooltip" data-original-title="Reciept" data-container="body" title=""></i>
                          <?php }else{?>
                            <i class="icon wb-close ml-10 red-600" aria-hidden="true" data-toggle="tooltip" data-original-title="No reciept" data-container="body" title=""></i>
                          <?php }?> 
                        </td>
                        <td>
                          <?php if($arr_level['user_edit'] == 1){?>
                            <i class="icon wb-check ml-10 green-600" aria-hidden="true" data-toggle="tooltip" data-original-title="Reciept" data-container="body" title=""></i>
                          <?php }else{?>
                            <i class="icon wb-close ml-10 red-600" aria-hidden="true" data-toggle="tooltip" data-original-title="No reciept" data-container="body" title=""></i>
                          <?php }?> 
                        </td>
                        <td>
                          <?php if($arr_level['user_delete'] == 1){?>
                            <i class="icon wb-check ml-10 green-600" aria-hidden="true" data-toggle="tooltip" data-original-title="Reciept" data-container="body" title=""></i>
                          <?php }else{?>
                            <i class="icon wb-close ml-10 red-600" aria-hidden="true" data-toggle="tooltip" data-original-title="No reciept" data-container="body" title=""></i>
                          <?php }?> 
                        </td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/page_permission/edit_permission_form/<?php echo $arr_level['user_level_authen_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/page_permission/del_action/<?php echo $arr_level['user_level_authen_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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