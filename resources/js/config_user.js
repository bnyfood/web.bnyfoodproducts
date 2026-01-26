$(document).ready(function() {
	$("#Zip").keyup (function (e) {

	    var sip_val = $(this).val();
	    //console.log(sip_val);
	    if(sip_val.length == 5){
	    	//alert(sip_val);
	    	document.getElementById("zip_search").style.display = "block";

    		var urls = hostname_site+"/"+"province/get_by_zip";

    		$.ajax({
			  type: "POST",
			  url: urls,
			  data:  {zip_txt: sip_val}, 
			  dataType: "json"
			})
		  	.done(function( data ) {	
		  	
		  	  const provinces = data.province;
			
			  const ui = new UI_User();
		      ui.deleteAll();
		      provinces.forEach(function (province) {
		        // Add company to UI
		        ui.addToList(province);
		      });
           
		  	});

	    }
	})

  .focus (function (e) {

      var sip_val = $(this).val();
      //console.log(sip_val);
      if(sip_val.length == 5){
        //alert(sip_val);
        document.getElementById("zip_search").style.display = "block";
        
        var urls = hostname_site+"/"+"province/get_by_zip";

        $.ajax({
        type: "POST",
        url: urls,
        data:  {zip_txt: sip_val}, 
        dataType: "json"
      })
        .done(function( data ) {  
        
          const provinces = data.province;
      
        const ui = new UI_User();
          ui.deleteAll();
          provinces.forEach(function (province) {
            // Add company to UI
            ui.addToList(province);
          });
           
        });

      }
  });

});
var edit_id = 0;
var edit_value = "";
class UI_User {
  addToList(province) {
    const list = document.getElementById("province-list");
    // Create tr element
    const row = document.createElement(`tr`);
    //row.setAttribute("id", `${province.province_id}_${province.district_id}_${province.subdistrict_id}"`);
    //row.setAttribute("name", `${province.province_id}_${province.district_id}_${province.subdistrict_id}"`);

    row.setAttribute("onMouseOver", `this.bgColor = '#F4F4F5'`);
    row.setAttribute("onMouseOut", `this.bgColor = '#FFFFFF'`);
    // Insert cols
    row.innerHTML = `
      <td id="${province.province_id}_${province.district_id}_${province.subdistrict_id}_${province.district_name}_${province.subdistrict_name}" >${province.province_name}</td>
      <td id="${province.province_id}_${province.district_id}_${province.subdistrict_id}_${province.district_name}_${province.subdistrict_name}" >${province.district_name}</td>
      <td id="${province.province_id}_${province.district_id}_${province.subdistrict_id}_${province.district_name}_${province.subdistrict_name}" >${province.subdistrict_name}</td>
      <td id="${province.province_id}_${province.district_id}_${province.subdistrict_id}_${province.district_name}_${province.subdistrict_name}" >${province.zipcode}</td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#province-list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

 }

 document.getElementById("province-list").addEventListener("click", function (e) {
//alert(e.tr.name);
//console.log(e.target.id);


    edit_id = e.target.id;
    edit_value = e.target.value;

    //console.log(edit_id);
    const arr_province_id = edit_id.split("_");
    //const arr_province_name = edit_value.split("_");
    //console.log(arr_province_id[0]);
    document.getElementById("province_sel").value = arr_province_id[0];

    var listbox_districts = "<option value='"+arr_province_id[1]+"'>"+arr_province_id[3]+"</option>";
    $("#district_sel").html(listbox_districts);

    var listbox_subdistricts = "<option value='"+arr_province_id[2]+"'>"+arr_province_id[4]+"</option>";
    $("#subdistrict_sel").html(listbox_subdistricts);
  
    document.getElementById("zip_search").style.display = "none";
  e.preventDefault();
});
