<div class="page">
  <div class="page-header">
    <h1 class="page-title">shelf</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."store_manage/shelf/loaddata_more";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <input type="text" class="form-control" id="shelf_search" name="shelf_search"  placeholder="Search..." value="<?php echo $data_search['shelf_search']?>">
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
                        <a href="<?php echo base_url();?>store_manage/shelf/shelf_list/<?php echo $from_pop?>" id="addToTable" class="btn-primary btn" >ทั้งหมด</a>
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
          <a href="<?php echo base_url();?>store_manage/shelf/add_shelf_form/<?php echo $from_pop?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม shelf
                    </a>
          <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม shelf สำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม shelf ไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไข shelf สำเร็จ
              </div>
            <?php }?>          
          <div class="example" id="highlighting">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ชื่อ shelf
                  <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('store_shelf_name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('store_shelf_name','desc',1)"></i></th>
                    <th>Subshelf</th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_shelfs)){
                  foreach($arr_shelfs as $arr_shelf){
              ?>  
              <tr>
                <td colspan="2"><?php echo $arr_shelf['store_shelf_name']?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>store_manage/shelf/sub_shelf_add_form/<?php echo $arr_shelf['store_shelf_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-plus" aria-hidden="true"></i>
                  </a>
                  <a href="<?php echo base_url();?>store_manage/shelf/shelf_edit_form/<?php echo $arr_shelf['store_shelf_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>store_manage/shelf/del_action/<?php echo $arr_shelf['store_shelf_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                </td>
              </tr>          
            <?php 
              if(!empty($arr_shelf['arr_sub_shelfs'])){
              foreach($arr_shelf['arr_sub_shelfs'] as $arr_sub_shelf){
            ?>
              <tr>
                <td width="20%"></td>
                <td width="60%"><?php echo $arr_sub_shelf['store_sub_shelf_name'];?></td>
                <td width="20%">
                  <a href="<?php echo base_url();?>store_manage/shelf/sub_shelf_edit_form/<?php echo $arr_sub_shelf['store_sub_shelf_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>store_manage/shelf/del_sub_action/<?php echo $arr_sub_shelf['store_sub_shelf_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                </td>
              </tr>
          <?php }}}}?>
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