$(document).ready(function() {
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"config_system/users/user_chk_username_invalid";
		$("#user_edit_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                    customer_name: {
                    	required: true
                    },
                	CompanyName:{
                		required: true
                	}
                	
			},
            messages: {
				customer_name: {
					required : "กรุณากรอกชื่อลูกค้า"
				},
				CompanyName: {
					required : "กรุณากรอก ชื่อบริษัท"
				}
			
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	
		
		$.validator.addMethod("LowserCase", function(value, element) {
		    return this.optional(element) || /[a-z]+/.test(value);
		},"Password should have atleast one lowercase letter." );
		
		$.validator.addMethod("Uppercase", function(value, element) {
		    return this.optional(element) || /[A-Z]+/.test(value);
		},"Password should have atleast one uppercase letter." );
		
		$.validator.addMethod( "Character", function(value, element) {
        return this.optional(element) || /[!@#$%^&*()_+-=<>?:;]+/.test(value);
    }, "Password should have atleast one special character.");
    
    $('#txt_repassword').bind("cut copy paste",function(e) {
          e.preventDefault();
      });

	});