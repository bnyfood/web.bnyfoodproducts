<div class="page">
  <div class="page-content container-fluid">
    <div class="row">
      <div class="col-xxl-12 col-xl-12 col-lg-12">
        <div class="card">
          <div class="card-block">
            <div class="alert alert-success alert-dismissible" role="alert" id="alt_action" style="display: none">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มสำเร็จ
              </div>
              <h4 class="card-title project-title">
                 Robot
                <btn class="btn btn-pure btn-default icon wb-pencil btn-edit"></btn>
              </h4>
              <p class="card-text">
                Robot : <select  name="robotID" id="robotID">
                          <?php foreach($arr_robots as $arr_robot){?>
                            <option value="<?php echo $arr_robot['robotID']?>"><?php echo $arr_robot['name']?></option>
                          <?php }?>  
                      </select>
                Target x: <input type="number"  name="targetx" id="targetx">
                Target y: <input type="number" name="targety" id="targety">
                Target heading: <input type="number" name="targetheading" id="targetheading">
                <button type="button" class="btn btn-primary" id="btn_position"> <i class="icon wb-plus" aria-hidden="true"></i>Position</button>
              </p>
              <a href="<?php echo base_url();?>robot/robot_list" id="addToTable" class="btn btn-outline btn-primary" >
                    <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                  </a>
          </div>
        </div>
      </div>
    </div>
   
    <div class="row">
      <div class="col-lg-12 col-xl-12 push-xl-3">
        <div class="card user-visitors">
          <div class="card-header card-header-transparent p-20">
            <h4 class="card-title mb-0">Robot Path</h4>
          </div>
          <div class="card-block" id="card-block" style="width:1000px" >

            <div style="position: relative;" id="mastercanvas"></div>
 



            

          </div>
        </div>
      </div>
    </div>
   </div> 
</div>       

