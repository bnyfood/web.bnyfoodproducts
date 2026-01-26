$(document).ready(function() {
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"config_system/usergroup/usergroup_chk_username_invalid_edit";

		$("#usergroup_edit_form").validate({
			 errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
            	group_name: {
                	required: true
                	/*remote: {
							  type: "POST",
							  url: urls,
							  data: {'group_name' : function () { return $('#group_name').val(); },'group_id_en':function () { return $('#id_en').val(); }},
							  dataType: "json"
							}*/
                },
			},
            messages: {
            	group_name: {
					required : "กรุณากรอกชื่อกลุ่ม"
					//remote : "ชื่อกลุ่มซ้ำกรุณากรอกใหม่"
				},
			},
			highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            }
		});	
		
	});