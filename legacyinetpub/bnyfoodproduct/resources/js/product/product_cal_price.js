$("#chk_all_product").click(function () {
     $('.chk_product').not(this).prop('checked', this.checked);
     var searchIDs = $("#list-group input:checkbox:checked").map(function(){
      return $(this).val();
    }).get(); 

	var UnsearchIDs = $("#list-group input:checkbox:not(:checked)").map(function(){
      return $(this).val();
    }).get(); 
    
    // <----
    //console.log(searchIDs);
    //cal_price(searchIDs);

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;

	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

    var DiscountType = $("#DiscountType").val();
	var DiscountAmount = $("#DiscountAmount").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id: searchIDs,
	      un_product_id : UnsearchIDs,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType: PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

 });



$(".chk_product").change(function(event){
    event.preventDefault();
    var searchIDs = $("#list-group input:checkbox:checked").map(function(){
      return $(this).val();
    }).get(); // <----

    var UnsearchIDs = $("#list-group input:checkbox:not(:checked)").map(function(){
      return $(this).val();
    }).get(); 

    console.log(UnsearchIDs);

    //console.log(UnsearchIDs);

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

    var DiscountType = $("#DiscountType").val();
	var DiscountAmount = $("#DiscountAmount").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id: searchIDs,
	      un_product_id : UnsearchIDs,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$("#PriceMain").change(function(event){

    var PriceMain = $(this).val();

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

    var DiscountType = $("#DiscountType").val();
    var DiscountAmount = $("#DiscountAmount").val();
    var DiscountAmountType = $("#DiscountAmountType").val();
    var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();
	

    const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$("#PriceMainType").change(function(event){

    var PriceMainType = $(this).val();

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

    var PriceMain = $("#PriceMain").val();
    var DiscountAmount = $("#DiscountAmount").val();
    var DiscountAmountType = $("#DiscountAmountType").val();
    var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();
	

    const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$("#PriceMainAmount").keyup(function(event){

    var PriceMainAmount = $(this).val();

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

	var DiscountAmount = $("#DiscountAmount").val();
    var DiscountType = $("#DiscountType").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();

    const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmountม,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$("#ProQuantity").keyup(function(event){

	var ProQuantity = $(this).val();

	var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

    var DiscountType = $("#DiscountType").val();
	var DiscountAmount = $("#DiscountAmount").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

	const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType: PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

});

$("#DiscountAmount").keyup(function(event){

    var DiscountAmount = $(this).val();

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}
    var DiscountType = $("#DiscountType").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

    const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$("#DiscountType").change(function(event){

    var DiscountType = $(this).val();

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

    var DiscountAmount = $("#DiscountAmount").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

    const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$("#DiscountAmountType").change(function(event){

    var DiscountAmountType = $(this).val();

    var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}

	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

    var DiscountType = $("#DiscountType").val();
    var DiscountAmount = $("#DiscountAmount").val();

    var PriceMain = $("#PriceMain").val();
    var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();
	

    const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

    //console.log(searchIDs);
   // cal_price(searchIDs);
});

$('input[name="is_discount"]').click(function(){

	var is_check = $(this).prop('checked');
    var is_discount = 0;
   	if(is_check){
   		is_discount = 1;
   	}else{
   		is_discount = 2;
   	}

   	var is_upquan = 2;
	if ($('input[name="is_upquan"]').prop('checked')) {
    	is_upquan = 1;
	}
	var ProQuantity = $("#ProQuantity").val();

    var DiscountType = $("#DiscountType").val();
	var DiscountAmount = $("#DiscountAmount").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

	const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

});

$('input[name="is_upquan"]').click(function(){

	var is_check = $(this).prop('checked');
    var is_upquan = 0;
   	if(is_check){
   		is_upquan = 1;
   	}else{
   		is_upquan = 2;
   	}

   	var is_discount = 2;

    if ($('input[name="is_discount"]').prop('checked')) {
    	is_discount = 1;
	}



	var ProQuantity = $("#ProQuantity").val();

    var DiscountType = $("#DiscountType").val();
	var DiscountAmount = $("#DiscountAmount").val();
	var DiscountAmountType = $("#DiscountAmountType").val();

	var PriceMain = $("#PriceMain").val();
	var PriceMainType = $("#PriceMainType").val();
	var PriceMainAmount = $("#PriceMainAmount").val();

	const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

    localStorage.setItem(
	    "ProductDiscountStore",
	    JSON.stringify({
	      product_id:ProductDiscountStore.product_id,
	      un_product_id :ProductDiscountStore.un_product_id,
	      is_discount : is_discount,
	      dis_type : DiscountType,
	      dis_amount :DiscountAmount,
	      dis_amount_type : DiscountAmountType,
	      PriceMain : PriceMain,
	      PriceMainType : PriceMainType,
	      PriceMainAmount : PriceMainAmount,
	      is_upquan:is_upquan,
	      ProQuantity:ProQuantity
	    })
	  );

    cal_price_store();

});

document.addEventListener("DOMContentLoaded", cal_price_store);

function cal_price_store(){
	const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );
    
	//console.log(ProductDiscountStore.product_id);
	/*console.log(ProductDiscountStore.product_id);
	console.log(ProductDiscountStore.dis_type);
	console.log(ProductDiscountStore.dis_amount);
	console.log(ProductDiscountStore.dis_amount_type);*/

	var un_arr_id = ProductDiscountStore.un_product_id;
	var arr_id = ProductDiscountStore.product_id;
	var DiscountType = ProductDiscountStore.dis_type;
	var DiscountAmount = ProductDiscountStore.dis_amount;
	var DiscountAmountType = ProductDiscountStore.dis_amount_type;
	var is_discount = ProductDiscountStore.is_discount;
	var is_upquan = ProductDiscountStore.is_upquan;

	var PriceMain = ProductDiscountStore.PriceMain;
	var PriceMainType = ProductDiscountStore.PriceMainType;
	var PriceMainAmount = ProductDiscountStore.PriceMainAmount;
	//console.log(ProductDiscountStore.is_discount);

	var ProQuantity = ProductDiscountStore.ProQuantity;

	$("#DiscountType").val(DiscountType);
	$("#DiscountAmount").val(DiscountAmount);
	$("#DiscountAmountType").val(DiscountAmountType);

	$("#PriceMain").val(PriceMain);
	$("#PriceMainAmount").val(PriceMainAmount);
	//console.log(arr_id);

	var product_id = "";
	var product_price = "";
	var new_price = 0;
	var dis_per = 0;
	var mainprice = 0;
	var per_mainprice = 0;
	var price_type_txt = "";
	var price_amount_txt = "";
	var price_amount_type_txt = "";

	if(is_discount == 1){
		document.getElementById("is_discount").checked = true;
		if(un_arr_id.length > 0){
			for (var j = 0; j < un_arr_id.length; j++) {
				var pArrUn = un_arr_id[j].split("_"); 

				un_product_id =  pArrUn[0];
				if(PriceMain == "0"){
					mainprice = pArrUn[1];
				}else if(PriceMain == "1"){
					per_mainprice = pArrUn[1]*(PriceMainAmount/100);
					if(PriceMainType == "0"){
						mainprice = parseInt(pArrUn[1])+parseInt(per_mainprice);
					}else{
						mainprice = pArrUn[1]-per_mainprice;
					}
					
				}
				$('#'+"price_"+un_product_id).css('text-decoration-line', '');
				document.getElementById("val_"+un_product_id).innerHTML = "ไม่ลดราคา";
				document.getElementById("dis_detail_"+un_product_id).innerHTML = "";
				document.getElementById("price_"+un_product_id).innerHTML = mainprice;
			}
		}

		if(arr_id.length > 0){
			for (var i = 0; i < arr_id.length; i++) {
				
				var pArr = arr_id[i].split("_"); 
				product_id =  pArr[0];
				//product_price = pArr[1];

				if(PriceMain == "0"){
					mainprice = pArr[1];
				}else if(PriceMain == "1"){
					per_mainprice = pArr[1]*(PriceMainAmount/100);

					if(PriceMainType == "0"){
						mainprice = parseInt(pArr[1])+parseInt(per_mainprice);
					}else{
						mainprice = pArr[1]-per_mainprice;
					}
				}

				document.getElementById(product_id).checked = true;
				//console.log(product_id);
				if(DiscountType == "0"){
					price_type_txt = "ลดเหลือ";
					if(DiscountAmountType == "0"){
						price_amount_type_txt = "%";
						new_price = mainprice*(DiscountAmount/100);
					}else if(DiscountAmountType == "1"){
						price_amount_type_txt = "บาท";
						new_price = DiscountAmount;
					}

				}else if(DiscountType == "1"){
					price_type_txt = "ลดลงไป";
					if(DiscountAmountType == "0"){
						price_amount_type_txt = "%";
						dis_per = mainprice*(DiscountAmount/100);
						new_price = mainprice - dis_per;
					}else if(DiscountAmountType == "1"){
						price_amount_type_txt = "บาท";
						new_price = mainprice-DiscountAmount;
					}	
					
				}
				
				document.getElementById("val_"+product_id).innerHTML = new_price;
				document.getElementById("dis_detail_"+product_id).innerHTML = price_type_txt+" "+DiscountAmount+" "+price_amount_type_txt;
				$('#'+"price_"+product_id).css('text-decoration-line', 'line-through');
				document.getElementById("price_"+product_id).innerHTML = mainprice;
			}
		}
	}else if(is_discount == 2){

		

		document.getElementById("is_discount").checked = false;

		if(un_arr_id.length > 0){
			for (var j = 0; j < un_arr_id.length; j++) {
				var pArrUn = un_arr_id[j].split("_"); 
				un_product_id =  pArrUn[0];
				if(PriceMain == "0"){
					mainprice = pArrUn[1];
				}else if(PriceMain == "1"){
					per_mainprice = pArrUn[1]*(PriceMainAmount/100);
					
					if(PriceMainType == "0"){
						mainprice = parseInt(pArrUn[1])+parseInt(per_mainprice);
					}else{
						mainprice = pArrUn[1]-per_mainprice;
					}
				}
				//console.log("price_"+un_product_id);
				$('#'+"price_"+un_product_id).css('text-decoration-line', '');
				document.getElementById("val_"+un_product_id).innerHTML = "ไม่ลดราคา";
				document.getElementById("dis_detail_"+un_product_id).innerHTML = "";
				document.getElementById("price_"+un_product_id).innerHTML = mainprice;
			}
		}

		if(arr_id.length > 0){
			for (var i = 0; i < arr_id.length; i++) {
				var pArr = arr_id[i].split("_"); 
				product_id =  pArr[0];
				//product_price = pArr[1];
				//alert('1');

				if(PriceMain == "0"){
					//alert(arr_id[i]);
					mainprice = pArr[1];
				}else if(PriceMain == "1"){
					per_mainprice = pArr[1]*(PriceMainAmount/100);

					if(PriceMainType == "0"){
						mainprice = parseInt(pArr[1])+parseInt(per_mainprice);
					}else{
						mainprice = pArr[1]-per_mainprice;
					}
				}

				document.getElementById(product_id).checked = true;
				$('#'+"price_"+product_id).css('text-decoration-line', '');
				document.getElementById("val_"+product_id).innerHTML = "ไม่ลดราคา";
				document.getElementById("dis_detail_"+product_id).innerHTML = "";
				document.getElementById("price_"+product_id).innerHTML = mainprice;
			}
		}	
	}
	//update product quantity 
	if(is_upquan == 1){
		document.getElementById("is_upquan").checked = true;	
		if(arr_id.length > 0){
			for (var j = 0; j < arr_id.length; j++) {
				var pArrUn = arr_id[j].split("_"); 

				un_product_id =  pArrUn[0];
				
				$("#pro_quantity_"+un_product_id).val(ProQuantity);
			}
		}

		if(un_arr_id.length > 0){

			if(un_arr_id.length > 0){
				for (var j = 0; j < un_arr_id.length; j++) {
					var pArrUn = un_arr_id[j].split("_"); 

					un_product_id =  pArrUn[0];
					quantity_old = pArrUn[2];
					
					$("#pro_quantity_"+un_product_id).val(quantity_old);
				}
			}

		}

	}else if(is_upquan == 2){
		//alert(is_upquan);
		document.getElementById("is_upquan").checked = false;

		if(un_arr_id.length > 0){

			if(un_arr_id.length > 0){
				for (var j = 0; j < un_arr_id.length; j++) {
					var pArrUn = un_arr_id[j].split("_"); 

					un_product_id =  pArrUn[0];
					quantity_old = pArrUn[2];
					
					$("#pro_quantity_"+un_product_id).val(quantity_old);
				}
			}

		}

		if(arr_id.length > 0){
			for (var j = 0; j < arr_id.length; j++) {
				var pArrUn = arr_id[j].split("_"); 

				un_product_id =  pArrUn[0];
				
				$("#pro_quantity_"+un_product_id).val(ProQuantity);
			}
		}
	}

}

$("#product_edit_price").click(function(event) {
		
	const ProductDiscountStore = JSON.parse(
      localStorage.getItem("ProductDiscountStore")
    );

	var un_arr_id = ProductDiscountStore.un_product_id;
	var arr_id = ProductDiscountStore.product_id;
	var DiscountType = ProductDiscountStore.dis_type;
	var DiscountAmount = ProductDiscountStore.dis_amount;
	var DiscountAmountType = ProductDiscountStore.dis_amount_type;
	var PriceMain = ProductDiscountStore.PriceMain;
	var PriceMainType = ProductDiscountStore.PriceMainType;
	var PriceMainAmount = ProductDiscountStore.PriceMainAmount;
	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/edit_product_group";

    $.ajax({
	    type: "POST",
	    url: urls,
	    data:  {un_arr_id: un_arr_id,arr_id:arr_id,DiscountType:DiscountType,DiscountAmount:DiscountAmount,DiscountAmountType:DiscountAmountType,PriceMain:PriceMain,PriceMainType:PriceMainType,PriceMainAmount:PriceMainAmount}, 
	    dataType: "json"
	  })
  		.done(function(  ) {  

  			console.log('Save');
  			localStorage.clear();

	  });
});	