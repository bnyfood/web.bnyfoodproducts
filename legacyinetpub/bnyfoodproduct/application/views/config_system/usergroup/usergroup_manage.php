
<div class='dashboard-content usergroup-manage-page'>
    <div class='container'>
      <div class="row">
        <div class="col-xxl-12 col-xl-8 col-lg-12">
          <div class='card'>
            <div class='card-body usergroup-manage-body'>
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
            <a href="<?php echo base_url();?>users/gen_link_user_regis/<?php echo $id_en;?>" target="_blank" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="icon wb-arrow-left" aria-hidden="true"></i> Create new user
            </a>
            <input type="hidden" name="id_en" id="id_en" value="<?php echo $id_en?>">
            </div>
          </div>
        </div>
      </div>
      <div class="row usergroup-manage-columns" style="flex-direction:row;">
        <div class="col-lg-12 col-xl-6">
          <div class="card">
            <div class="card-body usergroup-manage-body">
              <h4>เมนู</h4>
              <div class="usergroup-menu-content">
            <?php
              $reward_menu_id = 10068;
              $reward_root_menu = null;
              $is_reward_menu = function($menu_item) use ($reward_menu_id) {
                $menu_id = !empty($menu_item['menu_id']) ? (int)$menu_item['menu_id'] : 0;
                $menu_name = !empty($menu_item['menu_name']) ? trim($menu_item['menu_name']) : '';
                $menu_link = '';
                if (!empty($menu_item['link'])) {
                  $menu_link = (string)$menu_item['link'];
                } elseif (!empty($menu_item['menu_link'])) {
                  $menu_link = (string)$menu_item['menu_link'];
                } elseif (!empty($menu_item['url'])) {
                  $menu_link = (string)$menu_item['url'];
                }
                $is_by_id = ($menu_id === $reward_menu_id);
                $is_by_name = ($menu_name !== '' && (mb_strpos($menu_name, 'จัดการรางวัล') !== false || mb_strpos($menu_name, 'รางวัล') !== false));
                $is_by_link = ($menu_link !== '' && stripos($menu_link, 'bnyadminreward') !== false);
                return ($is_by_id || $is_by_name || $is_by_link);
              };
              if (!empty($data_groupmenus)) {
                foreach ($data_groupmenus as $tmp_menu) {
                  if ($is_reward_menu($tmp_menu)) {
                    $reward_root_menu = $tmp_menu;
                    break;
                  }

                  if (!empty($tmp_menu['submenus'])) {
                    foreach ($tmp_menu['submenus'] as $tmp_submenu) {
                      if ($is_reward_menu($tmp_submenu)) {
                        $reward_root_menu = $tmp_submenu;
                        break 2;
                      }

                      if (!empty($tmp_submenu['lv3_submenus'])) {
                        foreach ($tmp_submenu['lv3_submenus'] as $tmp_lv3_submenu) {
                          if ($is_reward_menu($tmp_lv3_submenu)) {
                            $reward_root_menu = $tmp_lv3_submenu;
                            break 3;
                          }
                        }
                      }
                    }
                  }
                }
              }
              if(!empty($data_groupmenus)){
                foreach ($data_groupmenus as $data_groupmenu){ ?>
                  <?php
                    if ($is_reward_menu($data_groupmenu)) { continue; }
                  ?>
                  <div class="row row-lg" style="flex-direction:row;">
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
                          $submenu_name = !empty($data_groupsubmenu['menu_name']) ? trim($data_groupsubmenu['menu_name']) : '';
                          if ($is_reward_menu($data_groupsubmenu)) { continue; }
                    ?>
                  <div class="row row-lg" style="flex-direction:row;">
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
                            if ($is_reward_menu($data_groupsubmenulv3)) { continue; }
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
                  <?php
                          }
                        }

                        if (empty($reward_root_menu)) {
                          $reward_root_menu = array(
                            'menu_id' => $reward_menu_id,
                            'menu_name' => 'จัดการรางวัล'
                          );
                        }
                        if (!empty($reward_root_menu) && strcasecmp($submenu_name, 'CRM') === 0) {
                  ?>
                  <div class="row row-lg">
                    <div class="col-lg-2">
                    </div>
                    <div class="col-lg-10">
                      <div class="checkbox-custom checkbox-primary">
                        <input type="checkbox" name="groupmenu_id[]" id="groupmenu_id" value="<?php echo $reward_root_menu['menu_id']?>" <?php if (in_array($reward_root_menu['menu_id'], $arr_menu_select)){echo "checked";}?>>
                        <label for="inputChecked"><?php echo $reward_root_menu['menu_name']?></label>
                      </div>
                    </div>
                  </div>
                  <?php
                        }
                      }
                    }
                  }
              }
            ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12 col-xl-6" style="min-width:0;">
          <div class="card">
            <div class="card-body usergroup-manage-body">
              <h4>User</h4>
              <div class="row mb-15 usergroup-user-controls">
                <div class="col-md-6">
                  <select class="form-control form-control-sm" name="user_sel" id="user_sel">
                    <option value="0">กรุณาเลือก</option>
                    <?php if(!empty($arr_list_users)){ foreach($arr_list_users as $arr_list_user){ ?>
                      <option value="<?php echo $arr_list_user['BNYCustomerID']?>"><?php echo $arr_list_user['Name']?></option>
                    <?php }} ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <button type="button" id="add_usertogroup" class="btn btn-primary" style="display: none;"><i class="icon wb-plus" aria-hidden="true"></i> ADD USER</button>
                </div>
              </div>
              <div class="usergroup-user-list">
                <div class="usergroup-user-list-head">
                  <span>ชื่อ</span>
                  <span></span>
                </div>
                <ul class="list-group list-group-dividered list-group-full mb-0" id="usergroup-list">
                  <?php if(!empty($arr_users)){ foreach($arr_users as $arr_user){ ?>
                    <li class="list-group-item usergroup-user-item">
                      <span class="usergroup-user-name" title="<?php echo htmlspecialchars($arr_user['Name'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $arr_user['Name']?></span>
                      <button class="btn btn-sm btn-icon btn-flat btn-danger" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url().'config_system/usergroup/move_usergroup_map/'.$arr_user['BNYCustomerID'].'/'.$id_en; ?>">
                        <i class="icon icon-xs wb-trash mr-0"></i>
                      </button>
                    </li>
                  <?php }} ?>
                </ul>
              </div>
              <style>
                .usergroup-manage-page .usergroup-manage-body {
                  padding: 24px 28px 28px 28px;
                }
                .usergroup-manage-page .usergroup-manage-body > h4 {
                  margin-top: 4px;
                  margin-bottom: 18px;
                }
                .usergroup-manage-page .usergroup-manage-body .card-text {
                  margin-bottom: 14px;
                }
                .usergroup-manage-page .usergroup-manage-columns {
                  margin-top: 8px;
                }
                .usergroup-manage-page .usergroup-menu-content {
                  padding: 4px 0 0 8px;
                }
                .usergroup-manage-page .usergroup-user-controls {
                  padding-left: 4px;
                  margin-bottom: 18px;
                }
                .usergroup-user-list { width: 100%; }
                .usergroup-user-list-head {
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  padding: 10px 20px;
                  font-weight: 500;
                  color: #76838f;
                  border-bottom: 1px solid #e4eaec;
                  background: #f3f7f9;
                }
                .usergroup-user-item {
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  gap: 8px;
                  padding: 12px 20px;
                }
                .usergroup-user-name {
                  flex: 1;
                  min-width: 0;
                  overflow: hidden;
                  text-overflow: ellipsis;
                  white-space: nowrap;
                }
                .usergroup-user-item .btn {
                  flex-shrink: 0;
                }
              </style>
            </div>
          </div>
        </div>
      </div>
</div>


