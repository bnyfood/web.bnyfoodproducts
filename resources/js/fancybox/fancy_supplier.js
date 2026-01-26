$(document).ready(function() {
	//jQuery("a#fancy_main").fancybox({
	
						
    var urls = hostname_site+"/"+"supplier/supplier_list/1";

	$("#manage_supplier").click(function(e){
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
				      
				      make_supplier_list();

				   }
			});

		//}
		 
	});

	function make_supplier_list(){


        var urls = hostname_site+"/"+"supplier/get_supplier_list";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	list_cat = data.list_cat;
		  	//console.log(list_cat);

			$("#web_supplier_id").empty();  			  	
			$("#web_supplier_id").html(list_cat['arr_cat_list']);
		  	//$("#save_info").show();
           
           
		  	});

	}
					
});		