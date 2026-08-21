<?php
$active_tab = !empty($active_tab) ? $active_tab : 'lucky';
$is_reward_tab = ($active_tab === 'reward');
$is_import_tab = ($active_tab === 'import');
$is_lucky_tab = ($active_tab === 'lucky');
?>
<style>
.bny-gift-send-switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 26px;
  margin: 0;
  vertical-align: middle;
}
.bny-gift-send-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.bny-gift-send-switch .slider {
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
.bny-gift-send-switch .slider:before {
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
.bny-gift-send-switch input:checked + .slider {
  background-color: #3e8ef7;
}
.bny-gift-send-switch input:checked + .slider:before {
  transform: translateX(20px);
}
.bny-gift-send-switch input:disabled + .slider {
  opacity: 0.6;
  cursor: not-allowed;
}
.bny-gift-send-cell {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.bny-gift-send-label {
  font-size: 14px;
  color: #37474f;
}
</style>
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
          <div class="example-wrap">
            <div class="example">
              <form role="form" name="lucky_search_form" id="lucky_search_form" action="<?php echo base_url()."marketing/crm/bnyadminreward/lucky_list_search";?>" method="post">
                <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>
                  <div class="row">
                    <div class="col-md-12">
                      <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                        <input type="text" class="form-control" id="lucky_search" name="lucky_search" placeholder="ค้นหาชื่อ เบอร์โทร หรือรายละเอียดรางวัล..." value="<?php echo htmlspecialchars($data_search['lucky_search'], ENT_QUOTES, 'UTF-8');?>" style="width:40%;">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                        <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/lucky_list" class="btn btn-default">ทั้งหมด</a>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="example-wrap">
            <div class="example table-responsive" id="highlighting">
              <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
                <thead>
                  <tr>
                    <th>รูป</th>
                    <th>รายละเอียด
                      <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_bny_gift_detail','asc',0)"></i>
                      <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_bny_gift_detail','desc',1)"></i>
                    </th>
                    <th>ชื่อ
                      <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('u.web_user_name','asc',2)"></i>
                      <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('u.web_user_name','desc',3)"></i>
                    </th>
                    <th>เบอร์โทร</th>
                    <th>การส่งของรางวัล</th>
                  </tr>
                </thead>
                <tbody id="content-list">
                <?php
                    if(!empty($arr_winners)){
                    foreach($arr_winners as $row){
                      $pic_url = !empty($row['web_bny_gift_pic']) ? $gift_pic_base_url.rawurlencode(basename($row['web_bny_gift_pic'])) : '';
                      $detail_short = !empty($row['web_bny_gift_detail']) ? mb_substr(strip_tags($row['web_bny_gift_detail']), 0, 80, 'UTF-8') : '';
                      $winner_name = !empty($row['winner_name']) ? $row['winner_name'] : '';
                      $winner_phone = !empty($row['winner_phone']) ? $row['winner_phone'] : '';
                      $gift_send = (int) (!empty($row['web_bny_gift_send']) ? $row['web_bny_gift_send'] : 0);
                      $reward_id = (int) $row['web_bny_reward_id'];
                ?>
                <tr>
                  <td>
                    <?php if($pic_url != ''){ ?>
                      <img src="<?php echo $pic_url;?>" alt="" style="max-height:56px;max-width:72px;object-fit:contain;">
                    <?php } ?>
                  </td>
                  <td><?php echo htmlspecialchars($detail_short, ENT_QUOTES, 'UTF-8');?></td>
                  <td><?php echo htmlspecialchars($winner_name, ENT_QUOTES, 'UTF-8');?></td>
                  <td><?php echo htmlspecialchars($winner_phone, ENT_QUOTES, 'UTF-8');?></td>
                  <td>
                    <div class="bny-gift-send-cell">
                      <label class="bny-gift-send-switch" title="การส่งของรางวัล">
                        <input type="checkbox" class="gift-send-toggle" data-reward-id="<?php echo $reward_id;?>" <?php echo $gift_send === 1 ? 'checked' : '';?>>
                        <span class="slider"></span>
                      </label>
                      <span class="bny-gift-send-label gift-send-status-text"><?php echo $gift_send === 1 ? 'ส่งของรางวัลแล้ว' : 'ยังไม่ส่งของรางวัล'; ?></span>
                    </div>
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
        </div>
      </div>
    </div>
  </div>
</div>
