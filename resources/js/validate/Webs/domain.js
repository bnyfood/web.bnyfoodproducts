$(document).ready(function() {
	var base = (typeof hostname_site !== 'undefined' && hostname_site)
		? hostname_site
		: (window.location.origin + '/' + (window.location.pathname.split('/')[1] || ''));
	var url_add = base + '/webs/domains/domain_chk_name_invalid';
	var url_edit = base + '/webs/domains/domain_chk_name_invalid_edit';

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
						}
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
