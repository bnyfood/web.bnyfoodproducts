$(function () {
  var $nav = $(".dashboard-nav");
  var $list = $nav.find(".dashboard-nav-list").first();
  var $up = $nav.find(".nav-scroll-hint-up");
  var $down = $nav.find(".nav-scroll-hint-down");
  if (!$nav.length || !$list.length) {
    return;
  }

  var enterTimer = null;
  var hideTimer = null;
  var FOCUS_DELAY = 450;
  var SHOW_MS = 1400;

  function canScroll() {
    var el = $list[0];
    var top = el.scrollTop;
    var max = el.scrollHeight - el.clientHeight;
    return {
      up: top > 4,
      down: max > 4 && top < max - 4
    };
  }

  function hideHints() {
    $up.removeClass("is-on");
    $down.removeClass("is-on");
  }

  function showHints() {
    var dir = canScroll();
    hideHints();
    if (dir.up) {
      $up.addClass("is-on");
    }
    if (dir.down) {
      $down.addClass("is-on");
    }
    if (dir.up || dir.down) {
      clearTimeout(hideTimer);
      hideTimer = setTimeout(hideHints, SHOW_MS);
    }
  }

  $nav.on("mouseenter focusin", function () {
    clearTimeout(enterTimer);
    clearTimeout(hideTimer);
    hideHints();
    enterTimer = setTimeout(showHints, FOCUS_DELAY);
  });

  $nav.on("mouseleave focusout", function (e) {
    if (e.type === "focusout" && $nav[0].contains(e.relatedTarget)) {
      return;
    }
    clearTimeout(enterTimer);
    clearTimeout(hideTimer);
    hideHints();
  });
});
