<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <a href="<?php echo base_url();?>automation/windows_and_door/add_form" id="addToTable" class="btn btn-outline btn-primary" >
              <i class="fa fa-plus"></i> เพิ่ม windows and door
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
                      <th>Room1</th>
                      <th>Room2</th>
                      <th>Type</th>
                      <th>Ground offset</th>
                      <th>X</th>
                      <th>Y</th>
                      <th>R</th>
                      <th>Ceta</th>
                      <th>Heading</th>
                      <th>Width</th>
                      <th>Height</th>
                      <th>Thickness</th>
                      <th>Description</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_datas)){
                        foreach($arr_datas as $arr_data){
                    ?>
                      <tr>
                        <td><?php echo $arr_data['room1']?></td>
                        <td><?php echo $arr_data['room2']?></td>
                        <td><?php echo $arr_data['type']?></td>
                        <td><?php echo $arr_data['ground_offset']?></td>
                        <td><?php echo $arr_data['x']?></td>
                        <td><?php echo $arr_data['y']?></td>
                        <td><?php echo $arr_data['r']?></td>
                        <td><?php echo $arr_data['ceta']?></td>
                        <td><?php echo $arr_data['heading']?></td>
                        <td><?php echo $arr_data['width']?></td>
                        <td><?php echo $arr_data['height']?></td>
                        <td><?php echo $arr_data['thickness']?></td>
                        <td><?php echo $arr_data['description']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>automation/windows_and_door/edit_form/<?php echo $arr_data['windows_and_door_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="fa fa-edit"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>automation/windows_and_door/del_action/<?php echo $arr_data['windows_and_door_id'];?>"><i class="fa fa-times" aria-hidden="true"></i></button>
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


