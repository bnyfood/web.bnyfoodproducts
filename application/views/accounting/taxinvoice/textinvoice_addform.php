<div class="page">
  <div class="page-header">
    <h1 class="page-title">ใบกำกับภาษีเต็มรูป</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <?php if($this->session->flashdata('alertbox') == 1){?>
          <div class="alert alert-secondary border-0" role="alert" id="dialog_alert">
              <strong>Success!</strong> Data update success.
          </div>
        <?php }?>
        <form name="invoice_action" id="invoice_action" action="<?php echo base_url()."accounting/taxinvoice/textinvoice_action"?>" method="post">
          <div class="row">
              <div class="col-lg-6">
                  <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label text-right">Name</label>
                      <div class="col-sm-10">
                          <input class="form-control" type="text" name="name"  id="example-text-input" value="<?php echo $arr_taxinvoice['name']?>">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label for="example-email-input" class="col-sm-2 col-form-label text-right">Address 1</label>
                      <div class="col-sm-10">
                          <textarea class="form-control" name="address1" rows="5" id="message"><?php echo $arr_taxinvoice['address1']?></textarea>
                      </div>
                  </div> 
                  <div class="form-group row">
                      <label for="example-tel-input" class="col-sm-2 col-form-label text-right">Tax</label>
                      <div class="col-sm-10">
                          <input class="form-control" type="text" name="Taxno" id="example-tel-input" value="<?php echo $arr_taxinvoice['TaxNo']?>">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label for="example-password-input" class="col-sm-2 col-form-label text-right">Note</label>
                      <div class="col-sm-10">
                          <textarea class="form-control" name="note" rows="5" id="message"><?php echo $arr_taxinvoice['Note']?></textarea>
                      </div>
                  </div>                             
              </div>


              <div class="col-lg-6">
                  <div class="form-group row">
                      <label for="example-search-input" class="col-sm-2 col-form-label text-right">Phone</label>
                      <div class="col-sm-10">
                          <input class="form-control" name="phone" type="text"  id="example-search-input" value="<?php echo $arr_taxinvoice['phone']?>">
                      </div>
                  </div> 
                  <div class="form-group row">
                      <label for="example-url-input" class="col-sm-2 col-form-label text-right">Address 2</label>
                      <div class="col-sm-10">
                          <textarea class="form-control" name="address2" rows="5" id="message"><?php echo $arr_taxinvoice['address2']?></textarea>
                      </div>
                  </div> 
                  <div class="form-group row">
                      <label for="example-date-input" class="col-sm-2 col-form-label text-right">Zip</label>
                      <div class="col-sm-10">
                          <input class="form-control" name="zip" type="text" value="<?php echo $arr_taxinvoice['zip']?>">
                      </div>
                  </div>   
                  <div class="form-group row">
                    <label class="col-md-3 my-1 control-label">ประเภท</label>
                        <div class="col-md-9">
                            <div class="form-check-inline my-1">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="invoice_type1" name="invoice_type" class="custom-control-input" value="1" <?php if(empty($arr_taxinvoice['invoice_type']) || $arr_taxinvoice['invoice_type'] == 1){echo "checked";}?> >
                                    <label class="custom-control-label" for="invoice_type1">บุคคล</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-1">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="invoice_type2" name="invoice_type" class="custom-control-input" value="2" <?php if($arr_taxinvoice['invoice_type'] == 2){echo "checked";}?>>
                                    <label class="custom-control-label" for="invoice_type2">นิติบุคคล</label>
                                </div>
                            </div>
                        </div>
                        <?php 
                          $style_head = "none";
                          if($arr_taxinvoice['invoice_type'] == 2){
                            $style_head = "contents";
                          }
                        ?>
                        <div id="headoffice" style="display: <?php echo $style_head;?>">
                          <label class="col-md-3 my-1 control-label">สำนักงานใหญ่</label>
                          <div class="col-md-9">
                              <div class="form-check-inline my-1">
                                  <div class="custom-control custom-radio">
                                      <input type="radio" id="head_office1" name="is_head_office" class="custom-control-input" value="1" <?php if(empty($arr_taxinvoice['is_head_office']) || $arr_taxinvoice['is_head_office'] == 1){echo "checked";}?>>
                                      <label class="custom-control-label" for="head_office1">ใช่</label>
                                  </div>
                              </div>
                              <div class="form-check-inline my-1">
                                  <div class="custom-control custom-radio">
                                      <input type="radio" id="head_office2" name="is_head_office" class="custom-control-input" value="2" <?php if($arr_taxinvoice['is_head_office'] == 2){echo "checked";}?>>
                                      <label class="custom-control-label" for="head_office2">ไม่ใช่</label>
                                  </div>
                              </div>
                          </div>
                      </div>
                    <?php 
                          $style_branch = "none";
                          if($arr_taxinvoice['is_head_office'] == 2){
                            $style_branch = "contents";
                          }
                        ?>    
                      <div id="branch_number" style="display: <?php echo $style_branch;?>">
                        <label for="example-date-input" class="col-sm-2 col-form-label text-right">เลขที่สาขา</label>
                      <div class="col-sm-10">
                          <input class="form-control" name="branch_number" type="text" value="<?php echo $arr_taxinvoice['branch_number']?>">
                      </div>
                      </div>  
                  </div>                                
              </div>
          </div>         
          <button type="submit" class="btn btn-primary">Submit</button>
          <button type="button" class="btn btn-danger" onClick="window.parent.jQuery.fancybox.close();">Cancel</button>
          <input type="hidden" name="order_number" value="<?php echo $order_number?>">
          <input type="hidden" name="is_action" value="<?php echo $is_action?>">
          <input type="hidden" name="platform" value="<?php echo $platform?>">
      </form>

      </div>
    </div>
  </div> 
</div>       