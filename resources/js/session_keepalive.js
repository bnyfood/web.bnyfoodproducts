(function ($) {
  "use strict";

  var remaining = 0;
  var timerId = null;
  var timedOut = false;
  var syncId = null;

  function pad2(n) {
    n = Math.floor(Math.abs(n));
    return n < 10 ? "0" + n : String(n);
  }

  function formatHms(sec) {
    sec = Math.max(0, Math.floor(sec));
    var h = Math.floor(sec / 3600);
    var m = Math.floor((sec % 3600) / 60);
    var s = sec % 60;
    return pad2(h) + ":" + pad2(m) + ":" + pad2(s);
  }

  function renderCountdown() {
    var $el = $("#session_countdown");
    if (!$el.length) return;
    $el.text(formatHms(remaining));
    $el.toggleClass("is-warning", remaining > 0 && remaining <= 300);
    $el.toggleClass("is-danger", remaining > 0 && remaining <= 60);
  }

  function $modal() {
    return $("#session_timeout_modal");
  }

  /** In-page Bootstrap <div> modal — never window.open */
  function showTimeoutModal() {
    var $m = $modal();
    if (!$m.length) return;
    if (timedOut && $m.hasClass("show")) return;
    timedOut = true;
    $("body").addClass("session-locked");
    $("#session_relogin_error").hide().text("");
    $("#session_relogin_password").val("");
    // Move modal to <body> so it is not trapped in layout
    if ($m.parent()[0] !== document.body) {
      $m.appendTo(document.body);
    }
    if (typeof $m.modal === "function") {
      $m.modal({
        backdrop: "static",
        keyboard: false,
        show: true
      });
    } else {
      $m.addClass("show").css("display", "block").attr("aria-hidden", "false");
      if (!$(".session-modal-backdrop").length) {
        $("<div class='modal-backdrop fade show session-modal-backdrop'></div>").appendTo(document.body);
      }
    }
  }

  function hideTimeoutModal() {
    timedOut = false;
    $("body").removeClass("session-locked");
    var $m = $modal();
    if (!$m.length) return;
    if (typeof $m.modal === "function") {
      $m.modal("hide");
    } else {
      $m.removeClass("show").css("display", "none").attr("aria-hidden", "true");
      $(".session-modal-backdrop").remove();
    }
    $("#session_relogin_error").hide().text("");
  }

  function tick() {
    if (remaining <= 0) {
      renderCountdown();
      showTimeoutModal();
      return;
    }
    remaining = Math.max(0, remaining - 1);
    renderCountdown();
    if (remaining <= 0) {
      showTimeoutModal();
    }
  }

  function applyRemaining(sec) {
    remaining = Math.max(0, parseInt(sec, 10) || 0);
    renderCountdown();
    if (remaining <= 0) {
      showTimeoutModal();
    } else {
      // Session alive — never show login UI
      hideTimeoutModal();
    }
  }

  function refreshSession() {
    var $btn = $("#session_refresh_btn");
    $btn.css("opacity", 0.5);
    $.ajax({
      type: "POST",
      url: hostname_site + "/users/session_refresh",
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          applyRemaining(res.remaining);
        } else {
          applyRemaining(0);
        }
      },
      error: function () {
        applyRemaining(0);
      },
      complete: function () {
        $btn.css("opacity", 1);
      }
    });
  }

  function syncStatus() {
    $.ajax({
      type: "GET",
      url: hostname_site + "/users/session_status",
      dataType: "json",
      success: function (res) {
        if (res && typeof res.remaining !== "undefined") {
          applyRemaining(res.remaining);
        }
      }
    });
  }

  function currentReturnPath() {
    var path = window.location.pathname || "";
    var search = window.location.search || "";
    if (typeof path_project !== "undefined" && path_project && path.indexOf("/" + path_project + "/") === 0) {
      path = path.substring(("/" + path_project).length);
    }
    return (path.replace(/^\//, "") + search).replace(/^\//, "");
  }

  /** Same tab only — never a new browser window */
  function googleRelogin() {
    var ret = encodeURIComponent(currentReturnPath());
    window.location.assign(hostname_site + "/users/session_google_relogin?return_to=" + ret);
  }

  function doRelogin() {
    var email = $.trim($("#session_relogin_email").val());
    var pass = $("#session_relogin_password").val();
    var $err = $("#session_relogin_error");
    if (!email || !pass) {
      $err.text("Enter email and password.").show();
      return;
    }
    $("#session_relogin_btn").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: hostname_site + "/users/ajax_relogin",
      data: { txt_email: email, txt_password: pass },
      dataType: "json",
      success: function (res) {
        if (res && res.Status === "Success") {
          hideTimeoutModal();
          applyRemaining(res.remaining || 0);
          syncStatus();
        } else {
          $err.text((res && res.Message) || "Login failed").show();
          setTimeout(function () {
            window.location.assign(hostname_site + "/");
          }, 1200);
        }
      },
      error: function () {
        $err.text("Login failed").show();
        setTimeout(function () {
          window.location.assign(hostname_site + "/");
        }, 1200);
      },
      complete: function () {
        $("#session_relogin_btn").prop("disabled", false);
      }
    });
  }

  $(document).ready(function () {
    var $bar = $("#session_toolbar");
    if (!$bar.length) return;

    // Ensure modal starts hidden while session is alive
    hideTimeoutModal();

    remaining = parseInt($bar.attr("data-remaining"), 10) || 0;
    renderCountdown();
    syncStatus();

    timerId = setInterval(tick, 1000);
    syncId = setInterval(syncStatus, 60000);

    $("#session_refresh_btn").on("click", function (e) {
      e.preventDefault();
      refreshSession();
    });

    $("#session_google_relogin_btn").on("click", function (e) {
      e.preventDefault();
      googleRelogin();
    });

    $("#session_relogin_btn").on("click", function () {
      doRelogin();
    });

    $("#session_relogin_password, #session_relogin_email").on("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        doRelogin();
      }
    });
  });
})(jQuery);
