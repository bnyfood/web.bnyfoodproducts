function escHtml(text) {
  if (text === null || text === undefined) return "";
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function i18n(key, fallback) {
  if (window.BNY_I18N && window.BNY_I18N[key]) return window.BNY_I18N[key];
  return fallback || key;
}

function renderUnitRows(list_data) {
  var list = document.getElementById("unit-list");
  list.innerHTML = "";
  if (!list_data || list_data.length === 0) {
    list.innerHTML =
      '<tr><td colspan="5" class="text-center">' + escHtml(i18n("no_units", "No units found")) + "</td></tr>";
    return;
  }
  list_data.forEach(function (row) {
    var active = String(row.Status) === "1";
    var tr = document.createElement("tr");
    tr.innerHTML =
      "<td>" +
      escHtml(row.name_th || "") +
      "</td>" +
      "<td>" +
      escHtml(row.name_en || "") +
      "</td>" +
      "<td>" +
      escHtml(row.sort_order != null ? row.sort_order : 0) +
      "</td>" +
      "<td>" +
      escHtml(active ? i18n("active", "Active") : i18n("inactive", "Inactive")) +
      "</td>" +
      '<td class="text-nowrap">' +
      '<button type="button" class="btn btn-xs btn-primary js-unit-edit" data-id-en="' +
      escHtml(row.id_en) +
      '"><i class="icon wb-pencil"></i></button> ' +
      '<button type="button" class="btn btn-xs btn-danger js-unit-del" data-id-en="' +
      escHtml(row.id_en) +
      '"><i class="icon wb-trash"></i></button>' +
      "</td>";
    list.appendChild(tr);
  });
}

function hideForm() {
  $("#div_manage_unit").hide();
  $("#unit_name_th, #unit_name_en").val("");
  $("#unit_sort").val("0");
  $("#unit_status").prop("checked", true);
  $("#id_en").val("");
  $("#is_add").val("1");
}

function loadUnits() {
  $("#spinner-div").show();
  $.ajax({
    type: "POST",
    url: hostname_site + "/webs/units/unit/list_ajax",
    dataType: "json",
    success: function (res) {
      renderUnitRows((res && res.list_data) || []);
    },
    complete: function () {
      $("#spinner-div").hide();
    }
  });
}

$(document).ready(function () {
  loadUnits();

  $("#btn_add_unit").on("click", function () {
    hideForm();
    $("#manage_unit_txt").text(i18n("add_unit", "Add unit"));
    $("#is_add").val("1");
    $("#div_manage_unit").show();
  });

  $("#btn_cancel_unit").on("click", hideForm);

  $(document).on("click", ".js-unit-edit", function () {
    var idEn = $(this).data("id-en");
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/units/unit/get_ajax",
      data: { id_en: idEn },
      dataType: "json",
      success: function (res) {
        var u = (res && res.unit_data) || {};
        $("#manage_unit_txt").text(i18n("edit", "Edit"));
        $("#is_add").val("2");
        $("#id_en").val(u.id_en || idEn);
        $("#unit_name_th").val(u.name_th || "");
        $("#unit_name_en").val(u.name_en || "");
        $("#unit_sort").val(u.sort_order != null ? u.sort_order : 0);
        $("#unit_status").prop("checked", String(u.Status) === "1");
        $("#div_manage_unit").show();
      },
      complete: function () {
        $("#spinner-div").hide();
      }
    });
  });

  $(document).on("click", ".js-unit-del", function () {
    if (!confirm(i18n("confirm_delete", "Remove this item?"))) return;
    var idEn = $(this).data("id-en");
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/units/unit/del_ajax",
      data: { id_en: idEn },
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideForm();
          loadUnits();
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

  $("#btn_save_unit").on("click", function () {
    var th = $.trim($("#unit_name_th").val());
    var en = $.trim($("#unit_name_en").val());
    if (!th && !en) {
      alert("Name required");
      return;
    }
    $("#spinner-div").show();
    $.ajax({
      type: "POST",
      url: hostname_site + "/webs/units/unit/save_ajax",
      data: {
        is_add: $("#is_add").val(),
        id_en: $("#id_en").val(),
        name_th: th,
        name_en: en,
        sort_order: $("#unit_sort").val() || 0,
        Status: $("#unit_status").is(":checked") ? 1 : 0
      },
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideForm();
          loadUnits();
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
