<!-- Page -->
    <div class="page">
      <div class="page-content">
        <!-- Panel -->
        <div class="panel">
          <div class="panel-body">
            <h4 class="example-title">ค้นหาสินค้า</h4>
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."product/product_list_search";?>" method="post">
              <div class="row">
                <div class="form-group col-md-4">
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
                <div class="form-group col-md-4">
                  <div class="input-search input-search-dark">
                    <i class="input-search-icon wb-search" aria-hidden="true"></i>
                    <input type="text" class="form-control" id="txt_product_search" name="txt_product_search" placeholder="Search" value="<?php echo $arr_search['txt_product_search'];?>" style="border-radius: 0px;">
                  </div>
              </div>
              <div class="form-group col-md-4">
                <input type="hidden" name="search_type" value="1">
                <button type="submit" class="btn btn-primary"><i class="icon wb-search" aria-hidden="true"></i> ค้นหา</button>
                <button  id="btn_clear_search" type="button" class="btn btn-primary"><i class="icon wb-refresh" aria-hidden="true"></i> ลบการค้นหา</button>
                <button  id="btn_add_product" type="button" class="btn btn-primary"><i class="icon wb-plus" aria-hidden="true"></i> เพิ่มสินค้า</button>
              </div>
            </div>
          </form>
          <hr>
          <h4 class="example-title">แก้ไขเป็นชุด</h4>
          <div class="row">
            <div class="form-group col-md-2">
              <h4 class="example-title">ราคาหลัก</h4>
                <select class="form-control" name="PriceMain" id="PriceMain">
                  <option value="0">ไม่เปลี่ยนแปลงราคาหลัก</option> 
                  <option value="1">เปลี่ยนแปลงราคาหลัก</option> 
                </select> 
            </div>
            <div class="form-group col-md-2">
              <h4 class="example-title">เพิ่ม/ลด ราคาหลัก</h4>
              <select class="form-control" name="PriceMainType" id="PriceMainType">
                    <option value="0">เพิ่มขึ้น</option> 
                    <option value="1">ลดลง</option> 
                  </select> 
            </div>
            <div class="form-group col-md-1">
              <h4 class="example-title">&nbsp</h4>
              <input type="number" class="form-control" name="PriceMainAmount" id='PriceMainAmount'  autocomplete="off">%
            </div>
          </div>  
          <div class="row">
            <div class="form-group col-md-1">
              <h4 class="example-title">ลดราคาสินค้า</h4>
              <div class="checkbox-custom checkbox-primary">
                <input type="checkbox" name="is_discount" id="is_discount" value="1">
                <label></label>
              </div>
            </div>
            <div class="form-group col-md-2">
              <h4 class="example-title">ราคาส่วนลด (Sale Price)</h4>
              <select class="form-control" name="DiscountType" id="DiscountType">
                    <option value="0">ลดเหลือ</option> 
                    <option value="1">ลดลงไป</option> 
                  </select> 
            </div>
            <div class="form-group col-md-1">
              <h4 class="example-title">&nbsp</h4>
              <input type="number" class="form-control" name="DiscountAmount" id='DiscountAmount'  autocomplete="off">
            </div>
            <div class="form-group col-md-1">
              <h4 class="example-title">&nbsp</h4>
              <select class="form-control" name="DiscountAmountType" id="DiscountAmountType">
                    <option value="0">%</option> 
                    <option value="1">บาท</option> 
                  </select> 
            </div>
            <div class="form-group col-md-6">
              <h4 class="example-title">&nbsp</h4>
              <button type="button" id="product_edit_price" class="btn btn-primary"><i class="icon wb-search" aria-hidden="true"></i> อัพเดททั้งหมด</button>
            </div>
          </div>  
        </div>
      </div> 
        <div class="panel">
          <div class="panel-body">
            <div class="tab-pane animation-fade active" id="all_contacts" role="tabpanel">
              <input type="checkbox" id="chk_all_product" name="chk_all_product"  autocomplete="off" >
            <?php if(!empty($arr_products)){?>
              <ul class="list-group">
                <?php foreach($arr_products as $arr_product){?>
                <?php 
                  $product_price =$arr_product['Price'];
                  $new_price = 0;
                  $dis_per = 0;
                  $DiscountType = $arr_product['DiscountType'];
                  $DiscountAmountType = $arr_product['DiscountAmountType'];
                  $DiscountAmount = $arr_product['DiscountAmount'];

                  if($DiscountType == 0){

                    if($DiscountAmountType == 0){
                      $new_price = $product_price*($DiscountAmount/100); 
                    }elseif($DiscountAmountType == 1){
                      $new_price = $DiscountAmount;
                    } 

                  }elseif($DiscountType == 1){

                    if($DiscountAmountType == 0){
                      $dis_per = $product_price*($DiscountAmount/100); 
                      $new_price = $product_price-$dis_per;
                    }elseif($DiscountAmountType == 1){
                      $new_price = $product_price-$DiscountAmount;
                    } 
                  }
                ?>  
                <li class="list-group-item">
                  <div class="media">
                    <div class="pr-0 pr-sm-20">
                      <input type="checkbox" id="<?php echo $arr_product['ProductID'];?>" name="chk_product_id" class="chk_product" value="<?php echo $arr_product['ProductID']."_".$arr_product['Price']?>" autocomplete="off" style="vertical-align:top">
                      <div class="avatar avatar-online" style="width:100px">
                        <img src="<?php echo base_url();?>global/product/1.jpg" alt="..." style="border-radius:0px">
                        <i class="avatar avatar-busy"></i>
                      </div>
                    </div>
                    <div class="media-body ">
                      <h5 class="mt-0 mb-5">
                        <?php echo $arr_product['Title']?>
                        <small>Last Access: 1 hour ago</small>
                      </h5>
                      <?php if($arr_product['OnDiscount'] == 0){ // On sale?>
                                <p id="price_<?php echo $arr_product['ProductID']?>" style="text-decoration-line:line-through">ราคา : 
                                  <?php echo number_format($arr_product['Price'],2);?>
                                </p>
                                <p id="val_<?php echo $arr_product['ProductID']?>">
                                  ราคาหลังลด :
                                  <?php echo number_format($new_price,2);?>
                                </p>
                      <?php }elseif($arr_product['OnDiscount'] == 1){ //Not on sell?>
                                <p id="price_<?php echo $arr_product['ProductID']?>">ราคา : 
                                  <?php echo number_format($arr_product['Price'],2);?>
                                </p>
                                <p id="val_<?php echo $arr_product['ProductID']?>">
                                  ไม่ลดราคา
                                </p>
                      <?php }?>  
                      <p>
                        <i class="icon icon-color wb-order" aria-hidden="true"></i> 
                        <?php echo $arr_product['Description']?>
                      </p>
                      <div>
                        <a class="text-action" href="javascript:void(0)">
                        <i class="icon icon-color wb-envelope" aria-hidden="true"></i>
                      </a>
                          <a class="text-action" href="javascript:void(0)">
                        <i class="icon icon-color wb-mobile" aria-hidden="true"></i>
                      </a>
                          <a class="text-action" href="javascript:void(0)">
                        <i class="icon icon-color bd-twitter" aria-hidden="true"></i>
                      </a>
                          <a class="text-action" href="javascript:void(0)">
                        <i class="icon icon-color bd-facebook" aria-hidden="true"></i>
                      </a>
                          <a class="text-action" href="javascript:void(0)">
                        <i class="icon icon-color bd-dribbble" aria-hidden="true"></i>
                      </a>
                      </div>
                    </div>
                    <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">
                      <button type="button" class="btn btn-outline btn-primary btn-sm"><i class="icon wb-pencil" aria-hidden="true" style="margin-right:0px"></i></button>
                      <button type="button" class="btn btn-outline btn-danger btn-sm"><i class="icon wb-trash" aria-hidden="true" style="margin-right:0px"></i></button>
                    </div>
                  </div>
                </li>
              <?php }?>
              </ul>
              <?php }?>
              <nav>
                <?php echo $pagination_link;?>
              </nav>
            </div>
          </div>
        </div>
        <!-- End Panel -->
      </div>
    </div>
    <!-- End Page -->