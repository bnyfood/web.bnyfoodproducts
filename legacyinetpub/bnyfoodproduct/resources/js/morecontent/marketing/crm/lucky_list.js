function giftSendStatusText(isSent) {
  return isSent ? "ส่งของรางวัลแล้ว" : "ยังไม่ส่งของรางวัล";
}

function updateGiftSendStatusText($input) {
  var $label = $input.closest(".bny-gift-send-cell").find(".gift-send-status-text");
  $label.text(giftSendStatusText($input.is(":checked")));
}

$(function () {
  $(document).on("change", ".gift-send-toggle", function () {
    var $input = $(this);
    var rewardId = $input.data("reward-id");
    var giftSend = $input.is(":checked") ? 1 : 0;
    var prevChecked = giftSend !== 1;

    updateGiftSendStatusText($input);
    $input.prop("disabled", true);

    $.ajax({
      type: "POST",
      url: hostname_site + "/marketing/crm/bnyadminreward/update_gift_send_ajax",
      data: {
        web_bny_reward_id: rewardId,
        web_bny_gift_send: giftSend
      },
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== "success") {
          $input.prop("checked", prevChecked);
          updateGiftSendStatusText($input);
          alert("อัปเดตสถานะการส่งของรางวัลไม่สำเร็จ");
        }
      },
      error: function () {
        $input.prop("checked", prevChecked);
        updateGiftSendStatusText($input);
        alert("อัปเดตสถานะการส่งของรางวัลไม่สำเร็จ");
      },
      complete: function () {
        $input.prop("disabled", false);
      }
    });
  });
});

class UI_MORECONTENT {
  addToList(list_data, gift_pic_base_url) {
    const list = document.getElementById("content-list");
    const row = document.createElement("tr");

    var pic_html = "";
    if (list_data.web_bny_gift_pic) {
      pic_html =
        '<img src="' +
        gift_pic_base_url +
        encodeURIComponent(list_data.web_bny_gift_pic.split(/[/\\\\]/).pop()) +
        '" alt="" style="max-height:56px;max-width:72px;object-fit:contain;">';
    }
    var detail = list_data.web_bny_gift_detail ? list_data.web_bny_gift_detail : "";
    if (detail.length > 80) {
      detail = detail.substring(0, 80);
    }
    var winner_name = list_data.winner_name ? list_data.winner_name : "";
    var winner_phone = list_data.winner_phone ? list_data.winner_phone : "";
    var gift_send = parseInt(list_data.web_bny_gift_send, 10) === 1;
    var checked = gift_send ? " checked" : "";
    var status_text = giftSendStatusText(gift_send);
    var reward_id = parseInt(list_data.web_bny_reward_id, 10);

    row.innerHTML = `
    <td>${pic_html}</td>
    <td>${detail}</td>
    <td>${winner_name}</td>
    <td>${winner_phone}</td>
    <td>
      <div class="bny-gift-send-cell">
        <label class="bny-gift-send-switch" title="การส่งของรางวัล">
          <input type="checkbox" class="gift-send-toggle" data-reward-id="${reward_id}"${checked}>
          <span class="slider"></span>
        </label>
        <span class="bny-gift-send-label gift-send-status-text">${status_text}</span>
      </div>
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
  var lucky_search = document.getElementById("lucky_search").value;

  $("#spinner-div").show();

  var urls = hostname_site + "/" + "marketing/crm/bnyadminreward/loaddata_lucky_more_ajax";
  $.ajax({
    type: "POST",
    url: urls,
    data: { lucky_search: lucky_search, offset: offset, sortby: sortby, sorttype: sorttype },
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
