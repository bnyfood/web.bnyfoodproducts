$(function () {
  var $page = $("#ai_thread_page");
  if (!$page.length) {
    return;
  }
  var base = $page.data("base") || "";
  var threadId = $page.data("thread");
  var isEn = $page.data("lang") === "en";
  var $q = $("#ai_prod_q");
  var $strip = $("#ai_prod_strip");
  var $status = $("#ai_prod_status");
  var $attachBox = $("#ai_compose_attach");
  var $attachJson = $("#ai_attach_json");
  var $sendForm = $("#ai_send_form");
  var timer = null;
  var seq = 0;
  var attach = [];
  var coachAttach = [];
  var suggestAttach = [];
  var originalDraft = "";
  var $coachAttachBox = $("#ai_coach_attach");
  var $coachAttachJson = $("#ai_coach_attach_json");
  var $suggestAttachBox = $("#ai_suggest_attach");

  function objectFromEl($el) {
    return {
      kind: $el.attr("data-kind") === "order" ? "order" : "product",
      id: String($el.attr("data-id") || ""),
      name: $el.attr("data-name") || "",
      sku: $el.attr("data-sku") || "",
      image: $el.attr("data-image") || "",
      status: $el.attr("data-status") || "",
      items: $el.attr("data-items") || ""
    };
  }

  function insertTarget() {
    var v = $('input[name=ai_insert_target]:checked').val();
    if (v === "buyer" || v === "suggest") {
      return v;
    }
    return "coach";
  }

  function objectKey(p) {
    return (p.kind || "product") + ":" + String(p.id || "");
  }

  function pushUnique(list, p) {
    if (!p || !p.id) {
      return list;
    }
    var key = objectKey(p);
    var i;
    for (i = 0; i < list.length; i++) {
      if (objectKey(list[i]) === key) {
        return list;
      }
    }
    list.push({
      kind: p.kind === "order" ? "order" : "product",
      id: String(p.id),
      name: p.name || "",
      sku: p.sku || "",
      image: p.image || "",
      status: p.status || "",
      items: p.items || ""
    });
    return list;
  }

  function persistOriginal(val) {
    originalDraft = val == null ? "" : String(val);
    $("#ai_draft_hidden").val(originalDraft);
    $("#ai_original_draft").val(originalDraft);
  }

  function attachCardHtml(p) {
    var label = p.kind === "order" ? p.id : p.sku || p.id;
    return (
      '<div class="ai-compose-card" data-key="' +
      esc(objectKey(p)) +
      '" data-kind="' +
      esc(p.kind === "order" ? "order" : "product") +
      '" data-id="' +
      esc(p.id) +
      '" data-name="' +
      esc(p.name || "") +
      '" data-sku="' +
      esc(p.sku || "") +
      '" data-image="' +
      esc(p.image || "") +
      '" data-status="' +
      esc(p.status || "") +
      '" data-items="' +
      esc(p.items || "") +
      '">' +
      (p.kind === "product" && p.image
        ? '<img src="' + esc(p.image) + '" alt="">'
        : '<div class="ai-prod-ph">' + (p.kind === "order" ? "🧾" : "📦") + "</div>") +
      '<div class="ai-prod-sku">' +
      esc(label) +
      "</div>" +
      '<button type="button" class="ai-compose-x" aria-label="remove">&times;</button>' +
      "</div>"
    );
  }

  function renderAttachList($box, list) {
    if (!list.length) {
      $box.removeClass("is-on").empty();
      return;
    }
    $box.addClass("is-on").html(list.map(attachCardHtml).join(""));
  }

  function t(th, en) {
    return isEn ? en : th;
  }

  function esc(s) {
    return $("<div>").text(s == null ? "" : String(s)).html();
  }

  function setStatus(msg) {
    $status.text(msg || "");
  }

  function cardHtml(p, actionLabel) {
    var name = p.name || p.sku || p.id || "";
    var sku = p.sku || "";
    var img = p.image
      ? '<img src="' + esc(p.image) + '" alt="">'
      : '<div class="ai-prod-ph">📦</div>';
    var action = actionLabel
      ? '<button type="button" class="btn btn-default btn-sm ai-prod-send">' +
        esc(actionLabel) +
        "</button>"
      : "";
    return (
      '<div class="ai-prod-card" data-id="' +
      esc(p.id) +
      '" data-name="' +
      esc(p.name || "") +
      '" data-sku="' +
      esc(p.sku || "") +
      '" data-image="' +
      esc(p.image || "") +
      '">' +
      img +
      '<div class="ai-prod-sku">' +
      esc(sku || p.id) +
      "</div>" +
      '<div class="ai-prod-name" title="' +
      esc(name) +
      '">' +
      esc(name) +
      "</div>" +
      action +
      "</div>"
    );
  }

  function render(items) {
    items = items || [];
    if (!items.length) {
      $strip.empty();
      setStatus(t("ไม่พบสินค้าในร้านที่ตรงกับที่พิมพ์", "No matching shop products"));
      return;
    }
    setStatus("");
    $strip.html(
      items
        .map(function (p) {
          return cardHtml(p, t("ใส่การ์ด", "Insert card"));
        })
        .join("")
    );
  }

  function syncAttach() {
    $attachJson.val(JSON.stringify(attach));
    renderAttachList($attachBox, attach);
  }

  function syncCoachAttach() {
    $coachAttachJson.val(JSON.stringify(coachAttach));
    renderAttachList($coachAttachBox, coachAttach);
  }

  function syncSuggestAttach() {
    renderAttachList($suggestAttachBox, suggestAttach);
  }

  function addObject(p, where) {
    if (!p || !p.id) {
      return;
    }
    if (where === "coach") {
      var before = coachAttach.length;
      coachAttach = pushUnique(coachAttach, p);
      syncCoachAttach();
      if (coachAttach.length === before) {
        setStatus(t("การ์ดนี้มีในกล่องปรึกษาแล้ว", "This card is already in the AI discussion box"));
      } else {
        setStatus(t("ใส่ในกล่องปรึกษา AI แล้ว — กดส่งถึง AI เมื่อพร้อม", "Added to the AI discussion box — click Send to AI when ready"));
      }
      var coachBox = document.getElementById("ai_coach_form");
      if (coachBox && coachBox.scrollIntoView) {
        coachBox.scrollIntoView({ block: "nearest" });
      }
      return;
    }
    if (where === "suggest") {
      var beforeS = suggestAttach.length;
      suggestAttach = pushUnique(suggestAttach, p);
      syncSuggestAttach();
      if (suggestAttach.length === beforeS) {
        setStatus(t("การ์ดนี้มีในกล่อง AI เตรียมไว้แล้ว", "This card is already in the AI prepared box"));
      } else {
        setStatus(t("ใส่ในกล่อง AI เตรียมไว้แล้ว ยังไม่ส่งออก", "Added to AI prepared — not sent yet"));
      }
      var sugBox = document.getElementById("ai_suggest_box");
      if (sugBox && sugBox.scrollIntoView) {
        sugBox.scrollIntoView({ block: "nearest" });
      }
      return;
    }
    var beforeB = attach.length;
    attach = pushUnique(attach, p);
    syncAttach();
    if (attach.length === beforeB) {
      setStatus(t("การ์ดนี้มีในช่องส่งแล้ว", "This card is already in the send box"));
    } else {
      setStatus(t("ใส่ในช่องส่งถึงลูกค้าแล้ว ยังไม่ส่งออก", "Added to Send to buyer — not sent yet"));
    }
    var box = document.getElementById("ai_send_form");
    if (box && box.scrollIntoView) {
      box.scrollIntoView({ block: "nearest" });
    }
  }

  function addAttach(p) {
    addObject(p, insertTarget());
  }

  function load(q) {
    var my = ++seq;
    setStatus(t("กำลังค้นในร้าน…", "Searching this shop…"));
    $.ajax({
      url: base + "ai/product_suggest",
      data: { thread_id: threadId, q: q || "" },
      dataType: "json"
    })
      .done(function (res) {
        if (my !== seq) {
          return;
        }
        if (!res || !res.ok) {
          $strip.empty();
          setStatus(t("ค้นสินค้าไม่สำเร็จ", "Product search failed"));
          return;
        }
        render(res.items || []);
      })
      .fail(function () {
        if (my !== seq) {
          return;
        }
        $strip.empty();
        setStatus(t("ค้นสินค้าไม่สำเร็จ", "Product search failed"));
      });
  }

  $q.on("input", function () {
    var val = $.trim($q.val() || "");
    clearTimeout(timer);
    timer = setTimeout(function () {
      load(val);
    }, 250);
  });

  $strip.on("click", ".ai-prod-card", function () {
    addAttach({
      kind: "product",
      id: $(this).attr("data-id") || "",
      name: $(this).attr("data-name") || "",
      sku: $(this).attr("data-sku") || "",
      image: $(this).attr("data-image") || ""
    });
  });

  $(".ai-tools").on("click", ".ai-order-insert", function (e) {
    e.preventDefault();
    var $card = $(this).closest(".ai-order-card");
    addAttach(objectFromEl($card));
  });

  $attachBox.on("click", ".ai-compose-x", function (e) {
    e.preventDefault();
    var key = $(this).closest(".ai-compose-card").attr("data-key");
    attach = attach.filter(function (p) {
      return objectKey(p) !== String(key);
    });
    syncAttach();
  });

  $coachAttachBox.on("click", ".ai-compose-x", function (e) {
    e.preventDefault();
    var key = $(this).closest(".ai-compose-card").attr("data-key");
    coachAttach = coachAttach.filter(function (p) {
      return objectKey(p) !== String(key);
    });
    syncCoachAttach();
  });

  $suggestAttachBox.on("click", ".ai-compose-x", function (e) {
    e.preventDefault();
    var key = $(this).closest(".ai-compose-card").attr("data-key");
    suggestAttach = suggestAttach.filter(function (p) {
      return objectKey(p) !== String(key);
    });
    syncSuggestAttach();
  });

  function coachErr(code) {
    if (code === "empty_coach") {
      return t("พิมพ์ข้อความถึง AI ก่อน", "Type a message to the AI first");
    }
    if (code === "no_inbound") {
      return t("ยังไม่มีข้อความลูกค้าในห้องนี้", "This thread has no buyer message yet");
    }
    if (code === "missing_api_key") {
      return t("ยังไม่มี API key — ไปใส่ที่ตั้งค่าโมเดลแชท", "API key is missing — add it in Chat AI settings");
    }
    if (code === "http_fail") {
      return t("เรียกโมเดลไม่สำเร็จ ตรวจเน็ตหรือคีย์", "Model request failed. Check network or API key.");
    }
    return code || t("ปรึกษา AI ไม่สำเร็จ", "AI discussion failed");
  }

  function nl(s) {
    return esc(s).replace(/\n/g, "<br>");
  }

  function objectsHtml(list) {
    list = list || [];
    return list
      .map(function (p) {
        var kind = p.kind === "order" ? "order" : "product";
        var label = kind === "order" ? p.id : p.sku || p.id;
        var name = kind === "order" ? p.status || p.name || "" : p.name || "";
        return (
          '<div class="ai-obj-card" data-kind="' +
          esc(kind) +
          '" data-id="' +
          esc(p.id) +
          '" data-name="' +
          esc(p.name || "") +
          '" data-sku="' +
          esc(p.sku || "") +
          '" data-image="' +
          esc(p.image || "") +
          '" data-status="' +
          esc(p.status || "") +
          '" data-items="' +
          esc(p.items || "") +
          '">' +
          (kind === "product" && p.image
            ? '<img src="' + esc(p.image) + '" alt="">'
            : '<div class="ai-prod-ph">' + (kind === "order" ? "🧾" : "📦") + "</div>") +
          "<div><div class=\"ai-prod-sku\">" +
          esc(label) +
          "</div>" +
          (name ? '<div class="ai-prod-name">' + esc(name) + "</div>" : "") +
          "</div></div>"
        );
      })
      .join("");
  }

  function copyBtn() {
    return (
      '<button type="button" class="btn btn-default btn-xs ai-copy-to-send">' +
      esc(t("คัดลอกไปช่องส่งลูกค้า", "Copy to send box")) +
      "</button>"
    );
  }

  function appendCoach(role, body, objects) {
    var isAdmin = role === "admin";
    objects = objects || [];
    var copyText = isAdmin ? "" : ($suggest.val() || body);
    var html =
      '<div class="ai-msg ' +
      (isAdmin ? "admin" : "ai") +
      '"><div class="ai-msg-col"><div class="ai-msg-who">' +
      esc(isAdmin ? t("แอดมิน", "Admin") : "AI") +
      '</div><div class="ai-msg-bubble ai-copy-src" data-text="' +
      esc(copyText) +
      '" data-attach=\'' +
      JSON.stringify(objects).replace(/'/g, "&#39;") +
      "'><div>" +
      nl(body) +
      "</div>" +
      objectsHtml(objects) +
      copyBtn() +
      "</div></div></div>";
    $coachList.append(html);
    $coachList.scrollTop($coachList.prop("scrollHeight"));
  }

  function metaLine(meta) {
    if (!meta) {
      return "";
    }
    var parts = [];
    if (meta.engine) {
      parts.push(meta.engine);
    }
    parts.push(t("คู่มือ", "playbook") + " " + (meta.playbook ? t("มี", "yes") : t("ยังไม่มี", "none")));
    parts.push(t("ตัวอย่าง", "examples") + " " + (meta.examples || 0));
    parts.push(t("ออเดอร์", "orders") + " " + (meta.orders || 0));
    parts.push(t("สินค้า", "products") + " " + (meta.products || 0));
    return t("ดึงจากฐานร้าน: ", "From shop database: ") + parts.join(" · ");
  }

  function setSuggest(text, objects) {
    var val = text || "";
    $suggest.val(val);
    persistOriginal(val);
    if (objects) {
      suggestAttach = [];
      (objects || []).forEach(function (p) {
        suggestAttach = pushUnique(suggestAttach, p);
      });
      syncSuggestAttach();
    }
  }

  var $coachForm = $("#ai_coach_form");
  var $coachList = $("#ai_coach_list");
  var $coachStatus = $("#ai_coach_status");
  var $coachBody = $("#ai_coach_body");
  var $coachSend = $("#ai_coach_send");
  var $suggest = $("#ai_suggest_box");
  var $draftHidden = $("#ai_draft_hidden");
  var $useSuggest = $("#ai_use_suggest");
  var coachBusy = false;
  var coachHint = t(
    "พิมพ์แล้วกด Enter หรือปุ่มส่งถึง AI — Shift+Enter เพื่อขึ้นบรรทัดใหม่",
    "Press Enter or Send to AI. Shift+Enter for a new line."
  );

  function setCoachStatus(msg, busy, html) {
    if (html) {
      $coachStatus.html(html);
    } else {
      $coachStatus.text(msg || "");
    }
    $coachStatus.toggleClass("is-on", !!(msg || html));
    $coachStatus.toggleClass("is-err", !!html);
    $coachSend.prop("disabled", !!busy);
    $coachBody.prop("disabled", !!busy);
  }

  function coachErrHtml(code) {
    if (code === "missing_api_key") {
      return esc(t("ยังไม่มี API key ของโมเดล จึงตอบไม่ได้ — ", "The model API key is missing, so the AI cannot reply — ")) +
        '<a href="' +
        esc(base) +
        'ai/settings">' +
        esc(t("ไปหน้าตั้งค่าโมเดลแชท", "Open Chat AI settings")) +
        "</a>";
    }
    return "";
  }

  $coachList.scrollTop($coachList.prop("scrollHeight"));

  $useSuggest.on("click", function () {
    var val = $.trim($suggest.val() || "");
    if (!val && !suggestAttach.length) {
      setCoachStatus(t("ยังไม่มีข้อความที่ AI เตรียมไว้", "There is no AI-prepared reply yet"));
      return;
    }
    $sendForm.find("textarea[name=body]").val(val);
    persistOriginal(originalDraft);
    attach = [];
    suggestAttach.forEach(function (p) {
      attach = pushUnique(attach, p);
    });
    syncAttach();
    $sendForm.trigger("submit");
  });

  function sendCoach() {
    var msg = $.trim($coachBody.val() || "");
    if (!msg && !coachAttach.length) {
      setCoachStatus(t("พิมพ์ข้อความถึง AI หรือใส่การ์ดสินค้า/ออเดอร์ก่อน", "Type a message to the AI or attach a product/order card first"));
      return;
    }
    if (coachBusy) {
      return;
    }
    coachBusy = true;
    setCoachStatus(t("กำลังดึงข้อมูลจากฐานร้าน แล้วปรึกษา AI… รอได้ประมาณครึ่งนาที", "Reading the shop database, then asking the AI… this can take about 30 seconds"), true);
    $.ajax({
      url: base + "ai/coach",
      type: "POST",
      dataType: "json",
      timeout: 120000,
      data: {
        thread_id: threadId,
        body: msg,
        attach_json: JSON.stringify(coachAttach)
      }
    })
      .done(function (res) {
        appendCoach("admin", msg || t("แนบการ์ด", "Attached cards"), coachAttach.slice());
        $coachBody.val("");
        coachAttach = [];
        syncCoachAttach();
        if (!res || !res.ok) {
          var html = coachErrHtml(res && res.error ? res.error : "");
          if (html) {
            setCoachStatus("", false, html);
          } else {
            setCoachStatus(coachErr(res && res.error ? res.error : ""));
          }
          return;
        }
        if (res.reply || (res.objects && res.objects.length)) {
          setSuggest(res.reply || "", res.objects || []);
        }
        if (res.discuss) {
          appendCoach("ai", res.discuss, res.objects || []);
        }
        setCoachStatus(metaLine(res.meta) || t("AI ตอบในกล่องนี้แล้ว ลูกค้ายังไม่เห็น", "The AI replied here. The buyer cannot see this."));
      })
      .fail(function (xhr) {
        var msgFail = t("ปรึกษา AI ไม่สำเร็จ", "AI discussion failed");
        if (xhr && xhr.statusText === "timeout") {
          msgFail = t("รอโมเดลนานเกิน ลองกดส่งถึง AI อีกครั้ง", "The model took too long. Click Send to AI again.");
        } else if (xhr && xhr.status === 0) {
          msgFail = t("ต่อกับเซิร์ฟเวอร์ไม่ได้", "Could not reach the server");
        } else if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
          var htmlFail = coachErrHtml(xhr.responseJSON.error);
          if (htmlFail) {
            setCoachStatus("", false, htmlFail);
            return;
          }
          msgFail = coachErr(xhr.responseJSON.error);
        } else if (xhr && xhr.status) {
          msgFail = t("เซิร์ฟเวอร์ตอบไม่สำเร็จ (", "Server error (") + xhr.status + ")";
        }
        setCoachStatus(msgFail);
      })
      .always(function () {
        coachBusy = false;
        $coachSend.prop("disabled", false);
        $coachBody.prop("disabled", false);
      });
  }

  $coachForm.on("submit", function (e) {
    e.preventDefault();
    sendCoach();
  });

  $coachBody.on("keydown", function (e) {
    var key = e.key || "";
    var isEnter = key === "Enter" || e.which === 13;
    if (!isEnter || e.shiftKey) {
      return;
    }
    if (e.isComposing || e.keyCode === 229) {
      return;
    }
    e.preventDefault();
    sendCoach();
  });

  $page.on("click", ".ai-copy-to-send", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $src = $(this).closest(".ai-copy-src");
    var text = $src.attr("data-text") || "";
    var objs = [];
    try {
      objs = JSON.parse($src.attr("data-attach") || "[]");
    } catch (err) {
      objs = [];
    }
    if (text) {
      $sendForm.find("textarea[name=body]").val(text);
    }
    (objs || []).forEach(function (p) {
      attach = pushUnique(attach, p);
    });
    syncAttach();
    setStatus(t("คัดลอกไปช่องส่งถึงลูกค้าแล้ว ยังไม่ส่งออก", "Copied to Send to buyer — not sent yet"));
    var box = document.getElementById("ai_send_form");
    if (box && box.scrollIntoView) {
      box.scrollIntoView({ block: "nearest" });
    }
  });

  $page.on("click", ".ai-obj-card", function (e) {
    if ($(e.target).closest(".ai-copy-to-send").length) {
      return;
    }
    addObject(objectFromEl($(this)), "buyer");
  });

  $sendForm.on("submit", function (e) {
    syncAttach();
    persistOriginal(originalDraft);
    var text = $.trim($sendForm.find("textarea[name=body]").val() || "");
    if (!text && !attach.length) {
      e.preventDefault();
      setStatus(t("พิมพ์ข้อความ หรือใส่การ์ดสินค้า/ออเดอร์ก่อน แล้วค่อยกดส่งถึงลูกค้า", "Type a reply or add a product/order card first, then click Send to buyer"));
    }
  });

  var plusTarget = "buyer";
  var plusKind = "product";
  var plusTimer = null;
  var plusSeq = 0;
  var $plusModal = $("#ai_plus_modal");
  var $plusHome = $("#ai_plus_home");
  var $plusSearch = $("#ai_plus_search");
  var $plusQ = $("#ai_plus_q");
  var $plusList = $("#ai_plus_list");
  var $plusStatus = $("#ai_plus_status");
  var $plusTitle = $("#ai_plus_search_title");

  function closePlus() {
    $plusModal.attr("hidden", "hidden");
    $plusHome.removeAttr("hidden");
    $plusSearch.attr("hidden", "hidden");
    $plusQ.val("");
    $plusList.empty();
    plusSeq += 1;
  }

  function openPlus(target) {
    plusTarget = target === "coach" || target === "suggest" ? target : "buyer";
    $plusSearch.attr("hidden", "hidden");
    $plusHome.removeAttr("hidden");
    $plusModal.removeAttr("hidden");
  }

  function plusItemHtml(p) {
    var kind = p.kind === "order" ? "order" : "product";
    var label = kind === "order" ? p.id : p.sku || p.id;
    var name = kind === "order"
      ? ((p.status || "") + (p.items ? " · " + p.items : ""))
      : (p.name || "");
    var img = kind === "product" && p.image
      ? '<img src="' + esc(p.image) + '" alt="">'
      : '<div class="ai-prod-ph">' + (kind === "order" ? "🧾" : "📦") + "</div>";
    return (
      '<label class="ai-plus-row">' +
      '<input type="checkbox" class="ai-plus-check">' +
      img +
      '<div data-kind="' +
      esc(kind) +
      '" data-id="' +
      esc(p.id || "") +
      '" data-name="' +
      esc(p.name || "") +
      '" data-sku="' +
      esc(p.sku || "") +
      '" data-image="' +
      esc(p.image || "") +
      '" data-status="' +
      esc(p.status || "") +
      '" data-items="' +
      esc(p.items || "") +
      '"><div class="ai-prod-sku">' +
      esc(label) +
      "</div>" +
      (name ? '<div class="ai-prod-name">' + esc(name) + "</div>" : "") +
      "</div></label>"
    );
  }

  function loadPlus(q) {
    var my = ++plusSeq;
    var isOrder = plusKind === "order";
    $plusStatus.text(
      isOrder
        ? t("กำลังค้นออเดอร์ของลูกค้าคนนี้…", "Searching this buyer’s orders…")
        : t("กำลังค้นสินค้าในร้าน…", "Searching this shop…")
    );
    $.ajax({
      url: base + (isOrder ? "ai/order_suggest" : "ai/product_suggest"),
      data: { thread_id: threadId, q: q || "" },
      dataType: "json"
    })
      .done(function (res) {
        if (my !== plusSeq) {
          return;
        }
        if (!res || !res.ok) {
          $plusList.empty();
          $plusStatus.text(t("ค้นไม่สำเร็จ", "Search failed"));
          return;
        }
        var items = res.items || [];
        if (!items.length) {
          $plusList.empty();
          $plusStatus.text(
            isOrder
              ? t("ไม่มีออเดอร์ของลูกค้าคนนี้ที่ตรงกับที่พิมพ์", "No orders for this buyer match the search")
              : t("ไม่พบสินค้าในร้านที่ตรงกับที่พิมพ์", "No matching shop products")
          );
          return;
        }
        $plusStatus.text("");
        $plusList.html(
          items
            .map(function (p) {
              p.kind = isOrder ? "order" : "product";
              if (!p.id && p.order_id) {
                p.id = p.order_id;
              }
              return plusItemHtml(p);
            })
            .join("")
        );
      })
      .fail(function () {
        if (my !== plusSeq) {
          return;
        }
        $plusList.empty();
        $plusStatus.text(t("ค้นไม่สำเร็จ", "Search failed"));
      });
  }

  function showPlusSearch(kind) {
    plusKind = kind === "order" ? "order" : "product";
    $plusHome.attr("hidden", "hidden");
    $plusSearch.removeAttr("hidden");
    $plusTitle.text(
      plusKind === "order"
        ? t("ออเดอร์ของลูกค้าคนนี้", "This buyer’s orders")
        : t("สินค้าทั้งร้าน", "Shop products")
    );
    $plusQ.attr(
      "placeholder",
      plusKind === "order"
        ? t("ค้นเลขออเดอร์หรือชื่อสินค้าในออเดอร์", "Search order id or item name")
        : t("ค้นรหัสหรือชื่อสินค้า เช่น S2000", "Search SKU or name, e.g. S2000")
    );
    $plusQ.val("");
    $plusList.empty();
    loadPlus("");
    setTimeout(function () {
      $plusQ.trigger("focus");
    }, 50);
  }

  $page.on("click", ".ai-plus-btn", function (e) {
    e.preventDefault();
    openPlus($(this).attr("data-plus"));
  });
  $("#ai_plus_close").on("click", function (e) {
    e.preventDefault();
    closePlus();
  });
  $plusModal.on("click", function (e) {
    if (e.target === this) {
      closePlus();
    }
  });
  $("#ai_plus_back").on("click", function (e) {
    e.preventDefault();
    $plusSearch.attr("hidden", "hidden");
    $plusHome.removeAttr("hidden");
    $plusQ.val("");
    $plusList.empty();
  });
  $plusHome.on("click", ".ai-plus-tile", function () {
    showPlusSearch($(this).attr("data-kind"));
  });
  $plusQ.on("input", function () {
    var val = $.trim($plusQ.val() || "");
    clearTimeout(plusTimer);
    plusTimer = setTimeout(function () {
      loadPlus(val);
    }, 250);
  });
  $("#ai_plus_apply").on("click", function () {
    var n = 0;
    $plusList.find(".ai-plus-row").each(function () {
      var $row = $(this);
      if (!$row.find(".ai-plus-check").prop("checked")) {
        return;
      }
      addObject(objectFromEl($row.find("[data-id]").first()), plusTarget);
      n += 1;
    });
    if (!n) {
      $plusStatus.text(t("เลือกอย่างน้อยหนึ่งรายการ", "Select at least one item"));
      return;
    }
    closePlus();
  });
  $(document).on("keydown", function (e) {
    var key = e.key || "";
    if ((key === "Escape" || e.which === 27) && !$plusModal.attr("hidden")) {
      closePlus();
    }
  });

  function parseBnyClip(text) {
    var s = text == null ? "" : String(text);
    var i = s.lastIndexOf("[[BNYCHAT]]");
    if (i < 0) {
      return null;
    }
    try {
      var data = JSON.parse(s.slice(i + 11));
      if (!data || typeof data !== "object") {
        return null;
      }
      var vis = $.trim(s.slice(0, i));
      return {
        text: vis !== "" ? vis : (data.text || ""),
        objects: $.isArray(data.objects) ? data.objects : []
      };
    } catch (err) {
      return null;
    }
  }

  function insertAtCursor(el, text) {
    if (!el) {
      return;
    }
    text = text == null ? "" : String(text);
    var start = el.selectionStart != null ? el.selectionStart : String(el.value || "").length;
    var end = el.selectionEnd != null ? el.selectionEnd : start;
    var v = el.value || "";
    el.value = v.slice(0, start) + text + v.slice(end);
    var pos = start + text.length;
    if (el.setSelectionRange) {
      try {
        el.setSelectionRange(pos, pos);
      } catch (err6) {}
    }
  }

  function composeWhere(el) {
    if (!el) {
      return "";
    }
    if (el.id === "ai_coach_body") {
      return "coach";
    }
    if (el.id === "ai_suggest_box") {
      return "suggest";
    }
    if (el.id === "ai_buyer_body" || ($(el).attr("name") === "body" && $(el).closest("#ai_send_form").length)) {
      return "buyer";
    }
    return "";
  }

  function collectCopyPayload() {
    var sel = window.getSelection ? window.getSelection() : null;
    var text = sel ? String(sel.toString() || "") : "";
    var objects = [];
    var seen = {};
    function addP(p) {
      if (!p || !p.id) {
        return;
      }
      var k = objectKey(p);
      if (seen[k]) {
        return;
      }
      seen[k] = 1;
      objects.push(p);
    }
    function addEl(node) {
      var $el = $(node);
      if ($el.hasClass("ai-prod-card") && !$el.attr("data-kind")) {
        addP({
          kind: "product",
          id: $el.attr("data-id") || "",
          name: $el.attr("data-name") || "",
          sku: $el.attr("data-sku") || "",
          image: $el.attr("data-image") || ""
        });
        return;
      }
      if (
        $el.hasClass("ai-obj-card") ||
        $el.hasClass("ai-compose-card") ||
        $el.hasClass("ai-order-card") ||
        $el.hasClass("ai-prod-card")
      ) {
        addP(objectFromEl($el));
      }
    }
    var active = document.activeElement;
    var where = composeWhere(active);
    if (where && active && (active.tagName === "TEXTAREA" || active.tagName === "INPUT")) {
      var aStart = active.selectionStart;
      var aEnd = active.selectionEnd;
      if (aStart != null && aEnd != null && aEnd > aStart) {
        text = String(active.value || "").slice(aStart, aEnd);
      }
    }
    if (sel && sel.rangeCount) {
      var range = sel.getRangeAt(0);
      var node = range.commonAncestorContainer;
      if (node && node.nodeType === 3) {
        node = node.parentNode;
      }
      var $root = $(node);
      var $src = $root.closest(".ai-copy-src");
      if ($src.length) {
        if (!text) {
          text = $src.attr("data-text") || "";
        }
        try {
          (JSON.parse($src.attr("data-attach") || "[]") || []).forEach(addP);
        } catch (err3) {}
      }
      var $scope = $root.closest(".ai-copy-src, .ai-compose-attach, .ai-hitl-col, .ai-coach, .ai-msg, .ai-tools");
      if (!$scope.length) {
        $scope = $root;
      }
      $scope.find(".ai-obj-card, .ai-compose-card, .ai-order-card, .ai-prod-card").each(function () {
        var include = true;
        try {
          if (range.intersectsNode) {
            include = range.intersectsNode(this);
          }
        } catch (err4) {}
        if (include) {
          addEl(this);
        }
      });
    }
    if (!objects.length && active) {
      var $card = $(active).closest(".ai-obj-card, .ai-compose-card, .ai-order-card, .ai-prod-card");
      if ($card.length) {
        addEl($card[0]);
      }
    }
    return { text: text, objects: objects };
  }

  document.addEventListener("copy", function (e) {
    var inPage = false;
    if ($page[0] && e.target && $page[0].contains(e.target)) {
      inPage = true;
    }
    try {
      var sel = window.getSelection();
      if (sel && sel.rangeCount) {
        var n = sel.getRangeAt(0).commonAncestorContainer;
        if (n && n.nodeType === 3) {
          n = n.parentNode;
        }
        if ($page[0] && n && $page[0].contains(n)) {
          inPage = true;
        }
      }
    } catch (err5) {}
    if (!inPage) {
      return;
    }
    var payload = collectCopyPayload();
    if (!payload.objects.length) {
      return;
    }
    var vis = payload.text || "";
    var packed =
      vis +
      (vis ? "\n\n" : "") +
      "[[BNYCHAT]]" +
      JSON.stringify({ text: payload.text || "", objects: payload.objects });
    if (e.clipboardData) {
      e.preventDefault();
      e.clipboardData.setData("text/plain", packed);
    }
  });

  $("#ai_coach_body, #ai_suggest_box, #ai_buyer_body").on("paste", function (e) {
    var clip = e.originalEvent && e.originalEvent.clipboardData;
    var raw = clip ? clip.getData("text/plain") : "";
    var parsed = parseBnyClip(raw);
    if (!parsed) {
      return;
    }
    e.preventDefault();
    insertAtCursor(this, parsed.text || "");
    var where = composeWhere(this);
    (parsed.objects || []).forEach(function (p) {
      addObject(p, where);
    });
    setStatus(t("วางข้อความและการ์ดแล้ว ยังไม่ส่งออก", "Pasted text and cards — not sent yet"));
  });

  try {
    var boot = $page.attr("data-attach") || "[]";
    var parsed = JSON.parse(boot);
    if ($.isArray(parsed)) {
      attach = parsed;
    }
  } catch (err) {
    attach = [];
  }
  try {
    var sboot = $page.attr("data-suggest-attach") || "[]";
    var sparsed = JSON.parse(sboot);
    if ($.isArray(sparsed)) {
      suggestAttach = [];
      sparsed.forEach(function (p) {
        suggestAttach = pushUnique(suggestAttach, p);
      });
    }
  } catch (err2) {
    suggestAttach = [];
  }
  persistOriginal(String($page.attr("data-original-draft") || ""));
  syncAttach();
  syncCoachAttach();
  syncSuggestAttach();
  load($.trim($q.val() || ""));
});
