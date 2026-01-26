$(document).ready(function() {
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"config_system/users/user_chk_username_invalid_code";
		$("#user_add_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                    customer_name: {
                    	required: true
                    },
                    txt_email: {
                    	required: true,
                    	email: true,
                    	/*remote: {
							  type: "POST",
							  url: urls,
							  data: {'txt_email' : function () { return $('#txt_email').val(); }},
							  dataType: "json"
							}*/
                	},
                	CompanyName:{
                		required: true
                	},
                	txt_password: {
                    	required: true,
                    	minlength: 8,
                    	//Uppercase: true,
					    //LowserCase: true,
					    //Character: true,
                	},
                	txt_repassword: {
                		required: true,
                    	equalTo: "#register_password"
                	}
                	
			},
            messages: {
				customer_name: {
					required : "กรุณากรอกชื่อลูกค้า"
				},
				txt_email: {
					required : "กรุณากรอก email.",
					email:"กรุณากรอกชื่อลูกค้า email ให้ถูกต้อง",
					//remote : "Email ซ้ำกรุณากรอกใหม่"
				},
				CompanyName: {
					required : "กรุณากรอก ชื่อบริษัท"
				},
				txt_password: {
					required : "กรุณากรอก Password",
					minlength: "กรุณากรอก Password มากกว่า 8 ตัวอัษร"
					//Uppercase: "Password request Uppercase"
					//LowserCase: "Password request LowserCase"
					//Digit: "Password request some Digit"
				},
				txt_repassword : {
					required: "กรุณากรอกยืนยัน Password",
                	equalTo: "Passwords ของท่านไม่ตรงกัน"
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