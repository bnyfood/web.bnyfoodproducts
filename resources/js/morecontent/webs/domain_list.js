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

function fmtExpire(value) {
  if (!value) {
    return "-";
  }
  var d = new Date(value);
  if (isNaN(d.getTime())) {
    var s = String(value);
    return s.length >= 10 ? escHtml(s.substring(0, 10)) : escHtml(s);
  }
  var pad = function (n) {
    return n < 10 ? "0" + n : "" + n;
  };
  return escHtml(d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()));
}

function linkCell(url) {
  var u = (url || "").trim();
  if (!u) {
    return "-";
  }
  return '<a href="' + escHtml(u) + '" target="_blank" rel="noopener noreferrer">Open</a>';
}

function renderDomainRows(list_data) {
  var list = document.getElementById("content-list");
  list.innerHTML = "";

  if (!list_data || list_data.length === 0) {
    list.innerHTML = '<tr><td colspan="5" class="text-center">No domains found</td></tr>';
    return;
  }

  list_data.forEach(function (row) {
    var url_edit = hostname_site + "/webs/domains/domain_edit_form/" + row.web_domain_id;
    var expire =
      row.expire_date_display ||
      fmtExpire(row.expire_date);

    var tr = document.createElement("tr");
    tr.innerHTML =
      "<td>" +
      escHtml(row.web_domain_name) +
      "</td>" +
      "<td>" +
      linkCell(row.registrar_link) +
      "</td>" +
      "<td>" +
      linkCell(row.ssl_link) +
      "</td>" +
      "<td>" +
      (expire || "-") +
      "</td>" +
      '<td class="text-nowrap">' +
      '<a href="' +
      url_edit +
      '" data-toggle="tooltip" data-original-title="Edit"><i class="icon wb-wrench" aria-hidden="true"></i></a> ' +
      '<a href="#" class="js-domain-del" data-id="' +
      escHtml(row.web_domain_id) +
      '" data-toggle="tooltip" data-original-title="Delete"><i class="icon wb-close" aria-hidden="true"></i></a>' +
      "</td>";
    list.appendChild(tr);
  });
}

function updatePager(page, totalPages, total) {
  $("#page").val(page);
  $("#domain-pager-info").text(
    "Showing page " + page + " / " + totalPages + " (" + total + " total)"
  );
  $("#btn_prev_page").prop("disabled", page <= 1);
  $("#btn_next_page").prop("disabled", page >= totalPages);
}

function loadcontent(page, sortby, sorttype, is_new_load) {
  var domain_search = document.getElementById("domain_search").value;
  var per_page = parseInt(document.getElementById("per_page").value, 10) || 10;
  page = parseInt(page, 10) || 1;

  var urls = hostname_site + "/webs/domains/loaddata_more_ajax";
  $("#spinner-div").show();
  $.ajax({
    type: "POST",
    url: urls,
    data: {
      domain_search: domain_search,
      page: page,
      per_page: per_page,
      sortby: sortby || "",
      sorttype: sorttype || ""
    },
    dataType: "json",
    success: function (data) {
      renderDomainRows(data.list_data || []);
      updatePager(
        data.page || page,
        data.total_pages || 1,
        data.total || 0
      );
    },
    complete: function () {
      $("#spinner-div").hide();
    }
  });
}

function tablesort(v_sortby, v_sorttype, numclass) {
  $("#sortby").val(v_sortby);
  $("#sorttype").val(v_sorttype);

  var arrSortIcons = Array.from(document.getElementsByClassName("asssort"));
  arrSortIcons.forEach(function (icon) {
    icon.style.color = "#ccc";
  });
  if (document.getElementsByClassName("asssort")[numclass]) {
    document.getElementsByClassName("asssort")[numclass].style.color = "#000000";
  }

  loadcontent(1, v_sortby, v_sorttype, 1);
}

$(document).ready(function () {
  $("#per_page").on("change", function () {
    $("#page").val(1);
    $("#domain_search_form").submit();
  });

  $("#btn_prev_page").on("click", function () {
    var page = parseInt($("#page").val(), 10) || 1;
    if (page > 1) {
      loadcontent(page - 1, $("#sortby").val(), $("#sorttype").val(), 1);
    }
  });

  $("#btn_next_page").on("click", function () {
    var page = parseInt($("#page").val(), 10) || 1;
    loadcontent(page + 1, $("#sortby").val(), $("#sorttype").val(), 1);
  });

  $(document).on("click", ".js-domain-del", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    if (!id) {
      return;
    }
    $("#spinner-div").show();
    $.ajax({
      type: "GET",
      url: hostname_site + "/webs/domains/del_action/" + id,
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          var page = parseInt($("#page").val(), 10) || 1;
          loadcontent(page, $("#sortby").val(), $("#sorttype").val(), 1);
        } else {
          $("#spinner-div").hide();
          alert("Delete fail");
        }
      },
      error: function () {
        $("#spinner-div").hide();
        alert("Delete fail");
      }
    });
  });
});
