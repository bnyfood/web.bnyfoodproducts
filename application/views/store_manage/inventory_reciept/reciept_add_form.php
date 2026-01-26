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
                  <h4 class="example-title">Inventory reciept</h4>

                  <div class="row">
                    <div class="col-md-6">
                      PO Number :<?php echo $data_po_profile['po_number'];?>
                      Supplier :<?php echo $data_po_profile['supplier_name'];?>
                      PO Date :<?php echo $data_po_profile['po_date'];?>
                    </div>
                    <div class="col-md-6">

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
          <div class="example" id="highlighting">
            <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."store_manage/inventory_reciept/reciept_add";?>" method="post">
              <?php if($arr_permission['is_del'] == 1){?>
            <button type="submit" class="btn btn-primary">สร้าง reciept</button>
            <?php } ?>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="30%">ชื่อสินค้า</th>
                  <th width="10%">ขนาด</th>
                  <th width="10%">ราคา/หน่วย</th>
                  <th width="10%">จำนวน</th>
                  <th width="10%">ราคารวม</th>
                  <th width="15%">Location</th>
                  <th width="15%">Shelf</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($data_pos)){
                  foreach($data_pos as $data_po){
                    $name_web_material_id = "web_material_id_".$data_po['purchase_material_id'];
                    $name_price = "price_".$data_po['purchase_material_id'];
                    $name_unit = "unit_".$data_po['purchase_material_id'];
                    $name_qty = "qty_".$data_po['purchase_material_id'];
                    $name_location = "location_".$data_po['purchase_material_id'];
                    $name_shelf = "shelf_".$data_po['purchase_material_id'];
              ?>  
              <tr>
                <input type="hidden" name="<?php echo $name_web_material_id;?>" value="<?php echo $data_po['web_material_id']?>">
                <td><?php echo $data_po['material_name']?></td>
                <td><?php echo $data_po['material_size']?></td>
                <td><input type="number" class="form-control" name="<?php echo $name_unit;?>" value="<?php echo $data_po['material_unit_price']?>"></td>
                <td><input type="number" class="form-control" name="<?php echo $name_qty;?>" value="<?php echo $data_po['qty']?>"></td>
                <td>
                   <?php echo $data_po['material_unit_price']*$data_po['qty']?>
                </td>
                <td class="text-nowrap">
                  <select class="form-control" name="<?php echo $name_location;?>" id="shop_sel">
                      <option value="">กรุณาเลือก</option> 
                      <?php foreach($data_locations as $data_location){?>
                        <option value="<?php echo $data_location['store_location_id']?>"><?php echo $data_location['store_location_name']?></option>
                      <?php }?>  
                  </select>
                </td>
                <td class="text-nowrap">
                  <!--<select class="form-control" name="<?php echo $name_shelf;?>" id="shop_sel">
                      <option value="">กรุณาเลือก</option> 
                      <?php foreach($data_shelfs as $data_shelf){?>
                        <option value="<?php echo $data_shelf['store_shelf_id']?>"><?php echo $data_shelf['store_shelf_name']?></option>
                      <?php }?>  
                  </select>-->
                  
                   <select class="form-control" name="<?php echo $name_shelf;?>" id="shop_sel">
                      <?php 
                      if(!empty($data_shelfs)){
                      foreach($data_shelfs as $data_shelf){?>
                      <optgroup label="<?php echo $data_shelf['store_shelf_name'];?>">
                        <?php 
                          if(!empty($data_shelf['arr_sub_shelfs'])){
                          foreach($data_shelf['arr_sub_shelfs'] as $arr_sub_shelf){
                        ?>
                        <option value="<?php echo $arr_sub_shelf['store_sub_shelf_id'];?>"><?php echo $arr_sub_shelf['store_sub_shelf_name'];?></option>
                        <?php }}?>
                      </optgroup>
                      <?php }}?>
                    </select>
                   
                </td>
              </tr>          
              <?php }}else{?>
                <tr>
                  <td colspan="6">Empty Data</td>
                </tr>
              <?php }?>
                <input type="hidden" name="offset" id="offset" value="0">
              </tbody>
            </table>
            <div class="form-group">
                <a href="<?php echo base_url();?>store_manage/inventory_reciept/po_material_list" id="addToTable" class="btn btn-outline btn-primary" >
                  <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                </a>
                <button type="submit" class="btn btn-primary">สร้าง reciept</button>
              </div>
              <input type="hidden" name="arr_purchase_material_id" value='<?php echo $arr_purchase_material_id;?>'>
              <input type="hidden" name="web_purchase_order_id" value='<?php echo $data_po_profile['web_purchase_order_id'];?>'>
              <input type="hidden" name="po_number" value='<?php echo $data_po_profile['po_number'];?>'>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       