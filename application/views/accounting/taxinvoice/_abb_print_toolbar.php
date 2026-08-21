<style>
  .abb-print-bar {
    position: sticky;
    top: 0;
    z-index: 20;
    background: #f4f6f8;
    border: 1px solid #d0d5dd;
    border-radius: 6px;
    padding: 10px 14px;
    margin: 0 auto 12px auto;
    max-width: 1100px;
    text-align: left;
    font-size: 13px;
  }
  .abb-print-bar button {
    margin-right: 8px;
    padding: 6px 14px;
    border: 1px solid #2f6fed;
    border-radius: 4px;
    background: #2f6fed;
    color: #fff;
    cursor: pointer;
  }
  .abb-print-bar button:hover {
    background: #1d5bd6;
  }
  .abb-print-bar .abb-print-hint {
    color: #555;
  }
  @media print {
    .no-print,
    .abb-print-bar {
      display: none !important;
    }
  }
</style>
<div class="abb-print-bar no-print">
  <button type="button" id="print_abb_only">พิมพ์ใบกำกับภาษีอย่างย่อ</button>
  <span class="abb-print-hint">พิมพ์เฉพาะใบกำกับภาษีอย่างย่อในแท็บนี้ (ไม่รวมใบเต็มรูปและฟอร์มค้นหา)</span>
</div>
<script>
  (function () {
    var btn = document.getElementById("print_abb_only");
    if (btn) {
      btn.onclick = function () {
        window.print();
      };
    }
  })();
</script>
