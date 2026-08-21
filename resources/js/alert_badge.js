(function (window, $) {
  function paren(n) {
    n = parseInt(n, 10) || 0;
    return n > 0 ? " (" + n + ")" : "";
  }

  function apply(data) {
    data = data || {};
    var topics = data.topics != null ? data.topics : 0;
    var shops = data.token_shops != null ? data.token_shops : 0;
    var statusIssues = data.status_issues != null ? data.status_issues : 0;
    $("[data-alert-badge='topics']").text(paren(topics));
    $("[data-alert-badge='token']").text(paren(shops));
    $("[data-alert-badge='status']").text(paren(statusIssues));
  }

  function badgeUrl() {
    if (typeof hostname_site === "string" && hostname_site) {
      return hostname_site.replace(/\/?$/, "/") + "alerts/summary";
    }
    return "/alerts/summary";
  }

  window.BNYAlertBadge = { apply: apply };

  $(function () {
    if (!$("[data-alert-badge]").length) {
      return;
    }
    function load() {
      $.ajax({
        url: badgeUrl(),
        dataType: "json",
        cache: false
      }).done(function (data) {
        if (data && data.ok) {
          apply(data);
        }
      });
    }
    load();
    setInterval(load, 60000);
  });
})(window, jQuery);
