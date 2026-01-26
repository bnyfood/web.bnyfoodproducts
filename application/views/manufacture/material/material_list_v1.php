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
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-15">
                    <a href="<?php echo base_url();?>manufacture/material/add_material_form" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม Material
                    </a>
                  </div>
                </div>
              </div>
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
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                        <th>Material</th>
                        <th>ยี่ห้อ</th>
                        <th>Supplier</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody id="content-list">
                    <?php 
                        if(!empty($arr_materials)){
                        foreach($arr_materials as $arr_material){
                    ?>
                      <tr>
                          <td><?php echo $arr_material['material_name']?></td>
                          <td><?php echo $arr_material['material_brand_name']?></td>
                          <td><?php echo $arr_material['supplier_name']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>manufacture/brand/brand_edit_form/<?php echo $arr_material['web_material_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>manufacture/brand/del_action/<?php echo $arr_material['web_material_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
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
    </div>
  </div> 
</div>       