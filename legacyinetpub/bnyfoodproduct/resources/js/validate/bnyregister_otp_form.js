$(document).ready(function () {
  var validateOtpUrl = $("#validate_otp_url").val();
  $("#bnyregister_otp_form").validate({
    errorElement: "span",
    errorClass: "help-block",
    focusInvalid: false,
    ignore: "",
    rules: {
      key_otp: {
        required: true,
        minlength: 6,
        maxlength: 6,
        digits: true,
        remote: {
          url: validateOtpUrl,
          type: "post"
        }
      }
    },
    messages: {
      key_otp: {
        required: "กรุณากรอกรหัส OTP",
        minlength: "OTP ต้องมี 6 หลัก",
        maxlength: "OTP ต้องมี 6 หลัก",
        digits: "OTP ต้องเป็นตัวเลขเท่านั้น",
        remote: "รหัส OTP ไม่ถูกต้อง"
      }
    }
  });

  $("#key_otp").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
});
