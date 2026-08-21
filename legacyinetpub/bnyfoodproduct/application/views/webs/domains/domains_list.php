<div class="page">
  <div class="page-header">
    <h1 class="page-title">Manage Domains</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px">
    <div style="display:flex; gap:0px; align-items:flex-start;">
      <div style="flex:0 0 240px;">
        <div class="panel panel_box" style="margin: 20px 20px 20px 20px">
          <div class="panel-body" style="background:#fff; border-radius:7px; min-height: 200px;">
            <h4 class="example-title">Webs Menu</h4>
            <div class="list-group">
              <a class="list-group-item active" href="<?php echo base_url();?>webs/domains/domains_list">Domains</a>
              <a class="list-group-item" href="#">Example Menu 1</a>
              <a class="list-group-item" href="#">Example Menu 2</a>
              <a class="list-group-item" href="#">Example Menu 3</a>
            </div>
          </div>
        </div>
      </div>
      <div style="flex:1; min-width:0;">
        <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
          <div class="panel-body" style="background:#fff; border-radius:7px;">
            <div class="example-wrap">
              <div class="example">
                <form role="form" name="domain_search_form" id="domain_search_form" action="<?php echo base_url()."webs/domains/domains_list_search";?>" method="post">
                  
                <input type="hidden" name="search_type" id="search_type" value="1">
                    <div class="panel-body">
                      <h4 class="example-title">ค้นหา Domain</h4>
                      <div class="row">
                        <div class="col-md-12">
                          <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px;">
                            <input type="text" class="form-control" id="domain_search" name="domain_search" placeholder="Search..." value="<?php echo $data_search['domain_search']?>" style="width:20%;">
                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                            <a href="<?php echo base_url();?>webs/domains/domains_list" id="addToTable" class="btn btn-default">ทั้งหมด</a>
                            <a href="<?php echo base_url();?>webs/domains/add_domain_form" class="btn btn-outline btn-primary">
                              <i class="icon wb-plus" aria-hidden="true"></i> Add Domain
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
          <div class="panel-body" style="background:#fff; border-radius:7px;">
            <div class="example-wrap">
          <?php if($add_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              Add success
            </div>
          <?php }?>  
          <?php if($add_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              Add fail
            </div>
          <?php }?>
          <?php if($edit_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              Edit success
            </div>
          <?php }?>
          <?php if($edit_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              Edit fail
            </div>
          <?php }?>
          <?php if($del_alt == "success"){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              Delete success
            </div>
          <?php }?>
          <?php if($del_alt == "fail"){?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              Delete fail
            </div>
          <?php }?>
          <div class="example table-responsive" id="highlighting" >
            <table class="table table-bordered table-hover" style="margin: 20px 10px 20px 10px;max-width:1400px">
              <thead>
                <tr>
                  <th>Domain
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('web_domain_name','asc',0)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('web_domain_name','desc',1)"></i>
                  </th>
                  <th class="text-nowrap">Action</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_domains)){
                  foreach($arr_domains as $arr_domain){
              ?>  
              <tr>
                <td><?php echo $arr_domain['web_domain_name']?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>webs/domains/domain_edit_form/<?php echo $arr_domain['web_domain_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="<?php echo base_url();?>webs/domains/del_action/<?php echo $arr_domain['web_domain_id'];?>"><i class="icon wb-close" aria-hidden="true"></i></button>
                </td>
              </tr>          
            <?php }}?>
            <?php if(empty($arr_domains)){?>
              <tr>
                <td colspan="2" class="text-center">No domains found</td>
              </tr>
            <?php }?>
              <input type="hidden" name="offset" id="offset" value="0">
              <input type="hidden" name="sortby" id="sortby" value="<?php echo $data_search['sortby']?>">
              <input type="hidden" name="sorttype" id="sorttype" value="<?php echo $data_search['sorttype']?>">
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
