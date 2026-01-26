<div class="page">
  <div class="page-header">
    <h1 class="page-title">Expense</h1>
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
                          <input type="text" class="form-control" id="expense_search" name="expense_search"  placeholder="Search" value="<?php echo $data_search['expense_search']?>">
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
          <a href="<?php echo base_url();?>expense/expense_add_form" id="addToTable" class="btn btn-outline btn-primary" target="_top">
            <i class="icon wb-plus" aria-hidden="true"></i> สร้าง Expense
          </a>       
          <div class="example" id="highlighting">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>PO Number
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('po_number','asc',1)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('po_number','desc',2)"></i>
                  </th>
                  <th>Supplier name
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('supplier_name','asc',3)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('supplier_name','desc',4)"></i>
                  </th>
                  <th>Date
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('expense_date','asc',5)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('expense_date','desc',6)"></i>
                  </th>
                  <th>Amount
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('expense_amount','asc',7)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('expense_amount','desc',8)"></i>
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

<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>

<script> // กำหนดปุ่มเป็น disable ไว้ ต้องทำ reCHAPTCHA ก่อนจึงกดได้
  function makeaction(){
        document.getElementById('pop_login_btn').disabled = false;  
  }
  </script>