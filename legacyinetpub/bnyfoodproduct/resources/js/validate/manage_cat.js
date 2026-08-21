$(document).ready(function() {
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"config_system/usergroup/usergroup_chk_username_invalid";

		$("#cat_add_form").validate({
			 errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
            	cat_name: {
                	required: true
                },
			},
            messages: {
            	cat_name: {
					required : "กรุณากรอกชื่อหมวดหมู่"
				},
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	
		
	});