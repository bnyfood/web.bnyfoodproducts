$(document).ready(function() {
	//jQuery("a#fancy_main").fancybox({
	
							
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"supplier/supplier_list/1";

	$("#manage_brand").click(function(e){
		e.stopPropagation();
		//if($(this).is(":checked")){

			$.fancybox({
				'width'				: '90%',
				'height'			: '100%',
				'autoScale'			: false,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe',
				'cyclic'			: true,
				'hideOnOverlayClick'  		: false,
				'href' : urls,
				'onClosed': function(){
				      
				      make_cat_list();

				   }
			});

		//}
		 
	});

	function make_cat_list(){

		var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"webcategory/get_cat_list";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	list_cat = data.list_cat;
		  	//console.log(list_cat);

			$("#web_category_id").empty();  			  	
			$("#web_category_id").html(list_cat['arr_cat_list']);
		  	//$("#save_info").show();
           
           
		  	});

	}
					
});		