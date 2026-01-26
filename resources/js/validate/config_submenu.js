$(document).ready(function() {
		$("#sub_menu_add_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                    menu_name: {
                    	required: true
                    }
                	
			},
            messages: {
				menu_name: {
					required : "กรุณากรอกชื่อเมนู"
				}
			
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	
		

	});