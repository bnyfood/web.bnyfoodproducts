<style>
.bg-void-switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 26px;
  margin: 0;
  vertical-align: middle;
}
.bg-void-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.bg-void-switch .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .2s;
  border-radius: 26px;
}
.bg-void-switch .slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .2s;
  border-radius: 50%;
}
.bg-void-switch input:checked + .slider {
  background-color: #3e8ef7;
}
.bg-void-switch input:checked + .slider:before {
  transform: translateX(20px);
}
.bg-void-cell {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  padding-top: 6px;
}
.bg-void-label {
  font-size: 14px;
  color: #37474f;
}
.bg-readonly {
  background-color: #f3f4f6;
}
</style>
<div class="page">
  <div class="page-header">
    <h1 class="page-title"><?php echo !empty($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'เพิ่มข้อมูลลูกค้า';?></h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:40px;">
        <?php if(!empty($add_alt) && $add_alt == "fail"){?>
          <div class="alert alert-danger alert-dismissible" role="alert" style="margin:16px 16px 0;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            กรุณากรอก Order No
          </div>
        <?php }?>
        <?php if(!empty($add_alt) && $add_alt == "duplicate"){?>
          <div class="alert alert-danger alert-dismissible" role="alert" style="margin:16px 16px 0;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            Order No นี้มีในระบบแล้ว
          </div>
        <?php }?>
        <form role="form" name="biggrilldata_add_form" id="biggrilldata_add_form" action="<?php echo base_url()."accounting/biggrilldata/biggrilldata_add";?>" method="post">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-8">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูลลูกค้า</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Order No:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="order_id" id="order_id" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">วันเวลาที่สั่งซื้อ:</label>
                    <div class="col-md-6">
                      <div class="input-group">
                        <input type="text" class="form-control" name="ctime" id="ctime" autocomplete="off" value="<?php echo date('d/m/Y H:i');?>">
                        <div class="input-group-append">
                          <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">ชื่อลูกค้า:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="cus_name" id="cus_name" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">เบอร์โทร:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="cus_phone" id="cus_phone" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Status:</label>
                    <div class="col-md-6">
                      <select class="form-control" name="status" id="status">
                        <option value="จัดส่งแล้ว">จัดส่งแล้ว</option>
                        <option value="ยกเลิก">ยกเลิก</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">ค่าสินค้า:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control bg-num-only" name="price" id="price" autocomplete="off" value="0">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">ค่าส่ง:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control bg-num-only" name="delivery" id="delivery" autocomplete="off" value="0">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">ส่วนลด:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control bg-num-only" name="discount" id="discount" autocomplete="off" value="0">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Price include vat:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control bg-readonly" id="amount_include_vat_display" readonly value="0.00">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Price exclude vat:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control bg-readonly" id="amount_exclude_vat_display" readonly value="0.00">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Vat:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control bg-readonly" id="vat_display" readonly value="0.00">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Trackingid:</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="trackingid" id="trackingid" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Void:</label>
                    <div class="col-md-6">
                      <div class="bg-void-cell">
                        <label class="bg-void-switch" title="Void">
                          <input type="checkbox" class="void-form-toggle" id="is_void" name="is_void" value="1">
                          <span class="slider"></span>
                        </label>
                        <span class="bg-void-label void-form-status-text">No</span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>accounting/biggrilldata/biggrilldata_list?tab=manage" class="btn btn-outline btn-primary">
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
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
