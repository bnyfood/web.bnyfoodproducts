<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config Group</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <h4 class="example-title">Group</h4>
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
            <input type="hidden" name="id_en" id="id_en" value="<?php echo $id_en;?>">
            <a href="<?php echo base_url();?>config_system/usergroup/usergroup_list" id="addToTable" class="btn btn-outline btn-primary" >
                  <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                </a>
          </div>
        </div>  
      </div>
    </div>
  </div> 
</div>       