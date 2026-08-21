<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="page">
  <div class="page-header">
    <h1 class="page-title">Creditnote</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body" style="padding: 0px !important">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="#" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหาใบลดหนี้</h4>
                  TaxinvoiceType: <input type="radio" name="taxinvoicetype" value=1 checked="checked"> All type <input type="radio" name="taxinvoicetype" value=2> ABB  <input type="radio" name="taxinvoicetype" value=3> Full Taxinvoice    <hr> 
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <select name="platform" id="platform" class="form-control">
                          <option value="0" <?php if($arr_search['platform'] == "0"){echo "selected";}?>>Lazada
                          <option value="1" <?php if($arr_search['platform'] == "1"){echo "selected";}?>>Shopee
                          <option value="2" <?php if($arr_search['platform'] == "2"){echo "selected";}?>>Biggrill
                          <option value="3" <?php if($arr_search['platform'] == "3"){echo "selected";}?>>BigSauces
                       </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                    <div class="form-group">
                      <select name="search_type" id="search_type" class="form-control" onchange="set_search(this);">
                        <option value="0" <?php if($arr_search['search_type'] == "0"){echo "selected";}?>>Select one
                        <option value="1" <?php if($arr_search['search_type'] == "1"){echo "selected";}?>>Date Range
                        <option value="2" <?php if($arr_search['search_type'] == "2"){echo "selected";}?>>ABB Number
                        <option value="3" <?php if($arr_search['search_type'] == "3"){echo "selected";}?>>ABB Number & End Date
                        <option value="4" <?php if($arr_search['search_type'] == "4"){echo "selected";}?>>PlatformOrderNumber
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2" style="display: none" id='order_search'>
                    <div class="form-group">
                      <input type="text" name="order_number" placeholder="Order Number" id="ordernumber" class="form-control" value="<?php echo $arr_search['ordernumber'];?>">
                    </div>
                  </div>
                  <div class="col-md-2" style="display: none" id="date_search">
                    <div class="input-group">                                            
                        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo $arr_search['daterange'];?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-2" id="button_search" style="display: none">
                    <div class="form-group">
                      <div class="col-md-3 offset-md-0">
                        <input type="button" class="btn-primary btn" value="Print CN" id="search">
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
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example" id="highlighting">


          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       