<style>
  .cat_hilight tr:hover{
    background-color: rgba(41, 103, 182, 0.89);
    color: #FFF;
}
.cat_hilight tr.selected {
    background-color: rgba(41, 103, 182, 0.89);
    color: #FFF;
}
</style>
<div class="page">
  <div class="page-content container-fluid">

    <div class="row">
      <div class="col-lg-5 col-xl-6 push-xl-3">
        <?php if($add_alt == "success"){?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
            เพื่มหมวดหมู่สำเร็จ
          </div>
        <?php }?>  
        <?php if($add_alt == "fail"){?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
            เพื่มหมวดหมู่ไม่สำเร็จ
          </div>
        <?php }?> 
        <?php if($edit_alt == "success"){?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
            แก้ไขหมวดหมู่สำเร็จ
          </div>
        <?php }?> 
        <div class="card user-visitors">
          <div class="card-header card-header-transparent p-20">
            <h4 class="card-title mb-0">ร้านค้า</h4>
              <div class="col-md-6">
                <div class="mb-15">
                  <select class="form-control" name="shop_sel" id="shop_sel">
                      <option value="">กรุณาเลือก</option> 
                      <?php foreach($arr_shops as $arr_shop){?>
                        <option value="<?php echo $arr_shop['ShopID']?>" <?php if( $arr_shop['ShopID'] == $id_shop){echo "selected";}?>><?php echo $arr_shop['ShopName']?></option>
                      <?php }?>  
                  </select>
                </div>
              </div>  
            <h4 class="card-title mb-0">หมวดหมู่</h4>
            <button type="button" id="add_cat_root" value="<?php echo $arr_cat_root['web_category_id']?>" class="btn btn-icon btn-success btn-xs icon wb-plus"> หมวดหมู่หลัก</button>
            <table class="table">
              <thead>
                <tr>
                  <th>ชื่อหมวดหมู่</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody id="cate-list" class="cat_hilight">
                <?php 
                  foreach($arr_list_cats as $arr_list_cat){
                    $blank = "";
                    for($i=0;$i<=$arr_list_cat['level']*10;$i++){
                      $blank.= "&nbsp";
                    }
                  ?>
                <tr>
                  <td><?php echo $blank.$arr_list_cat['Title']?></td>
                  <td>
                    <button type="button" id="add_cat" value="<?php echo $arr_list_cat['web_category_id']?>" class="btn btn-icon btn-success btn-xs icon wb-plus"></button>
                    <button type="button" id="edit_cat" value="<?php echo $arr_list_cat['web_category_id']?>" class="btn btn-icon btn-primary btn-xs icon wb-pencil"></button>
                    <button type="button" id="del_cat" value="<?php echo $arr_list_cat['web_category_id']?>" class="btn btn-icon btn-danger btn-xs icon wb-trash"></button>
                  </td>
                </tr>
              <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-lg-7 col-xl-6 push-xl-3" id="div_manage_cat" style="display: none">
        <div class="card user-visitors">
          <div class="card-header card-header-transparent p-20">

            <h4 class="card-title mb-0" id="manage_cat_txt" style="padding: 0px 0px 10px 0px;"></h4>
            <h4 class="card-title mb-0" id="manage_main_cat_txt" style="padding: 0px 0px 10px 0px;display: none"></h4>
            <form role="form" name="cat_add_form" id="cat_add_form" action="<?php echo base_url()."webcategory/category_add";?>" method="post">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" id="cat_title">ชื่อหมวดหมู่ : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="cat_name" id='cat_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รายละเอียด : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="cat_des" id="cat_des"></textarea>
                    </div>
                  </div>
                  <input type="hidden" name="id_shop" id="id_shop" value="<?php echo $id_shop;?>">
                  <input type="hidden" name="parent_id" id="parent_id">
                  <input type="hidden" name="is_add" id="is_add">
                  <input type="hidden" name="from_pop" id="from_pop" value="<?php echo $from_pop;?>">
                  <button type="submit" class="btn btn-primary">Submit</button>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>   