<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="page">
  <div class="page-header">
    <h1 class="page-title">Creditnote</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content">
    <div class="panel panel_box">
      <div class="panel-body">
        <div class="example-wrap">
          <div class="example">
            <form role="form" name="product_search" id="product_search" action="#" method="post">
                <div class="panel-body">
                  <h4 class="example-title">ค้นหารายงานใบลดหนี้</h4>

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <select name="platform" id="platform" class="form-control">
                            <option value="0">Lazada
                            <option value="1">Shopee
                            <option value="2">Tiktok
                            <option value="3">BigSauces
                         </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="input-group">                                            
                        <input type="text" class="form-control" name="daterange" id="daterange">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="col-md-3 offset-md-0">
                           <input type="button" class="btn-primary btn" value="Search" id="search">
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
          <div class="example" id="highlighting">


          </div>
        </div>
      </div>
    </div>
  </div> 
</div>       