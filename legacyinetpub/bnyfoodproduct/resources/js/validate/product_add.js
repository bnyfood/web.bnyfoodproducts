$(document).ready(function() {
		$("#product_add_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                    Title: {
                    	required: true
                    }
                	
			},
            messages: {
				Title: {
					required : "กรุณากรอกชื่อ"
				}
			
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	

        /*$("#submit").click(function(){
            $("input#pro_price").each(function(){
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Specify the years you worked"
                    }
                } );            
            });
        });

        $("#submit").click(function(){
            $("input#pro_qty").each(function(){
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Specify the years you worked"
                    }
                } );            
            });
        });*/
		

	});