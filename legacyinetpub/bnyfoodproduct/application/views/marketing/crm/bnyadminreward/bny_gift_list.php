<?php
$active_tab = !empty($active_tab) ? $active_tab : 'reward';
$is_reward_tab = ($active_tab === 'reward');
$is_import_tab = ($active_tab === 'import');
$is_lucky_tab = ($active_tab === 'lucky');
?>
<div class="page">
  <div class="page-header">
    <h1 class="page-title">BNY Admin Reward</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div style="margin:0 0 12px 0; padding:8px 0 0 10px;">
          <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/bny_gift_list?tab=reward" class="btn <?php echo $is_reward_tab ? 'btn-primary' : 'btn-default';?>" style="margin-right:8px;">จัดการรางวัล</a>
          <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/bny_gift_list?tab=import" class="btn <?php echo $is_import_tab ? 'btn-primary' : 'btn-default';?>" style="margin-right:8px;">นำเข้าข้อมูลลูกค้า</a>
          <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/lucky_list" class="btn <?php echo $is_lucky_tab ? 'btn-primary' : 'btn-default';?>">ผลรางวัลผู้โชคดี</a>
        </div>
        <div style="padding:16px 18px 22px 18px;">
          <?php if($is_reward_tab){ ?>
            <div class="example-wrap">
              <div class="example">
                <form role="form" name="bny_gift_search_form" id="bny_gift_search_form" action="<?php echo base_url()."marketing/crm/bnyadminreward/bny_gift_list_search";?>" method="post">
                  <input type="hidden" name="search_type" id="search_type" value="1">
                  <div class="panel-body">
                    <h4 class="example-title">ค้นหา</h4>
                    <div class="row">
                      <div class="col-md-12">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                          <input type="text" class="form-control" id="bny_gift_search" name="bny_gift_search" placeholder="ค้นหารายละเอียด..." value="<?php echo htmlspecialchars($data_search['bny_gift_search'], ENT_QUOTES, 'UTF-8');?>" style="width:30%;">
                          <button type="submit" class="btn btn-primary">ค้นหา</button>
                          <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/bny_gift_list" class="btn btn-default">ทั้งหมด</a>
                          <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/add_bny_gift_form" class="btn btn-outline btn-primary">
                            <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มของรางวัล
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="example-wrap">
              <div id="bny-gift-flash-alerts">
              <?php if($add_alt == "success"){?>
                <div class="alert alert-success alert-dismissible bny-gift-flash-alert" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  เพิ่มของรางวัลสำเร็จ
                </div>
              <?php }?>
              <?php if($add_alt == "fail"){?>
                <div class="alert alert-danger alert-dismissible bny-gift-flash-alert" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  เพิ่มของรางวัลไม่สำเร็จ
                </div>
              <?php }?>
              <?php if($edit_alt == "success"){?>
                <div class="alert alert-success alert-dismissible bny-gift-flash-alert" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  แก้ไขของรางวัลสำเร็จ
                </div>
              <?php }?>
              <?php if($edit_alt == "fail"){?>
                <div class="alert alert-danger alert-dismissible bny-gift-flash-alert" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  แก้ไขของรางวัลไม่สำเร็จ
                </div>
              <?php }?>
              <?php if(!empty($delete_alt) && $delete_alt == "success"){?>
                <div class="alert alert-success alert-dismissible bny-gift-flash-alert" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  ลบของรางวัลสำเร็จ
                </div>
              <?php }?>
              <?php if(!empty($delete_alt) && $delete_alt == "fail"){?>
                <div class="alert alert-danger alert-dismissible bny-gift-flash-alert" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  ลบของรางวัลไม่สำเร็จ
                </div>
              <?php }?>
              </div>
              <div class="example table-responsive" id="highlighting">
                <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
                  <thead>
                    <tr>
                      <th>รูป</th>
                      <th>รายละเอียด
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_bny_gift_detail','asc',0)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_bny_gift_detail','desc',1)"></i>
                      </th>
                      <th>ของรางวัลล่าสุด
                        <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_bny_gift_now','asc',2)"></i>
                        <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_bny_gift_now','desc',3)"></i>
                      </th>
                      <th class="text-nowrap">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody id="content-list">
                  <?php
                      if(!empty($arr_gifts)){
                      foreach($arr_gifts as $arr_gift){
                        $pic_url = !empty($arr_gift['web_bny_gift_pic']) ? $gift_pic_base_url.rawurlencode(basename($arr_gift['web_bny_gift_pic'])) : '';
                        $detail_short = !empty($arr_gift['web_bny_gift_detail']) ? mb_substr(strip_tags($arr_gift['web_bny_gift_detail']), 0, 80, 'UTF-8') : '';
                  ?>
                  <tr>
                    <td>
                      <?php if($pic_url != ''){ ?>
                        <img src="<?php echo $pic_url;?>" alt="" style="max-height:56px;max-width:72px;object-fit:contain;">
                      <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($detail_short, ENT_QUOTES, 'UTF-8');?></td>
                    <td><?php echo ((int)$arr_gift['web_bny_gift_now'] === 1) ? '<span class="label label-success">ล่าสุด</span>' : ''; ?></td>
                    <td class="text-nowrap">
                      <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/edit_bny_gift_form/<?php echo $arr_gift['web_bny_gift_id'];?>" data-toggle="tooltip" data-original-title="Edit">
                        <i class="icon wb-wrench" aria-hidden="true"></i>
                      </a>
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
          <?php } ?>
          <?php if($is_import_tab){ ?>
            <?php if(!empty($import_alt) && $import_alt == "success"){?>
              <div class="alert alert-success alert-dismissible bny-gift-flash-alert" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                นำเข้าข้อมูลลูกค้าสำเร็จ
              </div>
            <?php }?>
            <?php if(!empty($import_alt) && $import_alt == "fail"){?>
              <div class="alert alert-danger alert-dismissible bny-gift-flash-alert" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                นำเข้าข้อมูลลูกค้าไม่สำเร็จ
              </div>
            <?php }?>
            <form name="import_orders_inw" id="import_orders_inw" action="<?php echo base_url()."marketing/crm/bnyadminreward/biggrill_import_data_action";?>" method="post" enctype="multipart/form-data" style="margin:2px 0 6px 0;">
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <input type="file" id="upload_file1" name="upload_file1" />
                <button type="submit" id="importfile" class="btn btn-primary">Import</button>
              </div>
            </form>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
