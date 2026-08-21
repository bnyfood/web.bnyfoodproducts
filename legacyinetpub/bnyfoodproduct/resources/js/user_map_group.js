$("#usergroup_sel").change(function(event) {
		
		usergroup_id = $(this).val();

		var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"config_system/usergroup/get_usergroup";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {usergroup_id: usergroup_id}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	usergroup = data.usergroup;
			
			$("#user_sel").html(usergroup['arr_usergroup_list']);
		  	//$("#save_info").show();
           
           
		  	});

	});