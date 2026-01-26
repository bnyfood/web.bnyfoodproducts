<style>
        body {
            font-family: "TH Sarabun New", sans-serif;
            margin: 40px;
        }
        .header, .footer {
            text-align: center;
        }
        .company-info, .po-info, .contact-info {
            display: inline-block;
            vertical-align: top;
        }
        .company-info img {
            width: 80px;
        }
        .po-info {
            float: right;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 18px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        .no-border {
            border: none;
        }
        .right-align {
            text-align: right;
        }
        .left-align {
            text-align: left;
        }
        .signature-section {
            margin-top: 40px;
            font-size: 18px;
        }
        .signature-section td {
            padding: 20px;
            text-align: center;
            vertical-align: bottom;
        }
        .bold {
            font-weight: bold;
        }
        .border-bottom-none {
          border-top: none; 
          border-bottom: none; 
          border-left: 1px solid black; 
          border-right: 1px solid black;
        }
    </style>
<div class="page">
  <div class="page-header">
    <h1 class="page-title">Purchase order</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example" id="highlighting">
            <table>
              <tr>
                <td class="left-align" colspan="2" style="border-top: none; border-bottom: none; border-left: 1px solid black; border-right: 1px solid black;">  <strong>ผู้ขาย</strong><br/>
                                <?php echo $supplier_data['supplier_name']?><br/>
                                <?php echo $supplier_data['supplier_address']?><br/>

                </td>
                <td class="left-align" colspan="4" style="border-top: none; border-bottom: none; border-left: 1px solid black; border-right: 1px solid black;">  <strong>การชำระเงิน:</strong> เงินสด<br/>
                  <strong>ผู้ติดต่อ:</strong> <?php echo $supplier_data['supplier_contact_name']?><br/>
                                เบอร์: <?php echo $supplier_data['phoneno1']?><br/>
                                Mail: <?php echo $supplier_data['supplier_email']?><br/>
                                Date: <?php echo $po_cdate?>
                </td>
              </tr>
              <tr>
                <th width="5%">ลำดับ</th>
                <th width="60%">รายละเอียด</th>
                <th width="5%">จำนวน</th>
                <th width="6%">หน่วย</th>
                <th width="12%">ราคาต่อหน่วย</th>
                <th width="12%">จำนวนเงิน</th>
              </tr>
              <?php 

              foreach($arr_pos as $row)
              { 
                $cal_price = $row["unit_price"]*$row["qty"];
                $page_total_price=$page_total_price+$cal_price;
              ?>
              <tr>
                <td class="border-bottom-none"><?php echo $row_runner;?></td>
                <td class="border-bottom-none left-align"><?php echo $row["material_name"];?></td>
                <td class="border-bottom-none"><?php echo $row["qty"];?></td>
                <td class="border-bottom-none"><?php echo $row["material_unit"];?></td>
                <td class="border-bottom-none right-align"><?php echo number_format($row["unit_price"],2,".",",");?></td>
                <td class="border-bottom-none right-align"><?php echo number_format($cal_price,2,".",",");?></td>
              </tr>
              <?php } ?>       
              <tr>
                <td colspan="2" style="border-bottom: none;"></td>
                <td class="left-align" colspan="3" style="border: 1px solid black;"><strong>รวมเงิน</strong></td>
                <td class="right-align"  style="border: 1px solid black;"><?php echo number_format($page_total_price,2,".",","); ?></td>
              </tr>
              <?php 
                $total_vat = $page_total_price*0.07;
                $total_price_vat = $page_total_price+ $total_vat;

              ?>
              <tr>
                <td colspan="2" class="border-bottom-none"></td>
                <td class="left-align" colspan="3" style="border: 1px solid black;">ภาษีมูลค่าเพิ่ม VAT 7%</td>
                <td class="right-align" style="border: 1px solid black;"><?php echo number_format($total_vat,2,".",","); ?></td>
              </tr>
              <tr>
                <td colspan="2" >(<?php echo baht_text($total_price_vat); ?>)</td>
                <td class="left-align" colspan="3" style="border: 1px solid black;"><strong>รวมยอดทั้งสิ้น</strong></td>
                <td class="right-align" style="border: 1px solid black;"><strong><?php echo number_format($total_price_vat,2,".",","); ?></strong></td>
              </tr>
            </table>
            <!-- Authen user  -->
            <form role="form" name="usergroup_add_form" id="usergroup_add_form" action="<?php echo base_url()."purchase_order/po_change_status";?>" method="post">
            
            <input type="radio" name="po_approve" value="1"> Approve
            <input type="radio" name="po_approve" value="0" checked> Not Approve

            <input type="hidden" name="po_number" value="<?php echo $po_number;?>">
            <input type="hidden" name="web_supplier_id" value="<?php echo $web_supplier_id;?>">
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       

<!-- ======= Modal Delete == --->

<div
    class="modal fade"
    id="ModalDelPo"
    tabindex="-1"
    role="dialog"
    aria-labelledby="ModalDelLabel"
    aria-hidden="true"
  >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalDelLabel">Delete</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure to delete this Purchase order!!!</p>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-dismiss="modal"
        >
          Close
        </button>
        <button
          id="del_po_btn"
          type="button"
          class="btn btn-primary"
          data-dismiss="modal"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</div>



<script> // กำหนดปุ่มเป็น disable ไว้ ต้องทำ reCHAPTCHA ก่อนจึงกดได้
  function makeaction(){
        document.getElementById('pop_login_btn').disabled = false;  
  }
  </script>