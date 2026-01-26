$(document).ready(function() {
		$("#atm_map_add_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                    upload_file_map: {
                    	required : true,
                        extension : "png|PNG"
                    }
                	
			},
            messages: {
				upload_file_map: {
					required : "กรุณาเลือกไฟล์",
                    extension : "กรุณาเลือกไฟล์ PNG"
				}
			
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	
		

	});