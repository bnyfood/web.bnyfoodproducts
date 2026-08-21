
var arr_pathc = window.location.pathname.split("/");
  var pahth_project ="";
  if(window.location.hostname == 'localhost'){
    pahth_project = arr_pathc[1]+"/";
  }

$("#btn_add_product").click(function(event) {
		
	var parent_cat = $("#parent_cat").val();
//alert(parent_cat);
	
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+pahth_project+"product/add_product_form/"+parent_cat;

    window.location.href = urls;

});	

$("#btn_clear_search").click(function(event) {
		
	
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+pahth_project+"product/product_list";

    window.location.href = urls;

});	

