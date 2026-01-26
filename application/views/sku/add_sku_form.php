<div class="page">
  <div class="page-header">
    <h1 class="page-title">สินค้าหลัก</h1>
    <div class="row">
      <div class="col-md-6">
        <div class="mb-15">
          ชื่อสินค้า : <span id='pro_name_ex'></span> SKU : <span id='sku_name_ex'></span>
        </div>
      </div>    
    </div>  
    <h1 class="page-title">เพิ่มสินค้าย่อย</h1>
    <div class="row">
      <div class="col-md-6">
        <form class="upload-form" role="form" name="form_sku_search" id="form_sku_search" action="<?php echo base_url()."sku/add_sku_form_search";?>" method="POST" >  
          <div class="mb-15">
            ค้นหาจากหมวดหมู่ : 
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
            <input type="text" class="form-control" name="search_pro_name" id="search_pro_name" value="<?php echo $arr_search['search_pro_name'];?>">
            <input type="hidden" name="is_search" id="is_search" value="<?php echo $is_search?>">
            <button type="submit" class="btn btn-block btn-primary">ค้นหา</button>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        ชื่อ SKU : 
        <input type="text" class="form-control" name="sku_name" id="sku_name"> 
      </div>
      <div class="col-md-3">
        &nbsp
        <button type="button" class="btn btn-block btn-primary" name="sku_add" id="sku_add">บันทึก</button>
      </div>
    </div>
  </div>
  <div class="example">
    <table class="table table-bordered">
      <thead>
      <tr>
        <th><input type="checkbox" id="chk_all_product" name="chk_all_product" autocomplete="off"></th>
        <th>รูปภาพ</th>
        <th>ชื่อสินค้า</th>
        <th>รายละเอียด</th>
        <th>ราคา</th>
        <td>จำนวน</td>
      </tr>
      </thead>
      <?php 
      if(!empty($arr_products)){
        ?>
      <tbody id="list-group">
      <?php foreach($arr_products as $arr_product){?>
        <tr>
          <td>
            <input type="checkbox" id="<?php echo $arr_product['ProductID'];?>" name="chk_product_id" class="chk_product" value="<?php echo $arr_product['ProductID']?>" autocomplete="off" style="vertical-align:top">
          </td>
          <td>
            <div class="avatar avatar-online" style="width:100px">
              <img src="<?php echo base_url();?>global/product/1.jpg" alt="..." style="border-radius:0px">
              <i class="avatar avatar-busy"></i>
            </div>
          </td>
          <td><?php echo $arr_product['Title']?></td>
          <td><?php echo $arr_product['Description']?></td>
          <td><?php echo $arr_product['Price']?></td>
          <td>
            <input type="number" class="form-control" name="quan" data-plugin="TouchSpin" id="quan_<?php echo $arr_product['ProductID'];?>"
              data-min="0" data-max="1000000000" data-stepinterval="50"
              data-maxboostedstep="10000000" data-prefix="" value="0" />
          </td>
        </tr>
      <?php 
          }
        }
      ?>

      </tbody>
    </table>
  </div>
</div>       