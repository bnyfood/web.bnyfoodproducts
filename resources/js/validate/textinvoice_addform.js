$(document).ready(function() {
	//var arr_path = window.location.pathname.split("/");
    //var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"agent/agent_chk_user_invalid";
		$("#invoice_action").validate({
			 errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                    name: {
                            required: true
                        },
                    branch_number: {required: function(element) {
                    	if($('input[name="is_head_office"]:checked').val() == 1){
                    		return  false;
                    	}else if($('input[name="is_head_office"]:checked').val() == 2){
		                  return  true;
                    	}
                    	
		            }}
            },        
            messages: {
                    name: {
                        required : "กรุณากรอก Name"
                    },
					branch_number: {
						required : "กรุณากรอกเลขที่สาขา"
					}
			
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	
		
	});