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
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">      
        <button type="button" class="btn btn-block btn-primary" id="reciept_print" class="btn btn-primary" style="display: none;">
              <i class="icon wb-print" aria-hidden="true"></i> พิมพ์ใบ reciept
            </button>   
          <div class="example" id="highlighting">
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
              ?>  
              <tr>
                <td><?php echo $data_po['material_name']?></td>
                <td><?php echo $data_po['material_size']?></td>
                <td><?php echo $data_po['material_unit_price']?></td>
                <td><?php echo $data_po['qty']?></td>
                <td>
                   <?php echo $data_po['material_unit_price']*$data_po['qty']?>
                </td>
                <td class="text-nowrap">
                  <?php echo $data_po['store_location_name']?>
                </td>
                <td class="text-nowrap">
                  <?php echo $data_po['store_sub_shelf_name']?>
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
              <input type="hidden" name="po_number" id="po_number" value="<?php echo $po_number?>" >
              <a href="<?php echo base_url();?>store_manage/inventory_reciept/po_material_list" id="addToTable" class="btn btn-outline btn-primary" >
                <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       