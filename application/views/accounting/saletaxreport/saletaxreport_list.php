<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."accounting/saletaxreport/saletaxreport_prep"?>" method="post" enctype="multipart/form-data">
                <div class="panel-body">
                  <h4 class="example-title">กระทบยอดขาย — ตรวจสอบความถูกต้องระหว่าง API และ Excel จาก Platform</h4>
                  <p class="text-muted">Lazada / Shopee / TikTok: ดาวน์โหลดออเดอร์แบบ <strong>ทั้งหมด (All)</strong> แล้วอัปโหลด — Shopee ภาษี = <strong>ราคาขายสุทธิ + ค่าส่งผู้ซื้อ</strong> · TikTok ภาษี = <strong>P + N + Q</strong> (= W + N + Payment platform discount) ต่อออเดอร์</p>
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
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                          <input type="submit" class="btn-primary btn" value="Check" id="chk_prep">
                        </div>
                      </div>
                    </div>
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
                <?php if($is_chk == "YES"){
                  $is_lazada_chk = isset($arr_search['platform']) && ((string)$arr_search['platform'] === '0');
                  $is_shopee_chk = isset($arr_search['platform']) && ((string)$arr_search['platform'] === '1');
                  $is_tiktok_chk = isset($arr_search['platform']) && ((string)$arr_search['platform'] === '2');
                  $laz = ($is_lazada_chk && !empty($arr_data_prep['laz_check_detail'])) ? $arr_data_prep['laz_check_detail'] : null;
                  if ($laz === null && $is_shopee_chk && !empty($arr_data_prep['sho_check_detail'])) {
                    $laz = $arr_data_prep['sho_check_detail'];
                  }
                  if ($laz === null && $is_tiktok_chk && !empty($arr_data_prep['tik_check_detail'])) {
                    $laz = $arr_data_prep['tik_check_detail'];
                  }
                  $shopee_diff = $arr_data_prep['total_price_api']-$arr_data_prep['total_price_cn_excel'];
                  $tax_match = abs($shopee_diff) < 0.01;
                  $cn_match = true;
                  $net_match = true;
                  if ($laz) {
                    $cn_match = abs($laz['api_cn'] - $laz['excel_cn']) < 0.01;
                    $net_match = abs($laz['api_net'] - $laz['excel_net']) < 0.01;
                  }
                  $all_match = $tax_match && $cn_match && $net_match;
                ?>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>แหล่งข้อมูล</th>
                      <?php if($laz){ ?>
                      <th>ภาษี (All − Ignore)</th>
                      <th>CN</th>
                      <th>สุทธิ</th>
                      <th>Ignore (ไม่รวมในภาษี)</th>
                      <?php }else{ ?>
                      <th>ยอด</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody>
                      <?php if($laz){ ?>
                      <tr>
                        <td>API</td>
                        <td><?php echo number_format($laz['api_tax'], 2);?></td>
                        <td><?php echo number_format($laz['api_cn'], 2);?></td>
                        <td><?php echo number_format($laz['api_net'], 2);?></td>
                        <td>-</td>
                      </tr>
                      <tr>
                        <td>Excel</td>
                        <td><?php echo number_format($laz['excel_tax'], 2);?></td>
                        <td><?php echo number_format($laz['excel_cn'], 2);?></td>
                        <td><?php echo number_format($laz['excel_net'], 2);?></td>
                        <td><?php echo number_format($laz['excel_ignore'], 2);?></td>
                      </tr>
                      <tr>
                        <td>ผลต่าง</td>
                        <td><?php echo number_format($laz['api_tax'] - $laz['excel_tax'], 2);?></td>
                        <td><?php echo number_format($laz['api_cn'] - $laz['excel_cn'], 2);?></td>
                        <td><?php echo number_format($laz['api_net'] - $laz['excel_net'], 2);?></td>
                        <td></td>
                      </tr>
                      <?php }else{ ?>
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
                      <?php } ?>
                        <tr>
                          <td colspan="<?php echo $laz ? '5' : '2'; ?>">
                          <?php if($all_match){?>
                            <input type="button" class="btn-primary btn" value="พิมพ์รายงานภาษีขาย" id="search">
                          <?php }else{?>
                            <span style="color:#a00;">ไม่สามารถออกรายงานภาษีขายได้ กรุณาติดต่อ Admin</span>
                            <?php if($customer_type == 2){ ?>
                              <input type="button" class="btn-primary btn" value="พิมพ์รายงานภาษีขาย" id="search">
                            <?php }?>
                          <?php }?>
                          </td>
                        </tr>
                  </tbody>
                </table>
                <?php 
                $cnt_prep_order = count($arr_data_prep['arr_order_check']);
                  if($cnt_prep_order > 0){
                    $first_chk = $arr_data_prep['arr_order_check'][0];
                    $chk_is_row = is_array($first_chk);
                ?>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <?php if($chk_is_row){ ?>
                      <th>Order No.</th>
                      <th>Bucket</th>
                      <th style="text-align:right">Excel</th>
                      <th style="text-align:right">API</th>
                      <th style="text-align:right">Diff</th>
                      <th>Note</th>
                      <?php }else{ ?>
                      <th>Order No.</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i=0;$i<=$cnt_prep_order-1;$i++){
                      $chk_row = $arr_data_prep['arr_order_check'][$i];
                      if($chk_is_row && is_array($chk_row)){ ?>
                      <tr>
                        <td><?php echo htmlspecialchars($chk_row['order_sn'], ENT_QUOTES, 'UTF-8');?></td>
                        <td><?php echo htmlspecialchars($chk_row['bucket'], ENT_QUOTES, 'UTF-8');?></td>
                        <td style="text-align:right"><?php echo number_format($chk_row['excel'], 2);?></td>
                        <td style="text-align:right"><?php echo number_format($chk_row['api'], 2);?></td>
                        <td style="text-align:right"><?php echo number_format($chk_row['diff'], 2);?></td>
                        <td><?php echo htmlspecialchars(isset($chk_row['note']) ? $chk_row['note'] : '', ENT_QUOTES, 'UTF-8');?></td>
                      </tr>
                      <?php }else{ ?>
                      <tr>
                        <td><?php echo htmlspecialchars(is_array($chk_row) ? json_encode($chk_row) : $chk_row, ENT_QUOTES, 'UTF-8');?></td>
                      </tr>
                      <?php } ?>
                    <?php }?>
                  </tbody>    
                </table>
                <?php }}?>
              </div>
            </div>
          </div>
        </div>
</div>


