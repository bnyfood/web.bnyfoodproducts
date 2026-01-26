$(document).ready(function() {
	//jQuery("a#fancy_main").fancybox({
	
    var urls = hostname_site+"/"+"manufacture/brand/brand_list/1";

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
				      alert('123');
				      make_brand_list();

				   }
			});

		//}
		 
	});

	function make_brand_list(){

		alert('456');

        var urls = hostname_site+"/"+"manufacture/brand/get_brand_list";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	list_cat = data.list_cat;
		  	//console.log(list_cat);

			$("#web_material_brand_id").empty();  			  	
			$("#web_material_brand_id").html(list_cat['arr_cat_list']);
		  	//$("#save_info").show();
           
           
		  	});

	}
					
});		