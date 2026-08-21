<div class="page">
  <div class="page-header">
    <h1 class="page-title">Authorize signature</h1>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:40px;">
        <?php if (!empty($save_alt) && $save_alt === 'success') { ?>
          <div class="alert alert-success">บันทึกลายเซ็นแล้ว</div>
        <?php } elseif (!empty($save_alt) && strpos($save_alt, 'fail') === 0) { ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($save_alt, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>
        <form role="form" action="<?php echo base_url();?>accounting/authorize_signature/authorize_signature_save" method="post" enctype="multipart/form-data">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <h4 class="example-title">ลายเซ็นอนุมัติเอกสาร (ไฟล์ .png)</h4>
              <div class="form-group">
                <label>ลายเซ็นปัจจุบัน</label>
                <div>
                  <?php if (!empty($signature_url)) { ?>
                    <img src="<?php echo $signature_url; ?>" alt="signature" style="max-height: 120px; border: 1px solid #ddd; padding: 6px; background: #fff;">
                  <?php } else { ?>
                    <span>ยังไม่มีไฟล์</span>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label>อัปโหลดไฟล์ .png</label>
                <input type="file" class="form-control" name="signature_file" accept=".png,image/png" required>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary" name="save_mode" value="update">บันทึกทับ ID ล่าสุด</button>
                <button type="submit" class="btn btn-default" name="save_mode" value="add">เพิ่มเป็น ID ใหม่</button>
              </div>
            </div>
          </div>
        </form>
        <hr>
        <h4>ประวัติลายเซ็น</h4>
        <p style="color:#666">เอกสารที่มีผลผูกพัน (ใบกำกับ / ใบลดหนี้ / ใบสั่งซื้อ) แก้ได้เฉพาะที่ยังอยู่ที่ลายเซ็น ID ล่าสุด พอเพิ่ม ID ใหม่ เอกสารที่ผูก ID เก่าจะแก้ไม่ได้และคงลายเซ็นเดิม แถว legacy แก้ไฟล์ไม่ได้</p>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>วันที่</th>
              <th>ลายเซ็น</th>
              <th>ไฟล์</th>
              <th>สถานะ</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($signature_history)) { ?>
              <tr><td colspan="5">ยังไม่มีประวัติ</td></tr>
            <?php } else {
              $latest_id = !empty($signature_row['web_authorize_signature_id']) ? $signature_row['web_authorize_signature_id'] : 0;
              foreach ($signature_history as $hist) {
                $when = !empty($hist['created_at']) ? $hist['created_at'] : '';
                $img = base_url().'uploads/authorize_signature/'.$hist['file_name'];
                $is_latest = ((int)$hist['web_authorize_signature_id'] === (int)$latest_id);
            ?>
              <tr>
                <td><?php echo (int)$hist['web_authorize_signature_id']; ?></td>
                <td><?php echo htmlspecialchars($when, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><img src="<?php echo $img; ?>" alt="" style="max-height: 60px;"></td>
                <td><?php echo htmlspecialchars($hist['file_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $is_latest ? 'ID ล่าสุด (แก้ได้)' : 'legacy (แก้ไม่ได้)'; ?></td>
              </tr>
            <?php }
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
