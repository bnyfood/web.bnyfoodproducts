$(".unit_price").keyup(function(event){

    var unit_price_val = $(this).val();
    var material_map_supplier_id = $(this).attr('id');
    console.log(unit_price_val);
    console.log(material_map_supplier_id);

    var urls = hostname_site+"/"+"manufacture/material/material_update_price";

    $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {material_map_supplier_id:material_map_supplier_id,unit_price_val:unit_price_val}, 
	    dataType: "json"
	  })
  		.done(function(  ) {  

  			console.log('Save');

	  });
});