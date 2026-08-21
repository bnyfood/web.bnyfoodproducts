function set_search(sel) {
  var mode = sel && sel.value != null ? String(sel.value) : "";
  var dateEl = document.getElementById("date_search");
  var orderEl = document.getElementById("order_search");
  var buttonEl = document.getElementById("button_search");
  if (!dateEl || !orderEl || !buttonEl) {
    return;
  }
  if (mode === "1") {
    dateEl.style.display = "";
    orderEl.style.display = "none";
    buttonEl.style.display = "";
  } else if (mode === "2" || mode === "4") {
    dateEl.style.display = "none";
    orderEl.style.display = "";
    buttonEl.style.display = "";
  } else if (mode === "3") {
    dateEl.style.display = "";
    orderEl.style.display = "";
    buttonEl.style.display = "";
  } else {
    dateEl.style.display = "none";
    orderEl.style.display = "none";
    buttonEl.style.display = "none";
  }
}

$(document).ready(function () {
  var sel = document.getElementById("search_type");
  if (sel) {
    set_search(sel);
  }
});
