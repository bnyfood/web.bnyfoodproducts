(function (window, $) {
  function paren(n) {
    n = parseInt(n, 10) || 0;
    return n > 0 ? " (" + n + ")" : "";
  }

  function apply(counts) {
    counts = counts || {};
    var all = counts.all != null ? counts.all : 0;
    $("[data-chat-badge='all']").text(paren(all));
    $("[data-chat-badge='chat']").text(paren(all));
  }

  function badgeUrl() {
    if (typeof hostname_site === "string" && hostname_site) {
      return hostname_site.replace(/\/?$/, "/") + "ai/unreplied";
    }
    return "/ai/unreplied";
  }

  window.BNYChatBadge = { apply: apply };

  $(function () {
    if (!$("[data-chat-badge]").length) {
      return;
    }
    function load() {
      $.ajax({
        url: badgeUrl(),
        dataType: "json",
        cache: false
      }).done(function (data) {
        if (data && data.ok) {
          apply(data.counts);
        }
      });
    }
    load();
    setInterval(load, 20000);
  });
})(window, jQuery);
