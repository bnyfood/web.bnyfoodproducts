<div class="page">
  <div class="page-header">
    <h1 class="page-title">Add Domain</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px">
    <div class="panel panel_box" style="margin:20px 20px 20px 20px">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding-bottom:5px;">
        <div class="example-wrap" style="margin:10px 10px 10px 10px">
          <div class="example">
            <form role="form" name="domain_add_form" id="domain_add_form" action="<?php echo base_url()."webs/domains/domain_add";?>" method="post">
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Domain : </label>
                <div class="col-md-7">
                  <input type="text" class="form-control" name="web_domain_name" id='web_domain_name' autocomplete="off" required>
                </div>
              </div>
              <div class="form-group">
                <a href="<?php echo base_url();?>webs/domains/domains_list" id="addToTable" class="btn btn-outline btn-primary" >
                  <i class="icon wb-arrow-left" aria-hidden="true"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>
