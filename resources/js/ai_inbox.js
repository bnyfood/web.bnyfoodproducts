$(function () {
  var $wrap = $("#ai_inbox");
  if (!$wrap.length) {
    return;
  }
  var base = $wrap.data("base") || "";
  var isEn = $wrap.data("lang") === "en";
  var plat = "shopee";
  var filter = "wait";
  var query = "";
  var inFlight = false;
  var lastData = window.AI_INBOX_BOOT || {};
  var lastHash = "";
  var $list = $("#ai_thread_list");
  var $up = $("#ai_scroll_up");
  var $down = $("#ai_scroll_down");
  var $status = $("#ai_inbox_status");
  var hovering = false;
  var colors = ["#ee4d2d", "#d4a017", "#2f6fed", "#0a9b6e", "#7b3fa0", "#c0392b"];

  function t(th, en) {
    return isEn ? en : th;
  }

  function esc(s) {
    return $("<div>").text(s == null ? "" : String(s)).html();
  }

  function paren(n) {
    n = parseInt(n, 10) || 0;
    return n > 0 ? "(" + n + ")" : "";
  }

  function initial(name) {
    name = $.trim(name || "");
    if (!name) {
      return "?";
    }
    return name.charAt(0).toUpperCase();
  }

  function colorFor(name) {
    var n = 0;
    var s = String(name || "");
    for (var i = 0; i < s.length; i++) {
      n += s.charCodeAt(i);
    }
    return colors[n % colors.length];
  }

  function applyCounts(counts) {
    counts = counts || {};
    $("[data-tab-count]").each(function () {
      var p = $(this).attr("data-tab-count");
      $(this).text(paren(counts[p]));
    });
    if (window.BNYChatBadge) {
      window.BNYChatBadge.apply(counts);
    }
  }

  function bucket(th) {
    if (th.needs_reply) {
      return th.overdue ? "follow" : "wait";
    }
    if (th.replied || th.last_dir === "out") {
      return "replied";
    }
    return "all";
  }

  function isFollow(th) {
    return !!(th.overdue || th.pinned);
  }

  function visibleRows(data) {
    var rows = (data.threads && data.threads[plat]) ? data.threads[plat].slice() : [];
    var q = query.toLowerCase();
    var out = [];
    var waitN = 0;
    var followN = 0;
    for (var i = 0; i < rows.length; i++) {
      var th = rows[i];
      if (th.needs_reply) {
        waitN++;
      }
      if (th.overdue) {
        followN++;
      } else if (th.pinned) {
        followN++;
      }
      if (filter === "wait" && !th.needs_reply) {
        continue;
      }
      if (filter === "follow" && !isFollow(th)) {
        continue;
      }
      if (filter === "replied" && !(th.replied || th.last_dir === "out")) {
        continue;
      }
      if (q) {
        var hay = ((th.buyer_name || "") + " " + (th.last_preview || "")).toLowerCase();
        if (hay.indexOf(q) === -1) {
          continue;
        }
      }
      out.push(th);
    }
    $("[data-filter-dot='wait']").prop("hidden", waitN < 1);
    return { rows: out, waitN: waitN, followN: followN };
  }

  function canScroll() {
    var el = $list[0];
    if (!el) {
      return { up: false, down: false };
    }
    var top = el.scrollTop;
    var max = el.scrollHeight - el.clientHeight;
    return {
      up: max > 4 && top > 4,
      down: max > 4 && top < max - 4
    };
  }

  function hideHints() {
    $up.removeClass("is-on");
    $down.removeClass("is-on");
  }

  function showHints() {
    var dir = canScroll();
    $up.toggleClass("is-on", hovering && dir.up);
    $down.toggleClass("is-on", hovering && dir.down);
  }

  function previewText(th) {
    var body = th.last_preview || "";
    if (th.last_dir === "out") {
      if (body === "[สติกเกอร์]" || /sticker/i.test(body)) {
        return t("คุณส่ง Sticker", "You sent a sticker");
      }
      if (body === "[รูป]") {
        return t("คุณส่งรูป", "You sent a photo");
      }
      return body ? body : t("คุณส่งข้อความ", "You sent a message");
    }
    return body;
  }

  function renderRows(rows) {
    if (!rows.length) {
      $list.html(
        "<div class=\"ai-chat-empty\">" +
          esc(t("ยังไม่มีห้องแชทในตัวกรองนี้", "No chats in this filter")) +
        "</div>"
      );
      return;
    }
    var html = [];
    for (var i = 0; i < rows.length; i++) {
      var th = rows[i];
      var name = th.buyer_name ? th.buyer_name : t("ไม่มีชื่อ", "No name");
      var href = esc(base) + "ai/thread/" + parseInt(th.thread_id, 10);
      var cls = "ai-chat-row";
      if (th.needs_reply) {
        cls += " is-wait";
      }
      if (th.overdue) {
        cls += " is-overdue";
      }
      var avaInner = th.avatar
        ? "<img src=\"" + esc(th.avatar) + "\" alt=\"\" onerror=\"this.style.display='none'\">" +
          "<span>" + esc(initial(name)) + "</span>"
        : "<span>" + esc(initial(name)) + "</span>";
      var ticks = th.last_dir === "out"
        ? "<span class=\"ai-chat-ticks\">✓✓</span> "
        : "";
      var unread = "";
      if (th.needs_reply) {
        var n = th.unread > 0 ? th.unread : 1;
        unread = "<span class=\"ai-chat-unread\">" + n + "</span>";
      }
      var tag = "";
      if (th.overdue) {
        tag = "<span class=\"ai-chat-tag\">" + esc(t("เกินเวลา", "Overtime")) + "</span>";
      } else if (th.needs_reply) {
        tag = "<span class=\"ai-chat-tag is-wait\">" + esc(t("รอตอบกลับ", "Waiting")) + "</span>";
      }
      html.push(
        "<a class=\"" + cls + "\" href=\"" + href + "\">" +
          "<span class=\"ai-chat-ava\" style=\"background:" + colorFor(name) + "\">" + avaInner + "</span>" +
          "<span class=\"ai-chat-main\">" +
            "<span class=\"ai-chat-top\">" +
              "<span class=\"ai-chat-name\">" + esc(name) + "</span>" +
              "<span class=\"ai-chat-time\">" + esc(th.time_label || "") + "</span>" +
            "</span>" +
            "<span class=\"ai-chat-bottom\">" +
              "<span class=\"ai-chat-snip\">" + ticks + esc(previewText(th)) + "</span>" +
              "<span class=\"ai-chat-meta\">" + tag + unread + "</span>" +
            "</span>" +
          "</span>" +
        "</a>"
      );
    }
    $list.html(html.join(""));
  }

  function syncLine(sync) {
    if (!sync) {
      return;
    }
    var names = { shopee: "Shopee", lazada: "Lazada", tiktok: "TikTok" };
    var parts = [];
    $.each(names, function (p, lab) {
      var r = sync[p];
      if (!r || r.ok) {
        return;
      }
      parts.push(lab + " — " + (r.error ? String(r.error) : "fail"));
    });
    $status.text(parts.join(" · "));
  }

  function applyPayload(data, fromSync) {
    if (!data || !data.ok) {
      return;
    }
    lastData = data;
    applyCounts(data.counts);
    var vis = visibleRows(data);
    var hash = plat + ":" + filter + ":" + query + ":" + JSON.stringify(vis.rows);
    if (hash !== lastHash) {
      var top = $list[0] ? $list[0].scrollTop : 0;
      renderRows(vis.rows);
      if ($list[0]) {
        $list[0].scrollTop = top;
      }
      lastHash = hash;
    }
    if (fromSync) {
      syncLine(data.sync);
    }
    showHints();
  }

  function poll() {
    if (inFlight) {
      return;
    }
    inFlight = true;
    $.ajax({
      url: base + "ai/poll_inbox",
      dataType: "json",
      cache: false
    }).done(function (data) {
      applyPayload(data, !!(data && data.synced));
    }).always(function () {
      inFlight = false;
    });
  }

  $wrap.on("click", ".ai-chat-plats .ai-pill", function () {
    plat = $(this).data("plat");
    $(".ai-chat-plats .ai-pill").removeClass("is-on");
    $(this).addClass("is-on");
    lastHash = "";
    applyPayload(lastData, false);
  });

  $wrap.on("click", ".ai-chat-filters .ai-pill", function () {
    filter = $(this).data("filter");
    $(".ai-chat-filters .ai-pill").removeClass("is-on");
    $(this).addClass("is-on");
    lastHash = "";
    applyPayload(lastData, false);
  });

  $("#ai_chat_q").on("input", function () {
    query = $.trim($(this).val() || "");
    lastHash = "";
    applyPayload(lastData, false);
  });

  $("#ai_chat_banner_x").on("click", function () {
    $("#ai_chat_banner").hide();
    try {
      sessionStorage.setItem("bny_chat_banner_off", "1");
    } catch (e) {}
  });
  try {
    if (sessionStorage.getItem("bny_chat_banner_off") === "1") {
      $("#ai_chat_banner").hide();
    }
  } catch (e) {}

  $("#ai_thread_scroll").on("mouseenter", function () {
    hovering = true;
    showHints();
  }).on("mouseleave", function () {
    hovering = false;
    hideHints();
  });

  $list.on("scroll", function () {
    if (hovering) {
      showHints();
    }
  });

  $up.on("click", function () {
    var el = $list[0];
    if (el) {
      el.scrollTop = Math.max(0, el.scrollTop - Math.max(120, el.clientHeight * 0.8));
    }
  });
  $down.on("click", function () {
    var el = $list[0];
    if (el) {
      el.scrollTop = el.scrollTop + Math.max(120, el.clientHeight * 0.8);
    }
  });

  if (window.AI_INBOX_BOOT) {
    applyPayload(window.AI_INBOX_BOOT, false);
  }
  poll();
  setInterval(poll, 5000);
});
