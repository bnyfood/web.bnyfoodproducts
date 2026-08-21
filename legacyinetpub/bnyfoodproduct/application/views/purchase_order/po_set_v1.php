
<div class="page">
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <h4 class="example-title">Supplier</h4>
            <form role="form" name="product_search" id="product_search" action="#" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
              <div aria-multiselectable="true" role="tablist" id="accordion" class="panel-group">
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
                      <div class="row_col row_col2" id ="mng_material_div" >
                        <div class="col-md-12">
                          <div class="row_col">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>
                                  Supplier
                                </label>
                                <select class="form-control" name="web_supplier_id" id="web_supplier_id">
                                  <option value="0">กรุณาเลือก</option> 
                                  <?php 
                                    foreach($arr_suppliers as $arr_supplier){
                                  ?>
                                  <option value="<?php echo $arr_supplier['web_supplier_id']?>" ><?php echo $arr_supplier['supplier_name']?></option>
                                  <?php }?> 
                                </select> 
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>
                                  รายการวัถุดิบ
                                </label>
                                <input name="material_txt" id="material_txt" class="form-control">
                              </div>
                            </div>
                            <div class="col-md-1">
                              <div style="padding-top: 30px">
                                <a href="<?php echo base_url()?>manufacture/material/material_list" id="fancy_main" class="btn btn-icon btn-primary btn-l icon wb-pencil">
                                <i class="fa fa-plus"></i> </a>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <div style="padding-top: 28px">
                                  <button type="button" class="btn btn-block btn-primary" id="po_add_btn" class="btn btn-primary" style="display: none">
                                    <i class="icon wb-plus" aria-hidden="true"></i> เพิ่ม
                                  </button>
                                </div>
                                <input type="hidden" name="mat_name" id="mat_name">
                                <input type="hidden" name="web_material_id" id="web_material_id">
                                <input type="hidden" name="ran_num_pocode" id="ran_num_pocode" value="<?php echo $ran_num_pocode;?>">
                                <input type="hidden" name="web_supplier_id_tmp" id="web_supplier_id_tmp" value="0">
                            </div>
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

    <div class="panel panel_box" id ="po_manage" >
      <div class="panel-body">
        <div class="example-wrap">
          <h4 class="example-title">Price</h4>

          <button type="button" class="btn btn-block btn-primary" id="po_make_btn" class="btn btn-primary" style="display: none;" >
              <i class="icon wb-pencil" aria-hidden="true"></i> สร้าง PO
            </button>
          <div class="example" id="po_data">
            <table class="table table-bordered" >
              <thead>
                <?php $cnt_sup = count($arr_suppliers);?>
                <tr>
                  <th width="20%" id="variant1_txt" rowspan="2" style="text-align:center;vertical-align:middle;">รายการวัถุดิบ2</th>
                  <th width="20%" id="variant2_txt" rowspan="2" style="text-align:center;vertical-align:middle;">Qty</th>
                  <th width="20%" colspan="<?php echo $cnt_sup?>">Supplier / ราคาต่อหน่วย</th>
                  <th width="20%" colspan="<?php echo $cnt_sup?>">ยอดรวม</th>
                  <th width="20%" rowspan="2"></th>
                </tr>
                <tr>
                  <?php foreach($arr_suppliers as $arr_supplier){?>
                    <th><?php echo $arr_supplier['supplier_name']?></th>
                  <?php }?>
                  <?php foreach($arr_suppliers as $arr_supplier){?>
                    <th><?php echo $arr_supplier['supplier_name']?></th>
                  <?php }?>
                </tr>
              </thead>
              <tbody id="list-sup-data">

              </tbody>
            </table>

            <table id="table_sum_po_price" class="table table-bordered" style="display: none;">
              <thead>
                <tr>
                  <td>รวม</td>
                  <td id="sum_sup_name"></td>
                  <td id="sum_po_price"></td>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel_box" id ="compare_div" style="display: none">
      <div class="panel-body">
        <div class="example-wrap">
          <h4 class="example-title">Compare Price</h4>
          <div class="example">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="20%" id="variant1_txt">SUPPLIER</th>
                  <th width="20%" id="variant1_txt">รายการวัถุดิบ</th>
                  <th width="20%" id="variant2_txt">ราคา</th>
                </tr>
              </thead>
              <tbody id="list-model-pricedata">

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="col-md-1">
            <h4 class="example-title">PO</h4>
          </div>
          <div class="col-md-2" style="padding:0px 0px 10px 0px; " id="div_po_print">
            <button type="button" class="btn btn-block btn-primary" id="po_print" class="btn btn-primary" style="display: none;" >
              <i class="icon wb-print" aria-hidden="true"></i> พิมพ์ใบ PO
            </button>
          </div>
          <div class="example">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="20%" id="variant1_txt">รายการวัถุดิบ</th>
                  <th width="20%" id="variant2_txt">ราคา</th>
                  <th width="20%">จำนวน</th>
                  <th width="20%">ยอด</th>
                  <th width="20%"></th>
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
</div>

<!-- ======= Modal Delete == --->

  <div
      class="modal fade"
      id="ModalDelMaterial"
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
            <p>Are you sure to delete material!!!</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button
              id="del_material_btn"
              type="button"
              class="btn btn-primary"
              data-dismiss="modal"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Model alt-->

    <div
      class="modal fade"
      id="ModalChangeSupplier"
      tabindex="-1"
      role="dialog"
      aria-labelledby="ModalChangeLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalChangeLabel">Change Supplier</h5>
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
            <p>Are you sure to Change Supplier!!!</p>
            <p>If confirm your supplier will be set.</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button
              id="change_supplier_btn"
              type="button"
              class="btn btn-primary"
              data-dismiss="modal"
            >
              Change
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">
      
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Modal Header</h4>
          </div>
          <div class="modal-body">
            <p>Some text in the modal.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
        
      </div>
    </div>
<style>

  .page-content {
      padding: 10px 30px !important;
  }

  .example {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }

  .panel-body {
    padding: 10px 10px !important;
}

.ui-autocomplete{

  background-color : #fff1d6 !important;
  width:400px !important;
  cursor: pointer;
}
.ui-autocomplete:hover {
        background-color: darkred;
      }


.ui-menu-item:hover {
        background-color: #b8e1e3;
      }

.ui-helper-hidden-accessible {
         /*   display: none;*/
        }

/* The Modal (background) */
.modal_v1 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modalv1-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.closev1:hover,
.closev1:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>

<div id="myModal_v1" class="modal_v1">

  <!-- Modal content -->
  <div class="modalv1-content">
    <span class="closev1">&times;</span>
    <p>Are you sure to Change Supplier!!!</p>
    <p>If confirm your supplier will be set.</p>
    <button type="button" class="btn btn-default" id="close_btn" >Close</button>
    <button type="button" class="btn btn-default change_supplier_btn" id="change_supplier_btn" >Change</button>
  </div>

</div>