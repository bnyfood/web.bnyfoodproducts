function parseNum(value) {
  var num = parseFloat(String(value).replace(/,/g, ""));
  return isNaN(num) ? 0 : num;
}

function fmtAmount(num) {
  return num.toFixed(2);
}

function calcBiggrillAmounts() {
  var price = parseNum($("#price").val());
  var delivery = parseNum($("#delivery").val());
  var discount = parseNum($("#discount").val());

  var amountIncludeVat = (price + delivery) - discount;
  var amountExcludeVat = Math.round((amountIncludeVat / 1.07) * 100) / 100;
  var vat = Math.round((amountIncludeVat - amountExcludeVat) * 100) / 100;

  $("#amount_include_vat_display").val(fmtAmount(amountIncludeVat));
  $("#amount_exclude_vat_display").val(fmtAmount(amountExcludeVat));
  $("#vat_display").val(fmtAmount(vat));
}

function updateVoidFormLabel() {
  var isChecked = $("#is_void").is(":checked");
  $(".void-form-status-text").text(isChecked ? "Yes" : "No");
}

$(function () {
  calcBiggrillAmounts();
  updateVoidFormLabel();

  if ($("#ctime").length && typeof $.fn.daterangepicker !== "undefined") {
    var ctimeVal = $("#ctime").val();
    var startMoment = ctimeVal ? moment(ctimeVal, "DD/MM/YYYY HH:mm") : moment();
    if (!startMoment.isValid()) {
      startMoment = moment();
    }

    $("#ctime").daterangepicker({
      singleDatePicker: true,
      timePicker: true,
      timePicker24Hour: true,
      autoUpdateInput: true,
      startDate: startMoment,
      locale: {
        format: "DD/MM/YYYY HH:mm"
      }
    });
  }

  $(document).on("input", ".bg-num-only", function () {
    var val = $(this).val();
    val = val.replace(/[^0-9.]/g, "");
    var parts = val.split(".");
    if (parts.length > 2) {
      val = parts[0] + "." + parts.slice(1).join("");
    }
    $(this).val(val);
    calcBiggrillAmounts();
  });

  $("#is_void").on("change", updateVoidFormLabel);

  $("#biggrilldata_add_form, #biggrilldata_edit_form").on("submit", function () {
    calcBiggrillAmounts();
  });
});
