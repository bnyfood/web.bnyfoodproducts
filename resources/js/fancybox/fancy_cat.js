$(document).ready(function() {
	//jQuery("a#fancy_main").fancybox({
	
							
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"category/manage_cat/1";

	$("#manage_cat").click(function(e){
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
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"category/get_cat_list";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	list_cat = data.list_cat;
		  	//console.log(list_cat);

			$("#ProductCategoryID").empty();  			  	
			$("#ProductCategoryID").html(list_cat['arr_cat_list']);
		  	//$("#save_info").show();
           
           
		  	});

	}
					
});		