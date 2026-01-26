<div class="page">
  <div class="page-header">
    <h1 class="page-title">Config SKU</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <div class="row row-lg">
          <div class="col-lg-12">
            <div class="example-wrap">
              <h4 class="example-title">พนักงาน</h4>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-15">
                    <input type="text" class="form-control"  name="sku_name" id='sku_name'>
                    <a href="<?php echo base_url();?>sku/add_sku_form" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม SKU
                    </a>
                  </div>
                </div>
              </div>
            <?php if($add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มพนักงานสำเร็จ
              </div>
            <?php }?>  
            <?php if($add_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                เพื่มพนักงานไม่สำเร็จ
              </div>
            <?php }?> 
            <?php if($edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                แก้ไขพนักงานสำเร็จ
              </div>
            <?php }?>  
              <div class="example table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ชื่อ SKU</th>
                      <th>SKU Value</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                        if(!empty($arr_skus)){
                        foreach($arr_skus as $arr_sku){
                    ?>
                      <tr>
                        <td><?php echo $arr_sku['sku_name']?></td>
                        <td><?php echo $arr_sku['sku_value']?></td>
                        <td class="text-nowrap">
                          <a href="<?php echo base_url();?>config_system/users/user_edit_form/<?php echo $arr_sku['web_sku_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                            <i class="icon wb-wrench" aria-hidden="true"></i>
                          </a>
                          <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>config_system/users/del_action/<?php echo $arr_sku['web_sku_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php }}?>
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