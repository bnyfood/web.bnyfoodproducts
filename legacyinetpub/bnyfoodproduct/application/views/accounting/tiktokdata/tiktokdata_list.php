<div class="page">
  <div class="page-header">
    <h1 class="page-title">TikTok Data</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px; margin-left: 10px;">
    <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div style="padding:16px 18px 22px 18px;">
          <?php if(!empty($import_alt) && $import_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
              นำเข้าข้อมูลสำเร็จ
            </div>
          <?php }?>
          <?php if(!empty($import_alt) && $import_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
              นำเข้าข้อมูลไม่สำเร็จ
            </div>
          <?php }?>
          <form name="import_tiktok_data" id="import_tiktok_data" action="<?php echo base_url()."accounting/tiktokdata/tiktok_import_data_action";?>" method="post" enctype="multipart/form-data" style="margin:2px 0 6px 0;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
              <input type="file" id="upload_file1" name="upload_file1" />
              <button type="submit" id="importfile" class="btn btn-primary">Import</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
