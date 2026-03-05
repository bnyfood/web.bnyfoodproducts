class UI_subunit {

	  addToList_sub_unit(data) {
	  	
	    const list = document.getElementById(`sub_unit-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.material_name} ${data.material_size}(${data.material_unit})</td>
	    <td>${data.material_brand_name}</td>
	    <td>${data.unit_type_name}</td>
	    <td>${data.subunit_volume_v} ${data.unit_type_name_v}</td>
	    <td>${data.sku}</td>
	    <td>
	    <input type="radio" presku='${data.sku}' name="web_material_subunit_id" id="web_material_subunit_id" value="${data.web_material_id_main}_${data.web_material_subunit_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAll_sub_unit() {
	    const elem = document.querySelectorAll(`#sub_unit-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	}

	function displayUnit(is_all) {

	  if(is_all == 'search_all'){
	  	$("#po_search").val('');
	  }		

	  const sub_unit_txt_search = $("#sub_unit_txt_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/material/"+"sub_unit_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {sub_unit_txt_search: sub_unit_txt_search}, 
	    dataType: "json"
	  }).done(function( data ) {  

	    const arr_sub_units = data.arr_sub_units;

	    const ui = new UI_subunit();
	    //console.log(arr_usergroups);
	    ui.deleteAll_sub_unit();
	    arr_sub_units.forEach(function (arr_sub_unit) {
	      // Add company to UI
	      ui.addToList_sub_unit(arr_sub_unit);
	    });

	  });
	}

	$("#btn_sub_unit_search").click(function(event) {

		displayUnit('search');

	});

	$("#btn_sub_unit_search_all").click(function(event) {

		displayUnit('search_all');

	});

	$('input[name=sub_unit]').change(function(){
		var sub_unit_type = $( 'input[name=sub_unit]:checked' ).val();
		//alert(account_type);

		if(sub_unit_type == 1){

			document.getElementById("subunit_search").style.display = "block";
			document.getElementById("subunit_name").style.display = "none";

		}else if(sub_unit_type == 2){

			document.getElementById("subunit_search").style.display = "none";
			document.getElementById("subunit_name").style.display = "block";

		}
	});




    function updateSubunitSku() {
      var baseSku = $("#newsku").data("base");
      if (!baseSku) {
        baseSku = $("#gennewsku").val();
      }

      if (!baseSku) {
        return;
      }

      var subunitTypeText = $("#web_material_subunit_type_id option:selected").text();
      var subunitTypeValue = $("#web_material_subunit_type_id").val();
      var subunitQty = $("#subunit_qty").val();

      if (!subunitTypeValue || subunitTypeText == "กรุณาเลือก" || subunitQty == "") {
        document.getElementById("newsku").innerHTML = baseSku;
        $("#gennewsku").val(baseSku);
        return;
      }

      var newSku = baseSku + subunitTypeText + "x" + subunitQty;
      document.getElementById("newsku").innerHTML = newSku;
      $("#gennewsku").val(newSku);
    }

    $( 'input[name="web_material_subunit_id"]:radio' ).live('change', function(e) {
      var presku = $(this).attr('presku');
      if (!presku) {
        return;
      }
      $("#newsku").data("base", presku);

      document.getElementById("newsku").innerHTML = presku;
      $("#gennewsku").val(presku);

      updateSubunitSku();
    });

    $("#web_material_subunit_type_id").on("change", function() {
      updateSubunitSku();
    });

    $("#subunit_qty").on("input change", function() {
      updateSubunitSku();
    });




