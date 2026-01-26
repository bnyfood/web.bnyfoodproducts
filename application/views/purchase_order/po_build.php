
<style>
  
  .panel_box .table tr:nth-child(even) {
    background: #ccc !important;
}
</style>
<div class="page">
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <h4 class="example-title">Purchase order</h4>
          <div class="example">
            <table class="table table-bordered">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Supplier</th>
                  <th>Status</th>
                  <th class="text-nowrap">Action</th>
                </tr>
                </thead>
              <?php if(!empty($arr_pos)){?>
              <?php 
              $num_row = 0;
              foreach($arr_pos as $arr_po){?>  
                <?php if(!empty($arr_po['arr_po_mats'])){?>
                  <tbody class="table-section" data-plugin="tableSection">
                    <td class="text-center"><i class="table-section-arrow"></i></td>
                <?php }else{?>
                  <tbody >
                    <td></td>
                <?php }?>
                <td><?php echo $arr_po['supplier_name']?></td>
                <td><?php echo $arr_po['po_status']?></td>
                <td class="more_text1 text-nowrap">
                  <button type="button" class="btn btn-outline btn-primary btn-sm">
                    <a href="<?php echo base_url();?>product/edit_product_form/<?php echo $arr_po['web_purchase_order_id'];?>" data-toggle="tooltip" data-original-title="Edit"> <i class="icon wb-pencil"
                                                                                      aria-hidden="true"
                                                                                      style="margin-right:0px"></i>
                                                                                    </a>
                  </button>
                  <button type="button" class="btn btn-outline btn-danger btn-sm"><i class="icon wb-trash"
                                                                                     aria-hidden="true"
                                                                                     style="margin-right:0px"></i>
                  </button>
                </td>
              </tr> 
            </tbody>
            <?php 
                if(!empty($arr_po['arr_po_mats'])){
                $pro_quan_model = 0;
            ?>
              <tbody style='background: #ccc;'>
              <?php foreach($arr_po['arr_po_mats'] as $arr_po_mat){?>
                <tr>
                  <td class="font-weight-medium text-success">
                    #
                  </td>
                  <td>
                    <?php echo $arr_po_mat['material_name']?>
                  </td>
                  <td class="hidden-sm-down">
                    <?php echo $arr_po_mat['material_brand_name']?>
                  </td>
                  <td><input type="number" name="qty"></td>
                </tr>
              <?php }?>
              </tbody>
              <?php }}?>
              
            <?php }?>
            </table>
            <nav>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>