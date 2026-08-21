<style>
.bny-step-switch{position:relative;display:inline-block;width:240px;max-width:100%;vertical-align:middle;touch-action:none;user-select:none;box-sizing:border-box;padding:0 2px 2px}
.bny-step-switch-rail{position:absolute;left:0;right:0;top:0;height:28px;z-index:2;pointer-events:none}
.bny-step-switch-track{position:absolute;left:12.5%;right:12.5%;top:12px;height:3px;background:#d0d5dd;border-radius:2px}
.bny-step-thumb{position:absolute;top:4px;left:12.5%;width:18px;height:18px;margin:0;padding:0;border:0;border-radius:50%;background:#1a73e8;box-shadow:0 0 0 3px #fff,0 1px 4px rgba(26,115,232,.35);cursor:grab;transform:translateX(-50%);transition:left .15s ease;outline:none;pointer-events:auto;z-index:3}
.bny-step-switch.is-dragging .bny-step-thumb{cursor:grabbing;transition:none}
.bny-step-switch-options{display:grid;grid-template-columns:repeat(4,1fr);position:relative;z-index:1}
.bny-step-switch-opt{display:flex;flex-direction:column;align-items:center;margin:0;padding:0;cursor:pointer;text-align:center}
.bny-step-switch-opt input[type=radio]{position:absolute;opacity:0;width:1px;height:1px;margin:-1px;clip:rect(0,0,0,0);overflow:hidden;appearance:none;-webkit-appearance:none;pointer-events:none;border:0}
.bny-step-dot{display:block;width:10px;height:10px;margin-top:9px;border-radius:50%;background:#c5cad3;box-shadow:0 0 0 3px #fff}
.bny-step-label{display:block;margin-top:6px;font-size:11px;font-weight:600;line-height:1.15;color:#8a9199;white-space:nowrap}
.bny-step-switch-opt.is-active .bny-step-label,.bny-step-switch-opt input[type=radio]:checked~.bny-step-label{color:#1a73e8}
.bny-step-switch-opt.is-active .bny-step-dot,.bny-step-switch-opt input[type=radio]:checked+.bny-step-dot{visibility:hidden}
</style>
<div class="page" style="padding-left: 12px; padding-right: 8px; box-sizing: border-box;">
  <div class="page-header">
    <h1 class="page-title">Products</h1>
  </div>
  <div class="page-content" style="margin-right: 0; margin-left: 0;">
    <div class="panel panel_box" style="margin:20px 0;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin:10px;">
          <label for="domain_sel" style="margin:0;">Domain</label>
          <select class="form-control" id="domain_sel" name="domain_sel" style="max-width:220px;">
            <?php if(empty($arr_domains)){ ?>
              <option value="">No domains — add a domain first</option>
            <?php } else { foreach($arr_domains as $d){ ?>
              <option value="<?php echo htmlspecialchars($d['web_domain_id_en'], ENT_QUOTES, 'UTF-8');?>" <?php echo ($d['web_domain_id_en'] === $selected_domain_en) ? 'selected' : '';?>>
                <?php echo htmlspecialchars($d['web_domain_name'], ENT_QUOTES, 'UTF-8');?>
              </option>
            <?php }} ?>
          </select>

          <label for="category_filter" style="margin:0;">Category</label>
          <select class="form-control" id="category_filter" name="category_filter" style="max-width:180px;">
            <option value="0">All categories</option>
          </select>

          <label for="product_search" style="margin:0;">Search</label>
          <input type="text" class="form-control" id="product_search" placeholder="Title or SKU" style="max-width:160px;" autocomplete="off">

          <label for="per_page" style="margin:0;">Per page</label>
          <select class="form-control" id="per_page" style="max-width:70px;">
            <option value="10">10</option>
            <option value="20" selected>20</option>
            <option value="50">50</option>
          </select>

          <div class="bny-step-switch" id="entry_mode_group" role="radiogroup" aria-label="Product entry type">
            <div class="bny-step-switch-rail" aria-hidden="true">
              <div class="bny-step-switch-track"></div>
              <button type="button" class="bny-step-thumb" id="entry_mode_thumb" tabindex="-1"></button>
            </div>
            <div class="bny-step-switch-options">
              <label class="bny-step-switch-opt">
                <input type="radio" name="entry_mode" value="regular">
                <span class="bny-step-dot"></span>
                <span class="bny-step-label"><?php echo htmlspecialchars(alang('entry_regular', 'Regular'), ENT_QUOTES, 'UTF-8'); ?></span>
              </label>
              <label class="bny-step-switch-opt">
                <input type="radio" name="entry_mode" value="bom" checked>
                <span class="bny-step-dot"></span>
                <span class="bny-step-label"><?php echo htmlspecialchars(alang('entry_bom', 'BOM'), ENT_QUOTES, 'UTF-8'); ?></span>
              </label>
              <label class="bny-step-switch-opt">
                <input type="radio" name="entry_mode" value="variant">
                <span class="bny-step-dot"></span>
                <span class="bny-step-label"><?php echo htmlspecialchars(alang('entry_variant', 'Variant'), ENT_QUOTES, 'UTF-8'); ?></span>
              </label>
              <label class="bny-step-switch-opt">
                <input type="radio" name="entry_mode" value="hybrid">
                <span class="bny-step-dot"></span>
                <span class="bny-step-label"><?php echo htmlspecialchars(alang('entry_hybrid', 'Hybrid'), ENT_QUOTES, 'UTF-8'); ?></span>
              </label>
            </div>
          </div>

          <button type="button" class="btn btn-outline btn-primary" id="btn_add_product" style="margin-left:auto;" <?php echo empty($arr_domains) ? 'disabled' : '';?>>
            <i class="icon wb-plus" aria-hidden="true"></i> Add product
          </button>
        </div>
      </div>
    </div>

    <div class="panel panel_box" style="margin-bottom:20px;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div class="table-responsive" style="margin:10px;">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width:72px;">Thumb</th>
                <th>Title</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Type</th>
                <th style="width:90px;" class="text-center">Display</th>
                <th class="text-nowrap">Action</th>
              </tr>
            </thead>
            <tbody id="product-list">
              <tr><td colspan="8" class="text-center">Select a domain</td></tr>
            </tbody>
          </table>
        </div>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin:10px;">
          <span id="product-pager-info" class="text-muted">—</span>
          <div>
            <button type="button" class="btn btn-default btn-sm" id="btn_prev_page" disabled>Prev</button>
            <input type="hidden" id="page" value="1">
            <button type="button" class="btn btn-default btn-sm" id="btn_next_page" disabled>Next</button>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel_box" id="div_manage_product" style="margin-bottom:20px; display:none;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding:20px;">
        <h4 class="example-title" id="manage_product_txt">Add product</h4>
        <form id="product_form" onsubmit="return false;">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;">
                  <label style="margin:0;">Title</label>
                  <span>
                    <button type="button" class="btn btn-xs btn-default js-translate" data-pair="title" data-to="th" title="Translate EN → TH">EN → TH</button>
                    <button type="button" class="btn btn-xs btn-default js-translate" data-pair="title" data-to="en" title="Translate TH → EN">TH → EN</button>
                  </span>
                </div>
                <div class="row" style="margin-top:6px;">
                  <div class="col-sm-6">
                    <label for="prod_title_en" class="text-muted" style="font-weight:normal;">English</label>
                    <input type="text" class="form-control" id="prod_title_en" autocomplete="off">
                  </div>
                  <div class="col-sm-6">
                    <label for="prod_title_th" class="text-muted" style="font-weight:normal;">ไทย</label>
                    <input type="text" class="form-control" id="prod_title_th" autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_sku">SKU</label>
                    <input type="text" class="form-control" id="prod_sku" autocomplete="off">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_barcode">Barcode</label>
                    <input type="text" class="form-control" id="prod_barcode" autocomplete="off">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_unit"><?php echo htmlspecialchars(alang('unit', 'Unit'), ENT_QUOTES, 'UTF-8'); ?></label>
                    <select class="form-control" id="prod_unit">
                      <option value="0">—</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="prod_category">Category</label>
                    <select class="form-control" id="prod_category">
                      <option value="0">— None —</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Entry type</label>
                    <div class="form-control-static" id="prod_entry_type_label" style="padding-top:7px; font-weight:600;">BOM</div>
                    <input type="hidden" id="prod_entry_type" value="bom">
                    <small class="text-muted">Set by the switches next to Add product (edit keeps the product’s type).</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_price">Price</label>
                    <input type="number" step="0.01" class="form-control" id="prod_price" autocomplete="off">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_cost">Cost price</label>
                    <input type="number" step="0.01" class="form-control" id="prod_cost" autocomplete="off">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_sort">Sort order</label>
                    <input type="number" class="form-control" id="prod_sort" value="0" autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;">
                  <label style="margin:0;">Description</label>
                  <span>
                    <button type="button" class="btn btn-xs btn-default js-translate" data-pair="description" data-to="th" data-format="html">EN → TH</button>
                    <button type="button" class="btn btn-xs btn-default js-translate" data-pair="description" data-to="en" data-format="html">TH → EN</button>
                  </span>
                </div>
                <ul class="nav nav-tabs" style="margin-top:8px;" role="tablist">
                  <li class="active"><a href="#tab_des_en" data-toggle="tab">English</a></li>
                  <li><a href="#tab_des_th" data-toggle="tab">ไทย</a></li>
                </ul>
                <div class="tab-content" style="padding-top:10px;">
                  <div class="tab-pane active" id="tab_des_en">
                    <textarea id="prod_des_en" name="prod_des_en" rows="6"></textarea>
                  </div>
                  <div class="tab-pane" id="tab_des_th">
                    <textarea id="prod_des_th" name="prod_des_th" rows="6"></textarea>
                  </div>
                </div>
              </div>

              <div class="form-group" style="margin-top:20px;">
                <h4 class="example-title" style="margin-bottom:12px;">Packing</h4>
                <div class="row">
                  <div class="col-sm-3">
                    <label for="prod_width">Width (cm)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_width">
                  </div>
                  <div class="col-sm-3">
                    <label for="prod_length">Length (cm)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_length">
                  </div>
                  <div class="col-sm-3">
                    <label for="prod_height">Height (cm)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_height">
                  </div>
                  <div class="col-sm-3">
                    <label for="prod_weight">Weight (g)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_weight">
                  </div>
                </div>
                <div class="row" style="margin-top:10px;">
                  <div class="col-sm-4">
                    <label for="prod_load_x">Max load X (g)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_load_x">
                  </div>
                  <div class="col-sm-4">
                    <label for="prod_load_y">Max load Y (g)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_load_y">
                  </div>
                  <div class="col-sm-4">
                    <label for="prod_load_z">Max load Z (g)</label>
                    <input type="number" step="0.01" class="form-control" id="prod_load_z">
                  </div>
                </div>
              </div>

              <div class="form-group" style="margin-top:28px;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
                  <h4 class="example-title" style="margin:0;">SEO</h4>
                  <span>
                    <button type="button" class="btn btn-xs btn-default js-translate" data-pair="seo" data-to="th">EN → TH</button>
                    <button type="button" class="btn btn-xs btn-default js-translate" data-pair="seo" data-to="en">TH → EN</button>
                  </span>
                </div>
                <div id="seo_google_preview" style="border:1px solid #e0e0e0; border-radius:4px; padding:14px 16px; margin-bottom:16px; background:#fff; max-width:600px;">
                  <div id="seo_prev_url" style="color:#006621; font-size:13px; word-break:break-all;">https://example.com/product/…</div>
                  <div id="seo_prev_title" style="color:#1a0dab; font-size:18px; line-height:1.3; margin:2px 0;">Product title</div>
                  <div id="seo_prev_desc" style="color:#545454; font-size:13px; line-height:1.4;">Meta description snippet</div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="seo_title_en">SEO title (EN)</label>
                      <input type="text" class="form-control" id="seo_title_en" maxlength="60" autocomplete="off">
                    </div>
                    <div class="form-group">
                      <label for="seo_description_en">Meta description (EN)</label>
                      <textarea class="form-control" id="seo_description_en" rows="3" maxlength="150"></textarea>
                    </div>
                    <div class="form-group">
                      <label for="seo_keywords_en">Keywords (EN)</label>
                      <input type="text" class="form-control" id="seo_keywords_en" maxlength="500" autocomplete="off">
                    </div>
                    <div class="form-group">
                      <label for="seo_slug_en">URL slug (EN)</label>
                      <input type="text" class="form-control" id="seo_slug_en" maxlength="255" autocomplete="off">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="seo_title_th">SEO title (TH)</label>
                      <input type="text" class="form-control" id="seo_title_th" maxlength="60" autocomplete="off">
                    </div>
                    <div class="form-group">
                      <label for="seo_description_th">Meta description (TH)</label>
                      <textarea class="form-control" id="seo_description_th" rows="3" maxlength="150"></textarea>
                    </div>
                    <div class="form-group">
                      <label for="seo_keywords_th">Keywords (TH)</label>
                      <input type="text" class="form-control" id="seo_keywords_th" maxlength="500" autocomplete="off">
                    </div>
                    <div class="form-group">
                      <label for="seo_slug_th">URL slug (TH)</label>
                      <input type="text" class="form-control" id="seo_slug_th" maxlength="255" autocomplete="off">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label style="display:block; margin-bottom:8px;">Display on storefront</label>
                <label class="bny-switch" title="Display on storefront">
                  <input type="checkbox" id="prod_is_visible" value="1" checked>
                  <span class="bny-switch-slider"></span>
                </label>
              </div>
              <div class="form-group">
                <label style="display:block; margin-bottom:8px;">Atomic (fundamental)</label>
                <label class="bny-switch">
                  <input type="checkbox" id="prod_is_atomic" value="1">
                  <span class="bny-switch-slider"></span>
                </label>
              </div>
              <div class="form-group">
                <label style="display:block; margin-bottom:8px;">Salable</label>
                <label class="bny-switch">
                  <input type="checkbox" id="prod_is_salable" value="1">
                  <span class="bny-switch-slider"></span>
                </label>
              </div>
              <div class="form-group">
                <label>Product image</label>
                <div id="prod_thumb_preview_wrap" style="margin-bottom:10px; display:none;">
                  <img id="prod_thumb_preview" src="" alt="Thumbnail preview" style="max-width:100%; max-height:180px; border:1px solid #ddd;">
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                  <button type="button" class="btn btn-default" id="btn_select_thumb">Select image</button>
                  <button type="button" class="btn btn-danger" id="btn_remove_thumb" style="display:none;">Remove</button>
                </div>
                <input type="file" id="prod_thumb_file" accept="image/jpeg,image/png,image/gif,image/jpg" style="display:none;">
                <input type="hidden" id="thumbnail_old" value="">
                <input type="hidden" id="clear_thumbnail" value="0">
                <small class="text-muted" style="display:block; margin-top:6px;">Square crop recommended.</small>
              </div>
            </div>
          </div>

          <input type="hidden" id="id_en" value="">
          <input type="hidden" id="is_add" value="1">
          <div style="margin-top:16px;">
            <button type="button" class="btn btn-default" id="btn_cancel_product">Cancel</button>
            <button type="button" class="btn btn-primary" id="btn_save_product">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="prod_crop_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Crop thumbnail</h4>
      </div>
      <div class="modal-body">
        <div class="btn-group" style="margin-bottom:12px;" role="group">
          <button type="button" class="btn btn-default active" id="btn_crop_mode"><i class="icon wb-crop"></i> Crop</button>
          <button type="button" class="btn btn-default" id="btn_move_mode"><i class="icon wb-move"></i> Move</button>
        </div>
        <div style="max-height:420px; overflow:hidden; background:repeating-conic-gradient(#e8e8e8 0% 25%, #fafafa 0% 50%) 50% / 16px 16px;">
          <img id="prod_crop_image" src="" alt="Crop" style="max-width:100%;">
        </div>
        <div style="margin-top:14px; display:flex; align-items:center; gap:8px;">
          <button type="button" class="btn btn-default btn-sm" id="btn_zoom_out">&minus;</button>
          <input type="range" id="prod_crop_zoom" min="5" max="300" value="100" step="1" style="flex:1; margin:0;">
          <button type="button" class="btn btn-default btn-sm" id="btn_zoom_in">+</button>
          <span id="prod_crop_zoom_label" style="min-width:44px; text-align:right;">100%</span>
          <button type="button" class="btn btn-default btn-sm" id="btn_zoom_reset">100%</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="btn_crop_apply">Apply</button>
      </div>
    </div>
  </div>
</div>
