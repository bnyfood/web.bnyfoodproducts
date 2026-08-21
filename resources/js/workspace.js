(function (window, $) {
  var STORE_KEY = "bny_ws_docs_v1";
  var cfg = {};
  var loaded = {};

  function readCfg() {
    var el = document.getElementById("bny_ws_cfg");
    if (!el) {
      return {};
    }
    try {
      return JSON.parse(el.textContent || "{}");
    } catch (e) {
      return {};
    }
  }

  function loadState() {
    try {
      var raw = sessionStorage.getItem(STORE_KEY);
      var data = raw ? JSON.parse(raw) : null;
      if (!data || typeof data !== "object") {
        data = { workspaces: {}, activeWs: "" };
      }
      if (!data.workspaces) {
        data.workspaces = {};
      }
      return data;
    } catch (e) {
      return { workspaces: {}, activeWs: "" };
    }
  }

  function saveState(state) {
    try {
      sessionStorage.setItem(STORE_KEY, JSON.stringify(state));
    } catch (e) {}
  }

  function sameId(a, b) {
    return String(a == null ? "" : a) === String(b == null ? "" : b);
  }

  function ensureWs(state) {
    if (!cfg.wsId) {
      return null;
    }
    if (!state.workspaces[cfg.wsId]) {
      state.workspaces[cfg.wsId] = {
        id: cfg.wsId,
        name: cfg.wsName || "",
        pageUrl: cfg.pageUrl || "",
        groups: {}
      };
    }
    var ws = state.workspaces[cfg.wsId];
    ws.name = cfg.wsName || ws.name;
    ws.pageUrl = cfg.pageUrl || ws.pageUrl;
    if (!ws.groups) {
      ws.groups = {};
    }
    if (cfg.menuId && !ws.groups[cfg.menuId]) {
      ws.groups[cfg.menuId] = {
        menuId: cfg.menuId,
        menuTitle: cfg.menuTitle || "",
        pageUrl: cfg.pageUrl || "",
        crumbPath: cfg.crumbPath || [],
        docs: [],
        active: ""
      };
    }
    if (cfg.menuId && ws.groups[cfg.menuId]) {
      var g = ws.groups[cfg.menuId];
      g.menuTitle = cfg.menuTitle || g.menuTitle;
      g.pageUrl = cfg.pageUrl || g.pageUrl;
      if (cfg.crumbPath && cfg.crumbPath.length) {
        g.crumbPath = cfg.crumbPath;
      }
    }
    state.activeWs = cfg.wsId;
    return ws;
  }

  function t(key) {
    var i18n = cfg.i18n || {};
    if (i18n[key]) {
      return i18n[key];
    }
    if (cfg.lang === "th") {
      if (key === "close_tab") {
        return "ปิดแถบ";
      }
      if (key === "close_all_tabs") {
        return "ปิดทุกแถบ";
      }
      if (key === "docs_in") {
        return "เอกสารใน";
      }
      if (key === "docs_this_page") {
        return "หน้านี้";
      }
    }
    if (key === "close_tab") {
      return "Close tab";
    }
    if (key === "close_all_tabs") {
      return "Close all tabs";
    }
    if (key === "docs_in") {
      return "Documents in";
    }
    if (key === "docs_this_page") {
      return "This page";
    }
    return key;
  }

  function makeCloseAllBtn(level) {
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "bny-ws-close-all";
    btn.setAttribute("data-act", "close-all");
    btn.setAttribute("data-level", level);
    btn.textContent = t("close_all_tabs");
    btn.title = t("close_all_tabs");
    return btn;
  }

  function makeTabCloseBtn() {
    var closeBtn = document.createElement("button");
    closeBtn.type = "button";
    closeBtn.className = "bny-doc-tab-x";
    closeBtn.setAttribute("data-act", "close");
    closeBtn.title = t("close_tab");
    closeBtn.textContent = "×";
    return closeBtn;
  }

  function currentGroup(state) {
    if (!cfg.wsId || !cfg.menuId) {
      return null;
    }
    var ws = state.workspaces[cfg.wsId];
    if (!ws || !ws.groups) {
      return null;
    }
    return ws.groups[cfg.menuId] || null;
  }

  function isCurrentGroup(wsId, groupId) {
    return sameId(cfg.wsId, wsId) && sameId(cfg.menuId, groupId);
  }

  function crumbPrefix() {
    var parts = cfg.crumbPath;
    if (!parts || !parts.length) {
      return "";
    }
    return parts.filter(Boolean).join(" › ");
  }

  function popupTitle(url) {
    var s = String(url || "");
    var more = s.match(/(?:_more|\/more)\/([^/?#]+)/i);
    if (more && more[1]) {
      try {
        return decodeURIComponent(more[1]);
      } catch (e) {
        return more[1];
      }
    }
    var byDate = s.match(/by_date\/(\d{4}-\d{2}-\d{2})/i);
    if (byDate) {
      return byDate[1];
    }
    var day = s.match(/(\d{4}-\d{2}-\d{2})(?:\/|$)/);
    if (day) {
      return day[1];
    }
    if (/_month|\/month/i.test(s)) {
      return cfg.lang === "en" ? "Whole month" : "ทั้งเดือน";
    }
    var parts = s.replace(/[?#].*$/, "").split("/").filter(Boolean);
    var last = parts.length ? parts[parts.length - 1] : "";
    if (last && last.length > 2 && last.length < 80 && !/^(original|copy|none)$/i.test(last) && !/^\d{1,2}$/.test(last)) {
      try {
        return decodeURIComponent(last);
      } catch (e2) {
        return last;
      }
    }
    return cfg.lang === "en" ? "Document" : "เอกสาร";
  }

  function criteriaOf(doc) {
    if (!doc) {
      return "";
    }
    var rest = doc.criteria || doc.title || "";
    if (!rest || rest === "รายละเอียด" || rest === "เอกสาร" || rest === "Document") {
      var fromUrl = popupTitle(doc.url);
      if (fromUrl) {
        return fromUrl;
      }
    }
    return rest;
  }

  function groupShortName(ws, group) {
    if (group && group.crumbPath && group.crumbPath.length) {
      return group.crumbPath[group.crumbPath.length - 1];
    }
    return (group && group.menuTitle) || (ws && ws.name) || "";
  }

  function docsInLabel(ws, group) {
    var name = groupShortName(ws, group);
    var prefix = t("docs_in");
    if (!name) {
      return prefix;
    }
    if (cfg.lang === "en") {
      return prefix + " " + name;
    }
    return prefix + name;
  }

  function docTabLabel(doc) {
    var prefix = crumbPrefix();
    var rest = criteriaOf(doc);
    if (prefix && rest) {
      if (rest.indexOf(prefix) === 0) {
        return rest;
      }
      return prefix + " › " + rest;
    }
    return prefix || rest;
  }

  function groupCrumbLabel(ws, group) {
    if (group && group.crumbPath && group.crumbPath.length) {
      return group.crumbPath.filter(Boolean).join(" › ");
    }
    var parts = [];
    if (ws && ws.name) {
      parts.push(ws.name);
    }
    if (group && group.menuTitle && (!ws || group.menuTitle !== ws.name)) {
      parts.push(group.menuTitle);
    }
    return parts.join(" › ") || (group && group.menuTitle) || "";
  }

  function listOtherGroups(state) {
    var out = [];
    Object.keys(state.workspaces || {}).forEach(function (wsId) {
      var ws = state.workspaces[wsId];
      if (!ws || !ws.groups) {
        return;
      }
      Object.keys(ws.groups).forEach(function (gid) {
        var g = ws.groups[gid];
        if (!g || !g.docs || !g.docs.length) {
          return;
        }
        if (isCurrentGroup(wsId, gid)) {
          return;
        }
        out.push({ wsId: wsId, groupId: gid, ws: ws, group: g });
      });
    });
    return out;
  }

  function focusedOther(state, others) {
    var f = state.otherFocus || {};
    var i;
    for (i = 0; i < others.length; i++) {
      if (sameId(others[i].wsId, f.wsId) && sameId(others[i].groupId, f.groupId)) {
        return others[i];
      }
    }
    return others[0] || null;
  }

  function clickAct(e) {
    var act = e.target && e.target.getAttribute && e.target.getAttribute("data-act");
    if (!act && e.target && e.target.closest) {
      var btn = e.target.closest("[data-act]");
      act = btn ? btn.getAttribute("data-act") : "";
    }
    return act || "";
  }

  function renderOtherBars(state) {
    var wrap = document.getElementById("bny_ws_other");
    var groupsEl = document.getElementById("bny_ws_level1");
    var docsEl = document.getElementById("bny_ws_other_docs");
    if (!groupsEl) {
      return;
    }
    var others = listOtherGroups(state);
    var focus = focusedOther(state, others);
    if (focus && (!state.otherFocus || !sameId(state.otherFocus.wsId, focus.wsId) || !sameId(state.otherFocus.groupId, focus.groupId))) {
      state.otherFocus = { wsId: focus.wsId, groupId: focus.groupId };
      saveState(state);
    }
    groupsEl.innerHTML = "";
    if (docsEl) {
      docsEl.innerHTML = "";
    }
    if (!others.length) {
      if (wrap) {
        wrap.className = "bny-ws-other";
      }
      groupsEl.className = "bny-ws-level1";
      if (docsEl) {
        docsEl.className = "bny-doc-tabs bny-ws-other-docs";
      }
      return;
    }
    if (wrap) {
      wrap.className = "bny-ws-other has-other";
    }
    groupsEl.className = "bny-ws-level1 has-other";
    groupsEl.appendChild(makeCloseAllBtn("ws"));
    others.forEach(function (item) {
      var isFocus = !!(focus && sameId(item.wsId, focus.wsId) && sameId(item.groupId, focus.groupId));
      var btn = document.createElement("a");
      btn.href = item.group.pageUrl || "#";
      btn.className = "bny-ws-lv1-tab" + (isFocus ? " is-active" : "");
      btn.setAttribute("data-ws-id", item.wsId);
      btn.setAttribute("data-group-id", item.groupId);
      var name = document.createElement("span");
      name.className = "bny-ws-lv1-name";
      name.textContent = groupCrumbLabel(item.ws, item.group);
      name.title = name.textContent;
      btn.appendChild(name);
      btn.appendChild(makeTabCloseBtn());
      groupsEl.appendChild(btn);
    });
    if (!docsEl || !focus) {
      return;
    }
    var docs = focus.group.docs || [];
    if (!docs.length) {
      docsEl.className = "bny-doc-tabs bny-ws-other-docs";
      return;
    }
    docsEl.className = "bny-doc-tabs bny-ws-other-docs has-tabs";
    var nestLabel = document.createElement("span");
    nestLabel.className = "bny-ws-nest-label";
    nestLabel.textContent = docsInLabel(focus.ws, focus.group);
    nestLabel.title = groupCrumbLabel(focus.ws, focus.group);
    docsEl.appendChild(nestLabel);
    docs.forEach(function (doc) {
      var tab = document.createElement("a");
      tab.href = focus.group.pageUrl || "#";
      tab.className = "bny-doc-tab" + (focus.group.active === doc.id ? " is-active" : "");
      tab.setAttribute("data-ws-id", focus.wsId);
      tab.setAttribute("data-group-id", focus.groupId);
      tab.setAttribute("data-doc-id", doc.id);
      var title = document.createElement("span");
      title.className = "bny-doc-tab-title";
      title.textContent = criteriaOf(doc);
      title.title = criteriaOf(doc);
      tab.appendChild(title);
      tab.appendChild(makeTabCloseBtn());
      docsEl.appendChild(tab);
    });
  }

  function renderDocTabs(state) {
    var wrap = document.getElementById("bny_doc_tabs");
    if (!wrap) {
      return;
    }
    var group = currentGroup(state);
    var docs = group && group.docs ? group.docs : [];
    wrap.innerHTML = "";
    if (!docs.length) {
      wrap.className = "bny-doc-tabs";
      return;
    }
    wrap.className = "bny-doc-tabs has-tabs";
    var hereLabel = document.createElement("span");
    hereLabel.className = "bny-ws-nest-label bny-ws-here-label";
    hereLabel.textContent = t("docs_this_page");
    wrap.appendChild(hereLabel);
    wrap.appendChild(makeCloseAllBtn("doc"));
    docs.forEach(function (doc) {
      var tab = document.createElement("div");
      tab.className = "bny-doc-tab" + (group.active === doc.id ? " is-active" : "");
      tab.setAttribute("data-doc-id", doc.id);
      var title = document.createElement("span");
      title.className = "bny-doc-tab-title";
      title.textContent = docTabLabel(doc);
      title.title = docTabLabel(doc);
      var printBtn = document.createElement("button");
      printBtn.type = "button";
      printBtn.className = "bny-doc-tab-print";
      printBtn.setAttribute("data-act", "print");
      printBtn.title = "Print";
      printBtn.innerHTML = '<i class="fas fa-print"></i>';
      tab.appendChild(title);
      tab.appendChild(printBtn);
      tab.appendChild(makeTabCloseBtn());
      wrap.appendChild(tab);
    });
  }

  function emptyStageMessage() {
    var stage = document.getElementById("bny_doc_stage");
    if (!stage) {
      return;
    }
    var empty = stage.querySelector(".bny-doc-empty");
    if (!empty) {
      return;
    }
    var hasPane = stage.querySelector(".bny-doc-pane");
    empty.style.display = hasPane ? "none" : "block";
  }

  function setLoading(on) {
    var el = document.getElementById("bny_doc_loading");
    if (!el) {
      return;
    }
    if (on) {
      el.classList.add("is-on");
    } else {
      el.classList.remove("is-on");
    }
  }

  function activatePane(id) {
    var stage = document.getElementById("bny_doc_stage");
    if (!stage) {
      return;
    }
    $(stage).find(".bny-doc-pane").removeClass("is-active");
    var pane = document.getElementById("bny_doc_pane_" + id);
    if (pane) {
      pane.classList.add("is-active");
    }
    emptyStageMessage();
  }

  function printDoc(id) {
    var frame = loaded[id];
    if (frame && frame.contentWindow) {
      frame.contentWindow.focus();
      frame.contentWindow.print();
    }
  }

  function removePane(id) {
    var pane = document.getElementById("bny_doc_pane_" + id);
    if (pane && pane.parentNode) {
      pane.parentNode.removeChild(pane);
    }
    delete loaded[id];
  }

  function refreshChrome(state) {
    renderOtherBars(state);
    renderDocTabs(state);
  }

  function closeDoc(id) {
    var state = loadState();
    var group = currentGroup(state);
    if (!group) {
      return;
    }
    group.docs = (group.docs || []).filter(function (d) { return d.id !== id; });
    if (group.active === id) {
      group.active = group.docs.length ? group.docs[group.docs.length - 1].id : "";
    }
    saveState(state);
    removePane(id);
    refreshChrome(state);
    if (group.active) {
      showDoc(group.active, false);
    } else {
      emptyStageMessage();
    }
  }

  function closeAllDocs() {
    var state = loadState();
    var group = currentGroup(state);
    if (!group) {
      return;
    }
    (group.docs || []).forEach(function (d) {
      removePane(d.id);
    });
    group.docs = [];
    group.active = "";
    saveState(state);
    refreshChrome(state);
    emptyStageMessage();
  }

  function closeOtherGroup(wsId, groupId) {
    if (isCurrentGroup(wsId, groupId)) {
      closeAllDocs();
      return;
    }
    var state = loadState();
    var ws = state.workspaces[wsId];
    if (!ws || !ws.groups || !ws.groups[groupId]) {
      return;
    }
    ws.groups[groupId].docs = [];
    ws.groups[groupId].active = "";
    if (state.otherFocus && sameId(state.otherFocus.wsId, wsId) && sameId(state.otherFocus.groupId, groupId)) {
      state.otherFocus = null;
    }
    saveState(state);
    renderOtherBars(state);
  }

  function closeAllOtherGroups() {
    var state = loadState();
    Object.keys(state.workspaces || {}).forEach(function (wsId) {
      var ws = state.workspaces[wsId];
      if (!ws || !ws.groups) {
        return;
      }
      Object.keys(ws.groups).forEach(function (gid) {
        if (isCurrentGroup(wsId, gid)) {
          return;
        }
        ws.groups[gid].docs = [];
        ws.groups[gid].active = "";
      });
    });
    state.otherFocus = null;
    saveState(state);
    renderOtherBars(state);
  }

  function closeOtherGroupDocs(wsId, groupId) {
    if (isCurrentGroup(wsId, groupId)) {
      closeAllDocs();
      return;
    }
    closeOtherGroup(wsId, groupId);
  }

  function closeOtherDoc(wsId, groupId, docId) {
    if (isCurrentGroup(wsId, groupId)) {
      closeDoc(docId);
      return;
    }
    var state = loadState();
    var ws = state.workspaces[wsId];
    var group = ws && ws.groups ? ws.groups[groupId] : null;
    if (!group) {
      return;
    }
    group.docs = (group.docs || []).filter(function (d) { return d.id !== docId; });
    if (group.active === docId) {
      group.active = group.docs.length ? group.docs[group.docs.length - 1].id : "";
    }
    if (!group.docs.length && state.otherFocus && sameId(state.otherFocus.wsId, wsId) && sameId(state.otherFocus.groupId, groupId)) {
      state.otherFocus = null;
    }
    saveState(state);
    renderOtherBars(state);
  }

  function samePage(url) {
    if (!url) {
      return true;
    }
    try {
      var a = new URL(url, window.location.href);
      var b = new URL(window.location.href);
      return a.pathname.replace(/\/+$/, "") === b.pathname.replace(/\/+$/, "");
    } catch (e) {
      return url === window.location.href;
    }
  }

  function goToGroupDoc(wsId, groupId, docId) {
    var state = loadState();
    var ws = state.workspaces[wsId];
    var group = ws && ws.groups ? ws.groups[groupId] : null;
    if (!group) {
      return;
    }
    var wasFocus = !!(state.otherFocus && sameId(state.otherFocus.wsId, wsId) && sameId(state.otherFocus.groupId, groupId));
    state.otherFocus = { wsId: wsId, groupId: groupId };
    if (docId) {
      group.active = docId;
    }
    saveState(state);
    if (!docId && !wasFocus) {
      renderOtherBars(state);
      return;
    }
    if (isCurrentGroup(wsId, groupId) && samePage(group.pageUrl)) {
      if (group.active) {
        showDoc(group.active, false);
      }
      refreshChrome(state);
      return;
    }
    if (group.pageUrl) {
      window.location.href = group.pageUrl;
    }
  }

  function hookIframeOpen(frame) {
    try {
      var win = frame.contentWindow;
      if (!win) {
        return;
      }
      var nativeOpen = win.__bnyNativeOpen || win.open;
      win.__bnyNativeOpen = nativeOpen;
      win.open = function (url) {
        if (url && url !== "about:blank") {
          openDocument({
            url: url,
            criteria: popupTitle(url)
          });
          return win;
        }
        return nativeOpen.apply(win, arguments);
      };
    } catch (e) {}
  }

  function injectPrintFix(doc) {
    if (!doc || !doc.head || doc.getElementById("bny_print_fix")) {
      return;
    }
    var style = doc.createElement("style");
    style.id = "bny_print_fix";
    style.textContent = [
      "@media print {",
      "  table { height: auto !important; }",
      "  table table { page-break-before: auto !important; page-break-after: auto !important; height: auto !important; }",
      "  tr { page-break-inside: auto !important; }",
      "  thead { display: table-row-group !important; }",
      "  body > center > table:first-of-type,",
      "  body > table:first-of-type,",
      "  .cn-sheet:first-of-type { page-break-before: auto !important; }",
      "}"
    ].join("\n");
    doc.head.appendChild(style);
  }

  function bindAiDebug(doc) {
    doc = doc || document;
    if (!doc || !doc.querySelectorAll) {
      return;
    }
    var K = "bny_ai_dbg_on";
    function isOn() {
      try {
        return localStorage.getItem(K) === "1";
      } catch (e) {
        return false;
      }
    }
    function apply() {
      var on = isOn();
      var wraps = doc.querySelectorAll("[data-bny-ai-debug-wrap]");
      for (var i = 0; i < wraps.length; i++) {
        var w = wraps[i];
        var chk = w.querySelector(".bny-ai-debug-chk");
        var body = w.querySelector(".bny-ai-debug");
        if (chk) {
          chk.checked = on;
        }
        if (on) {
          w.classList.add("is-on");
          if (body) {
            body.removeAttribute("hidden");
          }
        } else {
          w.classList.remove("is-on");
          if (body) {
            body.setAttribute("hidden", "hidden");
          }
        }
      }
    }
    if (!doc.documentElement.getAttribute("data-bny-ai-dbg-bound")) {
      doc.documentElement.setAttribute("data-bny-ai-dbg-bound", "1");
      doc.addEventListener("change", function (e) {
        var t = e.target;
        if (!t || !t.classList || !t.classList.contains("bny-ai-debug-chk")) {
          return;
        }
        try {
          localStorage.setItem(K, t.checked ? "1" : "0");
        } catch (err) {}
        apply();
        try {
          if (doc !== document) {
            bindAiDebug(document);
          }
        } catch (e2) {}
        try {
          var frames = document.querySelectorAll("iframe.bny-doc-frame");
          for (var fi = 0; fi < frames.length; fi++) {
            var fd = frames[fi].contentDocument;
            if (fd && fd !== doc) {
              bindAiDebug(fd);
            }
          }
        } catch (e3) {}
      });
    }
    apply();
  }

  function parseAiDebugFromUrl(url) {
    var bits = [];
    try {
      var abs = new URL(url, window.location.href);
      var segs = abs.pathname.replace(/^\/+|\/+$/g, "").split("/");
      // accounting / controller / method / ...
      var i0 = 0;
      if (segs[0] === "index.php") {
        i0 = 1;
      }
      if (segs[i0]) {
        bits.push("d=" + segs[i0]);
      }
      if (segs[i0 + 1]) {
        bits.push("c=" + segs[i0 + 1]);
      }
      if (segs[i0 + 2]) {
        bits.push("m=" + segs[i0 + 2]);
      }
      for (var i = i0 + 3; i < segs.length; i++) {
        if (segs[i]) {
          bits.push("s" + (i - i0) + "=" + decodeURIComponent(segs[i]));
        }
      }
      if (abs.search && abs.search.length > 1) {
        bits.push("q=" + abs.search.slice(1));
      }
      bits.push("u=" + abs.pathname + abs.search);
    } catch (e) {
      bits.push("u=" + String(url || ""));
    }
    return bits.join(" · ");
  }

  function ensureAiDebugCss(doc) {
    if (!doc.head || doc.getElementById("bny_ai_dbg_css")) {
      return;
    }
    var style = doc.createElement("style");
    style.id = "bny_ai_dbg_css";
    style.textContent = [
      ".bny-ai-debug-wrap{display:inline-flex;flex-wrap:wrap;align-items:center;gap:6px 8px;margin:0 0 0 8px;padding:0;vertical-align:middle;}",
      ".bny-ai-debug-tog{margin:0;color:#b0b8c0;font-size:11px;font-weight:400;cursor:pointer;white-space:nowrap;user-select:none;}",
      ".bny-ai-debug-tog input{margin:0 3px 0 0;vertical-align:-1px;}",
      ".bny-ai-debug{display:none;color:#9aa3ad;font-size:11px;line-height:1.35;word-break:break-all;user-select:text;margin-left:8px;}",
      ".bny-ai-debug[hidden]{display:none!important;}",
      ".bny-ai-debug-wrap.is-on .bny-ai-debug{display:inline;}"
    ].join("");
    doc.head.appendChild(style);
  }

  function ensureAiDebugBar(doc, url) {
    if (!doc || !doc.body) {
      return;
    }
    ensureAiDebugCss(doc);
    var existing = doc.querySelector("[data-bny-ai-debug-wrap]");
    var lineText = parseAiDebugFromUrl(url || "");
    if (existing) {
      var body = existing.querySelector(".bny-ai-debug");
      if (body && lineText) {
        var cur = (body.textContent || "").trim();
        if (!cur) {
          body.textContent = lineText;
        } else if (cur.indexOf("u=") < 0) {
          body.textContent = cur + " · " + lineText;
        }
      }
      placeAiDebugAfterPrint(doc, existing);
      return;
    }
    var wrap = doc.createElement("div");
    wrap.className = "bny-ai-debug-wrap no-print";
    wrap.setAttribute("data-bny-ai-debug-wrap", "1");
    wrap.style.cssText = "display:inline-flex;align-items:center;margin-left:8px;padding:0;vertical-align:middle;";
    wrap.innerHTML = '<label class="bny-ai-debug-tog" title="AI debug" style="margin:0;">' +
      '<input type="checkbox" class="bny-ai-debug-chk" autocomplete="off"> dbg</label>' +
      '<div class="bny-ai-debug" data-bny-ai-debug="1" hidden style="margin-left:8px;"></div>';
    var lineEl = wrap.querySelector(".bny-ai-debug");
    if (lineEl) {
      lineEl.textContent = lineText;
    }
    placeAiDebugAfterPrint(doc, wrap);
  }

  function placeAiDebugAfterPrint(doc, wrap) {
    if (!wrap) {
      return;
    }
    var printBtn = doc.querySelector(
      "#cols_print, #cols_print_orders, #cols_print_daily, button.preset-btn#cols_print, .col-picker button#cols_print"
    );
    if (!printBtn) {
      var buttons = doc.querySelectorAll(".col-picker button, .col-picker .preset-btn");
      for (var i = 0; i < buttons.length; i++) {
        var t = (buttons[i].textContent || "").replace(/\s+/g, "");
        if (t.indexOf("พิมพ์") >= 0) {
          printBtn = buttons[i];
        }
      }
      // prefer last print-ish in the first button row
      for (var j = buttons.length - 1; j >= 0; j--) {
        var tj = (buttons[j].textContent || "").replace(/\s+/g, "");
        if (tj === "พิมพ์" || tj.indexOf("พิมพ์รายออเดอร์") >= 0 || tj.indexOf("พิมพ์สรุป") >= 0) {
          printBtn = buttons[j];
          if (tj === "พิมพ์") {
            break;
          }
        }
      }
    }
    if (printBtn && printBtn.parentNode) {
      if (wrap.parentNode !== printBtn.parentNode || wrap.previousSibling !== printBtn) {
        if (printBtn.nextSibling) {
          printBtn.parentNode.insertBefore(wrap, printBtn.nextSibling);
        } else {
          printBtn.parentNode.appendChild(wrap);
        }
      }
      return;
    }
    var picker = doc.querySelector(".col-picker");
    if (picker) {
      picker.insertBefore(wrap, picker.firstChild);
      return;
    }
    if (!wrap.parentNode) {
      if (doc.body.firstChild) {
        doc.body.insertBefore(wrap, doc.body.firstChild);
      } else {
        doc.body.appendChild(wrap);
      }
    }
  }

  function writeFrame(frame, html, url) {
    var doc = frame.contentDocument || (frame.contentWindow && frame.contentWindow.document);
    if (!doc) {
      return;
    }
    doc.open();
    doc.write(html);
    doc.close();
    injectPrintFix(doc);
    hookIframeOpen(frame);
    ensureAiDebugBar(doc, url || frame.getAttribute("data-doc-url") || "");
    bindAiDebug(doc);
  }

  function ensureHostShell() {
    if (document.getElementById("bny_doc_stage")) {
      return true;
    }
    var page = document.querySelector(".bny-ws-page");
    if (!page) {
      return false;
    }
    var host = document.createElement("div");
    host.className = "dashboard-content bny-doc-host";
    host.setAttribute("data-bny-doc-host", "1");
    host.setAttribute("data-bny-injected", "1");
    host.innerHTML = '<div class="bny-doc-tabs" id="bny_doc_tabs"></div>' +
      '<div class="bny-doc-stage" id="bny_doc_stage">' +
      '<div class="bny-doc-empty" style="display:none"></div>' +
      '<div class="bny-doc-loading" id="bny_doc_loading">กำลังโหลดเอกสาร…</div>' +
      "</div>";
    page.appendChild(host);
    return true;
  }

  function ensurePane(doc) {
    if (!ensureHostShell()) {
      return null;
    }
    var stage = document.getElementById("bny_doc_stage");
    if (!stage) {
      return null;
    }
    var pane = document.getElementById("bny_doc_pane_" + doc.id);
    if (!pane) {
      pane = document.createElement("div");
      pane.className = "bny-doc-pane";
      pane.id = "bny_doc_pane_" + doc.id;
      var frame = document.createElement("iframe");
      frame.className = "bny-doc-frame";
      frame.setAttribute("title", doc.title);
      pane.appendChild(frame);
      stage.appendChild(pane);
      loaded[doc.id] = frame;
    }
    if (!loaded[doc.id]) {
      loaded[doc.id] = pane.querySelector("iframe");
    }
    return pane;
  }

  function fetchDoc(doc, force) {
    var frame = loaded[doc.id];
    if (!frame) {
      return;
    }
    if (frame.getAttribute("data-loaded") === "1" && !force) {
      activatePane(doc.id);
      return;
    }
    setLoading(true);
    frame.setAttribute("data-doc-url", doc.url || "");
    $.ajax({
      url: doc.url,
      type: "GET",
      dataType: "html",
      cache: false
    }).done(function (html) {
      writeFrame(frame, html, doc.url);
      frame.setAttribute("data-loaded", "1");
      activatePane(doc.id);
    }).fail(function () {
      writeFrame(frame, "<p style='padding:16px'>โหลดเอกสารไม่สำเร็จ</p>", doc.url);
      activatePane(doc.id);
    }).always(function () {
      setLoading(false);
    });
  }

  function showDoc(id, force) {
    var state = loadState();
    var group = currentGroup(state);
    if (!group) {
      return;
    }
    var doc = null;
    (group.docs || []).forEach(function (d) {
      if (d.id === id) {
        doc = d;
      }
    });
    if (!doc) {
      return;
    }
    group.active = id;
    saveState(state);
    refreshChrome(state);
    ensurePane(doc);
    fetchDoc(doc, !!force);
  }

  function openDocument(opts) {
    var state = loadState();
    ensureWs(state);
    var group = currentGroup(state);
    if (!group) {
      return;
    }
    var id = opts.id || ("d_" + String(opts.url || "").replace(/[^a-zA-Z0-9]+/g, "_").slice(-80));
    var found = null;
    (group.docs || []).forEach(function (d) {
      if (d.id === id || d.url === opts.url) {
        found = d;
      }
    });
    if (found) {
      found.title = opts.title || found.title;
      found.criteria = opts.criteria || found.criteria || found.title;
      found.url = opts.url || found.url;
      group.active = found.id;
      saveState(state);
      refreshChrome(state);
      ensurePane(found);
      fetchDoc(found, true);
      return;
    }
    var doc = {
      id: id,
      title: opts.title || "Document",
      criteria: opts.criteria || opts.title || "",
      url: opts.url
    };
    group.docs.push(doc);
    group.active = doc.id;
    saveState(state);
    refreshChrome(state);
    ensurePane(doc);
    fetchDoc(doc, true);
  }

  function encodeDateRange(value) {
    var text = String(value || "");
    text = text.replace(/\//g, "sl");
    text = text.replace(/ /g, "sp");
    text = text.replace(/-/g, "hp");
    return text;
  }

  function shortRange(value) {
    var text = String(value || "").replace(/\s+/g, " ").trim();
    var parts = text.split(" - ");
    function ymd(v) {
      var bits = (v || "").split("/");
      if (bits.length >= 2) {
        return bits[0] + "/" + bits[1];
      }
      return v;
    }
    if (parts.length === 2) {
      return ymd(parts[0]) + " - " + ymd(parts[1]);
    }
    return text;
  }

  function platformLabel(value) {
    var sel = document.getElementById("platform");
    if (sel && sel.options && sel.selectedIndex >= 0) {
      var opt = sel.options[sel.selectedIndex];
      if (opt && String(opt.value) === String(value) && opt.text) {
        return String(opt.text).replace(/\s+/g, " ").trim();
      }
    }
    var map = { "0": "Lazada", "1": "Shopee", "2": "Tiktok", "3": "BigSauces" };
    return map[String(value)] || String(value || "");
  }

  function slotValue(key) {
    if (key === "daterange") {
      return encodeDateRange($("#daterange").val() || "");
    }
    if (key === "ordernumber") {
      var n = String($("#ordernumber").val() || "").replace(/^\s+|\s+$/g, "");
      return n ? n : "none";
    }
    if (key === "search_type") {
      var st = $("select#search_type").val();
      if (st == null || st === "") {
        st = $("#search_type").val();
      }
      return st || "1";
    }
    if (key === "voidtype") {
      return $("input[name='voidtype']:checked").val() || "2";
    }
    if (key === "datesearch") {
      return $("#datesearch").val() || "";
    }
    if (key === "platform") {
      return $("#platform").val() || "0";
    }
    var el = document.getElementById(key);
    return el ? String(el.value || "") : "";
  }

  function buildMakeUrl(host) {
    var makeUrl = host.getAttribute("data-make-url") || "";
    var path = host.getAttribute("data-make-path") || "{platform}/{daterange}";
    return makeUrl + path.replace(/\{([a-zA-Z0-9_]+)\}/g, function (_, key) {
      return slotValue(key);
    });
  }

  function hostCriteria() {
    var parts = [];
    var plat = platformLabel($("#platform").val());
    if (plat) {
      parts.push(plat);
    }
    var ttype = $("input[name='taxinvoicetype']:checked").val();
    if (ttype === "2") {
      parts.push("ABB");
    } else if (ttype === "3") {
      parts.push("Full");
    }
    var st = $("select#search_type").val();
    var order = String($("#ordernumber").val() || "").replace(/^\s+|\s+$/g, "");
    var range = $("#daterange").val() || $("#datesearch").val() || "";
    if (order && st !== "1") {
      parts.push(order);
    }
    if (range && st !== "2" && st !== "4") {
      parts.push(shortRange(range));
    }
    return parts.join(" ");
  }

  function bindHost() {
    $(document).on("submit", "[data-bny-doc-host] #product_search", function (e) {
      var host = e.currentTarget && e.currentTarget.closest
        ? e.currentTarget.closest("[data-bny-doc-host]")
        : document.querySelector("[data-bny-doc-host]");
      e.preventDefault();
      var btn = host ? host.querySelector("[data-bny-doc-search]") : null;
      if (btn) {
        $(btn).trigger("click");
      }
      return false;
    });
    $(document).on("click", "[data-bny-doc-host] [data-bny-doc-search]", function (e) {
      var host = this.closest ? this.closest("[data-bny-doc-host]") : document.querySelector("[data-bny-doc-host]");
      if (!host || !host.getAttribute("data-make-url")) {
        return;
      }
      e.preventDefault();
      e.stopImmediatePropagation();
      var url = buildMakeUrl(host);
      var criteria = hostCriteria();
      openDocument({
        title: docTabLabel({ criteria: criteria }),
        criteria: criteria,
        url: url
      });
      return false;
    });
    $(document).on("click", "#bny_doc_tabs .bny-ws-close-all", function (e) {
      e.preventDefault();
      e.stopPropagation();
      closeAllDocs();
    });
    $(document).on("click", "#bny_doc_tabs .bny-doc-tab", function (e) {
      var id = this.getAttribute("data-doc-id");
      var act = clickAct(e);
      if (act === "close") {
        e.preventDefault();
        e.stopPropagation();
        closeDoc(id);
        return;
      }
      if (act === "print") {
        e.preventDefault();
        e.stopPropagation();
        printDoc(id);
        return;
      }
      showDoc(id, false);
    });
  }

  function bindOtherBars() {
    $(document).on("click", "#bny_ws_level1 .bny-ws-close-all", function (e) {
      e.preventDefault();
      e.stopPropagation();
      closeAllOtherGroups();
    });
    $(document).on("click", "#bny_ws_level1 .bny-ws-lv1-tab", function (e) {
      var act = clickAct(e);
      var wsId = this.getAttribute("data-ws-id");
      var groupId = this.getAttribute("data-group-id");
      if (act === "close") {
        e.preventDefault();
        e.stopPropagation();
        closeOtherGroup(wsId, groupId);
        return;
      }
      e.preventDefault();
      goToGroupDoc(wsId, groupId, null);
    });
    $(document).on("click", "#bny_ws_other_docs .bny-ws-close-all", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var state = loadState();
      var others = listOtherGroups(state);
      var focus = focusedOther(state, others);
      if (focus) {
        closeOtherGroupDocs(focus.wsId, focus.groupId);
      }
    });
    $(document).on("click", "#bny_ws_other_docs .bny-doc-tab", function (e) {
      var act = clickAct(e);
      var wsId = this.getAttribute("data-ws-id");
      var groupId = this.getAttribute("data-group-id");
      var docId = this.getAttribute("data-doc-id");
      if (act === "close") {
        e.preventDefault();
        e.stopPropagation();
        closeOtherDoc(wsId, groupId, docId);
        return;
      }
      e.preventDefault();
      goToGroupDoc(wsId, groupId, docId);
    });
  }

  function shouldCaptureOpen(url, name, features) {
    if (!url || url === "about:blank") {
      return false;
    }
    var feat = String(features || "");
    if (!/width\s*=/i.test(feat)) {
      return false;
    }
    try {
      var abs = new URL(url, window.location.href);
      if (abs.origin !== window.location.origin) {
        return false;
      }
    } catch (e) {
      return false;
    }
    return !!(cfg.wsId && cfg.menuId);
  }

  function patchWindowOpen() {
    if (window.__bnyNativeOpen) {
      return;
    }
    var nativeOpen = window.open;
    window.__bnyNativeOpen = nativeOpen;
    window.open = function (url, name, features) {
      if (shouldCaptureOpen(url, name, features)) {
        openDocument({
          url: url,
          criteria: hostCriteria() || popupTitle(url)
        });
        return window;
      }
      return nativeOpen.apply(this, arguments);
    };
  }

  function init() {
    cfg = readCfg();
    var state = loadState();
    ensureWs(state);
    saveState(state);
    refreshChrome(state);
    bindHost();
    bindOtherBars();
    patchWindowOpen();
    var group = currentGroup(state);
    if (document.querySelector("[data-bny-doc-host]") && group && group.active) {
      showDoc(group.active, false);
    } else {
      emptyStageMessage();
    }
  }

  window.BnyWorkspace = {
    openDocument: openDocument,
    encodeDateRange: encodeDateRange,
    platformLabel: platformLabel,
    shortRange: shortRange
  };

  $(init);
})(window, window.jQuery);
