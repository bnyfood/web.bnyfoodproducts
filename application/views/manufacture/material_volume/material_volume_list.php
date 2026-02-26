<div class="page">
  <div class="page-header">
    <h1 class="page-title">Material Volume</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="example-wrap">
          <?php if($add_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              เพื่ม Material Volume สำเร็จ
            </div>
          <?php }?>
          <?php if($add_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              เพื่ม Material Volume ไม่สำเร็จ
            </div>
          <?php }?>
          <?php if($edit_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              แก้ไข Material Volume สำเร็จ
            </div>
          <?php }?>
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="#" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
              <div class="panel-body">
                <h4 class="example-title">ค้นหา</h4>
                <div class="row">
                  <div class="col-md-12">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                      <input type="text" class="form-control" id="material_search" name="material_search" placeholder="Search..." value="<?php echo $data_search['material_search']?>" style="width:20%;">
                      <button type="submit" class="btn btn-primary">ค้นหา</button>
                      <a href="<?php echo base_url();?>manufacture/material_volume/material_volume_list" id="addToTable" class="btn btn-default">ทั้งหมด</a>
                      <a href="<?php echo base_url();?>manufacture/material_volume/add_material_volume_form" id="addToTable" class="btn btn-outline btn-primary" >
                        <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Material Volume
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
          <div class="example table-responsive" id="highlighting">
            <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
              <thead>
                <tr>
                  <th>Material
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('material_name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('material_name','desc',1)"></i>
                  </th>
                  <th>ประเภท
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_material_volume_type','asc',2)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_material_volume_type','desc',3)"></i>
                  </th>
                  <th>จำนวน
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_material_volume_unit','asc',4)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_material_volume_unit','desc',5)"></i>
                  </th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="material-volume-list">
              <?php 
                  if(!empty($arr_materials)){
                  foreach($arr_materials as $arr_material){
              ?>  
              <tr>
                <td><?php echo $arr_material['material_name'];?></td>
                <td><?php echo $arr_material['web_material_volume_type']?></td>
                <td><?php echo $arr_material['web_material_volume_unit']?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>manufacture/material_volume/edit_material_volume_form/<?php echo $arr_material['web_material_volume_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>manufacture/material_volume/del_action/<?php echo $arr_material['web_material_volume_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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