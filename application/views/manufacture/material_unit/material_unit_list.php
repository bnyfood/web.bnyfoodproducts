<div class="page">
  <div class="page-header">
    <h1 class="page-title">Material Unit</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="material_unit_search_form" id="material_unit_search_form" action="<?php echo base_url()."manufacture/material_unit/material_unit_list_search";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
              <div class="panel-body">
                <h4 class="example-title">ค้นหา</h4>
                <div class="row">
                  <div class="col-md-12">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                      <input type="text" class="form-control" id="material_unit_search" name="material_unit_search" placeholder="Search..." value="<?php echo $data_search['material_unit_search']?>" style="width:20%;">
                      <button type="submit" class="btn btn-primary">ค้นหา</button>
                      <a href="<?php echo base_url();?>manufacture/material_unit/material_unit_list" id="addToTable" class="btn btn-default">ทั้งหมด</a>
                      <a href="<?php echo base_url();?>manufacture/material_unit/add_material_unit_form" id="addToTable" class="btn btn-outline btn-primary" >
                        <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Material Unit
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="example-wrap">
          <?php if($add_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              เพื่ม Material Unit สำเร็จ
            </div>
          <?php }?>
          <?php if($add_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              เพื่ม Material Unit ไม่สำเร็จ
            </div>
          <?php }?>
          <?php if($edit_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              แก้ไข Material Unit สำเร็จ
            </div>
          <?php }?>
          <?php if($edit_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              แก้ไข Material Unit ไม่สำเร็จ
            </div>
          <?php }?>
          <div class="example table-responsive" id="highlighting">
            <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
              <thead>
                <tr>
                  <th>Material Unit
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('material_unit','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('material_unit','desc',1)"></i>
                  </th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_units)){
                  foreach($arr_units as $arr_unit){
              ?>  
              <tr>
                <td><?php echo $arr_unit['material_unit']?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>manufacture/material_unit/edit_material_unit_form/<?php echo $arr_unit['web_material_unit_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                </td>
              </tr>          
            <?php }}?>
              <input type="hidden" name="offset" id="offset" value="0">
              <input type="hidden" name="sortby" id="sortby" value="<?php echo $data_search['sortby']?>">
              <input type="hidden" name="sorttype" id="sorttype" value="<?php echo $data_search['sorttype']?>">
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>
