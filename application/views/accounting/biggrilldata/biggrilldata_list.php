<?php
$active_tab = !empty($active_tab) ? $active_tab : 'import';
$is_import_tab = ($active_tab === 'import');
$is_manage_tab = ($active_tab === 'manage');
$is_sale_report_tab = ($active_tab === 'sale_report');
$data_search = !empty($data_search) ? $data_search : array(
  'search_field' => 'order_id',
  'search_text' => '',
  'is_void' => '',
  'daterange' => '',
  'sortby' => '',
  'sorttype' => ''
);
$arr_rows = !empty($arr_rows) ? $arr_rows : array();

function biggrill_fmt_num($value) {
  return number_format((float) $value, 2, '.', ',');
}

function biggrill_fmt_ctime($value) {
  if (empty($value)) {
    return '';
  }
  $ts = strtotime($value);
  if ($ts === FALSE) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
  return date('d/m/Y H:i', $ts);
}

function biggrill_fmt_tracking($value) {
  if ($value === '' || $value === NULL) {
    return '';
  }
  $parts = array_filter(array_map('trim', explode(',', (string) $value)), function ($part) {
    return $part !== '';
  });
  if (empty($parts)) {
    return '';
  }
  $first = reset($parts);
  $display = (count($parts) > 1) ? $first . '.....' : $first;
  return htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
}
?>
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
.bg-void-switch input:disabled + .slider {
  opacity: 0.6;
  cursor: not-allowed;
}
.bg-void-cell {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.bg-void-label {
  font-size: 14px;
  color: #37474f;
}
.bg-search-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 15px;
  margin-top: 15px;
}
.bg-search-row .form-control,
.bg-search-row select.form-control {
  width: auto;
  min-width: 140px;
}
.bg-void-radio-group label {
  margin-right: 12px;
  font-weight: normal;
}
</style>
<div class="page">
  <div class="page-header">
    <h1 class="page-title">BigGrill Data</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div style="margin:0 0 12px 0; padding:8px 0 0 10px;">
          <a href="<?php echo base_url();?>accounting/biggrilldata/biggrilldata_list?tab=import" class="btn <?php echo $is_import_tab ? 'btn-primary' : 'btn-default';?>" style="margin-right:8px;">นำเข้าข้อมูลลูกค้า</a>
          <a href="<?php echo base_url();?>accounting/biggrilldata/biggrilldata_list?tab=manage" class="btn <?php echo $is_manage_tab ? 'btn-primary' : 'btn-default';?>" style="margin-right:8px;">จัดการข้อมูลลูกค้า</a>
          <a href="<?php echo base_url();?>accounting/biggrilldata/inwshop_import_sale_chk" class="btn <?php echo $is_sale_report_tab ? 'btn-primary' : 'btn-default';?>">นำเข้าและออกรายงาน</a>
        </div>
        <div style="padding:16px 18px 22px 18px;">
          <?php if($is_sale_report_tab){ ?>
            <form name="import_orders_inw" id="import_orders_inw" action="<?php echo base_url()."accounting/biggrilldata/inwshop_import_sale_chk_action"?>" method="post" enctype="multipart/form-data" style="margin:2px 0 6px 0;">
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <input type="file" id="upload_file1" name="upload_file1" />
                <button type="submit" id="importfile_sale_report" class="btn btn-primary">Import</button>
              </div>
            </form>
          <?php } ?>
          <?php if($is_import_tab){ ?>
            <?php if(!empty($import_alt) && $import_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                นำเข้าข้อมูลลูกค้าสำเร็จ
              </div>
            <?php }?>
            <?php if(!empty($import_alt) && $import_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                นำเข้าข้อมูลลูกค้าไม่สำเร็จ
              </div>
            <?php }?>
            <form name="import_biggrill_data" id="import_biggrill_data" action="<?php echo base_url()."accounting/biggrilldata/biggrill_import_data_action";?>" method="post" enctype="multipart/form-data" style="margin:2px 0 6px 0;">
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <input type="file" id="upload_file1" name="upload_file1" />
                <button type="submit" id="importfile" class="btn btn-primary">Import</button>
              </div>
            </form>
          <?php } ?>

          <?php if($is_manage_tab){ ?>
            <div class="example-wrap">
              <div class="example">
                <form role="form" name="biggrilldata_search_form" id="biggrilldata_search_form" action="<?php echo base_url()."accounting/biggrilldata/biggrilldata_list_search";?>" method="post">
                  <input type="hidden" name="search_type" id="search_type" value="1">
                  <div class="panel-body">
                    <h4 class="example-title">ค้นหา</h4>
                    <div class="bg-search-row">
                      <select class="form-control" id="search_field" name="search_field">
                        <option value="order_id" <?php echo ($data_search['search_field'] === 'order_id') ? 'selected' : '';?>>Order No</option>
                        <option value="cus_name" <?php echo ($data_search['search_field'] === 'cus_name') ? 'selected' : '';?>>ชื่อลูกค้า</option>
                        <option value="cus_phone" <?php echo ($data_search['search_field'] === 'cus_phone') ? 'selected' : '';?>>เบอร์โทร</option>
                        <option value="status" <?php echo ($data_search['search_field'] === 'status') ? 'selected' : '';?>>Status</option>
                        <option value="trackingid" <?php echo ($data_search['search_field'] === 'trackingid') ? 'selected' : '';?>>Trackingid</option>
                        <option value="taxinvoiceid" <?php echo ($data_search['search_field'] === 'taxinvoiceid') ? 'selected' : '';?>>Taxinvoiceid</option>
                      </select>
                      <input type="text" class="form-control" id="search_text" name="search_text" placeholder="คำค้นหา..." value="<?php echo htmlspecialchars($data_search['search_text'], ENT_QUOTES, 'UTF-8');?>" style="min-width:220px;">
                      <div class="bg-void-radio-group">
                        <label><input type="radio" name="is_void" value="" <?php echo ($data_search['is_void'] === '' || $data_search['is_void'] === NULL) ? 'checked' : '';?>> Void ทั้งหมด</label>
                        <label><input type="radio" name="is_void" value="1" <?php echo ((string)$data_search['is_void'] === '1') ? 'checked' : '';?>> Yes</label>
                        <label><input type="radio" name="is_void" value="0" <?php echo ((string)$data_search['is_void'] === '0') ? 'checked' : '';?>> No</label>
                      </div>
                      <div class="input-group" style="width:auto;">
                        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo htmlspecialchars($data_search['daterange'], ENT_QUOTES, 'UTF-8');?>" style="min-width:220px;">
                        <div class="input-group-append">
                          <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">ค้นหา</button>
                      <a href="<?php echo base_url();?>accounting/biggrilldata/biggrilldata_list?tab=manage" class="btn btn-default">ทั้งหมด</a>
                      <a href="<?php echo base_url();?>accounting/biggrilldata/add_biggrilldata_form" class="btn btn-outline btn-primary">
                        <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มข้อมูลลูกค้า
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <?php if(!empty($add_alt) && $add_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                เพิ่มข้อมูลลูกค้าสำเร็จ
              </div>
            <?php }?>
            <?php if(!empty($edit_alt) && $edit_alt == "success"){?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                แก้ไขข้อมูลลูกค้าสำเร็จ
              </div>
            <?php }?>
            <div class="example-wrap">
              <div class="example table-responsive" id="highlighting">
                <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;">
                  <thead>
                    <tr>
                      <th>Order No
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('order_id','asc',0)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('order_id','desc',1)"></i>
                      </th>
                      <th>วันเวลาที่สั่งซื้อ
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('ctime','asc',2)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('ctime','desc',3)"></i>
                      </th>
                      <th>ชื่อลูกค้า
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('cus_name','asc',4)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('cus_name','desc',5)"></i>
                      </th>
                      <th>เบอร์โทร
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('cus_phone','asc',6)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('cus_phone','desc',7)"></i>
                      </th>
                      <th>Status
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('status','asc',8)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('status','desc',9)"></i>
                      </th>
                      <th>ค่าสินค้า
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('price','asc',10)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('price','desc',11)"></i>
                      </th>
                      <th>ค่าส่ง
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('delivery','asc',12)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('delivery','desc',13)"></i>
                      </th>
                      <th>ส่วนลด
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('discount','asc',14)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('discount','desc',15)"></i>
                      </th>
                      <th>Price include vat
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('amount_include_vat','asc',16)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('amount_include_vat','desc',17)"></i>
                      </th>
                      <th>Price exclude vat
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('amount_exclude_vat','asc',18)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('amount_exclude_vat','desc',19)"></i>
                      </th>
                      <th>Vat
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('vat','asc',20)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('vat','desc',21)"></i>
                      </th>
                      <th>Trackingid
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('trackingid','asc',22)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('trackingid','desc',23)"></i>
                      </th>
                      <th>Taxinvoiceid
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('taxinvoiceID','asc',24)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('taxinvoiceID','desc',25)"></i>
                      </th>
                      <th>Void</th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody id="content-list">
                  <?php
                    if(!empty($arr_rows)){
                      foreach($arr_rows as $row){
                        $is_void = (int) (!empty($row['is_void']) ? $row['is_void'] : 0);
                        $row_id = (int) $row['biggrill_data_id'];
                  ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8');?></td>
                      <td><?php echo biggrill_fmt_ctime($row['ctime']);?></td>
                      <td><?php echo htmlspecialchars($row['cus_name'], ENT_QUOTES, 'UTF-8');?></td>
                      <td><?php echo htmlspecialchars($row['cus_phone'], ENT_QUOTES, 'UTF-8');?></td>
                      <td><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');?></td>
                      <td><?php echo biggrill_fmt_num($row['price']);?></td>
                      <td><?php echo biggrill_fmt_num($row['delivery']);?></td>
                      <td><?php echo biggrill_fmt_num($row['discount']);?></td>
                      <td><?php echo biggrill_fmt_num($row['amount_include_vat']);?></td>
                      <td><?php echo biggrill_fmt_num($row['amount_exclude_vat']);?></td>
                      <td><?php echo biggrill_fmt_num($row['vat']);?></td>
                      <td><?php echo biggrill_fmt_tracking($row['trackingid']);?></td>
                      <td><?php echo htmlspecialchars(!empty($row['taxinvoiceID']) ? $row['taxinvoiceID'] : '', ENT_QUOTES, 'UTF-8');?></td>
                      <td>
                        <div class="bg-void-cell">
                          <label class="bg-void-switch" title="Void">
                            <input type="checkbox" class="void-toggle" data-row-id="<?php echo $row_id;?>" <?php echo $is_void === 1 ? 'checked' : '';?>>
                            <span class="slider"></span>
                          </label>
                          <span class="bg-void-label void-status-text"><?php echo $is_void === 1 ? 'Yes' : 'No'; ?></span>
                        </div>
                      </td>
                      <td class="text-nowrap">
                        <a href="<?php echo base_url();?>accounting/biggrilldata/edit_biggrilldata_form/<?php echo $row_id;?>" data-toggle="tooltip" data-original-title="Edit">
                          <i class="icon wb-wrench" aria-hidden="true"></i>
                        </a>
                      </td>
                    </tr>
                  <?php }} ?>
                    <input type="hidden" name="offset" id="offset" value="0">
                    <input type="hidden" name="per_page" id="per_page" value="<?php echo (int)(!empty($list_per_page) ? $list_per_page : 20);?>">
                    <input type="hidden" name="sortby" id="sortby" value="<?php echo htmlspecialchars($data_search['sortby'], ENT_QUOTES, 'UTF-8');?>">
                    <input type="hidden" name="sorttype" id="sorttype" value="<?php echo htmlspecialchars($data_search['sorttype'], ENT_QUOTES, 'UTF-8');?>">
                  </tbody>
                </table>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
