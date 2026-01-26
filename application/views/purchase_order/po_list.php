<div class="page">
  <div class="page-header">
    <h1 class="page-title">Purchase order</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="#" method="post">
              <input type="hidden" name="search_type" id="search_type" value="1">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหา</h4>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <input type="text" class="form-control" id="po_search" name="po_search"  placeholder="Po number..." value="<?php echo $data_search['po_search']?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <div class="input-search">
                          <i class="input-search-icon wb-search" aria-hidden="true"></i>
                          <select class="form-control" name="po_status" id="po_status">
                            <option value="All">All</option>
                            <option value="Active" selected>Active</option>
                            <option value="Canceled">Canceled</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="input-group">                                            
                        <input type="text" class="form-control" name="daterange" id="daterange">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                           <button type="button" class="btn-primary btn" id="btn_search">
                           ค้นหา
                        </button>
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
          <a href="<?php echo base_url();?>purchase_order/po_set" id="addToTable" class="btn btn-outline btn-primary" target="_top">
            <i class="icon wb-plus" aria-hidden="true"></i> สร้าง Purchase order
          </a>       
          <div class="example" id="highlighting">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Po number
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('po_number','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('po_number','desc',1)"></i>
                  </th>
                  <th>Status
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('status','asc',2)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('status','desc',3)"></i>
                  </th>
                  <th>Supplier name
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_name','asc',4)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_name','desc',5)"></i>
                  </th>
                  <th>Date
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_purchase_order.cdate','asc',6)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_purchase_order.cdate','desc',7)"></i>
                  </th>
                  <th class="text-nowrap">จัดการ</th>
                </tr>
              </thead>
            <tbody id="content-list">
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
    id="ModalDelPo"
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
        <p>Are you sure to delete this Purchase order!!!</p>
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
          id="del_po_btn"
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



<script> // กำหนดปุ่มเป็น disable ไว้ ต้องทำ reCHAPTCHA ก่อนจึงกดได้
  function makeaction(){
        document.getElementById('pop_login_btn').disabled = false;  
  }
  </script>