$("#province_sel").change(function(event) {
		
		province_id = $(this).val();


        var urls = hostname_site+"/"+"province/get_districts";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {province_id: province_id}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	districts = data.districts;
			
			$("#district_sel").empty();  	
			$("#subdistrict_sel").empty();  
			$("#Zip").val('');
			$("#district_sel").html(districts['arr_district_list']);
		  	//$("#save_info").show();
           
           
		  	});

	});
$("#district_sel").change(function(event) {
		
		district_id = $(this).val();

        var urls = hostname_site+"/"+"province/get_subdistricts";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {district_id: district_id}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	subdistricts = data.subdistricts;

			$("#subdistrict_sel").empty();  
			$("#Zip").val('');				  	
			$("#subdistrict_sel").html(subdistricts['arr_subdistrict_list']);
		  	//$("#save_info").show();
           
           
		  	});

	});	
	
$("#subdistrict_sel").change(function(event) {
		
		subdistrict_id = $(this).val();

        var urls = hostname_site+"/"+"province/get_zipcode";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {subdistrict_id: subdistrict_id}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	ZipCode = data.ZipCode;
			  	
			$("#Zip").val(ZipCode);
		  	//$("#save_info").show();
           
           
		  	});
});	