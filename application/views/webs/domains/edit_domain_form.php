<div class="page">
  <div class="page-header">
    <h1 class="page-title">Edit Domain</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px">
    <div class="panel panel_box" style="margin:20px 20px 20px 20px">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:5px;">
        <div class="example-wrap" style="margin:10px 10px 10px 10px">
          <h4 class="example-title">Domain</h4>
          <div class="example">
            <form role="form" name="domain_edit_form" id="domain_edit_form" action="<?php echo base_url()."webs/domains/domain_edit";?>" method="post">
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Domain : </label>
                <div class="col-md-7">
                  <input type="text" class="form-control" name="web_domain_name" id='web_domain_name' autocomplete="off" value="<?php echo htmlspecialchars($arr_domain['web_domain_name'], ENT_QUOTES, 'UTF-8');?>" required>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Registrar link : </label>
                <div class="col-md-7">
                  <input type="text" class="form-control" name="registrar_link" id="registrar_link" placeholder="https://..." autocomplete="off" value="<?php echo htmlspecialchars(isset($arr_domain['registrar_link']) ? $arr_domain['registrar_link'] : '', ENT_QUOTES, 'UTF-8');?>">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 col-form-label">SSL link : </label>
                <div class="col-md-7">
                  <input type="text" class="form-control" name="ssl_link" id="ssl_link" placeholder="https://..." autocomplete="off" value="<?php echo htmlspecialchars(isset($arr_domain['ssl_link']) ? $arr_domain['ssl_link'] : '', ENT_QUOTES, 'UTF-8');?>">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Expiration date : </label>
                <div class="col-md-7">
                  <input type="date" class="form-control" name="expire_date" id="expire_date" autocomplete="off" value="<?php echo htmlspecialchars(isset($arr_domain['expire_date']) ? $arr_domain['expire_date'] : '', ENT_QUOTES, 'UTF-8');?>">
                </div>
              </div>
              <div class="form-group" >
                <a href="<?php echo base_url();?>webs/domains/domains_list" id="addToTable" class="btn btn-outline btn-primary" >
                  <i class="icon wb-arrow-left" aria-hidden="true"></i> Back
                </a>
                <input type="hidden" name="id_en" id="id_en" value="<?php echo htmlspecialchars($arr_domain['web_domain_id'], ENT_QUOTES, 'UTF-8');?>">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>
