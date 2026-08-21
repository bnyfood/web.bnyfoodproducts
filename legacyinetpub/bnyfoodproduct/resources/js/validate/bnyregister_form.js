$(document).ready(function() {
	$("#bnyregister_form").validate({
		errorElement: 'span',
		errorClass: 'help-block',
		focusInvalid: false,
		ignore: "",
		rules: {
			web_user_name: {
				required: true,
				minlength: 2
			},
			web_user_phone: {
				required: true,
				minlength: 10,
				digits: true
			}
		},
		messages: {
			web_user_name: {
				required: "กรุณากรอกชื่อ",
				minlength: "กรุณากรอกชื่ออย่างน้อย 2 ตัวอักษร"
			},
			web_user_phone: {
				required: "กรุณากรอกเบอร์โทรศัพท์",
				minlength: "กรุณากรอกเบอร์โทร 10 หลัก",
				digits: "กรอกได้เฉพาะตัวเลข"
			}
		},
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		}
	});

	$('#web_user_phone').on('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '');
	});
});
