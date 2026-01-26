
$(document).ready(function() {

	//const sleep = ms => new Promise(r => setTimeout(r, 5000));

	//$("#12346a").val("55");
					    //document.getElementById("12346a").value = "88";
//make_sku();

//const myInterval = setInterval(make_sku, 3000);


//function make_sku() {
	const ProductSkuStore = JSON.parse(
      localStorage.getItem("ProductSkuStore")
    );


    document.getElementById("pro_name_ex").innerHTML = ProductSkuStore.title;
    document.getElementById("sku_name_ex").innerHTML = ProductSkuStore.sku_name

	const array_skus = JSON.parse(
	      localStorage.getItem("SkuStore")
	    );

	//console.log('make2');
	/*const SkuNameStore = JSON.parse(
	      localStorage.getItem("SkuName")
	    );*/

	//$("#UU8yV0cxNXFucWNOZUppaVNub1p6Zz09").val(555);
	
	   // console.log(array_skus['arr_sku']);

	   /* array_skus.forEach(function (array_sku) {
			console.log('----');
	    });*/

	    //console.log(Object.keys(array_skus['arr_sku']).length);

	    var is_search = $("#is_search").val();

	    if(is_search == 1){
	    	//$("#pro_name_ex").val(ProductSkuStore.sku_name);
		    if(isNotEmpty(array_skus)){
		    	//console.log(array_skus['arr_sku']);
			    if(Object.keys(array_skus['arr_sku']).length > 0){
			    	var arr_sku = {};
				    for (var key in array_skus['arr_sku']) {
				    	console.log(key);
					    console.log(array_skus['arr_sku'][key]);
					    //$("#"+key).val("55");
					    //alert(array_skus['arr_sku'][key]);
					    $("#"+key).val(array_skus['arr_sku'][key]);
					    arr_sku[key] = array_skus['arr_sku'][key];
					}
				}
			}else{
				var arr_sku = {};
			}
		}else{

			var arr_sku = {};
			localStorage.removeItem("SkuName");
			localStorage.removeItem("SkuStore");
		}
	
//}

	function isNotEmpty(object) {
	  for (const property in object) {
	    return true;
	  }
	  return false;
	}


	
	$("input[name='quan']").change(function(event){

		const ProductSkuStore = JSON.parse(
	      localStorage.getItem("ProductSkuStore")
	    );

	    ran_id = ProductSkuStore.ran_id;

		var quan = $(this).val();
		var product_id = $(this).attr('id');

		//alert(quan);
		
		
		/*if(isNotEmpty(arr_sku)){
			let hasKey = arr_sku.hasOwnProperty(product_id); 

			if (hasKey) {
			    console.log('This key exists.');
			} else {
			    console.log('This key does not exist.');
			    arr_sku[product_id] = quan;
			}
		}else{
			arr_sku[product_id] = quan;
		}*/

			//const arr_sku_data = [];

			arr_sku[product_id] = quan;

			//alert(arr_sku_data);

			//arr_sku.push(arr);

			/*element.id = product_id;
			element.quan = quan;
			arr_sku.push(element);*/
			//console.log(arr);


			localStorage.setItem(
			    "SkuStore",
			    JSON.stringify({arr_sku:arr_sku})
			  );
		

		

	});

	$("#sku_name").change(function(event){
		var sku_name = $(this).val();

		localStorage.setItem(
	    "SkuName",
	    JSON.stringify({sku_name:sku_name})
	  );

	});

	$("#sku_add").click(function(){

		const array_skus = JSON.parse(
	      localStorage.getItem("SkuStore")
	    );
		const SkuNameStore = JSON.parse(
	      localStorage.getItem("SkuName")
	    );

	    var ran_id = ProductSkuStore.ran_id;

		var sku_data = "";
		var num = 1;
		var pip = "";
		if(isNotEmpty(array_skus)){
		    if(Object.keys(array_skus['arr_sku']).length > 0){
			    for (var key in array_skus['arr_sku']) {

			    	const pro_arr = key.split("_");
			    	const pro_id_en = pro_arr[1];
			    	const pro_quean = array_skus['arr_sku'][key];
			    	if(num > 1 ){
			    		pip = "|";
			    	}
				    sku_data += pip + pro_id_en+"_"+pro_quean;
				    num = num+1;
				}
			}
		}
		//console.log(sku_data);
	  var arr_path = window.location.pathname.split("/");
	  var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"sku/sku_add_ajax";
	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {sku_name:ProductSkuStore.sku_name,ran_id:ran_id,sku_data: sku_data}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	  	//console.log('aaaaa');
	  	//alert('save');

	  	parent.jQuery.fancybox.close();
	  });
        
    });

	/*$("#product_cat_search").change(function(event) {
		
		cat_id_sel = $(this).val();

		var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"sku/add_sku_form_search/"+cat_id_sel;

        window.location.href = urls;

	});*/

	$("#search_pro").click(function(){

		cat_id_sel = $("#product_cat_search").val();
		search_pro_name = $("#search_pro_name").val();

		var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"sku/add_sku_form_search/"+cat_id_sel+"/"+search_pro_name;

        window.location.href = urls;

	});

});