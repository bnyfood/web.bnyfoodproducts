class UI_model {
	  addToList(data,method) {
	    const list = document.getElementById(`bankaccount-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.bookbank_number}</td>
	    <td>${data.bookbank_name}</td>
	    <td>${data.bank_name_th}</td>
	    <td>
	    <input type="radio" name="web_bankaccount_id" id="web_bankaccount_id" value="${data.web_bankaccount_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAll() {
	    const elem = document.querySelectorAll(`#bankaccount-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	  addToList_supplire(data,method) {
	    const list = document.getElementById(`supplier-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.supplier_name}</td>
	    <td>
	    <input type="radio" name="web_supplier_id" id="web_supplier_id" value="${data.web_supplier_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAll_supplire() {
	    const elem = document.querySelectorAll(`#supplier-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	  addToList_po(data,method) {
	    const list = document.getElementById(`po-list`);
	    // Create tr element
	    const row = document.createElement("tr");
	    // Insert cols
	    row.innerHTML = `
	    <td>${data.po_number}</td>
	    <td>${data.supplier_name}</td>
	    <td>${data.po_price}</td>
	    <td>${data.po_cdate}</td>	
	    <td>
	    <input type="radio" name="web_purchase_order_id" id="web_purchase_order_id" value="${data.web_purchase_order_id}">
	    </td>
	    `;

	    list.appendChild(row);
	  }

	  deleteAll_po() {
	    const elem = document.querySelectorAll(`#po-list tr`);
	    Array.prototype.forEach.call(elem, function (node) {
	      node.parentNode.removeChild(node);
	    });
	  }

	}



	
	function displayBankaccount(is_all) {

	  if(is_all == 'search_all'){
	  	$("#accounttxt_search").val('');
	  }		
	  
	  const accounttxt_search = $("#accounttxt_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"bankaccount_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {accounttxt_search: accounttxt_search}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	    const arr_bankaccounts = data.arr_bankaccounts;
	    const ui = new UI_model();
	    //console.log(arr_usergroups);
	    ui.deleteAll();
	    arr_bankaccounts.forEach(function (arr_bankaccount) {
	      // Add company to UI
	      ui.addToList(arr_bankaccount);
	    });

	  });
	}

	$("#btn_account_search").click(function(event) {

		displayBankaccount('search');

	});

	$("#btn_account_search_all").click(function(event) {

		displayBankaccount('search_all');

	});


	function displaySupplier(is_all) {

	  if(is_all == 'search_all'){
	  	$("#suppliertxt_search").val('');
	  }		

	  const suppliertxt_search = $("#suppliertxt_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"supplier_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {suppliertxt_search: suppliertxt_search}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	    const arr_suppliers = data.arr_suppliers;
	    const ui = new UI_model();
	    //console.log(arr_usergroups);
	    ui.deleteAll_supplire();
	    arr_suppliers.forEach(function (arr_supplier) {
	      // Add company to UI
	      ui.addToList_supplire(arr_supplier);
	    });

	  });
	}

	$("#btn_supplier_search").click(function(event) {

		displaySupplier('search');

	});

	$("#btn_supplier_search_all").click(function(event) {

		displaySupplier('search_all');

	});

	function displayPo(is_all) {

	  if(is_all == 'search_all'){
	  	$("#po_search").val('');
	  }		

	  const po_search = $("#po_search").val();

	  var arr_path = window.location.pathname.split("/");
	  var urls = 'https://'+window.location.hostname+"/"+arr_path[1]+"/"+"po_search";

	  $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {po_search: po_search}, 
	    dataType: "json"
	  })
	  .done(function( data ) {  
	    const arr_pos = data.arr_pos;
	    const ui = new UI_model();
	    //console.log(arr_usergroups);
	    ui.deleteAll_po();
	    arr_pos.forEach(function (arr_po) {
	      // Add company to UI
	      ui.addToList_po(arr_po);
	    });

	  });
	}

	$("#btn_po_search").click(function(event) {

		displayPo('search');

	});

	$("#btn_po_search_all").click(function(event) {

		displayPo('search_all');

	});


	$('input[name=account_type]').change(function(){
		var account_type = $( 'input[name=account_type]:checked' ).val();
		//alert(account_type);

		if(account_type == 1){

			document.getElementById("account_search").style.display = "block";
			document.getElementById("account_add_form").style.display = "none";

		}else if(account_type == 2){

			document.getElementById("account_search").style.display = "none";
			document.getElementById("account_add_form").style.display = "block";

		}
	});

	$('input[name=supplier_type]').change(function(){
		var supplier_type = $( 'input[name=supplier_type]:checked' ).val();
		//alert(supplier_type);

		if(supplier_type == 1){

			document.getElementById("supplier_search").style.display = "block";
			document.getElementById("supplier_add_form").style.display = "none";

		}else if(supplier_type == 2){

			document.getElementById("supplier_search").style.display = "none";
			document.getElementById("supplier_add_form").style.display = "block";

		}
	});


