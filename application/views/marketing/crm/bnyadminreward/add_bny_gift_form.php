<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่มของรางวัล</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:40px;">
        <form role="form" name="bny_gift_add_form" id="bny_gift_add_form" action="<?php echo base_url()."marketing/crm/bnyadminreward/bny_gift_add";?>" method="post" enctype="multipart/form-data">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-8">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูลของรางวัล</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">รูป:</label>
                    <div class="col-md-8">
                      <?php
                        $this->load->view('marketing/crm/bnyadminreward/_bny_gift_pic_field', array(
                          'existing_pic_url' => '',
                          'existing_pic_name' => '',
                          'show_keep_hint' => false
                        ));
                      ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">รายละเอียด:</label>
                    <div class="col-md-8">
                      <textarea class="form-control" name="web_bny_gift_detail" id="web_bny_gift_detail" rows="5"></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">ของรางวัลล่าสุด:</label>
                    <div class="col-md-8" style="padding-top:7px;">
                      <label class="checkbox-inline">
                        <input type="checkbox" name="web_bny_gift_now" id="web_bny_gift_now" value="1"> ตั้งเป็นของรางวัลล่าสุด (มีได้เพียง 1 รายการ)
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>marketing/crm/bnyadminreward/bny_gift_list" class="btn btn-outline btn-primary">
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
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
