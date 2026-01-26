<style type="text/css">
  
  .container_model {
    position: relative;
    padding: 20px 50px 20px 50px;
}

.left_model {
    width: 550px;
    position: absolute;
    left: 0;
}

.right_model {
    margin-left: 550px;
}

</style>
<div class="page">
  <div class="page-header">
    <h1 class="page-title">เพิ่มสินค้า</h1>
    <div class="page-header-actions">
    </div>
  </div>

  
  <div class="page-content">
    <div class="panel">
      <div class="panel-body container-fluid">
        <form class="upload-form" role="form" name="user_add_form" id="fileupload" action="<?php echo base_url()."upload/do_upload";?>" method="POST" enctype="multipart/form-data">  
          <div class="row row-lg">
            <div class="col-md-12 col-lg-12">
              <div class="example-wrap">
                <h4 class="example-title">รูป</h4>
                <div class="example">
                <div class="form-group row">
                  <div class="col-md-12">
                    <div class="row fileupload-buttonbar">
                      <div class="col-lg-7">
                        <!-- The fileinput-button span is used to style the file input field as button -->
                        <span class="btn btn-success fileinput-button">
                          <i class="glyphicon glyphicon-plus"></i>
                          <span>Add files...</span>
                          <input type="file" name="userfile" multiple />
                        </span>
                        <button type="submit" class="btn btn-primary start">
                          <i class="glyphicon glyphicon-upload"></i>
                          <span>Start upload</span>
                        </button>
                        <button type="reset" class="btn btn-warning cancel">
                          <i class="glyphicon glyphicon-ban-circle"></i>
                          <span>Cancel upload</span>
                        </button>
                        <button type="button" class="btn btn-danger delete">
                          <i class="glyphicon glyphicon-trash"></i>
                          <span>Delete selected</span>
                        </button>
                        <input type="checkbox" class="toggle" />
                        <!-- The global file processing state -->
                        <span class="fileupload-process"></span>
                      </div>
                      <!-- The global progress state -->
                      <div class="col-lg-5 fileupload-progress fade">
                        <!-- The global progress bar -->
                        <div
                          class="progress progress-striped active"
                          role="progressbar"
                          aria-valuemin="0"
                          aria-valuemax="100"
                        >
                          <div
                            class="progress-bar progress-bar-success"
                            style="width: 0%;"
                          ></div>
                        </div>
                        <!-- The extended global progress state -->
                        <div class="progress-extended">&nbsp;</div>
                      </div>
                    </div>
                    <!-- The table listing the files available for upload/download -->
                    <table role="presentation" class="table table-striped">
                      <tbody class="files"></tbody>
                    </table>
                  </div>
                </div>
              </div>
              </div>
            </div>
          </div>
        </form>
          <hr>
          <form class="upload-form" role="form" name="product_add_form" id="product_add_form" action="<?php echo base_url()."product/product_add";?>" method="POST" enctype="multipart/form-data">  
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">ข้อมูลสินค้า</h4>
                <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ชื่อสินค้า : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Title" id='Title'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">หมวดหมู่ : </label>
                    <div class="form-group col-md-6">
                      <select class="form-control" name="ProductCategoryID" id="ProductCategoryID">
                        <option value="">กรุณาเลือก</option> 
                        <?php 
                          foreach($arr_list_cats as $arr_list_cat){
                            $blank = "";
                            for($i=0;$i<=$arr_list_cat['level']*2;$i++){
                              $blank.= "&nbsp";
                            }
                          ?>
                          <option value="<?php echo $arr_list_cat['ProductCategoryID']?>" <?php if($arr_list_cat['ProductCategoryID']==$parent_id){echo "selected";}?> ><?php echo $blank.$arr_list_cat['Title']?></option>
                        <?php }?> 
                      </select>  
                    </div>
                    <div class="form-group col-md-1">
                      <button type="button" id="manage_cat"  class="btn btn-icon btn-primary btn-l icon wb-pencil"></button>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รหัสสินค้า : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Sku" id='Sku'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">หน่วย : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="Unit" id='Unit'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ราคา : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="Price" id='Price'  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ราคาลด : </label>
                    <div class="col-md-3">
                      <input type="number" class="form-control" name="Pricesale"  id='Pricesale'  autocomplete="off">
                    </div>
                    <div class="col-md-3" >
                      <input type="text" class="form-control" name="Pricesale_per" id='Pricesale_per'  autocomplete="off" >
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">รหัสสินค้า(SKU) : </label>
                    <div class="col-md-6">
                      <input type="text" class="form-control"  name="sku_name" id='sku_name'>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">สินค้าขายเป็นชุด : </label>
                    <input type="hidden" name="ran_id" id="ran_id" value="<?php echo $ran_id;?>">
                    <div class="col-md-6">
                      <button type="button" class="btn btn-primary" name="pro_set" id="pro_set">จัดการชุดสินค้า</button>

                    </div>
                  </div>

              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-6">
            <div class="example-wrap">
              <h4 class="example-title">การจัดส่ง</h4>
              <div class="example">
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">น้ำหนัก : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="Weight" id='Weight'  autocomplete="off">
                    </div>
                  </div>

                 <div class="row">
                  <label class="col-md-2 col-form-label">ขนาด : </label>
                      <div class="form-group col-md-3">
                        <input type="number" class="form-control" name="Dimension" id="Dimension" placeholder="กว้าง" autocomplete="off">
                      </div>
                      <div class="form-group col-md-3">
                        <input type="number" class="form-control" name="lastname" placeholder="ยาว" autocomplete="off">
                      </div>
                      <div class="form-group col-md-3">
                        <input type="number" class="form-control" name="lastname" placeholder="สูง" autocomplete="off">
                      </div>
                    </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">คลัง : </label>
                    <div class="col-md-6">
                      <input type="number" class="form-control" name="product_quality" id='product_quality'  autocomplete="off">
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>

          <div class="row row-lg" id="div_product_set"  >
            <div class="col-md-12 col-lg-12">
              <div class="panel">
                <div class="left_model">
                  <p>ประกอบด้วย</p>
                </div>
                <div class="panel-body">
                  <div class="example table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="10%" id="variant1_txt">รูปภาพ</th>
                          <th width="10%" id="variant2_txt">ชื่อสินค้า</th>
                          <th width="20%">รายละเอียด</th>
                          <th width="10%">ราคาทุน/หน่วย</th>
                          <th width="10%">จำนวน</th>
                          <th width="10%">ราคาทุนรวม</th>
                          <th width="10%">ราคาขาย/หน่วย</th>
                          <th width="10%">ราคาแยกชิ้น</th>
                          <th width="10%">ราคาเป็นชุด</th>
                        </tr>
                      </thead>
                      <tbody id="list-sku-data">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr>
          <div class="row row-lg">
            <div class="col-md-12 col-lg-12">
              <div class="example-wrap">
                <h4 class="example-title">รายละเอียดราคา</h4>
                <h4 class="example-title">คุณสมบัติ (ตัวเลือกสินค้า) <input type="checkbox" name="is_model" id="is_model" value="1" class="js-switch-small" data-plugin="switchery" data-color="#17b3a3"  data-size="small"/></h4>
                <p>สามารถเปิดใช้งานคุณสมบัติ เพื่อเพิ่มตัวเลือกสินค้าได้ (เช่น สี ขนาด วัสดุ รอบส่ง)</p>
                <div class="example">
                  <div class="form-group row">
                    <div class="col-md-12" id="show_model" style="display: none;">
                      <div class="panel">
                        <div class="example-wrap">
                          <div class="container_model">
                            <div class="left_model">
                              <p>ชื่อคุณสมบัติ</p>
                              <input type="text" class="form-control" name="variant1" id="variant1" value="" placeholder="สี" />
                            </div>
                            <div class="right_model">
                              <p>ค่าของคุณสมบัติ เพิ่มรายการคุณสมบัติสินค้าด้วยเครื่องหมายจุลภาคหรือกดปุ่ม Enter</p>
                              <input type="text" class="form-control tokenfield" name="variant1_val" id="variant1_val" value="" placeholder="สีแดง,สีดำ" />
                            </div>
                          </div>
                          <div class="container_model">
                            <div class="left_model">
                              <input type="text" class="form-control" name="variant2" id="variant2" value="" placeholder="ขนาด" />
                            </div>
                            <div class="right_model">
                              <input type="text" class="form-control tokenfield2" name="variant2_val" id="variant2_val" value="" placeholder="S,M,L,XL" data-plugin="tokenfield"/>
                            </div>
                          </div>
                        </div>
                        <br>
                        <div class="panel-body" >
                          <table class="table table-bordered">
                              <thead>
                                <tr>
                                  <th>รูปภาพ</th>
                                </tr>
                              </thead>
                              <tbody id="list-model-file">
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row row-lg" id="show_model_detail" style="display: none;">
            <div class="col-md-12 col-lg-12">
              <div class="panel">
                <div class="panel-body">
                  <div class="example table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="20%" id="variant1_txt">คุณสมบัติ1</th>
                          <th width="20%" id="variant2_txt">คุณสมบัติ2</th>
                          <th width="20%">ราคา</th>
                          <th width="20%">มีสินค้า</th>
                          <th width="20%">น้ำหนัก</th>
                        </tr>
                      </thead>
                      <tbody id="list-model-data">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div> 
          <hr>
          <div class="row row-lg">
            <div class="col-md-12 col-lg-6">
              <div class="example-wrap">
                <h4 class="example-title">รายละเอียดสินค้า</h4>
                <div class="example">
                  <div class="form-group row">
                    <div class="col-md-12">
                      <div class="panel">
                        <div class="panel-heading">
                          <h3 class="panel-title">รายละเอียด</h3>
                        </div>
                        <div class="panel-body">
                          <textarea name="Description" class="summernote" data-plugin="summernote"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <a href="<?php echo base_url();?>product/product_list" id="addToTable" class="btn btn-outline btn-primary" >
                      <i class="icon wb-arrow-left" aria-hidden="true"></i> กลับ
                    </a>
                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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

