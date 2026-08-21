$(function () {
  var $input = $("#web_bny_gift_pic");
  var $preview = $("#bny_gift_pic_preview");
  var $previewBox = $("#bny_gift_pic_preview_box");
  var $placeholder = $("#bny_gift_pic_placeholder");
  var $filename = $("#bny_gift_pic_filename");

  if (!$input.length) {
    return;
  }

  var existingSrc = $preview.attr("data-existing-src") || "";
  var existingName = $preview.attr("data-existing-name") || "รูปปัจจุบัน";

  function showPreview(src, name) {
    $preview.attr("src", src);
    $previewBox.show();
    $placeholder.hide();
    $filename.text(name || "");
  }

  function showExistingOrPlaceholder() {
    if (existingSrc) {
      showPreview(existingSrc, existingName);
    } else {
      $preview.attr("src", "");
      $previewBox.hide();
      $placeholder.show();
      if (!$filename.data("default-hint")) {
        $filename.data("default-hint", $filename.text());
      }
      $filename.text($filename.data("default-hint") || "รองรับ JPG, PNG, GIF");
    }
  }

  if (existingSrc) {
    showPreview(existingSrc, existingName);
  } else {
    showExistingOrPlaceholder();
  }

  $input.on("change", function () {
    var file = this.files && this.files[0];
    if (!file) {
      showExistingOrPlaceholder();
      return;
    }
    if (!/^image\//.test(file.type)) {
      alert("กรุณาเลือกไฟล์รูปภาพ");
      $(this).val("");
      showExistingOrPlaceholder();
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
      showPreview(e.target.result, file.name);
    };
    reader.readAsDataURL(file);
  });
});
