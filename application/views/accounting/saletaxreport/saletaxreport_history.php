<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <form role="form" name="product_search" id="product_search" action="<?php echo base_url()."accounting/saletaxreport/saletaxreport_make"?>" method="post" enctype="multipart/form-data">
                <div class="panel-body">
                  <hr>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <select name="platform" id="platform" class="form-control">
                            <option value="0" <?php if($arr_search['platform'] == 0){echo "selected";}?>>Lazada
                            <option value="1" <?php if($arr_search['platform'] == 1){echo "selected";}?>>Shopee
                            <option value="2" <?php if($arr_search['platform'] == 2){echo "selected";}?>>Tiktok
                            <option value="3" <?php if($arr_search['platform'] == 3){echo "selected";}?>>BigSauces
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="input-group">                                            
                        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo $arr_search['daterange']?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-3">
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

