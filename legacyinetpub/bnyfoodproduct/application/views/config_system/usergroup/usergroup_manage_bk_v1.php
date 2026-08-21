
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class="col-xxl-12 col-xl-8 col-lg-12">
          <div class='card'>
            <div class='card-body'>
              <h4 class="card-title project-title">
               Group
              <btn class="btn btn-pure btn-default icon wb-pencil btn-edit"></btn>
            </h4>
            <p class="card-text">
              ชื่อร้านค้า: <?php echo $arr_group['ShopName']?>
            </p>
            <p class="card-text">
              ชื่อกลุ่ม: <select class="form-control" name="manage_group_sel" id="manage_group_sel">
                        <?php foreach($arr_usergroups as $arr_usergroup){?>
                          <option value="<?php echo $arr_usergroup['usergroup_id']?>" <?php if( $arr_usergroup['usergroup_id'] == $arr_group['usergroup_id']){echo "selected";}?>><?php echo $arr_usergroup['group_name']?></option>
                        <?php }?> 
                     </select>
            </p>
            <a href="<?php echo base_url();?>config_system/usergroup/usergroup_list" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
            </a>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-xl-6 push-xl-3">
          <div class='card'>
            <div class='card-body'>
              <h4 class="card-title mb-0">พนักงาน</h4>
              <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."config_system/usergroup/usergroup_map";?>" method="post">
                <div class="row">
                  <div class="col-md-3">
                    <div class="mb-15">
                      <select class="form-control form-control-sm" name="user_sel" id="user_sel">
                          <option value="0">กรุณาเลือก</option> 
                          <?php foreach($arr_list_users as $arr_list_user){?>
                            <option value="<?php echo $arr_list_user['BNYCustomerID']?>"><?php echo $arr_list_user['Name']?></option>
                          <?php }?>  
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="mb-15">
                      <input type="hidden" name="id_en" id="id_en" value="<?php echo $id_en?>">
                      <button type="button" id="add_usertogroup" class="btn btn-primary" style="display: none;"><i class="icon wb-plus" aria-hidden="true"></i> เพิ่มเข้ากลุ่ม</button>
                    </div> 
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-12 col-xl-6 push-xl-3">
          <div class="card">
            <div class="card-body">
              <h4 >เมนู</h4>
            <?php
              if(!empty($data_groupmenus)){
                foreach ($data_groupmenus as $data_groupmenu){ ?>
                  <div class="row row-lg">
                    <div class="col-lg-12">
                      <div class="checkbox-custom checkbox-primary">
                        <input type="checkbox" name="groupmenu_id[]" id="groupmenu_id"  value="<?php echo $data_groupmenu['menu_id']?>" <?php if (in_array($data_groupmenu['menu_id'], $arr_menu_select)){echo "checked";}?>>
                        <label for="inputChecked"><?php echo $data_groupmenu['menu_name']?></label>
                      </div>
                    </div>
                   </div> 
                    <?php 
                      if(!empty($data_groupmenu['submenus'])){
                        foreach ($data_groupmenu['submenus'] as $data_groupsubmenu){
                    ?>
                  <div class="row row-lg">
                    <div class="col-lg-1">
                    </div>
                    <div class="col-lg-10">
                      <div class="checkbox-custom checkbox-primary">
                        <input type="checkbox" name="groupmenu_id[]" id="groupmenu_id" value="<?php echo $data_groupsubmenu['menu_id']?>" <?php if (in_array($data_groupsubmenu['menu_id'], $arr_menu_select)){echo "checked";}?>>
                        <label for="inputChecked"><?php echo $data_groupsubmenu['menu_name']?></label>
                      </div>
                    </div>  
                  </div>    
                  <?php 
                      if(!empty($data_groupsubmenu['lv3_submenus'])){
                        foreach ($data_groupsubmenu['lv3_submenus'] as $data_groupsubmenulv3){
                    ?>
                  <div class="row row-lg">
                    <div class="col-lg-2">
                    </div>
                    <div class="col-lg-10">
                      <div class="checkbox-custom checkbox-primary">
                        <input type="checkbox" name="groupmenu_id[]" id="groupmenu_id" value="<?php echo $data_groupsubmenulv3['menu_id']?>" <?php if (in_array($data_groupsubmenulv3['menu_id'], $arr_menu_select)){echo "checked";}?>>
                        <label for="inputChecked"><?php echo $data_groupsubmenulv3['menu_name']?></label>
                      </div>
                    </div>  
                  </div>  
            <?php }}}}}}?>
            </div>
          </div>
        </div>
      </div>
</div>


