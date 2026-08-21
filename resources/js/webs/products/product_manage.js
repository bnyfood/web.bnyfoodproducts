function escHtml(text) {
  if (text === null || text === undefined) return "";
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function slugifyTitle(text) {
  var s = String(text || "").trim().toLowerCase();
  s = s.replace(/\s+/g, "-");
  s = s.replace(/[^\u0E00-\u0E7Fa-z0-9\-]+/g, "");
  s = s.replace(/-+/g, "-").replace(/^-|-$/g, "");
  return s || "product";
}

function isValidSeoSlug(slug) {
  if (!slug) return true;
  return /^[\u0E00-\u0E7Fa-zA-Z0-9\-]+$/.test(slug);
}

var ENTRY_TYPE_COOKIE = "bny_wdp_entry_type";
var ENTRY_TYPE_DEFAULT = "bom";
var ENTRY_TYPES = { regular: 1, bom: 1, variant: 1, hybrid: 1 };

function normalizeEntryType(t) {
  var v = String(t || "").toLowerCase();
  if (v === "combined") v = "hybrid";
  if (ENTRY_TYPES[v]) return v;
  return ENTRY_TYPE_DEFAULT;
}

function entryTypeLabel(t) {
  var v = normalizeEntryType(t);
  if (v === "regular") return "Regular";
  if (v === "variant") return "Variant";
  if (v === "hybrid") return "Hybrid";
  return "BOM";
}

function getCookie(name) {
  var match = document.cookie.match(
    new RegExp("(?:^|; )" + name.replace(/([.$?*|{}()[\]\\/+^])/g, "\\$1") + "=([^;]*)")
  );
  return match ? decodeURIComponent(match[1]) : "";
}

function setCookie(name, value, days) {
  var maxAge = Math.max(1, (parseInt(days, 10) || 365) * 24 * 60 * 60);
  document.cookie =
    name +
    "=" +
    encodeURIComponent(value) +
    "; path=/; max-age=" +
    maxAge +
    "; SameSite=Lax";
}

function getPreferredEntryType() {
  return normalizeEntryType(getCookie(ENTRY_TYPE_COOKIE) || ENTRY_TYPE_DEFAULT);
}

function setPreferredEntryType(type) {
  var v = normalizeEntryType(type);
  setCookie(ENTRY_TYPE_COOKIE, v, 365);
  return v;
}

function setEntryModeUi(type) {
  var v = normalizeEntryType(type);
  $("#entry_mode_group .bny-step-switch-opt").removeClass("is-active");
  $("#entry_mode_group input[name='entry_mode']").each(function () {
    var on = normalizeEntryType($(this).val()) === v;
    this.checked = on;
    if (on) {
      $(this).closest(".bny-step-switch-opt").addClass("is-active");
    }
  });
  positionEntryThumb(v, false);
  return v;
}

function getEntryModeValues() {
  var vals = [];
  $("#entry_mode_group input[name='entry_mode']").each(function () {
    vals.push(normalizeEntryType($(this).val()));
  });
  return vals;
}

function getEntryStepCenters() {
  var $group = $("#entry_mode_group");
  if (!$group.length) return [];
  var groupLeft = $group[0].getBoundingClientRect().left;
  var centers = [];
  $group.find(".bny-step-switch-opt").each(function () {
    var rect = this.getBoundingClientRect();
    centers.push(rect.left + rect.width / 2 - groupLeft);
  });
  return centers;
}

function positionEntryThumb(type, animate) {
  var $thumb = $("#entry_mode_thumb");
  var $group = $("#entry_mode_group");
  if (!$thumb.length || !$group.length) return;
  var vals = getEntryModeValues();
  var centers = getEntryStepCenters();
  if (!vals.length || !centers.length) return;
  var idx = vals.indexOf(normalizeEntryType(type));
  if (idx < 0) idx = Math.max(0, vals.indexOf(ENTRY_TYPE_DEFAULT));
  if (idx < 0 || idx >= centers.length) idx = 0;
  if (animate === false) {
    $group.addClass("is-dragging");
  }
  $thumb.css("left", centers[idx] + "px");
  if (animate === false) {
    void $thumb[0].offsetWidth;
    $group.removeClass("is-dragging");
  }
}

function applyEntryModeChoice(type) {
  var v = setPreferredEntryType(type);
  setEntryModeUi(v);
  if (String($("#is_add").val()) === "1" && $("#div_manage_product").is(":visible")) {
    setFormEntryType(v);
  }
  return v;
}

function nearestEntryIndex(clientX) {
  var $group = $("#entry_mode_group");
  if (!$group.length) return 0;
  var centers = getEntryStepCenters();
  var groupLeft = $group[0].getBoundingClientRect().left;
  var x = clientX - groupLeft;
  var best = 0;
  var bestDist = Infinity;
  for (var i = 0; i < centers.length; i++) {
    var d = Math.abs(centers[i] - x);
    if (d < bestDist) {
      bestDist = d;
      best = i;
    }
  }
  return best;
}

function initEntryModeDrag() {
  var $group = $("#entry_mode_group");
  var $thumb = $("#entry_mode_thumb");
  if (!$group.length || !$thumb.length) return;

  var dragging = false;

  function setThumbFromClientX(clientX, snap) {
    var centers = getEntryStepCenters();
    if (!centers.length) return 0;
    var groupRect = $group[0].getBoundingClientRect();
    var x = clientX - groupRect.left;
    var min = centers[0];
    var max = centers[centers.length - 1];
    x = Math.max(min, Math.min(max, x));
    var idx = nearestEntryIndex(clientX);
    if (snap) {
      x = centers[idx];
    }
    $thumb.css("left", x + "px");
    return idx;
  }

  function onMove(e) {
    if (!dragging) return;
    var pt = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
    setThumbFromClientX(pt.clientX, false);
    e.preventDefault();
  }

  function onEnd(e) {
    if (!dragging) return;
    dragging = false;
    $group.removeClass("is-dragging");
    var pt = e.originalEvent && e.originalEvent.changedTouches ? e.originalEvent.changedTouches[0] : e;
    var idx = setThumbFromClientX(pt.clientX, true);
    var vals = getEntryModeValues();
    if (vals[idx]) {
      applyEntryModeChoice(vals[idx]);
    }
    $(document).off(".entryModeDrag");
  }

  function startDrag(e) {
    var pt = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
    dragging = true;
    $group.addClass("is-dragging");
    setThumbFromClientX(pt.clientX, false);
    $(document).on("mousemove.entryModeDrag touchmove.entryModeDrag", onMove);
    $(document).on("mouseup.entryModeDrag touchend.entryModeDrag touchcancel.entryModeDrag", onEnd);
    e.preventDefault();
    e.stopPropagation();
  }

  $thumb.on("mousedown touchstart", startDrag);

  $(window).on("resize", function () {
    positionEntryThumb(
      $("input[name='entry_mode']:checked").val() || getPreferredEntryType(),
      false
    );
  });

  // place thumb after layout
  setTimeout(function () {
    positionEntryThumb(getPreferredEntryType(), false);
  }, 0);
}

function setFormEntryType(type) {
  var v = normalizeEntryType(type);
  $("#prod_entry_type").val(v);
  $("#prod_entry_type_label").text(entryTypeLabel(v));
  return v;
}

function fmtMoney(v) {
  if (v === null || v === undefined || v === "") return "—";
  var n = parseFloat(v);
  if (isNaN(n)) return "—";
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

var pendingThumbBlob = null;
var pendingThumbName = "";
var cropperInstance = null;
var cropObjectUrl = null;
var cropZoomSyncing = false;
var clearThumbnailFlag = 0;
var summernoteReady = false;
var visibilityToggleBusy = false;
var cachedCategoryList = [];
var searchTimer = null;

function setCropZoomUi(percent) {
  var p = Math.round(percent);
  if (isNaN(p)) p = 100;
  p = Math.max(5, Math.min(300, p));
  cropZoomSyncing = true;
  $("#prod_crop_zoom").val(p);
  $("#prod_crop_zoom_label").text(p + "%");
  cropZoomSyncing = false;
}

function getCropZoomPercent() {
  return parseInt($("#prod_crop_zoom").val(), 10) || 100;
}

function applyCropZoomTo(percent) {
  if (!cropperInstance) return;
  var p = Math.max(5, Math.min(300, Math.round(percent)));
  setCropZoomUi(p);
  try {
    $("#prod_crop_image").cropper("zoomTo", p / 100);
  } catch (e) {}
}

function syncCropZoomFromCropper() {
  if (!cropperInstance || cropZoomSyncing) return;
  try {
    var data = $("#prod_crop_image").cropper("getCanvasData");
    if (!data || !data.naturalWidth) return;
    setCropZoomUi((data.width / data.naturalWidth) * 100);
  } catch (e) {}
}

function resetThumbState() {
  pendingThumbBlob = null;
  pendingThumbName = "";
  clearThumbnailFlag = 0;
  $("#prod_thumb_file").val("");
  $("#thumbnail_old").val("");
  $("#clear_thumbnail").val("0");
  $("#prod_thumb_preview").attr("src", "");
  $("#prod_thumb_preview_wrap").hide();
  $("#btn_remove_thumb").hide();
  destroyCropper();
}

function setThumbPreview(url) {
  if (url) {
    $("#prod_thumb_preview").attr("src", url);
    $("#prod_thumb_preview_wrap").show();
    $("#btn_remove_thumb").show();
  } else {
    $("#prod_thumb_preview").attr("src", "");
    $("#prod_thumb_preview_wrap").hide();
    $("#btn_remove_thumb").hide();
  }
}

function destroyCropper() {
  if (cropperInstance) {
    try {
      $("#prod_crop_image").cropper("destroy");
    } catch (e) {}
    cropperInstance = null;
  }
  if (cropObjectUrl) {
    try {
      URL.revokeObjectURL(cropObjectUrl);
    } catch (e2) {}
    cropObjectUrl = null;
  }
  $("#prod_crop_image").attr("src", "");
  $("#btn_crop_mode").addClass("active");
  $("#btn_move_mode").removeClass("active");
  setCropZoomUi(100);
}

function destroyCropperKeepUrl() {
  if (cropperInstance) {
    try {
      $("#prod_crop_image").cropper("destroy");
    } catch (e) {}
    cropperInstance = null;
  }
}

function initSummernote() {
  if (summernoteReady) return;
  var opts = {
    height: 160,
    toolbar: [
      ["style", ["style"]],
      ["font", ["bold", "italic", "underline", "clear"]],
      ["para", ["ul", "ol", "paragraph"]],
      ["insert", ["link"]],
      ["view", ["codeview"]]
    ]
  };
  $("#prod_des_en").summernote(opts);
  $("#prod_des_th").summernote(opts);
  summernoteReady = true;
}

function destroySummernote() {
  if (!summernoteReady) return;
  try {
    $("#prod_des_en").summernote("destroy");
    $("#prod_des_th").summernote("destroy");
  } catch (e) {}
  summernoteReady = false;
  $("#prod_des_en, #prod_des_th").val("");
}

function setDescriptionHtml(enHtml, thHtml) {
  if (summernoteReady) {
    $("#prod_des_en").summernote("code", enHtml || "");
    $("#prod_des_th").summernote("code", thHtml || "");
  } else {
    $("#prod_des_en").val(enHtml || "");
    $("#prod_des_th").val(thHtml || "");
  }
}

function getDescriptionHtml(lang) {
  var id = lang === "th" ? "#prod_des_th" : "#prod_des_en";
  if (summernoteReady) {
    return $(id).summernote("code");
  }
  return $(id).val() || "";
}

function fillFieldTranslated(selector, text, force) {
  var $el = $(selector);
  var cur = $.trim($el.val() || "");
  if (!force && cur !== "") {
    if (!confirm("Overwrite existing text?")) return false;
  }
  $el.val(text);
  return true;
}

function translateText(text, source, target, format, done) {
  $.ajax({
    type: "POST",
    url: hostname_site + "/webs/products/product/translate_ajax",
    data: {
      text: text,
      source: source,
      target: target,
      format: format || "text"
    },
    dataType: "json",
    success: function (res) {
      if (res && res.Status === "Success") {
        done(null, res.translated || "");
      } else {
        done((res && res.Message) || "Translate failed");
      }
    },
    error: function () {
      done("Translate failed");
    }
  });
}

function categoryOptionLabel(row) {
  var level = parseInt(row.level, 10) || 0;
  var pad = "";
  for (var i = 0; i < level; i++) pad += "— ";
  return pad + (row.Title || "");
}

function fillCategorySelects(list) {
  cachedCategoryList = list || [];
  var filterVal = $("#category_filter").val() || "0";
  var formVal = $("#prod_category").val() || "0";

  var $filter = $("#category_filter");
  var $form = $("#prod_category");
  $filter.empty().append('<option value="0">All categories</option>');
  $form.empty().append('<option value="0">— None —</option>');

  (cachedCategoryList || []).forEach(function (row) {
    var id = String(row.web_domain_category_id);
    var label = categoryOptionLabel(row);
    $filter.append(
      $("<option></option>").attr("value", id).text(label)
    );
    $form.append(
      $("<option></option>").attr("value", id).text(label)
    );
  });

  $filter.val(filterVal);
  if ($filter.val() !== filterVal) $filter.val("0");
  $form.val(formVal);
  if ($form.val() !== formVal) $form.val("0");
}

function loadCategories(done) {
  var domainEn = $("#domain_sel").val();
  if (!domainEn) {
    fillCategorySelects([]);
    if (typeof done === "function") done();
    return;
  }
  $.ajax({
    type: "POST",
    url: hostname_site + "/webs/products/category/list_ajax",
    data: { web_domain_id_en: domainEn },
    dataType: "json",
    success: function (res) {
      fillCategorySelects((res && res.list_data) || []);
      if (typeof done === "function") done();
    },
    error: function () {
      fillCategorySelects([]);
      if (typeof done === "function") done();
    }
  });
}

function fillUnitSelect(list, selectedId) {
  var $sel = $("#prod_unit");
  var cur = selectedId != null ? String(selectedId) : String($sel.val() || "0");
  $sel.empty().append('<option value="0">—</option>');
  (list || []).forEach(function (row) {
    var id = String(row.web_shop_unit_id);
    var label = row.display_name || row.name_en || row.name_th || id;
    $sel.append($("<option></option>").attr("value", id).text(label));
  });
  $sel.val(cur);
  if ($sel.val() !== cur) $sel.val("0");
}

function loadShopUnits(done) {
  $.ajax({
    type: "POST",
    url: hostname_site + "/webs/products/product/units_ajax",
    dataType: "json",
    success: function (res) {
      fillUnitSelect((res && res.list_data) || [], null);
      if (typeof done === "function") done();
    },
    error: function () {
      fillUnitSelect([], null);
      if (typeof done === "function") done();
    }
  });
}

function updateSeoPreview() {
  var domainText = $.trim($("#domain_sel option:selected").text()) || "example.com";
  domainText = domainText.replace(/^https?:\/\//i, "").replace(/\/+$/, "");
  var name =
    $.trim($("#prod_title_en").val()) ||
    $.trim($("#prod_title_th").val()) ||
    "Product";
  var slug =
    $.trim($("#seo_slug_en").val()) ||
    $.trim($("#seo_slug_th").val()) ||
    "";
  var pathSlug = slug || slugifyTitle(name);
  var url = "https://" + domainText + "/product/" + pathSlug;
  var title =
    $.trim($("#seo_title_en").val()) ||
    $.trim($("#seo_title_th").val()) ||
    name;
  var desc =
    $.trim($("#seo_description_en").val()) ||
    $.trim($("#seo_description_th").val()) ||
    "";
  if (desc.length > 160) desc = desc.substring(0, 157) + "...";
  $("#seo_prev_url").text(url);
  $("#seo_prev_title").text(title);
  $("#seo_prev_desc").text(desc || "Product description snippet");
}

function updatePager(page, totalPages, total) {
  $("#page").val(page);
  $("#product-pager-info").text(
    "Showing page " + page + " / " + totalPages + " (" + total + " total)"
  );
  $("#btn_prev_page").prop("disabled", page <= 1);
  $("#btn_next_page").prop("disabled", page >= totalPages);
}

function renderProductRows(list_data) {
  var list = document.getElementById("product-list");
  list.innerHTML = "";
  if (!list_data || list_data.length === 0) {
    list.innerHTML = '<tr><td colspan="8" class="text-center">No products found</td></tr>';
    return;
  }

  list_data.forEach(function (row) {
    var thumbHtml = "—";
    if (row.thumbnail_url) {
      thumbHtml =
        '<img src="' +
        escHtml(row.thumbnail_url) +
        '" alt="" style="width:40px;height:40px;object-fit:cover;">';
    }
    var visibleOn =
      row.is_visible === undefined ||
      row.is_visible === null ||
      String(row.is_visible) === "1" ||
      row.is_visible === true;

    var tr = document.createElement("tr");
    tr.innerHTML =
      "<td>" +
      thumbHtml +
      "</td>" +
      "<td>" +
      escHtml(row.display_title || row.Title) +
      "</td>" +
      "<td>" +
      escHtml(row.Sku || "—") +
      "</td>" +
      "<td>" +
      escHtml(row.category_title || "—") +
      "</td>" +
      "<td>" +
      fmtMoney(row.Price) +
      "</td>" +
      "<td>" +
      escHtml(entryTypeLabel(row.entry_type)) +
      "</td>" +
      '<td class="text-center">' +
      '<label class="bny-switch" title="Display on storefront">' +
      '<input type="checkbox" class="js-prod-visible" value="1"' +
      (visibleOn ? " checked" : "") +
      ' data-id-en="' +
      escHtml(row.id_en) +
      '">' +
      '<span class="bny-switch-slider"></span>' +
      "</label>" +
      "</td>" +
      '<td class="text-nowrap">' +
      '<button type="button" class="btn btn-xs btn-primary js-prod-edit" data-id-en="' +
      escHtml(row.id_en) +
      '"><i class="icon wb-pencil"></i></button> ' +
      '<button type="button" class="btn btn-xs btn-danger js-prod-del" data-id-en="' +
      escHtml(row.id_en) +
      '"><i class="icon wb-trash"></i></button>' +
      "</td>";
    list.appendChild(tr);
  });
}

function loadProducts(page) {
  var domainEn = $("#domain_sel").val();
  page = parseInt(page, 10) || 1;
  if (!domainEn) {
    renderProductRows([]);
    updatePager(1, 1, 0);
    return;
  }

  var per_page = parseInt($("#per_page").val(), 10) || 20;
  $("#spinner-div").show();
  $.ajax({
    type: "POST",
    url: hostname_site + "/webs/products/product/list_ajax",
    data: {
      web_domain_id_en: domainEn,
      web_domain_category_id: $("#category_filter").val() || 0,
      q: $.trim($("#product_search").val()),
      page: page,
      per_page: per_page
    },
    dataType: "json",
    success: function (res) {
      renderProductRows((res && res.list_data) || []);
      updatePager(
        (res && res.page) || page,
        (res && res.total_pages) || 1,
        (res && res.total) || 0
      );
    },
    complete: function () {
      $("#spinner-div").hide();
    }
  });
}

function hideForm() {
  $("#div_manage_product").hide();
  destroySummernote();
  $("#prod_title_en, #prod_title_th").val("");
  $("#prod_sku, #prod_barcode").val("");
  $("#prod_unit").val("0");
  $("#prod_category").val("0");
  setFormEntryType(getPreferredEntryType());
  $("#prod_price").val("");
  $("#prod_cost").val("");
  $("#prod_sort").val("0");
  $("#prod_width, #prod_length, #prod_height, #prod_weight").val("");
  $("#prod_load_x, #prod_load_y, #prod_load_z").val("");
  $("#seo_title_en, #seo_title_th, #seo_description_en, #seo_description_th").val("");
  $("#seo_keywords_en, #seo_keywords_th, #seo_slug_en, #seo_slug_th").val("");
  $("#id_en").val("");
  $("#is_add").val("1");
  $("#prod_is_visible").prop("checked", true);
  $("#prod_is_atomic, #prod_is_salable").prop("checked", false);
  resetThumbState();
  updateSeoPreview();
}

function showFormPanel(descriptionEn, descriptionTh) {
  $("#div_manage_product").show();
  var panel = document.getElementById("div_manage_product");
  if (panel && panel.scrollIntoView) {
    panel.scrollIntoView({ behavior: "smooth", block: "start" });
  }
  setTimeout(function () {
    initSummernote();
    setDescriptionHtml(descriptionEn || "", descriptionTh || "");
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
  $("#prod_crop_image").attr("src", cropObjectUrl);
  $("#prod_crop_modal").modal("show");
}

function initCropperWhenShown() {
  var $img = $("#prod_crop_image");
  destroyCropperKeepUrl();
  setCropZoomUi(100);
  $img.cropper({
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

function numOrEmpty(v) {
  if (v === null || v === undefined || v === "") return "";
  return String(v);
}

$(document).ready(function () {
  setEntryModeUi(getPreferredEntryType());
  initEntryModeDrag();

  $("#entry_mode_group").on("change", "input[name='entry_mode']", function () {
    applyEntryModeChoice($(this).val());
  });

  // Clicking a label/option snaps thumb + selects
  $("#entry_mode_group").on("click", ".bny-step-switch-opt", function (e) {
    e.preventDefault();
    var val = $(this).find("input[name='entry_mode']").val();
    if (val) applyEntryModeChoice(val);
  });

  loadShopUnits(function () {
    loadCategories(function () {
      loadProducts(1);
    });
  });

  $("#domain_sel").on("change", function () {
    hideForm();
    $("#category_filter").val("0");
    $("#page").val(1);
    loadCategories(function () {
      loadProducts(1);
    });
  });

  $("#category_filter, #per_page").on("change", function () {
    $("#page").val(1);
    loadProducts(1);
  });

  $("#product_search").on("input", function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
      $("#page").val(1);
      loadProducts(1);
    }, 350);
  });

  $("#btn_prev_page").on("click", function () {
    var page = parseInt($("#page").val(), 10) || 1;
    if (page > 1) loadProducts(page - 1);
  });

  $("#btn_next_page").on("click", function () {
    var page = parseInt($("#page").val(), 10) || 1;
    loadProducts(page + 1);
  });

  $("#btn_add_product").on("click", function () {
    if (!$("#domain_sel").val()) {
      alert("Select a domain first");
      return;
    }
    resetThumbState();
    $("#manage_product_txt").text("Add product");
    $("#is_add").val("1");
    $("#id_en").val("");
    $("#prod_title_en, #prod_title_th").val("");
    $("#prod_sku, #prod_barcode").val("");
    $("#prod_unit").val("0");
    $("#prod_category").val($("#category_filter").val() || "0");
    setFormEntryType(getPreferredEntryType());
    setEntryModeUi(getPreferredEntryType());
    $("#prod_price, #prod_cost").val("");
    $("#prod_sort").val("0");
    $("#prod_width, #prod_length, #prod_height, #prod_weight").val("");
    $("#prod_load_x, #prod_load_y, #prod_load_z").val("");
    $("#seo_title_en, #seo_title_th, #seo_description_en, #seo_description_th").val("");
    $("#seo_keywords_en, #seo_keywords_th, #seo_slug_en, #seo_slug_th").val("");
    $("#prod_is_visible").prop("checked", true);
    $("#prod_is_atomic, #prod_is_salable").prop("checked", false);
    showFormPanel("", "");
  });

  $("#btn_cancel_product").on("click", hideForm);

  $(document).on("click", ".js-prod-edit", function () {
    var idEn = $(this).data("id-en");
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/product/get_ajax",
      data: { id_en: idEn },
      dataType: "json",
      success: function (res) {
        var p = (res && res.product_data) || {};
        resetThumbState();
        $("#manage_product_txt").text("Edit product");
        $("#is_add").val("2");
        $("#id_en").val(p.id_en || idEn);
        $("#prod_title_en").val(p.Title_en || p.Title || "");
        $("#prod_title_th").val(p.Title_th || "");
        $("#prod_sku").val(p.Sku || "");
        $("#prod_barcode").val(p.Barcode || "");
        $("#prod_unit").val(String(p.web_shop_unit_id || 0));
        $("#prod_category").val(String(p.web_domain_category_id || 0));
        setFormEntryType(p.entry_type || "bom");
        // Do not rewrite cookie while editing — toolbar stays on last Add preference
        $("#prod_price").val(numOrEmpty(p.Price));
        $("#prod_cost").val(numOrEmpty(p.Cost_price));
        $("#prod_sort").val(p.sort_order != null ? p.sort_order : 0);
        $("#prod_width").val(numOrEmpty(p.width_cm));
        $("#prod_length").val(numOrEmpty(p.length_cm));
        $("#prod_height").val(numOrEmpty(p.height_cm));
        $("#prod_weight").val(numOrEmpty(p.weight_g));
        $("#prod_load_x").val(numOrEmpty(p.max_load_axis_x_g));
        $("#prod_load_y").val(numOrEmpty(p.max_load_axis_y_g));
        $("#prod_load_z").val(numOrEmpty(p.max_load_axis_z_g));
        $("#seo_title_en").val(p.seo_title_en || p.seo_title || "");
        $("#seo_title_th").val(p.seo_title_th || "");
        $("#seo_description_en").val(p.seo_description_en || p.seo_description || "");
        $("#seo_description_th").val(p.seo_description_th || "");
        $("#seo_keywords_en").val(p.seo_keywords_en || p.seo_keywords || "");
        $("#seo_keywords_th").val(p.seo_keywords_th || "");
        $("#seo_slug_en").val(p.seo_slug_en || p.seo_slug || "");
        $("#seo_slug_th").val(p.seo_slug_th || "");
        $("#thumbnail_old").val(p.thumbnail || "");
        setThumbPreview(p.thumbnail_url || "");
        $("#prod_is_visible").prop(
          "checked",
          p.is_visible === undefined ||
            p.is_visible === null ||
            String(p.is_visible) === "1" ||
            p.is_visible === true
        );
        $("#prod_is_atomic").prop("checked", String(p.is_atomic) === "1" || p.is_atomic === true);
        $("#prod_is_salable").prop("checked", String(p.is_salable) === "1" || p.is_salable === true);
        showFormPanel(p.Description_en || p.Description || "", p.Description_th || "");
      },
      complete: function () {
        $("#spinner-div").hide();
      }
    });
  });

  $(document).on("click", ".js-prod-del", function () {
    if (!confirm("Remove this product from the active list?")) return;
    var idEn = $(this).data("id-en");
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/product/del_ajax",
      data: { id_en: idEn },
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideForm();
          loadProducts(parseInt($("#page").val(), 10) || 1);
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

  $(document).on("change", ".js-prod-visible", function () {
    if (visibilityToggleBusy) return;
    var el = this;
    var idEn = $(el).data("id-en");
    var val = el.checked ? 1 : 0;
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/product/visibility_ajax",
      data: { id_en: idEn, is_visible: val },
      dataType: "json",
      success: function (res) {
        if (!(res && res.Status === "Success")) {
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

  $("#btn_select_thumb").on("click", function () {
    $("#prod_thumb_file").trigger("click");
  });

  $("#btn_remove_thumb").on("click", function () {
    pendingThumbBlob = null;
    pendingThumbName = "";
    clearThumbnailFlag = 1;
    $("#clear_thumbnail").val("1");
    $("#prod_thumb_file").val("");
    setThumbPreview("");
  });

  $("#prod_thumb_file").on("change", function () {
    var files = this.files;
    if (!files || !files.length) return;
    clearThumbnailFlag = 0;
    $("#clear_thumbnail").val("0");
    openCropModal(files[0]);
    $(this).val("");
  });

  $("#prod_crop_modal").on("shown.bs.modal", function () {
    if ($("#prod_crop_image").attr("src")) {
      initCropperWhenShown();
    }
  });

  $("#prod_crop_modal").on("hidden.bs.modal", function () {
    destroyCropperKeepUrl();
    if (cropObjectUrl) {
      try {
        URL.revokeObjectURL(cropObjectUrl);
      } catch (e) {}
      cropObjectUrl = null;
    }
    $("#prod_crop_image").attr("src", "");
  });

  $("#btn_crop_mode").on("click", function () {
    $("#prod_crop_image").cropper("setDragMode", "crop");
    $(this).addClass("active");
    $("#btn_move_mode").removeClass("active");
  });

  $("#btn_move_mode").on("click", function () {
    $("#prod_crop_image").cropper("setDragMode", "move");
    $(this).addClass("active");
    $("#btn_crop_mode").removeClass("active");
  });

  $("#prod_crop_zoom").on("input change", function () {
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
      canvas = $("#prod_crop_image").cropper("getCroppedCanvas", {
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
    canvas.toBlob(function (blob) {
      if (!blob) {
        alert("Could not create image");
        return;
      }
      pendingThumbBlob = blob;
      pendingThumbName = "product_thumb_" + Date.now() + ".png";
      clearThumbnailFlag = 0;
      $("#clear_thumbnail").val("0");
      setThumbPreview(URL.createObjectURL(blob));
      $("#prod_crop_modal").modal("hide");
    }, "image/png");
  });

  $("#prod_title_en, #prod_title_th, #seo_title_en, #seo_title_th, #seo_description_en, #seo_description_th, #seo_slug_en, #seo_slug_th").on(
    "input",
    updateSeoPreview
  );

  $(document).on("click", ".js-translate", function () {
    var pair = $(this).data("pair");
    var to = $(this).data("to") === "th" ? "th" : "en";
    var from = to === "th" ? "en" : "th";
    var format = $(this).data("format") === "html" ? "html" : "text";
    var jobs = [];

    if (pair === "title") {
      var srcTitle = $.trim(from === "en" ? $("#prod_title_en").val() : $("#prod_title_th").val());
      if (!srcTitle) {
        alert("Enter source title first");
        return;
      }
      jobs.push({
        text: srcTitle,
        apply: function (t) {
          fillFieldTranslated(to === "th" ? "#prod_title_th" : "#prod_title_en", t, false);
        }
      });
    } else if (pair === "description") {
      var srcDes = getDescriptionHtml(from);
      var plain = $("<div>").html(srcDes).text().replace(/\s+/g, " ").trim();
      if (!plain) {
        alert("Enter source description first");
        return;
      }
      jobs.push({
        text: srcDes,
        format: "html",
        apply: function (t) {
          var targetId = to === "th" ? "#prod_des_th" : "#prod_des_en";
          var cur = summernoteReady ? $(targetId).summernote("code") : $(targetId).val();
          var curPlain = $("<div>").html(cur || "").text().replace(/\s+/g, " ").trim();
          if (curPlain && !confirm("Overwrite existing description?")) return;
          if (summernoteReady) $(targetId).summernote("code", t);
          else $(targetId).val(t);
        }
      });
    } else if (pair === "seo") {
      var map = [
        ["#seo_title_" + from, "#seo_title_" + to],
        ["#seo_description_" + from, "#seo_description_" + to],
        ["#seo_keywords_" + from, "#seo_keywords_" + to]
      ];
      map.forEach(function (m) {
        var src = $.trim($(m[0]).val());
        if (!src) return;
        jobs.push({
          text: src,
          apply: function (t) {
            fillFieldTranslated(m[1], t, false);
          }
        });
      });
      if (!jobs.length) {
        alert("Enter SEO fields in the source language first");
        return;
      }
    }

    if (!jobs.length) return;
    $("#spinner-div").show();
    var i = 0;
    function next(err) {
      if (err) {
        $("#spinner-div").hide();
        alert(err);
        return;
      }
      if (i >= jobs.length) {
        $("#spinner-div").hide();
        updateSeoPreview();
        return;
      }
      var job = jobs[i++];
      translateText(job.text, from, to, job.format || format, function (e, translated) {
        if (e) return next(e);
        job.apply(translated);
        next(null);
      });
    }
    next(null);
  });

  $("#btn_save_product").on("click", function () {
    var titleEn = $.trim($("#prod_title_en").val());
    var titleTh = $.trim($("#prod_title_th").val());
    if (!titleEn && !titleTh) {
      alert("Please enter product title (EN or TH)");
      return;
    }
    var slugEn = $.trim($("#seo_slug_en").val());
    var slugTh = $.trim($("#seo_slug_th").val());
    if ((slugEn && !isValidSeoSlug(slugEn)) || (slugTh && !isValidSeoSlug(slugTh))) {
      alert("URL slug: use Thai/English letters, numbers, and dashes only");
      return;
    }

    var fd = new FormData();
    fd.append("is_add", $("#is_add").val());
    fd.append("Title_en", titleEn);
    fd.append("Title_th", titleTh);
    fd.append("Sku", $.trim($("#prod_sku").val()));
    fd.append("Barcode", $.trim($("#prod_barcode").val()));
    fd.append("web_shop_unit_id", $("#prod_unit").val() || "0");
    fd.append("Description_en", getDescriptionHtml("en"));
    fd.append("Description_th", getDescriptionHtml("th"));
    fd.append("web_domain_category_id", $("#prod_category").val() || "0");
    fd.append("web_domain_id_en", $("#domain_sel").val());
    fd.append("id_en", $("#id_en").val());
    fd.append("Cost_price", $("#prod_cost").val());
    fd.append("Price", $("#prod_price").val());
    fd.append("is_visible", $("#prod_is_visible").is(":checked") ? "1" : "0");
    fd.append("is_atomic", $("#prod_is_atomic").is(":checked") ? "1" : "0");
    fd.append("is_salable", $("#prod_is_salable").is(":checked") ? "1" : "0");
    fd.append("entry_type", $("#prod_entry_type").val() || "bom");
    fd.append("seo_title_en", $.trim($("#seo_title_en").val()));
    fd.append("seo_title_th", $.trim($("#seo_title_th").val()));
    fd.append("seo_description_en", $.trim($("#seo_description_en").val()));
    fd.append("seo_description_th", $.trim($("#seo_description_th").val()));
    fd.append("seo_keywords_en", $.trim($("#seo_keywords_en").val()));
    fd.append("seo_keywords_th", $.trim($("#seo_keywords_th").val()));
    fd.append("seo_slug_en", slugEn);
    fd.append("seo_slug_th", slugTh);
    fd.append("sort_order", $("#prod_sort").val() || "0");
    fd.append("width_cm", $("#prod_width").val());
    fd.append("length_cm", $("#prod_length").val());
    fd.append("height_cm", $("#prod_height").val());
    fd.append("weight_g", $("#prod_weight").val());
    fd.append("max_load_axis_x_g", $("#prod_load_x").val());
    fd.append("max_load_axis_y_g", $("#prod_load_y").val());
    fd.append("max_load_axis_z_g", $("#prod_load_z").val());
    fd.append("thumbnail_old", $("#thumbnail_old").val());
    fd.append("clear_thumbnail", clearThumbnailFlag ? "1" : "0");
    if (pendingThumbBlob) {
      fd.append("thumbnail", pendingThumbBlob, pendingThumbName || "thumbnail.png");
    }

    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/products/product/save_ajax",
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideForm();
          loadProducts(parseInt($("#page").val(), 10) || 1);
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