<div
      class="modal fade"
      id="Modal_add_model"
      tabindex="-1"
      role="dialog"
      aria-labelledby="locModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="locModalLabel"></h5>
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
            <form id="company-form">
              <div class="form-company">
                <label for="city">Model Name</label>
                <input type="text" id="model_name" class="form-control" />
                <span id="err_valid"></span>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button id="add_model_btn" type="button" class="btn btn-primary">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="Modal_add_model_data"
      tabindex="-1"
      role="dialog"
      aria-labelledby="locModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="locModalLabel"></h5>
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
            <form id="form_model_add" method="post" enctype="multipart/form-data">
              <div class="row row-lg">
                <div class="col-md-12 col-lg-4">
                  <div class="form-company">
                    <label for="city">รูป1</label>
                    <input type="file" id="fileUpload1" name="fileUpload1" />
                    <span id="err_valid"></span>
                  </div>
                </div>
                <div class="col-md-12 col-lg-4">
                  <div class="form-company">
                    <label for="city">Model1</label>
                    <select name="model1" id="model1" class="form-control">
                            <option value="">Select One...</option>
                          </select>
                    <span id="err_valid"></span>
                  </div>
                </div>
                <div class="col-md-12 col-lg-4">
                  <div class="form-company">
                    <label for="city">Title1</label>
                    <input type="text" id="title1" name="title1" class="form-control" />
                    <span id="err_valid"></span>
                  </div>
                </div>
              </div>
              <div class="row row-lg">
                <div class="col-md-12 col-lg-4">
                  <div class="form-company">
                    <label for="city">รูป2</label>
                    <input type="file" id="fileUpload2" name="fileUpload2" />
                    <span id="err_valid"></span>
                  </div>
                </div>
                <div class="col-md-12 col-lg-4">
                  <div class="form-company">
                    <label for="city">Model2</label>
                    <select name="model2" id="model2" class="form-control">
                            <option value="">Select One...</option>
                          </select>
                    <span id="err_valid"></span>
                  </div>
                </div>
                <div class="col-md-12 col-lg-4">
                  <div class="form-company">
                    <label for="city">Title2</label>
                    <input type="text" id="title2" name="title2" class="form-control" />
                    <span id="err_valid"></span>
                  </div>
                </div>
              </div>
              <div class="row row-lg">
                <div class="col-md-12 col-lg-6">
                  <label for="city">ราคา</label>
                  <input type="text" id="model_price" name="model_price" class="form-control" />
                  <span id="err_valid"></span>
                </div>
              </div>    
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button id="add_model_data_btn" type="button" class="btn btn-primary">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>

