function escHtml(text) {
  if (text === null || text === undefined) return "";
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function tierLabel(level) {
  var n = parseInt(level, 10) || 0;
  if (n === 0) return "1 (mother)";
  if (n === 1) return "2 (child)";
  return "3 (grandchild)";
}

function slugifyTitle(text) {
  var s = String(text || "").trim().toLowerCase();
  s = s.replace(/\s+/g, "-");
  s = s.replace(/[^\u0E00-\u0E7Fa-z0-9\-]+/g, "");
  s = s.replace(/-+/g, "-").replace(/^-|-$/g, "");
  return s || "category";
}

function isValidSeoSlug(slug) {
  if (!slug) return true;
  return /^[\u0E00-\u0E7Fa-zA-Z0-9\-]+$/.test(slug);
}

var pendingThumbBlob = null;
var pendingThumbName = "";
var cropperInstance = null;
var cropObjectUrl = null;
var cropZoomSyncing = false;
var cachedCategoryList = [];
var clearThumbnailFlag = 0;
var summernoteReady = false;
var visibilityToggleBusy = false;

function setCropZoomUi(percent) {
  var p = Math.round(percent);
  if (isNaN(p)) p = 100;
  p = Math.max(5, Math.min(300, p));
  cropZoomSyncing = true;
  $("#cat_crop_zoom").val(p);
  $("#cat_crop_zoom_label").text(p + "%");
  cropZoomSyncing = false;
}

function getCropZoomPercent() {
  return parseInt($("#cat_crop_zoom").val(), 10) || 100;
}

function applyCropZoomTo(percent) {
  if (!cropperInstance) return;
  var p = Math.max(5, Math.min(300, Math.round(percent)));
  setCropZoomUi(p);
  try {
    $("#cat_crop_image").cropper("zoomTo", p / 100);
  } catch (e) {}
}

function syncCropZoomFromCropper() {
  if (!cropperInstance || cropZoomSyncing) return;
  try {
    var data = $("#cat_crop_image").cropper("getCanvasData");
    if (!data || !data.naturalWidth) return;
    var ratio = data.width / data.naturalWidth;
    setCropZoomUi(ratio * 100);
  } catch (e) {}
}

function resetThumbState() {
  pendingThumbBlob = null;
  pendingThumbName = "";
  clearThumbnailFlag = 0;
  $("#cat_thumb_file").val("");
  $("#thumbnail_old").val("");
  $("#clear_thumbnail").val("0");
  $("#cat_thumb_preview").attr("src", "");
  $("#cat_thumb_preview_wrap").hide();
  $("#btn_remove_thumb").hide();
  destroyCropper();
}

function setThumbPreview(url) {
  if (url) {
    $("#cat_thumb_preview").attr("src", url);
    $("#cat_thumb_preview_wrap").show();
    $("#btn_remove_thumb").show();
  } else {
    $("#cat_thumb_preview").attr("src", "");
    $("#cat_thumb_preview_wrap").hide();
    $("#btn_remove_thumb").hide();
  }
}

function destroyCropper() {
  if (cropperInstance) {
    try {
      $("#cat_crop_image").cropper("destroy");
    } catch (e) {}
    cropperInstance = null;
  }
  if (cropObjectUrl) {
    try {
      URL.revokeObjectURL(cropObjectUrl);
    } catch (e2) {}
    cropObjectUrl = null;
  }
  $("#cat_crop_image").attr("src", "");
  $("#btn_crop_mode").addClass("active");
  $("#btn_move_mode").removeClass("active");
  setCropZoomUi(100);
}

function destroyCropperKeepUrl() {
  if (cropperInstance) {
    try {
      $("#cat_crop_image").cropper("destroy");
    } catch (e) {}
    cropperInstance = null;
  }
}

function getDescendantIds(list, rootId) {
  var ids = {};
  ids[String(rootId)] = true;
  var changed = true;
  while (changed) {
    changed = false;
    (list || []).forEach(function (row) {
      var id = String(row.web_domain_category_id);
      var pid = String(row.parent_id || 0);
      if (!ids[id] && ids[pid]) {
        ids[id] = true;
        changed = true;
      }
    });
  }
  return ids;
}

function getSubtreeDepth(list, rootId) {
  var byParent = {};
  (list || []).forEach(function (row) {
    var pid = String(row.parent_id || 0);
    if (!byParent[pid]) byParent[pid] = [];
    byParent[pid].push(row);
  });
  function walk(id) {
    var kids = byParent[String(id)] || [];
    if (!kids.length) return 0;
    var max = 0;
    kids.forEach(function (k) {
      var d = 1 + walk(k.web_domain_category_id);
      if (d > max) max = d;
    });
    return max;
  }
  return walk(rootId);
}

/**
 * Build parent <select>: No parent + indented titles.
 * When editing, exclude self + descendants; also exclude parents that would push depth > 2.
 */
function buildParentOptions(selectedParentId, editingId) {
  var $sel = $("#cat_parent");
  $sel.empty();
  $sel.append('<option value="0">No parent</option>');

  var list = cachedCategoryList || [];
  var exclude = {};
  var subtreeDepth = 0;
  if (editingId) {
    exclude = getDescendantIds(list, editingId);
    subtreeDepth = getSubtreeDepth(list, editingId);
  }

  list.forEach(function (row) {
    var id = parseInt(row.web_domain_category_id, 10) || 0;
    var level = parseInt(row.level, 10) || 0;
    if (editingId && exclude[String(id)]) {
      return;
    }
    // Parent at level L => new node level = L+1; must keep (L+1 + subtreeDepth) <= 2
    var newLevel = level + 1;
    if (newLevel > 2) {
      return;
    }
    if (editingId && newLevel + subtreeDepth > 2) {
      return;
    }
    var pad = "";
    for (var i = 0; i < level; i++) {
      pad += "— ";
    }
    var opt = $("<option></option>")
      .attr("value", id)
      .text(pad + (row.Title || ""));
    $sel.append(opt);
  });

  var want = selectedParentId != null ? String(selectedParentId) : "0";
  if ($sel.find('option[value="' + want + '"]').length) {
    $sel.val(want);
  } else {
    $sel.val("0");
  }
  $("#parent_id").val($sel.val() || "0");
}

function setVisibilityChecked(on) {
  var el = document.getElementById("cat_is_visible");
  if (!el) return;
  el.checked = !!on;
}

function getVisibilityValue() {
  return $("#cat_is_visible").is(":checked") ? 1 : 0;
}

function isRowVisible(row) {
  if (row.is_visible === undefined || row.is_visible === null) return true;
  return !(String(row.is_visible) === "0" || row.is_visible === false || row.is_visible === 0);
}

function initSummernote() {
  var $el = $("#cat_des");
  if (!$el.length) {
    return;
  }
  if (typeof $.fn.summernote !== "function") {
    if (window.console && console.warn) {
      console.warn("Summernote (WYSIWYG) not loaded");
    }
    return;
  }
  if (summernoteReady) {
    return;
  }
  try {
    // Destroy any leftover note-editor wrappers first
    if ($el.next(".note-editor").length) {
      try {
        $el.summernote("destroy");
      } catch (e0) {}
    }
    $el.summernote({
      height: 220,
      placeholder: "Category description…",
      toolbar: [
        ["style", ["style"]],
        ["font", ["bold", "italic", "underline", "clear"]],
        ["fontname", ["fontname"]],
        ["color", ["color"]],
        ["para", ["ul", "ol", "paragraph"]],
        ["insert", ["link", "picture"]],
        ["view", ["codeview", "help"]]
      ]
    });
    summernoteReady = true;
  } catch (e) {
    summernoteReady = false;
    if (window.console && console.error) console.error(e);
  }
}

function destroySummernote() {
  var $el = $("#cat_des");
  if (summernoteReady && $el.length && typeof $el.summernote === "function") {
    try {
      $el.summernote("destroy");
    } catch (e) {}
  }
  summernoteReady = false;
  $el.val("");
}

function getDescriptionHtml() {
  var $el = $("#cat_des");
  if (summernoteReady && typeof $el.summernote === "function") {
    try {
      return $el.summernote("code");
    } catch (e) {}
  }
  return $el.val() || "";
}

function setDescriptionHtml(html) {
  var $el = $("#cat_des");
  if (summernoteReady && typeof $el.summernote === "function") {
    try {
      $el.summernote("code", html || "");
      return;
    } catch (e) {}
  }
  $el.val(html || "");
}

function updateSeoPreview() {
  var domainText = $.trim($("#domain_sel option:selected").text()) || "example.com";
  domainText = domainText.replace(/^https?:\/\//i, "").replace(/\/+$/, "");
  var isAdd = String($("#is_add").val()) === "1";
  var catId = $("#cat_id").val() || "";
  var name = $.trim($("#cat_name").val()) || "Category";
  var slug = $.trim($("#seo_slug").val());
  var pathSlug = slug || slugifyTitle(name);
  var url;
  if (isAdd || !catId) {
    url = "https://" + domainText + "/category/{id}/" + pathSlug;
  } else {
    url = "https://" + domainText + "/category/" + catId + "/" + pathSlug;
  }
  var title = $.trim($("#seo_title").val()) || name;
  var desc = $.trim($("#seo_description").val()) || "";
  if (desc.length > 160) {
    desc = desc.substring(0, 157) + "...";
  }
  $("#seo_prev_url").text(url);
  $("#seo_prev_title").text(title);
  $("#seo_prev_desc").text(desc || "Category description snippet");
  $("#seo_title_count").text(String($.trim($("#seo_title").val()).length));
  $("#seo_desc_count").text(String($.trim($("#seo_description").val()).length));
}

function renderCategoryRows(list_data) {
  var list = document.getElementById("cate-list");
  list.innerHTML = "";
  if (!list_data || list_data.length === 0) {
    list.innerHTML = '<tr><td colspan="5" class="text-center">No categories for this domain</td></tr>';
    return;
  }

  list_data.forEach(function (row) {
    var level = parseInt(row.level, 10) || 0;
    var indentPx = level * 28;
    var treeMark = "";
    if (level === 1) {
      treeMark = '<span class="cat-tree-mark" aria-hidden="true">└─</span> ';
    } else if (level >= 2) {
      treeMark = '<span class="cat-tree-mark" aria-hidden="true">└─</span> ';
      indentPx = 28 + (level - 1) * 28;
    }
    var canAddChild = level < 2;
    var thumbHtml = "—";
    if (row.thumbnail_url) {
      thumbHtml =
        '<img src="' +
        escHtml(row.thumbnail_url) +
        '" alt="" style="width:40px;height:40px;object-fit:cover;">';
    }
    var visibleOn = isRowVisible(row);
    var tr = document.createElement("tr");
    tr.className = "cat-row cat-level-" + level;
    tr.innerHTML =
      '<td class="cat-thumb-cell" style="padding-left:' +
      (level > 0 ? indentPx + 8 : 8) +
      'px;">' +
      thumbHtml +
      "</td>" +
      '<td class="cat-title-cell" style="padding-left:' +
      (8 + indentPx) +
      'px;">' +
      treeMark +
      '<span class="cat-title-text">' +
      escHtml(row.Title) +
      "</span>" +
      "</td>" +
      "<td>" +
      tierLabel(level) +
      "</td>" +
      '<td class="text-center cat-display-cell">' +
      '<label class="bny-switch" title="Display on storefront">' +
      '<input type="checkbox" class="js-cat-visible" value="1"' +
      (visibleOn ? " checked" : "") +
      ' data-id-en="' +
      escHtml(row.id_en) +
      '">' +
      '<span class="bny-switch-slider"></span>' +
      "</label>" +
      "</td>" +
      '<td class="text-nowrap">' +
      (canAddChild
        ? '<button type="button" class="btn btn-xs btn-success js-cat-add" title="Add child category" data-id="' +
          escHtml(row.web_domain_category_id) +
          '" data-title="' +
          escHtml(row.Title) +
          '" data-level="' +
          level +
          '"><i class="icon wb-plus"></i></button> '
        : "") +
      '<button type="button" class="btn btn-xs btn-primary js-cat-edit" data-id-en="' +
      escHtml(row.id_en) +
      '"><i class="icon wb-pencil"></i></button> ' +
      '<button type="button" class="btn btn-xs btn-danger js-cat-del" data-id-en="' +
      escHtml(row.id_en) +
      '"><i class="icon wb-trash"></i></button>' +
      "</td>";
    list.appendChild(tr);
  });
}

function loadCategories(done) {
  var domainEn = $("#domain_sel").val();
  if (!domainEn) {
    cachedCategoryList = [];
    renderCategoryRows([]);
    if (typeof done === "function") done();
    return;
  }
  $("#spinner-div").show();
  $.ajax({
    type: "POST",
    url: hostname_site + "/webs/products/category/list_ajax",
    data: { web_domain_id_en: domainEn },
    dataType: "json",
    success: function (res) {
      cachedCategoryList = (res && res.list_data) || [];
      renderCategoryRows(cachedCategoryList);
      if (typeof done === "function") done();
    },
    complete: function () {
      $("#spinner-div").hide();
    }
  });
}

function hideForm() {
  $("#div_manage_cat").hide();
  destroySummernote();
  $("#cat_name").val("");
  $("#seo_title").val("");
  $("#seo_description").val("");
  $("#seo_keywords").val("");
  $("#seo_slug").val("");
  $("#parent_id").val("0");
  $("#cat_parent").val("0");
  $("#id_en").val("");
  $("#cat_id").val("");
  $("#is_add").val("1");
  setVisibilityChecked(true);
  resetThumbState();
  updateSeoPreview();
}

function showFormPanel(descriptionHtml) {
  // Show first — WYSIWYG must init on a visible container
  $("#div_manage_cat").show();
  var panel = document.getElementById("div_manage_cat");
  if (panel && panel.scrollIntoView) {
    panel.scrollIntoView({ behavior: "smooth", block: "start" });
  }
  var html = descriptionHtml != null ? descriptionHtml : "";
  setTimeout(function () {
    initSummernote();
    setDescriptionHtml(html);
    updateSeoPreview();
  }, 50);
}

function openCropModal(file) {
  if (!file || !/^image\/\w+$/.test(file.type)) {
    alert("Please choose an image file");
    return;
  }
  destroyCropper();
  cropObjectUrl = URL.createObjectURL(file);
  $("#cat_crop_image").attr("src", cropObjectUrl);
  $("#cat_crop_modal").modal("show");
}

function initCropperWhenShown() {
  var $img = $("#cat_crop_image");
  destroyCropperKeepUrl();
  setCropZoomUi(100);
  $img.cropper({
    // 0 = allow zoom-out so crop box can include empty (transparent) areas
    viewMode: 0,
    aspectRatio: 1,
    dragMode: "crop",
    autoCropArea: 1,
    responsive: true,
    background: true,
    checkOrientation: true,
    zoomOnWheel: true,
    wheelZoomRatio: 0.08,
    ready: function () {
      syncCropZoomFromCropper();
    },
    zoom: function () {
      syncCropZoomFromCropper();
    }
  });
  cropperInstance = true;
  $("#btn_crop_mode").addClass("active");
  $("#btn_move_mode").removeClass("active");
}

$(document).ready(function () {
  // Ensure switchery on visibility (global template usually auto-inits data-plugin)
  setTimeout(function () {
    setVisibilityChecked(true);
  }, 50);

  loadCategories();

  $("#domain_sel").on("change", function () {
    hideForm();
    loadCategories();
  });

  $("#btn_add_root").on("click", function () {
    if (!$("#domain_sel").val()) {
      alert("Select a domain first");
      return;
    }
    try {
      resetThumbState();
      $("#manage_cat_txt").text("Add category");
      $("#cat_name").val("");
      $("#seo_title").val("");
      $("#seo_description").val("");
      $("#seo_keywords").val("");
      $("#seo_slug").val("");
      $("#id_en").val("");
      $("#cat_id").val("");
      $("#is_add").val("1");
      setVisibilityChecked(true);
      buildParentOptions(0, null);
      showFormPanel("");
    } catch (err) {
      $("#div_manage_cat").show();
      if (window.console && console.error) console.error(err);
      alert("Could not open add form. Please refresh and try again.");
    }
  });

  $("#btn_cancel_cat").on("click", hideForm);

  $(document).on("click", ".js-cat-add", function () {
    if (!$("#domain_sel").val()) {
      alert("Select a domain first");
      return;
    }
    var parentId = $(this).data("id");
    try {
      resetThumbState();
      $("#manage_cat_txt").text("Add child category");
      $("#cat_name").val("");
      $("#seo_title").val("");
      $("#seo_description").val("");
      $("#seo_keywords").val("");
      $("#seo_slug").val("");
      $("#id_en").val("");
      $("#cat_id").val("");
      $("#is_add").val("1");
      setVisibilityChecked(true);
      buildParentOptions(parentId, null);
      showFormPanel("");
    } catch (err) {
      $("#div_manage_cat").show();
      if (window.console && console.error) console.error(err);
      alert("Could not open add form. Please refresh and try again.");
    }
  });

  $(document).on("click", ".js-cat-edit", function () {
    var idEn = $(this).data("id-en");
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/category/get_ajax",
      data: { id_en: idEn },
      dataType: "json",
      success: function (res) {
        var cat = (res && res.cat_data) || {};
        resetThumbState();
        $("#manage_cat_txt").text("Edit category");
        $("#cat_name").val(cat.Title || "");
        $("#id_en").val(cat.id_en || idEn);
        $("#cat_id").val(cat.web_domain_category_id || "");
        $("#is_add").val("2");
        $("#seo_title").val(cat.seo_title || "");
        $("#seo_description").val(cat.seo_description || "");
        $("#seo_keywords").val(cat.seo_keywords || "");
        $("#seo_slug").val(cat.seo_slug || "");
        $("#thumbnail_old").val(cat.thumbnail || "");
        setThumbPreview(cat.thumbnail_url || "");
        var vis = cat.is_visible === undefined || cat.is_visible === null || String(cat.is_visible) === "1" || cat.is_visible === true;
        setVisibilityChecked(vis);
        buildParentOptions(cat.parent_id || 0, cat.web_domain_category_id);
        showFormPanel(cat.Description || "");
      },
      complete: function () {
        $("#spinner-div").hide();
      }
    });
  });

  $(document).on("click", ".js-cat-del", function () {
    var idEn = $(this).data("id-en");
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/category/del_ajax",
      data: { id_en: idEn },
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideForm();
          loadCategories();
        } else {
          $("#spinner-div").hide();
          alert((res && res.Message) || "Delete failed");
        }
      },
      error: function () {
        $("#spinner-div").hide();
        alert("Delete failed");
      }
    });
  });

  $(document).on("change", ".js-cat-visible", function () {
    if (visibilityToggleBusy) return;
    var el = this;
    var idEn = $(el).data("id-en");
    var val = el.checked ? 1 : 0;
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/category/visibility_ajax",
      data: { id_en: idEn, is_visible: val },
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          (cachedCategoryList || []).forEach(function (row) {
            if (String(row.id_en) === String(idEn)) {
              row.is_visible = val;
            }
          });
        } else {
          visibilityToggleBusy = true;
          el.checked = !val;
          visibilityToggleBusy = false;
          alert((res && res.Message) || "Could not update display");
        }
      },
      error: function () {
        visibilityToggleBusy = true;
        el.checked = !val;
        visibilityToggleBusy = false;
        alert("Could not update display");
      }
    });
  });

  $("#cat_parent").on("change", function () {
    $("#parent_id").val($(this).val() || "0");
  });

  $("#btn_select_thumb").on("click", function () {
    $("#cat_thumb_file").trigger("click");
  });

  $("#btn_remove_thumb").on("click", function () {
    pendingThumbBlob = null;
    pendingThumbName = "";
    clearThumbnailFlag = 1;
    $("#clear_thumbnail").val("1");
    $("#cat_thumb_file").val("");
    setThumbPreview("");
  });

  $("#cat_thumb_file").on("change", function () {
    var files = this.files;
    if (!files || !files.length) return;
    clearThumbnailFlag = 0;
    $("#clear_thumbnail").val("0");
    openCropModal(files[0]);
    $(this).val("");
  });

  $("#cat_crop_modal").on("shown.bs.modal", function () {
    if ($("#cat_crop_image").attr("src")) {
      initCropperWhenShown();
    }
  });

  $("#cat_crop_modal").on("hidden.bs.modal", function () {
    destroyCropperKeepUrl();
    if (cropObjectUrl) {
      try {
        URL.revokeObjectURL(cropObjectUrl);
      } catch (e) {}
      cropObjectUrl = null;
    }
    $("#cat_crop_image").attr("src", "");
  });

  $("#btn_crop_mode").on("click", function () {
    $("#cat_crop_image").cropper("setDragMode", "crop");
    $(this).addClass("active");
    $("#btn_move_mode").removeClass("active");
  });

  $("#btn_move_mode").on("click", function () {
    $("#cat_crop_image").cropper("setDragMode", "move");
    $(this).addClass("active");
    $("#btn_crop_mode").removeClass("active");
  });

  $("#cat_crop_zoom").on("input change", function () {
    if (cropZoomSyncing || !cropperInstance) return;
    applyCropZoomTo($(this).val());
  });

  $("#btn_zoom_out").on("click", function () {
    applyCropZoomTo(getCropZoomPercent() - 10);
  });

  $("#btn_zoom_in").on("click", function () {
    applyCropZoomTo(getCropZoomPercent() + 10);
  });

  $("#btn_zoom_reset").on("click", function () {
    applyCropZoomTo(100);
  });

  $("#btn_crop_apply").on("click", function () {
    var canvas;
    try {
      // No fillColor / transparent → empty areas stay transparent (PNG)
      canvas = $("#cat_crop_image").cropper("getCroppedCanvas", {
        width: 400,
        height: 400,
        fillColor: "transparent",
        imageSmoothingEnabled: true,
        imageSmoothingQuality: "high"
      });
    } catch (err) {
      alert("Crop failed");
      return;
    }
    if (!canvas) {
      alert("Crop failed");
      return;
    }
    canvas.toBlob(
      function (blob) {
        if (!blob) {
          alert("Could not create image");
          return;
        }
        pendingThumbBlob = blob;
        pendingThumbName = "category_thumb_" + Date.now() + ".png";
        clearThumbnailFlag = 0;
        $("#clear_thumbnail").val("0");
        setThumbPreview(URL.createObjectURL(blob));
        $("#cat_crop_modal").modal("hide");
      },
      "image/png"
    );
  });

  $("#cat_name, #seo_title, #seo_description, #seo_slug").on("input", updateSeoPreview);

  $("#btn_save_cat").on("click", function () {
    var title = $.trim($("#cat_name").val());
    if (!title) {
      alert("Please enter category name");
      return;
    }
    var slug = $.trim($("#seo_slug").val());
    if (slug && !isValidSeoSlug(slug)) {
      alert("URL slug: use Thai/English letters, numbers, and dashes only");
      return;
    }

    var fd = new FormData();
    fd.append("is_add", $("#is_add").val());
    fd.append("Title", title);
    fd.append("Description", getDescriptionHtml());
    fd.append("parent_id", $("#cat_parent").val() || "0");
    fd.append("web_domain_id_en", $("#domain_sel").val());
    fd.append("id_en", $("#id_en").val());
    fd.append("is_visible", getVisibilityValue());
    fd.append("seo_title", $.trim($("#seo_title").val()));
    fd.append("seo_description", $.trim($("#seo_description").val()));
    fd.append("seo_keywords", $.trim($("#seo_keywords").val()));
    fd.append("seo_slug", slug);
    fd.append("thumbnail_old", $("#thumbnail_old").val());
    fd.append("clear_thumbnail", clearThumbnailFlag ? "1" : "0");
    if (pendingThumbBlob) {
      fd.append("thumbnail", pendingThumbBlob, pendingThumbName || "thumbnail.png");
    }

    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/category/save_ajax",
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideForm();
          loadCategories();
        } else {
          $("#spinner-div").hide();
          alert((res && res.Message) || "Save failed");
        }
      },
      error: function () {
        $("#spinner-div").hide();
        alert("Save failed");
      }
    });
  });
});
