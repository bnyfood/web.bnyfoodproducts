
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>config_system/usergroup/add_usergroup_form" id="addToTable" class="btn btn-outline btn-primary" >
                    <i class="fa fa-plus"></i> เพิ่มกลุ่ม
                  </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
              <div class="table-responsive-sm">
                <table class="table table-striped">
                  <thead >
                    <tr>
                      <th>ชื่อกลุ่ม</th>
                      <th>Add</th>
                      <th>Edit</th>
                      <th>Delete</th>
                      <th>Read</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_usergroups)){
                        foreach($arr_usergroups as $arr_usergroup){
                    ?>
                      <tr>
                        <td><?php echo $arr_usergroup['group_name']?></td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php if($arr_usergroup['is_add'] == 1){echo "checked";}?>>
                            <!--
                            <label class="form-check-label" for="flexCheckDefault">
                              Default checkbox
                            </label> 
                            !-->

                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php if($arr_usergroup['is_edit'] == 1){echo "checked";}?>>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php if($arr_usergroup['is_del'] == 1){echo "checked";}?>>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php if($arr_usergroup['is_read'] == 1){echo "checked";}?>>
                          </div>
                        </td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/usergroup/usergroup_edit_form/<?php echo $arr_usergroup['usergroup_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/usergroup/del_action/<?php echo $arr_usergroup['usergroup_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
                          <a href="<?php echo base_url();?>config_system/usergroup/usergroup_manage/<?php echo $arr_usergroup['usergroup_id'];?>" data-toggle="tooltip" data-original-title="Manage"> 
                            <i class="fa fa-bars" ></i>
                          </a>
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


