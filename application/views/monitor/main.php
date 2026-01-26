<style>
  @keyframes color-change {
  0% {
    background-color: white;
  }
  100% {
    background-color: red;
  }
}
  </style>
<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <h3>Bnyfoods Overview</h3>
          </div>
        </div>
      </div>
      <div class="row">
        <div class='card'>
            <div class='card-body'>
            <div class="page-content container-fluid">
        <div class="row">

        	<div class="col-xl-3 col-md-6 info-panel" id="order_lazada">
            <div class="card card-shadow">
              <div class="card-block bg-white p-20">
                <img src="<?php echo base_url();?>resources/images/lazada-icon.png" style="width:45px;">
                <span class="ml-15 font-weight-400">Lazada Token</span>
                <div class="content-text text-center mb-0">
                  <span class="font-size-40 font-weight-100" id="txt_order_lazada_alert">Expire in 0 day</span>
                  <p class="blue-grey-400 font-weight-100 m-0"><a href="https://auth.lazada.com/oauth/authorize?response_type=code&redirect_uri=https://www.bnyfoodproducts.com/lazcallback&force_auth=true&client_id=123793" target="_top">Click to renew token</a></p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 info-panel" id="order_shopee">
            <div class="card card-shadow" >
              <div class="card-block bg-white p-20" >
                <img src="<?php echo base_url();?>resources/images/shopee-icon5.png" style="width:45px;">
                <span class="ml-15 font-weight-400" >Shopee Token</span>
                <div class="content-text text-center mb-0">
                  <span class="font-size-40 font-weight-100" id="txt_order_shopee_alert">Expire in 0 day</span>
                  <p class="blue-grey-400 font-weight-100 m-0"><a href="<?php echo $link?>" target="_top">Click to renew token</a></p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 info-panel">
            <div class="card card-shadow">
              <div class="card-block bg-white p-20">
                <button type="button" class="btn btn-floating btn-sm btn-success">
                  <i class="icon wb-eye"></i>
                </button>
                <span class="ml-15 font-weight-400">VISITORS</span>
                <div class="content-text text-center mb-0">
                  <i class="text-danger icon wb-triangle-up font-size-20">
              </i>
                  <span class="font-size-40 font-weight-100">23,456</span>
                  <p class="blue-grey-400 font-weight-100 m-0">+25% From previous month</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6 info-panel">
            <div class="card card-shadow">
              <div class="card-block bg-white p-20">
                <button type="button" class="btn btn-floating btn-sm btn-danger">
                  <i class="icon wb-user"></i>
                </button>
                <span class="ml-15 font-weight-400">CUSTOMERS</span>
                <div class="content-text text-center mb-0">
                  <i class="text-danger icon wb-triangle-up font-size-20">
              </i>
                  <span class="font-size-40 font-weight-100">4,367</span>
                  <p class="blue-grey-400 font-weight-100 m-0">+25% From previous month</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6" id="ecommerceRecentOrder">
            <div class="card card-shadow table-row">
              <div class="card-header card-header-transparent py-20">
                <div class="btn-group dropdown">
                  <a href="#" class="text-body dropdown-toggle blue-grey-700" data-toggle="dropdown">API LAZADA ORDER LOG</a>
                </div>
              </div>
              <div class="card-block bg-white table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Note</th>
                      <th>Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="lazada_api_list">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- End Third Left -->

          <!-- Third Right -->
          <div class="col-lg-6" id="ecommerceRecentOrder">
            <div class="card card-shadow table-row">
              <div class="card-header card-header-transparent py-20">
                <div class="btn-group dropdown">
                  <a href="#" class="text-body dropdown-toggle blue-grey-700" data-toggle="dropdown">API LAZADA FINANCE LOG</a>
                </div>
              </div>
              <div class="card-block bg-white table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Note</th>
                      <th>Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="lazada_api_finance_list">
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
</div>
<!-- ======= Modal Delete == --->

<div
      class="modal fade"
      id="ModalChangeStatus"
      tabindex="-1"
      role="dialog"
      aria-labelledby="ModalDelLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalDelLabel">Edit</h5>
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
            <p>Are you sure to change status!!!</p>
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
              id="change_status_btn"
              type="button"
              class="btn btn-primary"
              data-dismiss="modal"
            >
              Change Status
            </button>
          </div>
        </div>
      </div>
    </div>


