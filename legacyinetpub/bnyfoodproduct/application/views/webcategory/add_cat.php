<div class="page">
  <div class="page-content container-fluid">
    <div class="row">
      <div class="col-xxl-12 col-xl-8 col-lg-12">
        <div class="card">
          <div class="card-block">
            <h4 class="card-title project-title">
               Category
              <btn class="btn btn-pure btn-default icon wb-pencil btn-edit"></btn>
            </h4>
            <div class="row">
              <label class="col-md-2 col-form-label">หมวดหมู่ของ : </label>
                <div class="col-md-6">
                  <div class="mb-15">
                    <select class="form-control" name="parent_cat" id="parent_cat">
                      <option value="">กรุณาเลือก</option> 
                      <?php 
                        foreach($arr_list_cats as $arr_list_cat){
                          $blank = "";
                          for($i=0;$i<=$arr_list_cat['level']*2;$i++){
                            $blank.= "&nbsp";
                          }
                        ?>
                        <option value="<?php echo $arr_list_cat['web_category_id']?>" <?php if($arr_list_cat['web_category_id']==$parent_id){echo "selected";}?> ><?php echo $blank.$arr_list_cat['Title']?></option>
                      <?php }?> 
                    </select>    
                    <button type="button" id="add_cat" class="btn btn-primary">เพิ่ม</button>
                    <button type="button" id="edit_cat" class="btn btn-primary">แก้ไข</button>
                    <button type="button" id="del_cat" class="btn btn-primary">ลบ</button>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-xl-6 push-xl-3">
        <div class="card user-visitors">
          <div class="card-header card-header-transparent p-20">
            <h4 class="card-title mb-0">เพิ่ม Category</h4>
            <form role="form" name="cat_add_form" id="cat_add_form" action="<?php echo base_url()."webcategory/category_add_action";?>" method="post">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อหมวดหมู่ : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="cat_name" id='cat_name'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รายละเอียด : </label>
                    <div class="col-md-6">
                      <textarea class="form-control" name="cat_des"></textarea>
                    </div>
                  </div>
                  <input type="hidden" name="parent_id" value="<?php echo $parent_id;?>">
                  <button type="submit" class="btn btn-primary">Submit</button>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>       