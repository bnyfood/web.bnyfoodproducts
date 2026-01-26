<div class="page">
  <div class="page-header">
    <h1 class="page-title">Brand</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."manufacture/brand/loaddata_more";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <input type="text" class="form-control" id="brand_search" name="brand_search"  placeholder="Search..." value="<?php echo $data_search['brand_search']?>">
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
                        <a href="<?php echo base_url();?>manufacture/brand/brand_list/<?php echo $from_pop?>" id="addToTable" class="btn-primary btn" >ทั้งหมด</a>
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

   <!-- <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-15">
                    <a href="<?php echo base_url();?>manufacture/brand/add_brand_form/<?php echo $from_pop?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Brand
                    </a>

                  </div>
                </div>
              </div>
            <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Brand สำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Brand ไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไข Brand สำเร็จ
              </div>
            <?php }?>  
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ชื่อ Brand</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody id="content-list">
                    <?php 
                        if(!empty($arr_brands)){
                        foreach($arr_brands as $arr_brand){
                    ?>
                      <tr>
                        <td><?php echo $arr_brand['material_brand_name']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>manufacture/brand/brand_edit_form/<?php echo $arr_brand['web_material_brand_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>manufacture/brand/del_action/<?php echo $arr_brand['web_material_brand_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php }}?>
                    <input type="hidden" name="offset" id="offset" value="5">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>-->

    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <a href="<?php echo base_url();?>manufacture/brand/add_brand_form/<?php echo $from_pop?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Brand
                    </a>
          <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Brand สำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Brand ไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไข Brand สำเร็จ
              </div>
            <?php }?>          
          <div class="example" id="highlighting">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ชื่อ Brand
                  <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('material_brand_name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('material_brand_name','desc',1)"></i></th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_brands)){
                  foreach($arr_brands as $arr_brand){
              ?>  
              <tr>
                <td><?php echo $arr_brand['material_brand_name']?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>manufacture/brand/brand_edit_form/<?php echo $arr_brand['web_material_brand_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>manufacture/brand/del_action/<?php echo $arr_brand['web_material_brand_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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