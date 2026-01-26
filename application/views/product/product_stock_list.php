<div class="page">
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <h4 class="example-title">ค้นหาสินค้า</h4>
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."product/product_list_search";?>" method="post">
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
          <h4 class="example-title">รายการสินค้า</h4>
          <div class="example">
            <table class="table table-bordered">
              <thead>
              <tr>
                <th>รูปภาพ</th>
                <th>ชื่อสินค้า</th>
                <th>ตัวเลือกสินค้า</th>
                <th>มีสินค้า</th>
                <th>พร้อมขาย</th>
                <th>เพิ่มขึ้น</th>
                <th>ลดลง</th>
              </tr>
              </thead>
              <?php if(!empty($arr_products)){?>
              <tbody id="list-group"> 
              <?php foreach($arr_products as $arr_product){?>
              <tr>
                <td>
                  <div class="avatar avatar-online" style="width:100px">
                    <img src="<?php echo base_url();?>global/product/1.jpg" alt="..." style="border-radius:0px">
                    <i class="avatar avatar-busy"></i>
                  </div>
                </td>
                <td><?php echo $arr_product['Title']?></td>
                <td><?php echo $arr_product['product_choice']?></td>
                <td><?php echo $arr_product['product_quality']?> 
                    <input type="hidden" name="product_quality" id="product_quality" value="<?php echo $arr_product['product_quality']?>">
                </td>
                <td><span id="pro_qty-<?php echo $arr_product['ProductID'];?>"><?php echo $arr_product['product_quality']?></span></td>
                <td><input type="number" name="qty_add[]" id="qty_add-<?php echo $arr_product['ProductID'];?>" class='qty_add'></td>
                <td><input type="number" name="qty_down[]" id="qty_down-<?php echo $arr_product['ProductID'];?>" class='qty_down'></td>
              </tr>
              <?php }?>
              </tbody>
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