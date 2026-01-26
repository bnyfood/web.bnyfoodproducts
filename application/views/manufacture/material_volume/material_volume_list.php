<div class="page">
  <div class="page-header">
    <h1 class="page-title">Material Volume</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="#" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <input type="text" class="form-control" id="material_search" name="material_search"  placeholder="Search..." value="<?php echo $data_search['material_search']?>">
                          <button type="button" class="input-search-close icon wb-close" aria-label="Close"></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                           <button type="submit" class="btn-primary btn">
                           ค้นหา
                        </button>
                        <a href="<?php echo base_url();?>manufacture/material/material_list" id="addToTable" class="btn-primary btn" >ทั้งหมด</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <a href="<?php echo base_url();?>manufacture/material_volume/add_material_volume_form" id="addToTable" class="btn btn-outline btn-primary" >
            <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Material Volume
          </a>         
          <div class="example" id="highlighting">
            <table class="table table-bordered">
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