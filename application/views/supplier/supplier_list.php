<div class="page">
  <div class="page-header">
    <h1 class="page-title">Supplier</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."supplier/supplier_list_search";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <input type="text" class="form-control" id="sipplier_search" name="sipplier_search"  placeholder="Search..." value="<?php echo $data_search['sipplier_search']?>">
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
                        <a href="<?php echo base_url();?>supplier/supplier_list/<?php echo $from_pop;?>" id="addToTable" class="btn-primary btn" >ทั้งหมด</a>
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

    <!--<div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-15">
                    <a href="<?php echo base_url();?>supplier/add_supplier_form/<?php echo $from_pop;?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Supplier
                    </a>
                  </div>
                </div>
              </div>
            <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Supplier สำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Supplier ไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไข Supplier สำเร็จ
              </div>
            <?php }?>  
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ชื่อ Supplier 
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_name','asc',0)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_name','desc',1)"></i></th>
                      <th>
                        ที่อยู่
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_address','asc',2)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_address','desc',3)"></i>
                      </th>
                      <th>
                        Line
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_line','asc',4)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_line','desc',5)"></i>
                      </th>
                      <th>
                        โทรศัพท์
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('phoneno1','asc',6)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('phoneno1','desc',7)"></i>
                      </th>
                      <th>
                        Email
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_email','asc',8)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_email','desc',9)"></i>
                      </th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody id="content-list">
                    <?php 
                        if(!empty($arr_sippliers)){
                        foreach($arr_sippliers as $arr_sipplier){
                    ?>
                      <tr>
                        <td><?php echo $arr_sipplier['supplier_name']?></td>
                        <td><?php echo $arr_sipplier['supplier_address']?></td>
                        <td><?php echo $arr_sipplier['supplier_line']?></td>
                        <td><?php echo $arr_sipplier['phoneno1']?></td>
                        <td><?php echo $arr_sipplier['supplier_email']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>supplier/supplier_edit_form/<?php echo $arr_sipplier['web_supplier_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>supplier/del_action/<?php echo $arr_sipplier['web_supplier_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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
    </div>-->

    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <a href="<?php echo base_url();?>supplier/add_supplier_form/<?php echo $from_pop;?>" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Supplier
                    </a>
          <div class="example">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ชื่อ Supplier 
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_name','desc',1)"></i></th>
                  <th>
                    ที่อยู่
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_address','asc',2)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_address','desc',3)"></i>
                  </th>
                  <th>
                    Line
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_line','asc',4)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_line','desc',5)"></i>
                  </th>
                  <th>
                    โทรศัพท์
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('phoneno1','asc',6)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('phoneno1','desc',7)"></i>
                  </th>
                  <th>
                    Email
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_email','asc',8)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_email','desc',9)"></i>
                  </th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_sippliers)){
                  foreach($arr_sippliers as $arr_sipplier){
              ?>  
              <tr>
                <td><?php echo $arr_sipplier['supplier_name']?></td>
                <td><?php echo $arr_sipplier['supplier_address']?></td>
                <td><?php echo $arr_sipplier['supplier_line']?></td>
                <td><?php echo $arr_sipplier['phoneno1']?></td>
                <td><?php echo $arr_sipplier['supplier_email']?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>supplier/supplier_edit_form/<?php echo $arr_sipplier['web_supplier_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>supplier/del_action/<?php echo $arr_sipplier['web_supplier_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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