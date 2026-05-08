$(document).ready(function () {
  $("#bny_changephone_form").validate({
    errorElement: "span",
    errorClass: "help-block",
    focusInvalid: false,
    ignore: "",
    rules: {
      web_user_phone_old: {
        required: true,
        minlength: 10,
        digits: true,
        equalTo: "#current_phone"
      },
      web_user_phone_new: {
        required: true,
        minlength: 10,
        digits: true
      }
    },
    messages: {
      web_user_phone_old: {
        required: "กรุณากรอกเบอร์โทรเก่า",
        minlength: "กรุณากรอกเบอร์โทรเก่า 10 หลัก",
        digits: "กรอกได้เฉพาะตัวเลข",
        equalTo: "เบอร์โทรเก่าไม่ตรงกับข้อมูลปัจจุบัน"
      },
      web_user_phone_new: {
        required: "กรุณากรอกเบอร์โทรใหม่",
        minlength: "กรุณากรอกเบอร์โทรใหม่ 10 หลัก",
        digits: "กรอกได้เฉพาะตัวเลข"
      }
    },
    highlight: function (element) {
      $(element).closest(".form-group").addClass("has-error");
    }
  });

  $("#web_user_phone_old, #web_user_phone_new").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
  $("#current_phone").val(($("#current_phone").val() || "").replace(/[^0-9]/g, ""));
});
