
$(document).ready(function() {
document.getElementById("cate-list").addEventListener("click", function (e) {

  var arr_pathc = window.location.pathname.split("/");
  var pahth_project ="";
  if(window.location.hostname == 'localhost'){
    pahth_project = arr_pathc[1]+"/";
  }

  var parent_cat;
  if (e.target.id === "add_cat") {

  	parent_cat = e.target.value
    
    var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_get_by_id";

    $.ajax({
      type: "POST",
      url: urls,
      data:  {parent_cat: parent_cat}, 
      dataType: "json"
    }).done(function( object ) {  

      cat_data = object.cat_data;
    	//alert(cat_data['Title']);
    	document.getElementById("div_manage_cat").style.display = "block";
    	document.getElementById("manage_cat_txt").innerHTML = "เพิ่ม หมวดหมู่";
      document.getElementById("cat_title").innerHTML = "ชื่อหมวดหมู่ย่อย : ";
    	document.getElementById("cat_name").value = "";
  	  document.getElementById("cat_des").value = "";
    	document.getElementById("parent_id").value = parent_cat;
    	document.getElementById("is_add").value = 1;

      document.getElementById("manage_main_cat_txt").style.display = "block";
      document.getElementById("manage_main_cat_txt").innerHTML = "ชื่อ หมวดหมู่หลัก : "+cat_data['Title'];

    	document.getElementById('div_manage_cat').scrollIntoView();

    }).fail(function()  {
      alert("Sorry. Server unavailable. ");
    }); 

  }else if (e.target.id === "edit_cat") {
  	parent_cat = e.target.value

    var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_get_by_id";

    $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {parent_cat: parent_cat}, 
	    dataType: "json"
	  })
  		.done(function( data ) {  

  		cat_data = data.cat_data;
  
	    document.getElementById("div_manage_cat").style.display = "block";
	  	document.getElementById("manage_cat_txt").innerHTML = "แก้ไข หมวดหมู่";
      document.getElementById("cat_title").innerHTML = "ชื่อหมวดหมู่ : ";
	  	document.getElementById("cat_name").value = cat_data['Title'];
	  	document.getElementById("cat_des").value = cat_data['Description'];
	  	document.getElementById("parent_id").value = parent_cat;
	  	document.getElementById("is_add").value = 2;

      document.getElementById("manage_main_cat_txt").style.display = "none";
      document.getElementById("manage_main_cat_txt").innerHTML = "";

	  	document.getElementById('div_manage_cat').scrollIntoView();

	  });

  	//alert(parent_cat);
  	
  }else if (e.target.id === "del_cat") {

  	parent_cat = e.target.value
    var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_del_action/"+parent_cat;

    window.location.href = urls;

  }

});

$("#add_cat_root").click(function(event) {
		
	var parent_cat_root = $("#add_cat_root").val();
	//alert(parent_cat_root);
	document.getElementById("div_manage_cat").style.display = "block";
  	document.getElementById("manage_cat_txt").innerHTML = "เพิ่ม หมวดหมู่หลัก";
  	document.getElementById("cat_name").value = "";
	document.getElementById("cat_des").value = "";
  	document.getElementById("parent_id").value = parent_cat_root;
  	document.getElementById("is_add").value = 1;
    document.getElementById("manage_main_cat_txt").style.display = "none";
      document.getElementById("manage_main_cat_txt").innerHTML = "";
  	document.getElementById('div_manage_cat').scrollIntoView();
  	$(".cat_hilight tr").removeClass("selected");
});	


$(".cat_hilight tr").click(function() {
    $(this).addClass('selected').siblings().removeClass("selected");
});

$("#shop_sel").change(function(event) {
    
    shop_id = $(this).val();

    var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"category/set_shop_search/"+shop_id;

        window.location.href = urls;

  });
});