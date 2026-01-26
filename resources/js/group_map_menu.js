$(document).ready(function() {

	$('input[name="groupmenu_id[]"]').click(function(){
	    //alert($(this).val());
	    //alert($(this).attr('id'));
	    groupmenu_id = $(this).val();

	    var id_en = $("#id_en").val();
	    var is_check = $(this).prop('checked');
	    var is_chk_val = 0;
	   	if(is_check){
	   		is_chk_val = 1;
	   	}else{
	   		is_chk_val = 2;
	   	}

        var urls = hostname_site+"/"+"config_system/usergroup/group_map_menu_action";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {id_en:id_en,groupmenu_id: groupmenu_id,is_chk_val:is_chk_val}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
           
		 });

	});
	
});