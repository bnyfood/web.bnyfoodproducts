<div class="page">
  <div class="page-header">
    <h1 class="page-title">Material</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <div class="example table-responsive">
                <form role="form" name="material_form" id="material_form" action="<?php echo base_url()."purchase_order/po_build";?>" method="post">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Material</th>
                        <th>ยี่ห้อ</th>
                        <th>Supplier</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                          if(!empty($arr_materials)){
                          foreach($arr_materials as $arr_material){
                      ?>
                        <tr>
                          <td>
                            <input type="checkbox" id="<?php echo $arr_material['web_material_id'];?>" name="chk_material_id[]" value="<?php echo $arr_material['web_material_id']."-".$arr_material['web_supplier_id'];?>" autocomplete="off" style="vertical-align:top">
                          </td>
                          <td><?php echo $arr_material['material_name']?></td>
                          <td><?php echo $arr_material['material_brand_name']?></td>
                          <td><?php echo $arr_material['supplier_name']?></td>
                        </tr>
                      <?php }}?>
                    </tbody>
                  </table>
                  <input type="hidden" name="ran_num_pocode" value="<?php echo $ran_num_pocode;?>">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div> 
</div>       