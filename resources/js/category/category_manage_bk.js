$("#add_cat").click(function(event) {
		
	var parent_cat = $("#parent_cat").val();
//alert(parent_cat);
	
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"category/category_add_form/"+parent_cat;

    window.location.href = urls;

});	

$("#edit_cat").click(function(event) {
		
	var parent_cat = $("#parent_cat").val();
//alert(parent_cat);
	
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"category/category_edit_form/"+parent_cat;

    window.location.href = urls;

});	

$("#del_cat").click(function(event) {
		
	var parent_cat = $("#parent_cat").val();
//alert(parent_cat);
	
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"category/category_del_action/"+parent_cat;

    window.location.href = urls;

});	