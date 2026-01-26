<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่ม Expense</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form role="form" name="user_add_form" id="user_add_form" action="<?php echo base_url()."expense/expense_add";?>" method="post" enctype="multipart/form-data">
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">วันที่ : </label>
                    <div class="form-row flex-nowrap">
                      <div class="col-md-4">
                        <div class="form-group">
                          <input type="text" class="form-control" id="expensedate" name="expense_date">
                        </div> 
                      </div>
                      <div class="col-md-1">
                          <select class="form-control" name="expen_time_hr" id="expen_time_hr">
                          <?php 
                            for($i=0;$i<=23;$i++){
                              $hr_val = $i;

                              if($i < 10){
                                $hr_val= "0".$i;
                              }

                          ?>
                            <option value="<?php echo $hr_val;?>" ><?php echo $hr_val;?></option>
                          <?php }?>
                          </select>
                      </div>
                      <div class="col-md-1">
                        <select class="form-control" name="expen_time_min" id="expen_time_min">
                        <?php 
                          for($x=0;$x<=59;$x++){
                            $min_val = $x;

                            if($x < 10){
                              $min_val= "0".$x;
                            }

                        ?>
                          <option value="<?php echo $min_val;?>" ><?php echo $min_val;?></option>
                        <?php }?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <h1 class="page-title">PO Number</h1>
                  <div class="form-group row">
                    PO Search :
                    <div class="form-row flex-nowrap">
                      <div class="col-md-4">
                        <input type="text" class="form-control" id="po_search" name="po_search" >
                      </div>
                      <div class="col-md-3">
                        <input type="button" class="btn btn-primary" name="btn_po_search" id="btn_po_search" value="Search">
                        <input type="button" class="btn btn-primary" name="btn_po_search_all" id="btn_po_search_all" value="Clear">
                      </div>
                    </div>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Po Number</th>
                          <th>Supplier name</th>
                          <th>Po Amount</th>
                          <th>Po Date</th>
                          <th class="text-nowrap">ตัวเลือก</th>
                        </tr>
                      </thead>
                      <tbody id="po-list">
                      </tbody>
                    </table>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Supplier : </label>
                    <div class="col-md-12">
                      <input type="radio" name="supplier_type" id="supplier_type" value="1" checked>Search
                      <input type="radio" name="supplier_type" id="supplier_type" value="2">Add New Supplier
                      <div id="supplier_search">

                        Supplier Search : 
                        <div class="form-row flex-nowrap">
                          <div class="col-md-4">
                            <input type="text" class="form-control" id="suppliertxt_search" name="suppliertxt_search" >
                          </div>
                          <div class="col-md-3">
                            <input type="button" class="btn btn-primary" name="btn_supplier_search" id="btn_supplier_search" value="Search">
                            <input type="button" class="btn btn-primary" name="btn_supplier_search_all" id="btn_supplier_search_all" value="Clear">
                          </div>
                        </div>

                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Supplier Name</th>
                              <th class="text-nowrap">ตัวเลือก</th>
                            </tr>
                          </thead>
                          <tbody id="supplier-list">
                          </tbody>
                        </table>
                      </div>
                      <div id="supplier_add_form" style="display: none;">

                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">Supplier Name : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="supplier_name" id='supplier_name'  autocomplete="off">
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ยอด : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="expense_amount" id='expense_amount'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">VAT : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="expense_vat" id='expense_vat'  autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Bank Account : </label>
                    <div class="col-md-12">
                      <input type="radio" name="account_type" id="account_type" value="1" checked>Search
                      <input type="radio" name="account_type" id="account_type" value="2">Add New Account
                      <div id="account_search">

                        Bookbank Search : 
                        <div class="form-row flex-nowrap">
                          <div class="col-md-4">
                            <input type="text" class="form-control" id="accounttxt_search" name="accounttxt_search" >
                          </div>
                          <div class="col-md-3">
                            <input type="button" class="btn btn-primary" name="btn_account_search" id="btn_account_search" value="Search">
                            <input type="button" class="btn btn-primary" name="btn_account_search_all" id="btn_account_search_all" value="Clear">
                          </div>
                        </div>

                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Account Number</th>
                              <th>Account Name</th>
                              <th>Bank</th>
                              <th class="text-nowrap">ตัวเลือก</th>
                            </tr>
                          </thead>
                          <tbody id="bankaccount-list">
                          </tbody>
                        </table>
                      </div>
                      <div id="account_add_form" style="display: none;">

                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">ธนาคาร : </label>
                          <div class="col-md-6">
                            <select class="form-control" name="bankaccount_id" id="bankaccount_id">
                              <option value="0">กรุณาเลือก</option> 
                              <?php 
                                foreach($arr_banks as $arr_bank){
                              ?>
                              <option value="<?php echo $arr_bank['bankaccount_id']?>" ><?php echo $arr_bank['bank_name_th']?></option>
                              <?php }?> 
                            </select> 
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">Bookbank Number : </label>
                          <div class="col-md-6">
                            <input type="number" class="form-control" name="bank_number" id='bank_number'  autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-6 col-form-label">Bookbank Name : </label>
                          <div class="col-md-6">
                            <input type="text" class="form-control" name="bookbank_name" id='bookbank_name'  autocomplete="off">
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Slip : </label>
                    <div class="col-md-6">
                      <input type="file" class="custom-file-input" id="upload_slip" name="upload_slip" accept=".jpg,.gif,.png">
                      <label class="custom-file-label" for="upload_slip">Slip</label>
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">Invoice : </label>
                    <div class="col-md-6">
                      <input type="file" class="custom-file-input" id="upload_invoice" name="upload_invoice" accept=".jpg,.gif,.png">
                      <label class="custom-file-label" for="upload_invoice">Invoice</label>
                    </div>
                  </div>  
                  
                  <div class="form-group">
                    <a href="<?php echo base_url();?>config_system/users/user_list" id="addToTable" class="btn btn-outline btn-primary" >
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


