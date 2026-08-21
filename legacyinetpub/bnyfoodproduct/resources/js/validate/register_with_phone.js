$(document).ready(function() {
	var arr_path = window.location.pathname.split("/");
    var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"user_chk_phone_invalid";
    var urls_email = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"user_chk_email_invalid";

		$("#register_phone_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
	            	txt_name: {
	                    	required: true,
	                    	minlength: 3
	                	},
	                txt_phone: {
	                    	required: true,
	                    	minlength: 10,
	                    	remote: {
							  type: "POST",
							  url: urls,
							  data: {'txt_phone' : function () { return $('#txt_phone').val(); }},
							  dataType: "json"
							}
	                	},	
	                txt_email: {
	                    	required: false,
	                    	remote: {
							  type: "POST",
							  url: urls,
							  data: {'txt_email' : function () { return $('#txt_email').val(); }},
							  dataType: "json"
							}
	                	},		
					
			},
            messages: {
            	txt_name: {
					required : "กรุณากรอก ชื่อ",
					minlength: "กรุณากรอก ชื่อ มากกว่า 3 ตัวอัษร"
				},
				txt_phone: {
					required : "กรุณากรอก เบอร์โทรศัพท์",
					minlength: "กรุณากรอก 10 ตัวอัษร",
					remote : "เบอร์โทรศัพท์ซ้ำ กรุณากรอกใหม่"
				},
				txt_email:{
					remote : "Email ซ้ำ กรุณากรอกใหม่"
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