$(document).ready(function() {
	var arr_path = window.location.pathname.split("/");
    var url_add = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"domains/domain_chk_name_invalid";
    var url_edit = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"domains/domain_chk_name_invalid_edit";

	$("#domain_add_form, #domain_edit_form").each(function() {
		var is_edit = $(this).attr('id') === 'domain_edit_form';
		$(this).validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            ignore: "",
            rules: {
                web_domain_name: {
                	required: true,
                	remote: {
						type: "POST",
						url: is_edit ? url_edit : url_add,
						data: {
							'web_domain_name' : function () { return $('#web_domain_name').val(); },
							'id_en' : function () { return $('#id_en').val(); }
						},
						dataType: "json"
					}
                }
			},
            messages: {
				web_domain_name: {
					required : "กรุณากรอก domain",
					remote : "domain ซ้ำกรุณากรอกใหม่"
				}
			},
			highlight: function(element) {
                $(element)
                    .closest('.form-group').addClass('has-error');
            }
		});
	});
});
