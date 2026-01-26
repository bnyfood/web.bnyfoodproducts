
<style>
  
  .panel_box .table tr:nth-child(even) {
    background: #ccc !important;
}
</style>
<div class="page">
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <h4 class="example-title">ค้นหาสินค้า</h4>
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."product/product_list_search";?>" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
              <div aria-multiselectable="true" role="tablist" id="accordion" class="panel-group">
                <div class="panel panel-default">
                  <a title="Tab 1" aria-controls="collapse1" aria-expanded="false" href="#collapse1"
                     data-parent="#accordion"
                     data-toggle="collapse" id="heading1" role="tab" class="panel-heading collapsed">
                  <span class="panel-title">
                    <i class="icon wb-search" aria-hidden="true"></i> ค้นหาสินค้า
                  </span>
                  </a>
                  <div aria-labelledby="heading1" role="tabpanel" class="panel-collapse collapse" id="collapse1"
                       aria-expanded="false">
                    <div class="panel-body">
                      <h4 class="example-title">ค้นหาสินค้า</h4>
                      <div class="row_col">
                        <div class="col-md-4">
                          <div class="form-group">
                            <select class="form-control" name="product_cat_search" id="product_cat_search">
                              <option value="">กรุณาเลือก</option> 
                              <?php 
                                foreach($arr_list_cats as $arr_list_cat){
                                  $blank = "";
                                  for($i=0;$i<=$arr_list_cat['level']*2;$i++){
                                    $blank.= "&nbsp";
                                  }
                                ?>
                                <option value="<?php echo $arr_list_cat['ProductCategoryID']?>" <?php if($arr_list_cat['ProductCategoryID']==$arr_search['product_cat_search']){echo "selected";}?>><?php echo $blank.$arr_list_cat['Title']?></option>
                                <?php }?> 
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="input-search">
                              <i class="input-search-icon wb-search" aria-hidden="true"></i>
                              <input type="text" class="form-control" id="txt_product_search" name="txt_product_search"  placeholder="Search...">
                              <button type="button" class="input-search-close icon wb-close" aria-label="Close"></button>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 box_button">
                          <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">
                              <i class="icon wb-search" aria-hidden="true"></i> ค้นหา
                            </button>
                            <button type="button" id="btn_clear_search" class="btn btn-block btn-primary">
                              <i class="icon wb-refresh" aria-hidden="true"></i> ลบการค้นหา
                            </button>
                            <button type="button" id="btn_add_product" class="btn btn-block btn-primary">
                              <i class="icon wb-plus" aria-hidden="true"></i> เพิ่มสินค้า
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <a title="Tab 2" aria-controls="collapse2" aria-expanded="false" href="#collapse2"
                     data-parent="#accordion" data-toggle="collapse" id="heading2" role="tab"
                     class="panel-heading collapsed">
                    <span class="panel-title">
                      <i class="icon wb-pencil" aria-hidden="true"></i>
                      <!--                    <i class="icon wb-pencil" aria-hidden="true"></i>-->
                      <!--                    <i class="icon wb-pencil" aria-hidden="true"></i>-->
                      แก้ไขเป็นชุด
                    </span>
                  </a>
                  <div aria-labelledby="heading2" role="tabpanel" class="panel-collapse collapse" id="collapse2"
                       aria-expanded="false">
                    <div class="panel-body">
                      <h4 class="example-title">แก้ไขเป็นชุด</h4>
                      <div class="row_col row_col2">
                        <div class="col-md-4">
                          <div class="row_col">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>
                                  ราคาหลัก
                                </label>
                                <select class="form-control" name="PriceMain" id="PriceMain">
                                  <option value="0">ไม่เปลี่ยนแปลงราคาหลัก</option> 
                                  <option value="1">เปลี่ยนแปลงราคาหลัก</option> 
                                </select> 
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>
                                  เพิ่ม/ลด ราคาหลัก
                                </label>
                                <select class="form-control" name="PriceMainType" id="PriceMainType">
                                  <option value="0">เพิ่มขึ้น</option> 
                                  <option value="1">ลดลง</option> 
                                </select> 
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-1">
                          <label class="text_null">&nbsp;</label>
                          <input type="text" class="form-control" placeholder="%" name="PriceMainAmount" id='PriceMainAmount' >
                        </div>

                        <hr style="border-color: #fff;">

                        <div class="col-md-2">
                          <label>สินค้าลดราคา</label>
                          <input type="checkbox" name="is_discount" id="is_discount" value="1">
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>
                              ราคาส่วนลด <span class="example-title">(Sale Price)</span>
                            </label>
                            <select class="form-control" name="DiscountType" id="DiscountType">
                              <option value="0">ลดเหลือ</option> 
                              <option value="1">ลดลงไป</option> 
                            </select> 
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <label class="text_null">&nbsp;</label>
                            <input type="number" class="form-control" name="DiscountAmount" id='DiscountAmount' >
                          </div>
                        </div>
                        <div class="col-md-1">
                          <label class="text_null">&nbsp;</label>
                          <select class="form-control" name="DiscountAmountType" id="DiscountAmountType">
                            <option value="0">%</option> 
                            <option value="1">บาท</option> 
                          </select> 
                        </div>
                        <div class="col-md-4 box_button">
                          <label>&nbsp;</label>
                          <div class="form-group">
                            <button type="button" class="btn btn-block btn-primary" id="product_edit_price" class="btn btn-primary">
                              <i class="icon wb-search" aria-hidden="true"></i> อัพเดททั้งหมด
                            </button>
                          </div>
                        </div>
                        <hr style="border-color: #fff;">
                        <div class="col-md-2">
                          <label>อัพเดทจำนวนสินค้าเป็นชุด</label>
                          <input type="checkbox" name="is_upquan" id="is_upquan" value="1">
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>
                              จำนวนสินค้า 
                            </label>
                            <label class="text_null">&nbsp;</label>
                            <input type="number" class="form-control" name="ProQuantity" id='ProQuantity' >
                          </div>
                        </div>
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
          <h4 class="example-title">Bordered Table</h4>
          <div class="example">
            <table class="table table-bordered">
                <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th>รูปภาพ</th>
                  <th>ชื่อสินค้า</th>
                  <th>Sku</th>
                  <th>รายละเอียด</th>
                  <th>ราคา</th>
                  <th>รายละเอียดการลดราคา</th>
                  <th>ราคาหลังลด</th>
                  <th>จำนวนสินค้า</th>
                  <th class="text-nowrap">Action</th>
                </tr>
                </thead>
              <?php if(!empty($arr_products)){?>
              <?php 
              $num_row = 0;
              foreach($arr_products as $arr_product){?>
                    <?php 
                    
                      $product_price =$arr_product['Price'];
                      $new_price = 0;
                      $dis_per = 0;
                      $price_type_txt = "";
                      $price_amount_type_txt = "";
                      $DiscountType = $arr_product['DiscountType'];
                      $DiscountAmountType = $arr_product['DiscountAmountType'];
                      $DiscountAmount = $arr_product['DiscountAmount'];

                      if($DiscountType == 0){
                        $price_type_txt = "ลดเหลือ";
                        if($DiscountAmountType == 0){
                          $price_amount_type_txt = "%";
                          $new_price = $product_price*($DiscountAmount/100); 
                        }elseif($DiscountAmountType == 1){
                          $price_amount_type_txt = "บาท";
                          $new_price = $DiscountAmount;
                        } 

                      }elseif($DiscountType == 1){
                        $price_type_txt = "ลดลงไป";
                        if($DiscountAmountType == 0){
                          $price_amount_type_txt = "%";
                          $dis_per = $product_price*($DiscountAmount/100); 
                          $new_price = $product_price-$dis_per;
                        }elseif($DiscountAmountType == 1){
                          $price_amount_type_txt = "บาท";
                          $new_price = $product_price-$DiscountAmount;
                        } 
                      }
                      $pro_quan = $arr_product['product_quality1'] + $arr_product['product_quality2'];

                      $num_row = $num_row+1;
                    ?>   
                    <?php if(!empty($arr_product['arr_pro_models'])){?>
                      <tbody class="table-section" data-plugin="tableSection">
                        <td class="text-center"><i class="table-section-arrow"></i></td>
                    <?php }else{?>
                      <tbody >
                        <td></td>
                    <?php }?>
                    
                <td>
                  <input type="checkbox" id="<?php echo $arr_product['ProductID'];?>" name="chk_product_id" class="chk_product" value="<?php echo $arr_product['ProductID']."_".$arr_product['Price']."_".$pro_quan?>" autocomplete="off" style="vertical-align:top">
                </td>
                <td>
                  <div class="avatar avatar-online" style="width:100px">
                    <img src="<?php echo base_url();?>global/product/1.jpg" alt="..." style="border-radius:0px">
                    <i class="avatar avatar-busy"></i>
                  </div>
                </td>
                <td><?php echo $arr_product['Title']?></td>
                <td><?php echo $arr_product['Sku']?></td>
                <td><?php echo $arr_product['Description']?></td>
                <?php if($arr_product['OnDiscount'] == 0){ // On sale?>
                  <td class="more_text1" id="price_<?php echo $arr_product['ProductID']?>" style="text-decoration-line:line-through"><?php echo number_format($arr_product['Price'],2);?></td>
                  <td class="more_text1" id="dis_detail_<?php echo $arr_product['ProductID']?>"></td>
                  <td class="more_text1" id="val_<?php echo $arr_product['ProductID']?>"><?php echo number_format($new_price,2);?></td>
                <?php }elseif($arr_product['OnDiscount'] == 1){ //Not on sell?>
                  <td class="more_text1" id="price_<?php echo $arr_product['ProductID']?>"><?php echo number_format($arr_product['Price'],2);?></td>
                  <td class="more_text1" id="dis_detail_<?php echo $arr_product['ProductID']?>"><?php echo $price_type_txt." ".$DiscountAmount." ".$price_amount_type_txt;?></td>
                  <td class="more_text1" id="val_<?php echo $arr_product['ProductID']?>">ไม่ลดราคา</td>
                <?php }?> 
                <td>
                  <?php if(empty($arr_product['arr_pro_models'])){?>
                    <input type="number" class="form-control" name="pro_quantity_<?php echo $arr_product['ProductID']?>" id='pro_quantity_<?php echo $arr_product['ProductID']?>' value="<?php echo $pro_quan;?>" min='0' style="width: 7em"> 
                  <?php }?>
                </td>
                <td class="more_text1 text-nowrap">
                  <button type="button" class="btn btn-outline btn-primary btn-sm">
                    <a href="<?php echo base_url();?>product/edit_product_form/<?php echo $arr_product['ProductID'];?>" data-toggle="tooltip" data-original-title="Edit"> <i class="icon wb-pencil"
                                                                                      aria-hidden="true"
                                                                                      style="margin-right:0px"></i>
                                                                                    </a>
                  </button>
                  <button type="button" class="btn btn-outline btn-danger btn-sm"><i class="icon wb-trash"
                                                                                     aria-hidden="true"
                                                                                     style="margin-right:0px"></i>
                  </button>
                </td>
                <td class="hide_less">
                  <button class="moreless_button moreless_button1">...</button>
                </td>
              </tr> 
            </tbody>
            <?php 
                if(!empty($arr_product['arr_pro_models'])){
                $pro_quan_model = 0;
            ?>
              <tbody style='background: #ccc;'>
              <?php foreach($arr_product['arr_pro_models'] as $arr_pro_model){?>
              <?php $pro_quan_model = $arr_pro_model['product_quality1'] + $arr_pro_model['product_quality2'];?>
                <tr>
                  <td></td>
                  <td class="font-weight-medium text-success">
                    #
                  </td>
                  <td>
                    
                  </td>
                  <td class="hidden-sm-down">
                    <?php echo $arr_pro_model['product_choice']?>
                  </td>
                  <td class="hidden-sm-down">
                    <?php echo $arr_pro_model['Sku']?>
                  </td>
                  <td class="hidden-sm-down">
                    <?php echo $arr_pro_model['Description']?>
                  </td>
                  <td class="hidden-sm-down">
                    July 27, 2017
                  </td>
                  <td class="hidden-sm-down">
                    July 27, 2017
                  </td>
                  <td class="hidden-sm-down">
                    July 27, 2017
                  </td>
                  <td class="hidden-sm-down">
                    <input type="number" class="form-control" name="pro_quantity_<?php echo $arr_pro_model['ProductID']?>" id='pro_quantity_<?php echo $arr_pro_model['ProductID']?>' value="<?php echo $pro_quan_model;?>" min='0' style="width: 7em"> 
                  </td>
                  <td class="hidden-sm-down">

                  </td>
                </tr>
              <?php }?>
              </tbody>
              <?php }}?>
              
            <?php }?>
            </table>
            <nav>
                <?php echo $pagination_link;?>
              </nav>
            <nav>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>