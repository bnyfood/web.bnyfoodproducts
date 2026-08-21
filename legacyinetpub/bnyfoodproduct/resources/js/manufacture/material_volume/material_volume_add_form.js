class UI_model {

	  addToListMaterial(data,method) {
	    const list = document.getElementById(`material-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.material_name}</td>
	    <td>${data.supplier_name}</td>
	    <td>
	    <input type="radio" name="web_material_id" id="web_material_id" value="${data.web_material_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAllMaterial() {
	    const elem = document.querySelectorAll(`#material-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	  addToList(data,method) {
	    const list = document.getElementById(`vt-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.web_material_volume_type}</td>
	    <td>
	    <input type="radio" name="web_material_volume_type_id" id="web_material_volume_type_id" value="${data.web_material_volume_type_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAll() {
	    const elem = document.querySelectorAll(`#vt-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	  addToListUnit(data,method) {
	    const list = document.getElementById(`unit-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.material_unit}</td>
	    <td>
	    <input type="radio" name="web_material_unit_id" id="web_material_unit_id" value="${data.web_material_unit_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAllUnit() {
	    const elem = document.querySelectorAll(`#unit-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	}

	function displayMaterialvolume(is_all) {

	  if(is_all == 'search_all'){
	  	$("#material_volume_search").val('');
	  }		
	  
	  const material_volume_search = $("#material_volume_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/material_volume/"+"material_volume_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {material_volume_search: material_volume_search}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	    const arr_material_volumes = data.arr_material_volumes;
	    const ui = new UI_model();
	    //console.log(arr_usergroups);
	    ui.deleteAll();
	    arr_material_volumes.forEach(function (arr_material_volume) {
	      // Add company to UI
	      ui.addToList(arr_material_volume);
	    });

	  });
	}

	$("#btn_material_volume_search").click(function(event) {

		displayMaterialvolume('search');

	});

	$("#btn_material_volume_search_all").click(function(event) {

		displayMaterialvolume('search_all');

	});

	function displayMaterialUnit(is_all) {

	  if(is_all == 'search_all'){
	  	$("#material_unit_search").val('');
	  }		
	  
	  const material_unit_search = $("#material_unit_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/material_volume/"+"material_unit_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {material_unit_search: material_unit_search}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	    const arr_material_units = data.arr_material_units;
	    const ui = new UI_model();
	    //console.log(arr_usergroups);
	    ui.deleteAllUnit();
	    arr_material_units.forEach(function (arr_material_unit) {
	      // Add company to UI
	      ui.addToListUnit(arr_material_unit);
	    });

	  });
	}

	$("#btn_material_unit_search").click(function(event) {

		displayMaterialUnit('search');

	});

	$("#btn_material_unit_search_all").click(function(event) {

		displayMaterialUnit('search_all');

	});

	function displayMaterial(is_all) {

	  if(is_all == 'search_all'){
	  	$("#material_search").val('');
	  }		
	  
	  const material_search = $("#material_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/material_volume/"+"material_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {material_search: material_search}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	    const arr_materials = data.arr_materials;
	    const ui = new UI_model();
	    //console.log(arr_usergroups);
	    ui.deleteAllMaterial();
	    arr_materials.forEach(function (arr_material) {
	      // Add company to UI
	      ui.addToListMaterial(arr_material);
	    });

	  });
	}

	$("#btn_material_search").click(function(event) {

		displayMaterial('search');

	});

	$("#btn_material_search_all").click(function(event) {

		displayMaterial('search_all');

	});


	$('input[name=vt_type]').change(function(){
		var vt_type = $( 'input[name=vt_type]:checked' ).val();
		//alert(account_type);

		if(vt_type == 1){

			document.getElementById("vt_search").style.display = "block";
			document.getElementById("vt_add_form").style.display = "none";

		}else if(vt_type == 2){

			document.getElementById("vt_search").style.display = "none";
			document.getElementById("vt_add_form").style.display = "block";

		}
	});

	$('input[name=unit_type]').change(function(){
		var unit_type = $( 'input[name=unit_type]:checked' ).val();
		//alert(account_type);

		if(unit_type == 1){

			document.getElementById("unit_search").style.display = "block";
			document.getElementById("unit_add_form").style.display = "none";

		}else if(unit_type == 2){

			document.getElementById("unit_search").style.display = "none";
			document.getElementById("unit_add_form").style.display = "block";

		}
	});