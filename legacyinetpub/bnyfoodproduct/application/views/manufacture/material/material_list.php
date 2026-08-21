<div class="page">
  <div class="page-header">
    <h1 class="page-title">Material</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."manufacture/material/material_list_search";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
              <div class="panel-body">
                <h4 class="example-title">ค้นหา</h4>
                <div class="row">
                  <div class="col-md-12">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                      <input type="text" class="form-control" id="material_search" name="material_search" placeholder="Search..." value="<?php echo $data_search['material_search']?>" style="width:20%;">
                      <button type="submit" class="btn btn-primary">ค้นหา</button>
                      <a href="<?php echo base_url();?>manufacture/material/material_list" id="addToTable" class="btn btn-default">ทั้งหมด</a>
                      <a href="<?php echo base_url();?>manufacture/material/add_material_form" id="addToTable" class="btn btn-outline btn-primary" >
                        <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Material
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
              เพื่ม Material สำเร็จ
            </div>
          <?php }?>
          <?php if($add_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              เพื่ม Material ไม่สำเร็จ
            </div>
          <?php }?>
          <?php if($edit_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              แก้ไข Material สำเร็จ
            </div>
          <?php }?>
          <div class="example table-responsive" id="highlighting">
            <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
              <thead>
                <tr>
                  <th>Material
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('material_name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('material_name','desc',1)"></i>
                  </th>
                  <th>ยี่ห้อ
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('material_brand_name','asc',2)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('material_brand_name','desc',3)"></i>
                  </th>
                  <th>Supplier
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_name','asc',4)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_name','desc',5)"></i>
                  </th>
                  <th>Price
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('material_unit_price','asc',6)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('material_unit_price','desc',7)"></i>
                  </th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_materials)){
                  foreach($arr_materials as $arr_material){
              ?>  
              <tr>
                <td><?php echo $arr_material['material_name']." ".$arr_material['material_size']." ".$arr_material['material_unit']?></td>
                <td><?php echo $arr_material['material_brand_name']?></td>
                <td><?php echo $arr_material['supplier_name']?></td>
                <td><input type="text" name="unit_price" id="<?php echo $arr_material['material_map_supplier_id']?>" class="form-control unit_price" value="<?php echo $arr_material['unit_price']?>"></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>manufacture/material/edit_material_form/<?php echo $arr_material['material_map_supplier_history_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>manufacture/material/del_action/<?php echo $arr_material['web_material_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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