<div class="page">
  <div class="page-header">
    <h1 class="page-title">แก้ไขของรางวัล</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:40px;">
        <form role="form" name="bny_gift_edit_form" id="bny_gift_edit_form" action="<?php echo base_url()."marketing/crm/bnyadminreward/bny_gift_edit";?>" method="post" enctype="multipart/form-data">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-8">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูลของรางวัล</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">รูป:</label>
                    <div class="col-md-8">
                      <?php
                        $gift_pic_file = !empty($arr_gift['web_bny_gift_pic']) ? $arr_gift['web_bny_gift_pic'] : '';
                        $this->load->view('marketing/crm/bnyadminreward/_bny_gift_pic_field', array(
                          'existing_pic_url' => ($gift_pic_file !== '')
                            ? $gift_pic_base_url . rawurlencode(basename($gift_pic_file))
                            : '',
                          'existing_pic_name' => ($gift_pic_file !== '') ? basename($gift_pic_file) : 'รูปปัจจุบัน',
                          'show_keep_hint' => true
                        ));
                      ?>
                      <input type="hidden" name="web_bny_gift_pic_old" value="<?php echo !empty($arr_gift['web_bny_gift_pic']) ? htmlspecialchars($arr_gift['web_bny_gift_pic'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">รายละเอียด:</label>
                    <div class="col-md-8">
                      <textarea class="form-control" name="web_bny_gift_detail" id="web_bny_gift_detail" rows="5"><?php echo !empty($arr_gift['web_bny_gift_detail']) ? htmlspecialchars($arr_gift['web_bny_gift_detail'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">ของรางวัลล่าสุด:</label>
                    <div class="col-md-8" style="padding-top:7px;">
                      <label class="checkbox-inline">
                        <input type="checkbox" name="web_bny_gift_now" id="web_bny_gift_now" value="1" <?php echo (!empty($arr_gift['web_bny_gift_now']) && (int)$arr_gift['web_bny_gift_now'] === 1) ? 'checked' : ''; ?>> ตั้งเป็นของรางวัลล่าสุด (มีได้เพียง 1 รายการ)
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/bny_gift_list" class="btn btn-outline btn-primary">
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <input type="hidden" name="id_en" id="id_en" value="<?php echo $id_en; ?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
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
