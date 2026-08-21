<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/map/add_map_form" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus"></i> เพิ่ม Map
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
                      <th>Pic</th>
                      <th>Path</th>
                      <th>Ratio</th>
                      <th>Width</th>
                      <th>Grid size</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_maps)){
                        foreach($arr_maps as $arr_map){
                    ?>
                      <tr>
                        <td><img src="<?php echo base_url();?><?php echo $arr_map['path']?>" width="150px"></td>
                        <td><?php echo $arr_map['path']?></td>
                        <td><?php echo $arr_map['ratio']?></td>
                        <td><?php echo $arr_map['actual_map_width']?></td>
                        <td><?php echo $arr_map['grid_size']?></td>
                        <td class="text-nowrap">
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>automation/map/del_action/<?php echo $arr_map['amt_map_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
                          <a href="<?php echo base_url();?>automation/map/genmap/<?php echo $arr_map['amt_map_id'];?>" data-toggle="tooltip" data-original-title="Manage"> 
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


