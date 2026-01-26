<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="page">
  <div class="page-header">
    <h1 class="page-title">TaxInvoice</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body" style="padding: 0px !important">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."accounting/taxinvoice/taxinvoice_search";?>" method="post">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหาใบกำกับภาษี</h4>
                  TaxinvoiceType: <input type="radio" name="taxinvoicetype" value=1 checked="checked"> All type <input type="radio" name="taxinvoicetype" value=2> ABB  <input type="radio" name="taxinvoicetype" value=3> Full Taxinvoice    <hr>             
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <select name="platform" id="platform" class="form-control">
                            <option value="0" <?php if($arr_search['platform'] == "0"){echo "selected";}?>>Lazada
                            <option value="1" <?php if($arr_search['platform'] == "1"){echo "selected";}?>>Shopee
                            <option value="2" <?php if($arr_search['platform'] == "2"){echo "selected";}?>>Tiktok
                            <option value="3" <?php if($arr_search['platform'] == "3"){echo "selected";}?>>Biggrill
                            <option value="4" <?php if($arr_search['platform'] == "4"){echo "selected";}?>>BigSauces
                         </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <select name="search_type" id="search_type" class="form-control" onchange="set_search(this);">
                          <option value="0" <?php if($arr_search['search_type'] == "0"){echo "selected";}?>>Select one
                          <option value="1" <?php if($arr_search['search_type'] == "1"){echo "selected";}?>>Search by DateRange
                          <option value="2" <?php if($arr_search['search_type'] == "2"){echo "selected";}?>>Search by OrderNumber
                          
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
                    <div class="col-md-1" id="button_search1" style="display: none">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                           <input type="button" class="btn-primary btn" value="Print ABB" id="search">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-1" id="button_search2" style="display: none">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                           <button type="submit" id="but_invoice" class="btn-primary btn">ออกใบกำกับภาษีเต็มรูป</button>  
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
          <div class="example table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>LazOrderNumber</th>
                  <th>TaxInvoice(ABB)</th>
                  <th>TaxInvoice</th>
                  <th>OrderDate</th>
                  <th>Shipping Fee</th>
                  <th>Voucher</th>
                  <th>Amount</th>
                  <th>WantTaxinvoice</th>
                  <th>Status</th>
                  <th class="text-nowrap">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                    if(!empty($arr_orders)){
                      $row_runer=1;
                    foreach($arr_orders as $arr_order){
                ?>
                  <tr>
                    <td><?php echo $row_runer;?></td>
                    <td><?php echo $arr_order['order_number']?></td>
                    <td><?php echo $arr_order['taxinvoiceID']?></td>
                    <td>
                      <?php 
                       if(!empty($arr_order["FullTaxinvoiceID"])){echo $arr_order["FullTaxinvoiceID"];}else{echo "-";}
                      ?>
                    </td>
                    <td><?php $datearr=explode(" ",$arr_order["created_at"]); echo $datearr[0];?></td>
                    <td><?php echo $arr_order['shipping_fee']?></td>
                    <td><?php echo $arr_order['voucher']?></td>
                    <td><?php echo $arr_order['price']?></td>
                    <td><?php echo $arr_order['want_taxinvoice']?></td>
                    <td><?php echo $arr_order['status']?></td>
                    <td class="text-nowrap">
                      <?php //if($arr_order['status'] == "shipped"){?>
                        <a href="<?php echo base_url();?>accounting/taxinvoice/textinvoice_addform/<?php echo $arr_order["order_number"];?>/<?php echo $arr_search['platform'];?>" id="fancy_main" class="btn btn-outline btn-primary">ออกใบกำกับเต็มรูป</a>
                        <a href="javascript:void(0)" class="btn btn-outline btn-primary" onclick="window.open('<?php echo base_url();?>accounting/taxinvoice/taxinvoice_print/<?php echo $arr_order["order_number"];?>', '_blank', 'location=yes,height=1000,width=1200,scrollbars=yes,status=yes');">พิมพ์ใบ</a>
                      <?php //}?>
                    </td>
                  </tr>
                <?php $row_runer+=1;}}?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       
