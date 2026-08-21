var bgListOffset = 0;
var bgListPerPage = 20;
var bgListLoading = false;
var bgListHasMore = true;
var sortby = "";
var sorttype = "";

function resetBiggrillListState() {
  bgListOffset = 0;
  bgListHasMore = true;
  $("#offset").val(0);
}

function initBiggrillListState() {
  var perPageEl = document.getElementById("per_page");
  if (perPageEl) {
    bgListPerPage = parseInt(perPageEl.value, 10) || 20;
  }

  sortby = $("#sortby").val() || "";
  sorttype = $("#sorttype").val() || "";

  var rowCount = $("#content-list tr").length;
  if (rowCount < bgListPerPage) {
    bgListHasMore = false;
    bgListOffset = rowCount;
  } else {
    bgListHasMore = true;
    bgListOffset = rowCount;
  }
  $("#offset").val(bgListOffset);
}

function voidStatusText(isVoid) {
  return isVoid ? "Yes" : "No";
}

function updateVoidStatusText($input) {
  var $label = $input.closest(".bg-void-cell").find(".void-status-text");
  $label.text(voidStatusText($input.is(":checked")));
}

function fmtCtime(value) {
  if (!value) {
    return "";
  }
  var d = new Date(value);
  if (isNaN(d.getTime())) {
    return escHtml(value);
  }
  var pad = function (n) {
    return n < 10 ? "0" + n : "" + n;
  };
  return (
    pad(d.getDate()) +
    "/" +
    pad(d.getMonth() + 1) +
    "/" +
    d.getFullYear() +
    " " +
    pad(d.getHours()) +
    ":" +
    pad(d.getMinutes())
  );
}

function fmtTracking(value) {
  if (!value) {
    return "";
  }
  var parts = String(value)
    .split(",")
    .map(function (p) {
      return p.trim();
    })
    .filter(function (p) {
      return p !== "";
    });
  if (parts.length === 0) {
    return "";
  }
  if (parts.length > 1) {
    return escHtml(parts[0] + ".....");
  }
  return escHtml(parts[0]);
}

function fmtNum(value) {
  var num = parseFloat(value);
  if (isNaN(num)) {
    return "0.00";
  }
  return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function escHtml(text) {
  if (text === null || text === undefined) {
    return "";
  }
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function tablesort(v_sortby, v_sorttype, numclass) {
  sortby = v_sortby;
  sorttype = v_sorttype;
  $("#sortby").val(v_sortby);
  $("#sorttype").val(v_sorttype);
  resetBiggrillListState();

  var arrSortIcons = Array.from(document.getElementsByClassName("asssort"));
  arrSortIcons.forEach(function (icon) {
    icon.style.color = "#ccc";
  });

  var activeIcon = document.getElementsByClassName("asssort")[numclass];
  if (activeIcon) {
    activeIcon.style.color = "#000000";
  }

  loadcontent(0, v_sortby, v_sorttype, 1);
}

$(function () {
  if (!document.getElementById("content-list")) {
    return;
  }

  initBiggrillListState();

  $('input[name="daterange"]').daterangepicker({
    autoUpdateInput: false,
    opens: "left"
  });

  $('input[name="daterange"]').on("apply.daterangepicker", function (ev, picker) {
    $(this).val(
      picker.startDate.format("MM/DD/YYYY") +
        " - " +
        picker.endDate.format("MM/DD/YYYY")
    );
  });

  $('input[name="daterange"]').on("cancel.daterangepicker", function () {
    $(this).val("");
  });

  $("#biggrilldata_search_form").on("submit", function (e) {
    e.preventDefault();
    sortby = $("#sortby").val() || "";
    sorttype = $("#sorttype").val() || "";
    resetBiggrillListState();
    loadcontent(0, sortby, sorttype, 1);
  });

  $(window).on("scroll.biggrill", function () {
    if (!bgListHasMore || bgListLoading) {
      return;
    }

    var scrollBottom = $(window).scrollTop() + $(window).height();
    var docHeight = $(document).height();
    if (scrollBottom < docHeight - 80) {
      return;
    }

    loadcontent(bgListOffset, sortby, sorttype, 0);
  });

  $(document).on("change", ".void-toggle", function () {
    var $input = $(this);
    var rowId = $input.data("row-id");
    var isVoid = $input.is(":checked") ? 1 : 0;
    var prevChecked = isVoid !== 1;

    updateVoidStatusText($input);
    $input.prop("disabled", true);

    $.ajax({
      type: "POST",
      url: hostname_site + "/accounting/biggrilldata/update_void_ajax",
      data: {
        biggrill_data_id: rowId,
        is_void: isVoid
      },
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== "success") {
          $input.prop("checked", prevChecked);
          updateVoidStatusText($input);
          alert("อัปเดต Void ไม่สำเร็จ");
        }
      },
      error: function () {
        $input.prop("checked", prevChecked);
        updateVoidStatusText($input);
        alert("อัปเดต Void ไม่สำเร็จ");
      },
      complete: function () {
        $input.prop("disabled", false);
      }
    });
  });
});

