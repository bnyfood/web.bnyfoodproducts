<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/silo/silo_add_form" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus"></i> เพิ่ม Silo
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
                      <th>Name</th>
                      <th>Width</th>
                      <th>Height</th>
                      <th>Length</th>
                      <th>Type</th>
                      <th>Ground offset</th>
                      <th>Load capacity(Liters)</th>
                      <th>Unit</th>
                      <th>No. of silo(s)</th>
                      <th>Description</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_silos)){
                        foreach($arr_silos as $arr_silo){
                    ?>
                      <tr>
                        <td><?php echo $arr_silo['silo_name']?></td>
                        <td><?php echo $arr_silo['silo_width']?></td>
                        <td><?php echo $arr_silo['silo_height']?></td>
                        <td><?php echo $arr_silo['silo_length']?></td>
                        <td><?php echo $arr_silo['silo_type']?></td>
                        <td><?php echo $arr_silo['ground_offset']?></td>
                        <td><?php echo $arr_silo['silo_load_capacity']?></td>
                        <td>
                          <?php 
                            if($arr_silo['silo_unit'] == '1'){
                              echo "kg/m";
                            }elseif($arr_silo['silo_unit'] == '2'){
                              echo "L/m";
                            }
                          ?>
                          </td>
                        <td><?php echo count($arr_silo['sub_silo']);?></td>
                        <td><?php echo $arr_silo['description']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>automation/silo/silo_edit_form/<?php echo $arr_silo['silo_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>automation/silo/del_action/<?php echo $arr_silo['silo_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


