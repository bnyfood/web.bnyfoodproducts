<div class="page" style="padding-left: 12px; padding-right: 8px; box-sizing: border-box;">
  <div class="page-header">
    <h1 class="page-title">Product Categories</h1>
  </div>
  <div class="page-content" style="margin-right: 0; margin-left: 0;">
    <div class="panel panel_box" style="margin:20px 0;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin:10px;">
          <label for="domain_sel" style="margin:0;">Domain</label>
          <select class="form-control" id="domain_sel" name="domain_sel" style="max-width:360px;">
            <?php if(empty($arr_domains)){ ?>
              <option value="">No domains — add a domain first</option>
            <?php } else { foreach($arr_domains as $d){ ?>
              <option value="<?php echo htmlspecialchars($d['web_domain_id_en'], ENT_QUOTES, 'UTF-8');?>" <?php echo ($d['web_domain_id_en'] === $selected_domain_en) ? 'selected' : '';?>>
                <?php echo htmlspecialchars($d['web_domain_name'], ENT_QUOTES, 'UTF-8');?>
              </option>
            <?php }} ?>
          </select>
          <button type="button" class="btn btn-outline btn-primary" id="btn_add_root" title="Add top-level (mother) category" <?php echo empty($arr_domains) ? 'disabled' : '';?>>
            <i class="icon wb-plus" aria-hidden="true"></i> Add category
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
                <th>Category</th>
                <th>Tier</th>
                <th style="width:90px;" class="text-center">Display</th>
                <th class="text-nowrap">Action</th>
              </tr>
            </thead>
            <tbody id="cate-list">
              <tr><td colspan="5" class="text-center">Select a domain</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="panel panel_box" id="div_manage_cat" style="margin-bottom:20px; display:none;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding:20px;">
        <h4 class="example-title" id="manage_cat_txt">Add category</h4>
        <form id="cat_form" onsubmit="return false;">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="cat_name">Name</label>
                <input type="text" class="form-control" name="cat_name" id="cat_name" autocomplete="off">
              </div>
              <div class="form-group">
                <label for="cat_parent">Parent category</label>
                <select class="form-control" id="cat_parent" name="cat_parent">
                  <option value="0">No parent</option>
                </select>
              </div>
              <div class="form-group">
                <label for="cat_des">Description</label>
                <textarea name="cat_des" id="cat_des" rows="6"></textarea>
                <small class="text-muted">Rich text (WYSIWYG) — bold, lists, links, etc.</small>
              </div>

              <div class="form-group" style="margin-top:28px;">
                <h4 class="example-title" style="margin-bottom:12px;">SEO</h4>
                <div id="seo_google_preview" style="border:1px solid #e0e0e0; border-radius:4px; padding:14px 16px; margin-bottom:16px; background:#fff; max-width:600px;">
                  <div id="seo_prev_url" style="color:#006621; font-size:13px; word-break:break-all;">https://example.com/category/…</div>
                  <div id="seo_prev_title" style="color:#1a0dab; font-size:18px; line-height:1.3; margin:2px 0;">Category title</div>
                  <div id="seo_prev_desc" style="color:#545454; font-size:13px; line-height:1.4;">Meta description snippet</div>
                </div>
                <div class="form-group">
                  <label for="seo_title">SEO title</label>
                  <input type="text" class="form-control" id="seo_title" maxlength="60" autocomplete="off">
                  <small class="text-muted">Max 60 characters <span id="seo_title_count">0</span>/60</small>
                </div>
                <div class="form-group">
                  <label for="seo_description">Meta description</label>
                  <textarea class="form-control" id="seo_description" rows="3" maxlength="150"></textarea>
                  <small class="text-muted">Plain text for Google snippet — max 150 characters <span id="seo_desc_count">0</span>/150</small>
                </div>
                <div class="form-group">
                  <label for="seo_keywords">Keywords</label>
                  <input type="text" class="form-control" id="seo_keywords" maxlength="500" autocomplete="off">
                  <small class="text-muted">Max ~10 keywords, comma-separated</small>
                </div>
                <div class="form-group">
                  <label for="seo_slug">URL slug</label>
                  <input type="text" class="form-control" id="seo_slug" maxlength="255" autocomplete="off">
                  <small class="text-muted">Thai/English, numbers, dashes only (optional)</small>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label style="display:block; margin-bottom:8px;">Display on storefront</label>
                <label class="bny-switch" title="Display on storefront">
                  <input type="checkbox" id="cat_is_visible" name="cat_is_visible" value="1" checked>
                  <span class="bny-switch-slider"></span>
                </label>
                <small class="text-muted" style="display:block; margin-top:6px;">On = show on front-end (default)</small>
              </div>
              <div class="form-group">
                <label>Category image</label>
                <div id="cat_thumb_preview_wrap" style="margin-bottom:10px; display:none;">
                  <img id="cat_thumb_preview" src="" alt="Thumbnail preview" style="max-width:100%; max-height:180px; border:1px solid #ddd;">
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                  <button type="button" class="btn btn-default" id="btn_select_thumb">Select image</button>
                  <button type="button" class="btn btn-danger" id="btn_remove_thumb" style="display:none;">Remove</button>
                </div>
                <input type="file" id="cat_thumb_file" accept="image/jpeg,image/png,image/gif,image/jpg" style="display:none;">
                <input type="hidden" id="thumbnail_old" value="">
                <input type="hidden" id="clear_thumbnail" value="0">
                <small class="text-muted" style="display:block; margin-top:6px;">Square crop recommended. Crop or move before save.</small>
              </div>
            </div>
          </div>

          <input type="hidden" id="parent_id" value="0">
          <input type="hidden" id="id_en" value="">
          <input type="hidden" id="cat_id" value="">
          <input type="hidden" id="is_add" value="1">
          <div style="margin-top:16px;">
            <button type="button" class="btn btn-default" id="btn_cancel_cat">Cancel</button>
            <button type="button" class="btn btn-primary" id="btn_save_cat">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Cropper modal -->