<div
      class="modal fade"
      id="ModalDel"
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
            <p>Are you sure to delete!!!</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button id="delete_model_btn" type="button" class="btn btn-primary">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>    
<div
      class="modal fade"
      id="ModalDelData"
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
            <p>Are you sure to delete!!!</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button id="delete_model_data_btn" type="button" class="btn btn-primary">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>      

    <script id="template-upload" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
          <tr class="template-upload fade{%=o.options.loadImageFileTypes.test(file.type)?' image':''%}">
              <td>
                  <span class="preview"></span>
              </td>
              <td>
                  <p class="name">{%=file.name%}</p>
                  <strong class="error text-danger"></strong>
              </td>
              <td>
                  <p class="size">Processing...</p>
                  <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
              </td>
              <td>
                  {% if (!o.options.autoUpload && o.options.edit && o.options.loadImageFileTypes.test(file.type)) { %}
                    <button class="btn btn-success edit" data-index="{%=i%}" disabled>
                        <i class="glyphicon glyphicon-edit"></i>
                        <span>Edit</span>
                    </button>
                  {% } %}
                  {% if (!i && !o.options.autoUpload) { %}
                      <button class="btn btn-primary start" disabled>
                          <i class="glyphicon glyphicon-upload"></i>
                          <span>Start</span>
                      </button>
                  {% } %}
                  {% if (!i) { %}
                      <button class="btn btn-warning cancel">
                          <i class="glyphicon glyphicon-ban-circle"></i>
                          <span>Cancel</span>
                      </button>
                  {% } %}
              </td>
          </tr>
      {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
          <tr class="template-download fade{%=file.thumbnailUrl?' image':''%}">
              <td>
                  <span class="preview">
                      {% if (file.thumbnailUrl) { %}
                          <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                      {% } %}
                  </span>
              </td>
              <td>
                  <p class="name">
                      {% if (file.url) { %}
                          <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                      {% } else { %}
                          <span>{%=file.name%}</span>
                      {% } %}
                  </p>
                  {% if (file.error) { %}
                      <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                  {% } %}
              </td>
              <td>
                  <span class="size">{%=o.formatFileSize(file.size)%}</span>
              </td>
              <td>
                  {% if (file.deleteUrl) { %}
                      <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                          <i class="glyphicon glyphicon-trash"></i>
                          <span>Delete</span>
                      </button>
                      <input type="checkbox" name="delete" value="1" class="toggle">
                  {% } else { %}
                      <button class="btn btn-warning cancel">
                          <i class="glyphicon glyphicon-ban-circle"></i>
                          <span>Cancel</span>
                      </button>
                  {% } %}
              </td>
          </tr>
      {% } %}
    </script>

    <script type="module">
      var arr_path_js = window.location.pathname.split("/");
      //var link_pintura = arr_path_js[1]+'/global/vendor/fileupload/js/vendor/pintura.min.js';
      // import Pintura Image Editor functionality:
      import { openDefaultEditor } from '/bnytech/global/vendor/fileupload/js/vendor/pintura.min.js';
      $(function () {
        $('#fileupload').fileupload('option', {
          // When editing a file use Pintura Image Editor:
          edit: function (file) {
            return new Promise((resolve, reject) => {
              const editor = openDefaultEditor({
                src: file,
                imageCropAspectRatio: 1,
              });
              editor.on('process', ({ dest }) => {
                resolve(dest);
              });
              editor.on('close', () => {
                resolve(file);
              });
            });
          }
        });
      });
    </script>  