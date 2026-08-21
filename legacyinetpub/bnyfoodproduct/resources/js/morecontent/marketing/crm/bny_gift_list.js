$(function () {
  var $flashAlerts = $("#bny-gift-flash-alerts .bny-gift-flash-alert");
  if ($flashAlerts.length) {
    window.setTimeout(function () {
      $flashAlerts.fadeOut(400, function () {
        $(this).remove();
      });
    }, 10000);
  }
});

class UI_MORECONTENT {
  addToList(list_data, gift_pic_base_url) {
    const list = document.getElementById("content-list");
    const row = document.createElement("tr");

    var url_edit = hostname_site + "/" + "marketing/crm/bnyadminreward/edit_bny_gift_form/" + list_data.web_bny_gift_id;
    var pic_html = "";
    if (list_data.web_bny_gift_pic) {
      pic_html = '<img src="' + gift_pic_base_url + encodeURIComponent(list_data.web_bny_gift_pic.split(/[/\\\\]/).pop()) + '" alt="" style="max-height:56px;max-width:72px;object-fit:contain;">';
    }
    var detail = list_data.web_bny_gift_detail ? list_data.web_bny_gift_detail : "";
    if (detail.length > 80) {
      detail = detail.substring(0, 80);
    }
    var now_label = parseInt(list_data.web_bny_gift_now, 10) === 1 ? '<span class="label label-success">ล่าสุด</span>' : "";

    row.innerHTML = `
    <td>${pic_html}</td>
    <td>${detail}</td>
    <td>${now_label}</td>
    <td class="text-nowrap">
      <a href="${url_edit}" data-toggle="tooltip" data-original-title="Edit">
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

function loadcontent(offset, sortby, sorttype, is_new_load) {
  var bny_gift_search = document.getElementById("bny_gift_search").value;

  $("#spinner-div").show();

  var urls = hostname_site + "/" + "marketing/crm/bnyadminreward/loaddata_more_ajax";
  $.ajax({
    type: "POST",
    url: urls,
    data: { bny_gift_search: bny_gift_search, offset: offset, sortby: sortby, sorttype: sorttype },
    dataType: "json",
    success: function (data) {
      const ui = new UI_MORECONTENT();
      var gift_pic_base_url = data.gift_pic_base_url || "";

      if (is_new_load == 1) {
        ui.deleteAll();
      }

      const list_data = data.list_data;

      list_data.forEach(function (item) {
        ui.addToList(item, gift_pic_base_url);
      });
    },
    complete: function () {
      $("#spinner-div").hide();
    }
  });
}
