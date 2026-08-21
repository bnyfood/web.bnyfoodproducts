(function (window, $) {
  var salesChart = null;
  var adsChart = null;
  var feesChart = null;
  var comboChart = null;

  function isEn() {
    return $("#dash_daterange").attr("data-lang") === "en";
  }

  function money(n) {
    var v = Number(n || 0);
    return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function parseRange() {
    var text = String($("#dash_daterange").val() || "");
    var parts = text.split(" - ");
    function iso(v) {
      var bits = (v || "").trim().split("/");
      if (bits.length === 3) {
        return bits[2] + "-" + bits[0] + "-" + bits[1];
      }
      return "";
    }
    return {
      from: iso(parts[0]),
      to: iso(parts[1] || parts[0])
    };
  }

  function picker() {
    return $("#dash_daterange").data("daterangepicker") || null;
  }

  function openPicker() {
    var drp = picker();
    if (drp) {
      drp.show();
    }
  }

  function setRangeDays(days) {
    var end = window.moment ? moment() : null;
    var start = window.moment ? moment().subtract(days - 1, "days") : null;
    var drp = picker();
    if (drp && start && end) {
      drp.setStartDate(start);
      drp.setEndDate(end);
    }
    if (start && end) {
      $("#dash_daterange").val(start.format("MM/DD/YYYY") + " - " + end.format("MM/DD/YYYY"));
    }
    $(".dash-range-btns .btn").removeClass("is-on");
    $('.dash-range-btns .btn[data-days="' + days + '"]').addClass("is-on");
    loadOverview();
  }

  function stackedCfg(labels, series, yLabel) {
    return {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          { label: "Lazada", backgroundColor: "rgba(15,20,109,0.85)", data: series.lazada, stack: "p" },
          { label: "Shopee", backgroundColor: "rgba(238,77,45,0.85)", data: series.shopee, stack: "p" },
          { label: "TikTok", backgroundColor: "rgba(17,17,17,0.85)", data: series.tiktok, stack: "p" }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { position: "bottom" },
        tooltips: { mode: "index", intersect: false },
        scales: {
          xAxes: [{ stacked: true, scaleLabel: { display: true, labelString: isEn() ? "Date" : "วันที่" } }],
          yAxes: [{ stacked: true, ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: yLabel } }]
        }
      }
    };
  }

  function comboCfg(labels, series) {
    var en = isEn();
    return {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: en ? "Revenue" : "รายได้",
            backgroundColor: "rgba(46,125,50,0.82)",
            data: series.sales,
            stack: "rev",
            yAxisID: "y-thb",
            order: 2
          },
          {
            label: en ? "Page ads" : "เพจแอด",
            backgroundColor: "rgba(238,77,45,0.85)",
            data: series.ads,
            stack: "exp",
            yAxisID: "y-thb",
            order: 2
          },
          {
            label: en ? "Fees" : "ค่าธรรมเนียม",
            backgroundColor: "rgba(123,31,162,0.75)",
            data: series.fees,
            stack: "exp",
            yAxisID: "y-thb",
            order: 2
          },
          {
            type: "line",
            label: en ? "Return (x)" : "รีเทิร์น (เท่า)",
            borderColor: "#0f146d",
            backgroundColor: "transparent",
            fill: false,
            data: series.return,
            yAxisID: "y-ret",
            order: 1,
            lineTension: 0.15,
            pointRadius: 3,
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { position: "bottom" },
        tooltips: { mode: "index", intersect: false },
        scales: {
          xAxes: [{ stacked: true, scaleLabel: { display: true, labelString: en ? "Date" : "วันที่" } }],
          yAxes: [
            {
              id: "y-thb",
              position: "left",
              stacked: true,
              ticks: { beginAtZero: true },
              scaleLabel: { display: true, labelString: "THB" }
            },
            {
              id: "y-ret",
              position: "right",
              stacked: false,
              ticks: { beginAtZero: true },
              gridLines: { drawOnChartArea: false },
              scaleLabel: { display: true, labelString: en ? "Return (x)" : "รีเทิร์น (เท่า)" }
            }
          ]
        }
      }
    };
  }

  function fillKpis(prefix, totals) {
    totals = totals || {};
    $(prefix + "-laz").text(money(totals.lazada));
    $(prefix + "-sho").text(money(totals.shopee));
    $(prefix + "-tik").text(money(totals.tiktok));
    $(prefix + "-all").text(money(totals.all));
  }

  function fillCombo(totals) {
    totals = totals || {};
    $("#combo-sales").text(money(totals.sales));
    $("#combo-ads").text(money(totals.ads));
    $("#combo-fees").text(money(totals.fees));
    $("#combo-exp").text(money(totals.expense));
    $("#combo-ret").text(money(totals.return) + "x");
  }

  function draw(canvasId, chartRef, cfg) {
    var el = document.getElementById(canvasId);
    if (!el || !window.Chart) {
      return null;
    }
    if (chartRef) {
      chartRef.destroy();
    }
    return new Chart(el.getContext("2d"), cfg);
  }

  function loadOverview() {
    var range = parseRange();
    if (!range.from || !range.to) {
      openPicker();
      return;
    }
    var base = window.hostname_site || "";
    if (base.slice(-1) !== "/") {
      base += "/";
    }
    $.ajax({
      url: base + "monitor/overview_data",
      type: "GET",
      dataType: "json",
      data: { from: range.from, to: range.to }
    }).done(function (data) {
      var empty = { lazada: [], shopee: [], tiktok: [] };
      var sales = (data && data.sales) || {};
      var ads = (data && data.ads) || {};
      var fees = (data && data.fees) || {};
      var combo = (data && data.combo) || {};
      fillKpis("#sales", sales.totals);
      fillKpis("#ads", ads.totals);
      fillKpis("#fees", fees.totals);
      fillCombo(combo.totals);
      salesChart = draw("dash_sales_chart", salesChart, stackedCfg(data.labels, sales.series || empty, "THB"));
      adsChart = draw("dash_ads_chart", adsChart, stackedCfg(data.labels, ads.series || empty, "THB"));
      feesChart = draw("dash_fees_chart", feesChart, stackedCfg(data.labels, fees.series || empty, "THB"));
      comboChart = draw("dash_combo_chart", comboChart, comboCfg(data.labels, combo.series || {
        sales: [], ads: [], fees: [], return: []
      }));
    });
  }

  function initPicker() {
    var el = $("#dash_daterange");
    if (!el.length || !$.fn.daterangepicker || !window.moment) {
      return;
    }
    if (el.data("daterangepicker")) {
      el.data("daterangepicker").remove();
    }
    var en = el.attr("data-lang") === "en";
    var start = moment().subtract(6, "days");
    var end = moment();
    el.daterangepicker({
      startDate: start,
      endDate: end,
      opens: "left",
      autoUpdateInput: true,
      autoApply: false,
      locale: {
        format: "MM/DD/YYYY",
        separator: " - ",
        applyLabel: en ? "Search" : "ค้นหา",
        cancelLabel: en ? "Cancel" : "ยกเลิก",
        fromLabel: en ? "From" : "จาก",
        toLabel: en ? "To" : "ถึง",
        customRangeLabel: en ? "Custom" : "กำหนดเอง",
        daysOfWeek: en ? ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"] : ["อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"],
        monthNames: en
          ? ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
          : ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]
      }
    });
    el.val(start.format("MM/DD/YYYY") + " - " + end.format("MM/DD/YYYY"));
  }

  $(function () {
    initPicker();
    $("#dash_daterange, #dash_date_open, #dash_date_wrap, .dash-date-label").on("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(".dash-range-btns .btn").removeClass("is-on");
      openPicker();
    });
    $("#dash_search").on("click", function () {
      loadOverview();
    });
    $(".dash-range-btns .btn[data-days]").on("click", function () {
      setRangeDays(parseInt(this.getAttribute("data-days"), 10));
    });
    $("#dash_daterange").on("apply.daterangepicker", function () {
      $(".dash-range-btns .btn").removeClass("is-on");
      loadOverview();
    });
    loadOverview();
  });
})(window, window.jQuery);
