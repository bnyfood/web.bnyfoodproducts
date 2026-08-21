<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่ม SKU</h1>
    <div class="row">
      <div class="col-md-6">
        <div class="mb-15">
          <select class="form-control" name="product_cat_search" id="product_cat_search">
            <option value="">กรุณาเลือก</option> 
            <option value="All">ทั้งหมด</option> 
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
          <input type="hidden" name="is_search" id="is_search" value="<?php echo $is_search?>">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        ชื่อ SKU : 
        <input type="text" class="form-control" name="sku_name" id="sku_name"> 
      </div>
      <div class="col-md-3">
        &nbsp
        <button type="button" class="btn btn-block btn-primary" name="sku_add" id="sku_add">เพิ่ม SKU</button>
      </div>
    </div>
  </div>
  <div id="exampleTransition" class="page-content container-fluid" data-plugin="animateList">
        <ul class="blocks-sm-100 blocks-lg-2 blocks-xxl-4">
          <?php foreach($arr_products as $arr_product){ ?>
          <li>
            <div class="panel panel-bordered animation-scale-up" style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 0ms;">
              <div class="panel-heading">
                <div class="avatar avatar-online " style="width:100px;padding: 10px 10px 10px 10px;float:left;">
                          <img src="<?php echo base_url();?>global/product/1.jpg" alt="..." style="border-radius:0px">
                        </div>
                <h3 class="panel-title" style="padding-bottom: 50px;"><?php echo $arr_product['Title']?></h3>

              </div>
              <div class="panel-body">
                <p><span>ราคา : <?php echo $arr_product['Price']?> </span></p>
                <p><?php echo $arr_product['Description']?></p>

                  <div class="example">
                    <h4 class="example-title">จำนวน</h4>
                    <input type="number" class="form-control" name="quan" data-plugin="TouchSpin" id="<?php echo $arr_product['ProductID'];?>"
                      data-min="0" data-max="1000000000" data-stepinterval="50"
                      data-maxboostedstep="10000000" data-prefix="" value="0" />
                  </div>
              </div>
            </div>
          </li>
         <?php } ?>
        </ul>
      </div>
</div>       