(function (window, document, $) {
	'use strict';

	var submitPhoneUrl;
	var verifyOtpUrl;
	var resendOtpUrl;

	function showMsg(msg, isErr) {
		var $el = $('#bny_msg');
		$el.text(msg || '');
		$el.css('color', isErr ? '#c00' : '#0a0');
		$el.toggle(!!msg);
	}

	function digitsOnly(val, maxLen) {
		var s = (val || '').replace(/[^0-9]/g, '');
		if (maxLen) {
			s = s.slice(0, maxLen);
		}
		return s;
	}

	$(function () {
		if (typeof bnyLuckyDrawConfig === 'undefined' || !bnyLuckyDrawConfig) {
			return;
		}
		submitPhoneUrl = bnyLuckyDrawConfig.submitPhoneUrl;
		verifyOtpUrl = bnyLuckyDrawConfig.verifyOtpUrl;
		resendOtpUrl = bnyLuckyDrawConfig.resendOtpUrl;

		$('#web_user_phone').on('input', function () {
			$(this).val(digitsOnly($(this).val(), 10));
		});
		$('#key_otp').on('input', function () {
			$(this).val(digitsOnly($(this).val(), 6));
		});

		$('#form_phone').on('submit', function (e) {
			e.preventDefault();
			var phone = digitsOnly($('#web_user_phone').val(), 10);
			if (phone.length < 9) {
				showMsg('กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง', true);
				return;
			}
			showMsg('', false);
			$.post(submitPhoneUrl, { web_user_phone: phone }, 'json')
				.done(function (res) {
					if (res && res.status === true) {
						$('#step_phone').hide();
						$('#key_otp').val('');
						$('#step_otp').show();
						$('#bny_resend_otp').hide();
						showMsg(res.message || 'ส่ง OTP แล้ว กรุณากรอกรหัส', false);
						return;
					}
					showMsg((res && res.message) || 'บันทึกไม่สำเร็จ', true);
				})
				.fail(function (xhr) {
					var m = 'เกิดข้อผิดพลาด';
					try {
						var j = JSON.parse(xhr.responseText);
						if (j && j.message) {
							m = j.message;
						}
					} catch (e) { /* ignore */ }
					showMsg(m, true);
				});
		});

		$('#form_otp').on('submit', function (e) {
			e.preventDefault();
			var phone = digitsOnly($('#web_user_phone').val(), 10);
			var otp = digitsOnly($('#key_otp').val(), 6);
			if (otp.length !== 6) {
				showMsg('กรุณากรอกรหัส OTP 6 หลัก', true);
				return;
			}
			showMsg('', false);
			$.post(verifyOtpUrl, { web_user_phone: phone, key_otp: otp }, 'json')
				.done(function (res) {
					if (res && res.status === true) {
						showMsg(res.message || 'ยืนยันสำเร็จ', false);
						$('#bny_resend_otp').hide();
						if (res.redirect) {
							window.location.href = res.redirect;
						}
						return;
					}
					if (res && res.resend_suggest) {
						$('#bny_resend_otp').show();
					} else {
						$('#bny_resend_otp').hide();
					}
					showMsg((res && res.message) || 'รหัสไม่ตรง', true);
				})
				.fail(function (xhr) {
					$('#bny_resend_otp').show();
					var m = 'เกิดข้อผิดพลาด';
					try {
						var j = JSON.parse(xhr.responseText);
						if (j && j.message) {
							m = j.message;
						}
					} catch (e2) { /* ignore */ }
					showMsg(m, true);
				});
		});

		$('#bny_resend_otp').on('click', function (e) {
			e.preventDefault();
			var phone = digitsOnly($('#web_user_phone').val(), 10);
			$.post(resendOtpUrl, { web_user_phone: phone }, 'json')
				.done(function (res) {
					if (res && res.status === true) {
						$('#bny_resend_otp').hide();
						showMsg('ส่ง OTP ใหม่แล้ว', false);
						$('#key_otp').val('');
						return;
					}
					showMsg((res && res.message) || 'ส่งใหม่ไม่สำเร็จ', true);
				});
		});
	});
})(window, document, jQuery);
