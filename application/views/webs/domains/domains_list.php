<div class="page">
  <div class="page-header">
    <h1 class="page-title">Manage Domains</h1>
    <div class="page-header-actions">
    </div>
  </div>
  <div class="page-content" style="margin-right: 10px">
        <div class="panel panel_box" style="margin-bottom:20px;margin-top:20px;">
          <div class="panel-body" style="background:#fff; border-radius:7px;">
            <div class="example-wrap">
              <div class="example">
                <form role="form" name="domain_search_form" id="domain_search_form" action="<?php echo base_url()."webs/domains/domains_list";?>" method="get">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;margin-top:15px; flex-wrap:wrap;">
                            <input type="text" class="form-control" id="domain_search" name="domain_search" placeholder="Search..." value="<?php echo htmlspecialchars($data_search['domain_search'], ENT_QUOTES, 'UTF-8');?>" style="width:20%; min-width:160px;">
                            <button type="submit" class="btn btn-primary" id="btn_search">ค้นหา</button>
                            <a href="<?php echo base_url();?>webs/domains/domains_list" id="addToTable" class="btn btn-default">ทั้งหมด</a>
                            <a href="<?php echo base_url();?>webs/domains/add_domain_form" class="btn btn-outline btn-primary">
                              <i class="icon wb-plus" aria-hidden="true"></i> Add Domain
                            </a>
                            <input type="hidden" name="page" id="page" value="<?php echo (int)$data_search['page'];?>">
                            <input type="hidden" name="sortby" id="sortby" value="<?php echo htmlspecialchars($data_search['sortby'], ENT_QUOTES, 'UTF-8');?>">
                            <input type="hidden" name="sorttype" id="sorttype" value="<?php echo htmlspecialchars($data_search['sorttype'], ENT_QUOTES, 'UTF-8');?>">
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
                  <th>Registrar link</th>
                  <th>SSL link</th>
                  <th>Expiration
                    <i class="icon wb-triangle-up asssort" aria-hidden="true" onclick="tablesort('expire_date','asc',2)"></i>
                    <i class="icon wb-triangle-down asssort" aria-hidden="true" onclick="tablesort('expire_date','desc',3)"></i>
                  </th>
                  <th class="text-nowrap">Action</th>
                </tr>
              </thead>
              <tbody id="content-list">
              <?php 
                  if(!empty($arr_domains)){
                  foreach($arr_domains as $arr_domain){
                    $reg = isset($arr_domain['registrar_link']) ? trim($arr_domain['registrar_link']) : '';
                    $ssl = isset($arr_domain['ssl_link']) ? trim($arr_domain['ssl_link']) : '';
                    $exp = isset($arr_domain['expire_date_display']) ? $arr_domain['expire_date_display'] : '';
              ?>  
              <tr>
                <td><?php echo htmlspecialchars($arr_domain['web_domain_name'], ENT_QUOTES, 'UTF-8');?></td>
                <td>
                  <?php if($reg !== ''){ ?>
                    <a href="<?php echo htmlspecialchars($reg, ENT_QUOTES, 'UTF-8');?>" target="_blank" rel="noopener noreferrer">Open</a>
                  <?php } else { echo '-'; } ?>
                </td>
                <td>
                  <?php if($ssl !== ''){ ?>
                    <a href="<?php echo htmlspecialchars($ssl, ENT_QUOTES, 'UTF-8');?>" target="_blank" rel="noopener noreferrer">Open</a>
                  <?php } else { echo '-'; } ?>
                </td>
                <td><?php echo $exp !== '' ? htmlspecialchars($exp, ENT_QUOTES, 'UTF-8') : '-';?></td>
                <td class="text-nowrap">
                  <a href="<?php echo base_url();?>webs/domains/domain_edit_form/<?php echo $arr_domain['web_domain_id'];?>" data-toggle="tooltip" data-original-title="Edit"> 
                    <i class="icon wb-wrench" aria-hidden="true"></i>
                  </a>
                  <a href="#" class="js-domain-del" data-id="<?php echo htmlspecialchars($arr_domain['web_domain_id'], ENT_QUOTES, 'UTF-8');?>" data-toggle="tooltip" data-original-title="Delete">
                    <i class="icon wb-close" aria-hidden="true"></i>
                  </a>
                </td>
              </tr>          
            <?php }}?>
            <?php if(empty($arr_domains)){?>
              <tr>
                <td colspan="5" class="text-center">No domains found</td>
              </tr>
            <?php }?>
            </tbody>
          </table>
          <div id="domain-pager" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:10px 10px 20px;">
            <span id="domain-pager-info">
              Showing page <?php echo (int)$data_search['page'];?> / <?php echo (int)$total_pages;?>
              (<?php echo (int)$total_rows;?> total)
            </span>
            <button type="button" class="btn btn-default btn-sm" id="btn_prev_page" <?php echo ((int)$data_search['page'] <= 1) ? 'disabled' : '';?>>Prev</button>
            <button type="button" class="btn btn-default btn-sm" id="btn_next_page" <?php echo ((int)$data_search['page'] >= (int)$total_pages) ? 'disabled' : '';?>>Next</button>
            <label for="per_page" style="margin:0 0 0 8px;">Rows</label>
            <select class="form-control input-sm" name="per_page" id="per_page" form="domain_search_form" style="width:90px; display:inline-block;">
              <?php foreach($allowed_per_page as $opt){ ?>
                <option value="<?php echo (int)$opt;?>" <?php echo ((int)$data_search['per_page'] === (int)$opt) ? 'selected' : '';?>><?php echo (int)$opt;?></option>
              <?php } ?>
            </select>
          </div>

          </div>
        </div>
          </div>
        </div>
  </div> 
</div>       