<div class="modal fade" id="cat_crop_modal" tabindex="-1" role="dialog" aria-labelledby="cat_crop_modal_title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="cat_crop_modal_title">Crop thumbnail</h4>
      </div>
      <div class="modal-body">
        <div class="btn-group" style="margin-bottom:12px;" role="group">
          <button type="button" class="btn btn-default active" id="btn_crop_mode" title="Draw / resize crop box">
            <i class="icon wb-crop"></i> Crop
          </button>
          <button type="button" class="btn btn-default" id="btn_move_mode" title="Pan / move image">
            <i class="icon wb-move"></i> Move
          </button>
        </div>
        <div style="max-height:420px; overflow:hidden; background:repeating-conic-gradient(#e8e8e8 0% 25%, #fafafa 0% 50%) 50% / 16px 16px;">
          <img id="cat_crop_image" src="" alt="Crop" style="max-width:100%;">
        </div>
        <div class="cat-crop-zoom-bar" style="margin-top:14px; display:flex; align-items:center; gap:8px;">
          <button type="button" class="btn btn-default btn-sm" id="btn_zoom_out" title="Zoom out">&minus;</button>
          <input type="range" id="cat_crop_zoom" min="5" max="300" value="100" step="1" style="flex:1; margin:0;" title="Zoom">
          <button type="button" class="btn btn-default btn-sm" id="btn_zoom_in" title="Zoom in">+</button>
          <span id="cat_crop_zoom_label" style="min-width:44px; text-align:right; font-variant-numeric:tabular-nums;">100%</span>
          <button type="button" class="btn btn-default btn-sm" id="btn_zoom_reset" title="Reset zoom">100%</button>
        </div>
        <small class="text-muted" style="display:block; margin-top:6px;">Zoom out past 100% to leave transparent padding around the image when you Apply.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btn_crop_cancel" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="btn_crop_apply">Apply</button>
      </div>
    </div>
  </div>
</div>
