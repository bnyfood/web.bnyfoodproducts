<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."accounting/saletaxreport/saletaxreport_prep"?>" method="post" enctype="multipart/form-data">
                <div class="panel-body">
                  <h4 class="example-title">ตรวจสอบความถูกต้องข้อมูล ระหว่าง API และ Report จาก Platform(Excel) </h4>
                  <hr>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <select name="platform" id="platform" class="form-control">
                            <option value="0" <?php if($arr_search['platform'] == 0){echo "selected";}?>>Lazada
                            <option value="1" <?php if($arr_search['platform'] == 1){echo "selected";}?>>Shopee
                            <option value="2" <?php if($arr_search['platform'] == 2){echo "selected";}?>>Tiktok
                            <option value="3" <?php if($arr_search['platform'] == 3){echo "selected";}?>>BigSauces
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="input-group">                                            
                        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo $arr_search['daterange']?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                        <input type="file" class="form-control" id="upload_file1" name="upload_file1" />
                      </div>
                    </div>
                    <?php if($arr_sho_prep['total_price_api'] == $arr_sho_prep['total_price_cn_excel']){?>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                          <input type="submit" class="btn-primary btn" value="Check" id="chk_prep">
                           <!--<input type="button" class="btn-primary btn" value="Search" id="search"> -->
                        </div>
                      </div>
                    </div>
                  <?php }?>
                  </div>
                  
                </div>
            </form>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
                <div class="example table-responsive">
                <?php if($is_chk == "YES"){?>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>แหล่งข้อมูล</th>
                      <th>ยอด</th>
                    </tr>
                  </thead>
                  <?php 
                    $shopee_diff = $arr_data_prep['total_price_api']-$arr_data_prep['total_price_cn_excel'];
                  ?>
                  <tbody>
                      <tr>
                        <td><?php echo "API"?></td>
                        <td><?php echo $arr_data_prep['total_price_api'];?></td>
                      </tr>
                      <tr>
                        <td><?php echo "Excel"?></td>
                        <td><?php echo $arr_data_prep['total_price_cn_excel'];?></td>
                      </tr>
                      <tr>
                        <td><?php echo "ผลต่าง"?></td>
                        <td><?php echo $shopee_diff;?></td>
                      </tr>
                      <td colspan="2">
                          <?php if($shopee_diff == 0){?>
                        <tr>
                          <td colspan="2">
                            <input type="button" class="btn-primary btn" value="พิมพ์รายงานภาษีขาย" id="search">
                            <?php //print_r($arr_data_prep['arr_order_check']);?>
                          </td>
                        </tr>  
                        <?php }else{?>
                          <tr style="background-color: red;">
                            <td colspan="2">
                              <!--<input type="button" class="btn-primary btn" value="พิมพ์รายงานภาษีขาย" id="search"> -->
                              ไม่สามารถออกรายงานภาษีขายได้ กรุณาติดต่อ Admin
                              <?php if($customer_type == 2){ ?>
                                <input type="button" class="btn-primary btn" value="พิมพ์รายงานภาษีขาย" id="search">
                              <?php }?>
                            </td>
                          </tr>
                        <?php }?>
                  </tbody>
                </table>
                <?php 
                $cnt_prep_order = count($arr_data_prep['arr_order_check']);
                  if($cnt_prep_order > 0){
                    //$cnt_prep_order = count($arr_data_prep['arr_order_check']);
                ?>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Order No.</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i=0;$i<=$cnt_prep_order-1;$i++){?>
                      <tr>
                        <td><?php echo $arr_data_prep['arr_order_check'][$i]?></td>
                      </tr>
                    <?php }?>
                  </tbody>    
                </table>
                <?php }}?>
                <?php
                  //echo $customer_type;
                //Super user
                if($customer_type == 2){
                ?>
                <form role="form" name="del_order" id="del_order" action="<?php echo base_url()."accounting/saletaxreport/del_data_platform"?>" method="post" enctype="multipart/form-data">
                  <div class="panel-body">
                  <h4 class="example-title">ลบข้อมูล API Platform</h4>
                  <hr>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <select name="platform_del" id="platform_del" class="form-control">
                            <option value="0">Lazada
                            <option value="1">Shopee
                         </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="input-group">                                            
                        <?php 
                              // รับค่าเริ่มต้นในรูปแบบ mm/yyyy
                              $inputDate = "2301"; // ตัวอย่าง input

                              // แปลง input เป็น DateTime object
                              $startDate = DateTime::createFromFormat('ym', $inputDate);
                              if (!$startDate) {
                                  die("รูปแบบวันที่ไม่ถูกต้อง! ใช้ ym");
                              }

                              // สร้างวันที่ปัจจุบัน
                              $currentDate = new DateTime();


                            ?>
                            YearMonth 
                            <select name="del_ym" id="del_ym" class="form-control">
                              <?php while ($startDate <= $currentDate) {
                                  $ym = $startDate->format('ym');
                                ?>
                                <option value="<?php echo $ym;?>" ><?php echo $ym;?></option>
                              <?php 
                                $startDate->modify('+1 month');
                              }?>
                           </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                          <input type="submit" class="btn-danger btn" value="Delete" id="del_btn" onclick="return confirm('Are you sure you want to delete?');">
                           <!--<input type="button" class="btn-primary btn" value="Search" id="search"> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  
                </div>

                  
                </form>
              <?php }?>
              </div>
            </div>
          </div>
        </div>
</div>


