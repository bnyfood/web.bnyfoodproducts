
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class="col-xxl-12 col-xl-12 col-lg-12">
          <div class='card'>
            <div class='card-body'>
              
              <div class="m-4">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="nav-item">
                        <a href="#home" class="nav-link active">MAP</a>
                    </li>
                    <li class="nav-item">
                        <a href="#blocks" class="nav-link">Blocks</a>
                    </li>
                    <li class="nav-item">
                        <a href="#profile" class="nav-link">Robot</a>
                    </li>
                    <li class="nav-item">
                        <a href="#messages" class="nav-link">Messages</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="home">
                        <table class="table">
                          <tr>

                            <td>MapWidth:&nbsp;<input type="text" id="actual_map_width" name="actual_map_width" value="<?php echo $data_map['actual_map_width'];?>">Meters</td>
                            <td>

                              <input type="hidden" id="img_width" name="img_width" >
                              <input type="hidden" id="img_height" name="img_height" >
                              <input type="hidden" id="canvas_width" name="canvas_width" >
                              <input type="hidden" id="canvas_height" name="canvas_height" >

                              <input type="hidden" id="amt_map_id" name="amt_map_id" value="<?php echo $data_map['amt_map_id'];?>">
                                

                              GridOn:&nbsp;<input type="checkbox" id="grid_status" name="grid_status" value=1 <?php echo ($data_map['grid_status'] == 1 ? "checked" : ""); ?>></td>
                            <td>SnapGrid:&nbsp;<input type="checkbox" id="grid_snap" name="grid_snap" value=1 <?php echo ($data_map['grid_snap'] == 1 ? "checked" : ""); ?>></td>
                            <td>GridZize:&nbsp;<select name="grid_size" id="grid_size">
                              <?php
                              $db_gridsize=floatval($data_map['grid_size']);
                              
                              for($i=0.05;$i<=1;$i=$i+0.01)
                              {
                                
                                if(bccomp(floatval($i), $db_gridsize, 3)==0)
                                {
                                  
                                  ?>
                                  <option value="<?php echo $i;?>" selected ><?php echo $i;?></option>
                                  <?php
                                 }
                                else
                                {
                                  ?>
                                  <option value="<?php echo $i;?>" ><?php echo $i;?></option>
                                  <?php
                                  
                                }
                                
                                ?>
                                
                               <?php
                               }  
                               ?>
</select>&nbsp;Meters</td>
                            
                          </tr>
                        </table>

                       
                        
                    </div>
                    <div class="tab-pane fade" id="blocks">
                        <div class='container' style="padding: 5 5 5 5!important;">
                        <div class="row">
                               <div class="col-xxl-12 col-xl-12 col-lg-12" style="display: flex; flex-direction:row!important; padding: 5 5 5 5!important; justify-content: flex-start;" >
                                <div style="display: flex; flex-direction:row!important;">BlockSize:&nbsp;<input type="number" step="0.05" min="0.1" class="form-control" name="block_size" id='block_size'  autocomplete="off" style=" width: 100px;">Meters</div>
                                <div style="display: flex; flex-direction:row!important; padding: 5 5 5 5!important; justify-content: flex-start;"><button type="button" class="btn btn-primary" onClick="addNewBlock(<?php echo $data_map['ShopID'];?>,<?php echo $data_map['amt_map_id'];?>,<?php echo base_url();?>automation/add_block)">CreateBlock</button></div>
                               </div>        
                        </div>
                        </div>       
                    </div>
                    <div class="tab-pane fade" id="profile">
                        <h4 class="mt-2">Profile tab content</h4>
                        <p>Vestibulum nec erat eu nulla rhoncus fringilla ut non neque. Vivamus nibh urna, ornare id gravida ut, mollis a magna. Aliquam porttitor condimentum nisi, eu viverra ipsum porta ut. Nam hendrerit bibendum turpis, sed molestie mi fermentum id. Aenean volutpat velit sem. Sed consequat ante in rutrum convallis. Nunc facilisis leo at faucibus adipiscing.</p>
                    </div>
                    <div class="tab-pane fade" id="messages">
                        <h4 class="mt-2">Messages tab content</h4>
                        <p>Donec vel placerat quam, ut euismod risus. Sed a mi suscipit, elementum sem a, hendrerit velit. Donec at erat magna. Sed dignissim orci nec eleifend egestas. Donec eget mi consequat massa vestibulum laoreet. Mauris et ultrices nulla, malesuada volutpat ante. Fusce ut orci lorem. Donec molestie libero in tempus imperdiet. Cum sociis natoque penatibus et magnis.</p>
                    </div>
                </div>
              </div>   

            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-xl-12 col-xxl-12 push-xl-3 push-xxl-3">
          <div class='card' >

            <div class='card-body' id="map" imgpath="<?php echo base_url();?><?php echo $data_map['path']?>" imgwidth="<?php echo $data_map['img_map_width']?>" imgheight="<?php  echo $data_map['img_map_height']?>">
              <canvas id="canvas" ></canvas>
              
               
            </div>
          </div>
        </div>
      </div>
</div>