class UI_MORECONTENT {
  addToList(list_data) {
    const list = document.getElementById("content-list");
    const row = document.createElement("tr");

    var isVoid = parseInt(list_data.is_void, 10) === 1;
    var checked = isVoid ? " checked" : "";
    var statusText = voidStatusText(isVoid);
    var rowId = parseInt(list_data.biggrill_data_id, 10);
    var urlEdit = hostname_site + "/accounting/biggrilldata/edit_biggrilldata_form/" + rowId;

    row.innerHTML = `
    <td>${escHtml(list_data.order_id)}</td>
    <td>${fmtCtime(list_data.ctime)}</td>
    <td>${escHtml(list_data.cus_name)}</td>
    <td>${escHtml(list_data.cus_phone)}</td>
    <td>${escHtml(list_data.status)}</td>
    <td>${fmtNum(list_data.price)}</td>
    <td>${fmtNum(list_data.delivery)}</td>
    <td>${fmtNum(list_data.discount)}</td>
    <td>${fmtNum(list_data.amount_include_vat)}</td>
    <td>${fmtNum(list_data.amount_exclude_vat)}</td>
    <td>${fmtNum(list_data.vat)}</td>
    <td>${fmtTracking(list_data.trackingid)}</td>
    <td>${escHtml(list_data.taxinvoiceID || "")}</td>
    <td>
      <div class="bg-void-cell">
        <label class="bg-void-switch" title="Void">
          <input type="checkbox" class="void-toggle" data-row-id="${rowId}"${checked}>
          <span class="slider"></span>
        </label>
        <span class="bg-void-label void-status-text">${statusText}</span>
      </div>
    </td>
    <td class="text-nowrap">
      <a href="${urlEdit}" data-toggle="tooltip" data-original-title="Edit">
        <i class="icon wb-wrench" aria-hidden="true"></i>
      </a>
    </td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#content-list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }
}

function loadcontent(offset, v_sortby, v_sorttype, is_new_load) {
  if (bgListLoading) {
    return;
  }

  var searchFieldEl = document.getElementById("search_field");
  if (!searchFieldEl) {
    return;
  }

  var search_field = searchFieldEl.value;
  var search_text = document.getElementById("search_text").value;
  var is_void = "";
  var voidRadios = document.getElementsByName("is_void");
  for (var i = 0; i < voidRadios.length; i++) {
    if (voidRadios[i].checked) {
      is_void = voidRadios[i].value;
      break;
    }
  }
  var daterange = document.getElementById("daterange").value;

  bgListLoading = true;
  if ($("#spinner-div").length) {
    $("#spinner-div").show();
  }

  var urls = hostname_site + "/accounting/biggrilldata/loaddata_more_ajax";
  $.ajax({
    type: "POST",
    url: urls,
    data: {
      search_field: search_field,
      search_text: search_text,
      is_void: is_void,
      daterange: daterange,
      offset: offset,
      sortby: v_sortby,
      sorttype: v_sorttype
    },
    dataType: "json",
    success: function (data) {
      const ui = new UI_MORECONTENT();

      if (is_new_load == 1) {
        ui.deleteAll();
        bgListOffset = 0;
        bgListHasMore = true;
      }

      const list_data = data.list_data || [];
      list_data.forEach(function (item) {
        ui.addToList(item);
      });

      if (list_data.length < bgListPerPage) {
        bgListHasMore = false;
      }

      bgListOffset = offset + list_data.length;
      $("#offset").val(bgListOffset);
    },
    complete: function () {
      bgListLoading = false;
      if ($("#spinner-div").length) {
        $("#spinner-div").hide();
      }
    }
  });
}
