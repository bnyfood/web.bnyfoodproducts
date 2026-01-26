<div class="page">
  <div class="page-header">
    <h1 class="page-title">Inventory reciept</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."store_manage/inventory_reciept/po_material_search";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <input type="text" class="form-control" id="po_search" name="po_search"  placeholder="Search..." value="<?php echo $data_search['po_search']?>">
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
          <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Location สำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่ม Location ไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไข Location สำเร็จ
              </div>
            <?php }?>          
          <div class="example" id="highlighting">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ชื่อ PO number</th>
                  <th>Date</th>
                  <th>Reciept</th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_purchases)){
                  foreach($arr_purchases as $arr_purchase){
              ?>  
              <tr>
                <td><?php echo $arr_purchase['po_number']?></td>
                <td><?php echo $arr_purchase['cdate']?></td>
                <td>
                  <?php if($arr_purchase['reciept_yet'] == 1){?>
                    <i class="icon wb-check ml-10 green-600" aria-hidden="true" data-toggle="tooltip" data-original-title="Reciept" data-container="body" title=""></i>
                  <?php }else{?>
                    <i class="icon wb-close ml-10 red-600" aria-hidden="true" data-toggle="tooltip" data-original-title="No reciept" data-container="body" title=""></i>
                  <?php }?>  
                  </td>
                <td class="text-nowrap">
                  <?php if($arr_purchase['reciept_yet'] == 1){?>
                    <a href="<?php echo base_url();?>store_manage/inventory_reciept/reciept_data/<?php echo $arr_purchase['po_number'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                      <i class="icon wb-order" aria-hidden="true"></i>
                    </a>
                  <?php }else{?>  
                    <a href="<?php echo base_url();?>store_manage/inventory_reciept/reciept_add_form/<?php echo $arr_purchase['po_number'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                      <i class="icon wb-edit" aria-hidden="true"></i>
                    </a>
                  <?php }?>   
                </td>
              </tr>          
            <?php }}else{?>
              <tr>
                <td colspan="4">Empty Data</td>
              </tr>
            <?php }?>
              <input type="hidden" name="offset" id="offset" value="0">
            </tbody>
          </table>

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       