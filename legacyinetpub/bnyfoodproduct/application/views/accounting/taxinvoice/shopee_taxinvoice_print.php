<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!--Author      : @arboshiki-->
<style type="text/css">

    .page {
  width: 21cm;
  min-height: 29.7cm;
  padding: 2cm;
  margin: 1cm auto;
  border: 1px #D3D3D3 solid;
  border-radius: 5px;
  background: white;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.subpage {
  padding: 1cm;
  border: 5px red solid;
  height: 256mm;
  outline: 2cm #FFEAEA solid;
}

@page {
  size: A4;
  margin: 0;
}

@media print {
  .page {
    margin: 0;
    border: initial;
    border-radius: initial;
    width: initial;
    min-height: initial;
    box-shadow: initial;
    background: initial;
    page-break-after: always;
  }
}

    
    #invoice{
    padding: 35px;
}

.invoice {
    position: relative;
    background-color: #FFF;
    min-height: 100px;
    padding: 15px
}

.invoice header {
    padding: 10px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #3989c6
}

.invoice .company-details {
    text-align: right
}

.invoice .company-details .name {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .contacts {
    margin-bottom: 20px
}

.invoice .invoice-to {
    text-align: left
}

.invoice .invoice-sign {
    text-align: center;
    border-style: groove;
    margin-left: 1.5em;
}

.invoice .invoice-to .to {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .invoice-details {
    text-align: right
}

.invoice .invoice-details .invoice-id {
    margin-top: 0;
    color: #3989c6
}

.invoice main {
    padding-bottom: 50px
}

.invoice main .thanks {
    margin-top: -100px;
    font-size: 2em;
    margin-bottom: 50px
}

.invoice main .notices {
    padding-left: 6px;
    border-left: 6px solid #3989c6
}

.invoice main .notices .notice {
    font-size: 1.2em
}

.invoice table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 20px
}

.invoice table td,.invoice table th {
    padding: 10px;
    background: #eee;
    border-bottom: 1px solid #fff
}

.invoice table th {
    white-space: nowrap;
    font-weight: 400;
    font-size: 16px
}

.invoice table td h3 {
    margin: 0;
    font-weight: 400;
    color: #3989c6;
    font-size: 0.8em
}

.invoice table .qty,.invoice table .total,.invoice table .unit {
    text-align: right;
    font-size: 1em
}

.invoice table .no {
    background: #e1f2ff !important;
     color: #000 !important;
     font-size: 0.8em
}

.invoice table .total {
    background: #e1f2ff !important;
     color: #000 !important;
}

.invoice table tr {

     height: 25px !important;
}

.invoice table .no {
    color: #fff;

    background: #3989c6
}

.invoice table .unit {
    background: #ddd
}

.invoice table .total {
    background: #3989c6;
    color: #fff
}

.invoice table tbody tr:last-child td {
    border: none
}

.invoice table tfoot td {
    background: 0 0;
    border-bottom: none;
    white-space: nowrap;
    text-align: right;
    padding: 10px 20px;
    font-size: 1.2em;
    border-top: 1px solid #aaa
}

.invoice table tfoot tr:first-child td {
    border-top: none
}

.invoice table tfoot tr:last-child td {
    color: #3989c6;
    font-size: 1.4em;
    border-top: 1px solid #3989c6
}

.invoice table tfoot tr td:first-child {
    border: none
}

.invoice footer {
    width: 100%;
    text-align: center;
    color: #777;
    border-top: 1px solid #aaa;
    padding: 8px 0
}
.sign-line{
    border-bottom: 1px dotted;
    width:200px;
    margin:50px 0px 10px 23px;

}
.sign-auth{
    border-bottom: 1px dotted;
    margin:50px 0px 10px 23px;
    width:200px;
}
.bottomline:after{
    content: " ";
    border-bottom-style: dotted;
    border-bottom-width:2px;
    display:table-cell;
    width:130px;
}


</style>
<?php for($i=1;$i<=$num_page;$i++){?>
<div id="invoice">
    <div class="invoice overflow-auto">
        <div>
            <header>
                <div class="row" style="text-align: center">
                    <div class="col">
                        <h2 class="invoice-id">ใบกำกับภาษี</h2>
                    </div>
                </div>
                <div class="row">
     
                    <div class="col ">
                        <h3 class="invoice-id">
                            <?php echo $arr_company['company_name']?>
                        </h3>
                        <div>
                            <?php echo $arr_address['address']." ";?>
                            <?php if(!empty($arr_address['moo'])){echo 'หมู่ '.$arr_address['moo']." ";}?>
                            <?php if(!empty($arr_address['road'])){echo 'ถ.'.$arr_address['road']." ";}?>
                            <?php if(!empty($arr_address['tumbon'])){echo 'ต.'.$arr_address['tumbon']." ";}?>
                            <?php if(!empty($arr_address['aumper'])){echo 'อ.'.$arr_address['aumper']." ";}?>
                            <?php if(!empty($arr_address['province'])){echo 'จ.'.$arr_address['province']." ";}?>
                            <?php if(!empty($arr_address['postcode'])){echo $arr_address['postcode'];}?>
                        </div>
                        <div>เลขที่ผู้เสียภาษี : 13456 โทร : <?php echo $arr_company['phone']?></div>
                    </div>
                    <div class="col company-details">
                        <?php echo $i."/".$num_page?>
                        <h5 class="invoice-id">เลขที่ : <?php echo $arr_taxinvoice['FullTaxinvoiceID'];?></h5>
                        <div class="date">วันที่: <?php echo $arr_order['shipdate']?></div>
                    </div>
                </div>
            </header>
            <main>
                <div style="min-height: 800px">
                    <div class="row contacts">
                        <div class="col invoice-to">
                            <div class="text-gray-light">ลูกค้า</div>
                            <div class="to">ชื่อ : <?php echo $arr_taxinvoice['name'];?></div>
                            <div class="address">ที่อยู่ : <?php echo $arr_taxinvoice['address1'];?>
                                
                                <?php if($arr_taxinvoice['invoice_type'] == 1 ){echo "บุคคล";}elseif($arr_taxinvoice['invoice_type'] == 2 ){
                                    echo "นิติบุคคล";
                                    if($arr_taxinvoice['is_head_office'] == 2 ){
                                        echo " สำนักงานใหญ่";
                                        echo " ".$arr_taxinvoice['branch_number'];
                                    }
                                }?>
                                
                            </div>
                            <div class="address">
                                เลขที่ผู้เสียภาษี : <?php echo $arr_taxinvoice['TaxNo'];?>
                                โทร : <?php echo $arr_taxinvoice['phone'];?>
                            </div>
                        </div>
                    </div>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th class="text-left" width="80%">รายละเอียด</th>
                                <th class="text-right"width="5%">จำนวน</th>
                                <th class="text-right"width="5%">ราคาต่อหน่วย</th>
                                <th class="text-right"width="5%">ยอดรวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 

                                $loop_item = 9;
                                $start_loop = 0;

                                if(($i == 1)and($i == $last_page)){
                                    $loop_item = $last_item-1;
                                } 

                                if($i > 1){
                                    $num_j = $i-1;
                                    $start_loop = ($num_j*10);
                                    $loop_item = $start_loop + 9;

                                    if($i == $last_page){
                                        $loop_item = ($start_loop+$last_item)-1;
                                    }  
                                }
                                
                                   
                                $numcount=0;
                                  $priceacc=0;
                                  $acc_qty=0;
                                  $shipping_fee = 0;
                                  $discount = 0;
                                for($j=$start_loop;$j<=$loop_item;$j++){?>
                                <tr>
                                    <td class="no"><?php echo $j+1;?></td>
                                    <td class="text-left">
                                        <h3><?php echo $arr_items[$j]['ProductName'];?></h3>
                                    </td>
                                    <td class="unit"><?php echo $arr_items[$j]['qty'];?></td>
                                    <td class="qty"><?php echo $arr_items[$j]['item_price'];?></td>
                                    <td class="total"><?php echo $arr_items[$j]['item_price'];?></td>
                                </tr>
                            <?php 
                                $numcount++;
                                 $priceacc=$priceacc+$arr_items[$j]["amount"];
                                 $acc_qty += $arr_items[$j]["qty"];
                                 $shipping_fee += $arr_items[$j]["shipping_fee"];
                                 $discount += $arr_items[$j]["discount"];
                                }
                                $price=$priceacc+$shipping_fee-$discount;
                                  $pricebeforeVAT=$price/1.07;
                                  $VAT=$price-$pricebeforeVAT;
                            ?>
                        </tbody>
                        <?php if($i == $last_page){?>
                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">จำนวนรวม</td>
                                <td><?php echo $acc_qty;?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">จำนวนเงินรวมทั้งสิ้น</td>
                                <td><?php echo number_format($priceacc,2);?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">ค่าขนส่ง</td>
                                <td><?php echo number_format($shipping_fee,2);?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">ส่วนลด</td>
                                <td><?php echo number_format($discount,2);?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">จำนวนเงินหลังหักส่วนลด</td>
                                <td><?php echo number_format($price,2);?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">ภาษีมูลค่าเพิ่ม 7%</td>
                                <td><?php echo round($VAT,2);?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม</td>
                                <td><?php echo round($pricebeforeVAT,2);?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="3">จำนวนเงินรวมทั้งสิ้น (<?php echo $sum_before_vat_txt;?>)</td>
                            </tr>
                        </tfoot>
                    <?php }?>
                    </table>
                    
                </div>
                <div class="row contacts">
                    <div class="col invoice-sign">
                        
                            <div class="sign-line"></div>
                        
                        <div class="address">ผู้รับสินค้า / RECEIVER</div>
                        <div style="margin: 0px 0px 10px 0px">
                            <span class="bottomline">วันที่ / DATE</span>
                        </div>    
                        
                    </div>
                    <div class="col invoice-sign">
                        
                            <div class="sign-line"></div>
                        
                        <div class="address">ผู้ส่งสินค้า / SENDER</div>
                        <div style="margin: 0px 0px 10px 0px">
                            <span class="bottomline">วันที่ / DATE</span>
                        </div>
                    </div>
                    <div class="col invoice-sign">
                        <div class="sign-auth"></div>
                        <div class="address">ผู้มีอำนาจอนุมัติ / AUTHORIZER</div>
                    </div>
                </div>
            </main>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>
<?php } ?>
<script>
     $('#printInvoice').click(function(){
            Popup($('.invoice')[0].outerHTML);
            function Popup(data) 
            {
                window.print();
                return true;
            }
        });
    </script>