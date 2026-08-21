
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>app_plan/module_set/add_module_set_form" id="addToTable" class="btn btn-outline btn-primary" >
                    <i class="fa fa-plus"></i> เพิ่ม Module Set
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
                      <th>Module Set Name</th>
                      <th>Mobule</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_module_sets)){
                        foreach($arr_module_sets as $arr_module_set){
                    ?>
                      <tr>
                        <td><?php echo $arr_module_set['module_set_name']?></td>
                        <td>
                            <?php
                                if(!empty($arr_module_set['bny_module_map'])){
                                    foreach($arr_module_set['bny_module_map'] as $bny_module_map){
                                        echo $bny_module_map['module_name']."<br>";
                                    }
                                }
                            ?>
                        </td>
                        <td class="text-nowrap">
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>app_plan/module_set/del_action/<?php echo $arr_module_set['bny_module_set_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


