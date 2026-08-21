<div class="page" style="padding-left: 12px; padding-right: 8px; box-sizing: border-box;">
  <div class="page-header">
    <h1 class="page-title"><?php echo htmlspecialchars(alang('units', 'Units'), ENT_QUOTES, 'UTF-8'); ?></h1>
  </div>
  <div class="page-content" style="margin-right: 0; margin-left: 0;">
    <div class="panel panel_box" style="margin:20px 0;">
      <div class="panel-body" style="background:#fff; border-radius:7px;">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin:10px;">
          <button type="button" class="btn btn-outline btn-primary" id="btn_add_unit">
            <i class="icon wb-plus" aria-hidden="true"></i> <?php echo htmlspecialchars(alang('add_unit', 'Add unit'), ENT_QUOTES, 'UTF-8'); ?>
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
                <th><?php echo htmlspecialchars(alang('name_th', 'Name (Thai)'), ENT_QUOTES, 'UTF-8'); ?></th>
                <th><?php echo htmlspecialchars(alang('name_en', 'Name (English)'), ENT_QUOTES, 'UTF-8'); ?></th>
                <th><?php echo htmlspecialchars(alang('sort_order', 'Sort'), ENT_QUOTES, 'UTF-8'); ?></th>
                <th><?php echo htmlspecialchars(alang('status', 'Status'), ENT_QUOTES, 'UTF-8'); ?></th>
                <th class="text-nowrap"><?php echo htmlspecialchars(alang('actions', 'Action'), ENT_QUOTES, 'UTF-8'); ?></th>
              </tr>
            </thead>
            <tbody id="unit-list">
              <tr><td colspan="5" class="text-center">…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="panel panel_box" id="div_manage_unit" style="margin-bottom:20px; display:none;">
      <div class="panel-body" style="background:#fff; border-radius:7px; padding:20px;">
        <h4 class="example-title" id="manage_unit_txt"><?php echo htmlspecialchars(alang('add_unit', 'Add unit'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <form id="unit_form" onsubmit="return false;">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="unit_name_th"><?php echo htmlspecialchars(alang('name_th', 'Name (Thai)'), ENT_QUOTES, 'UTF-8'); ?></label>
                <input type="text" class="form-control" id="unit_name_th" autocomplete="off">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="unit_name_en"><?php echo htmlspecialchars(alang('name_en', 'Name (English)'), ENT_QUOTES, 'UTF-8'); ?></label>
                <input type="text" class="form-control" id="unit_name_en" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="unit_sort"><?php echo htmlspecialchars(alang('sort_order', 'Sort order'), ENT_QUOTES, 'UTF-8'); ?></label>
                <input type="number" class="form-control" id="unit_sort" value="0">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label style="display:block; margin-bottom:8px;"><?php echo htmlspecialchars(alang('active', 'Active'), ENT_QUOTES, 'UTF-8'); ?></label>
                <label class="bny-switch">
                  <input type="checkbox" id="unit_status" value="1" checked>
                  <span class="bny-switch-slider"></span>
                </label>
              </div>
            </div>
          </div>
          <input type="hidden" id="id_en" value="">
          <input type="hidden" id="is_add" value="1">
          <div style="margin-top:16px;">
            <button type="button" class="btn btn-default" id="btn_cancel_unit"><?php echo htmlspecialchars(alang('cancel', 'Cancel'), ENT_QUOTES, 'UTF-8'); ?></button>
            <button type="button" class="btn btn-primary" id="btn_save_unit"><?php echo htmlspecialchars(alang('save', 'Save'), ENT_QUOTES, 'UTF-8'); ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
window.BNY_I18N = {
  no_units: <?php echo json_encode(alang('no_units', 'No units found')); ?>,
  active: <?php echo json_encode(alang('active', 'Active')); ?>,
  inactive: <?php echo json_encode(alang('inactive', 'Inactive')); ?>,
  add_unit: <?php echo json_encode(alang('add_unit', 'Add unit')); ?>,
  edit: <?php echo json_encode(alang('edit', 'Edit')); ?>,
  confirm_delete: <?php echo json_encode(alang('confirm_delete', 'Remove this item?')); ?>
};
</script>
