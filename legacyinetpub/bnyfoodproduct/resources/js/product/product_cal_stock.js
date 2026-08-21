$(".qty_add").change(function(event){

	//alert(this.id);
	var pro_val = this.id;
	const pro_arr = pro_val.split("-");

	var qty_add_val = $(this).val();
	var qty_down = $("#qty_down-"+pro_arr[1]).val();
	var product_quality = $("#product_quality").val();
	

	var qty = product_quality + qty_add_val - qty_down;
	//alert(qty);


	document.getElementById("pro_qty-"+pro_arr[1]).innerHTML = qty;

});

$(".qty_down").change(function(event){

	//alert(this.name);
	var pro_val = this.id;
	const pro_arr = pro_val.split("-");

	var qty_down_val = $(this).val();
	var qty_add = $("#qty_add-"+pro_arr[1]).val();
	var product_quality = $("#product_quality").val();

	var qty = product_quality + qty_add - qty_down_val;

	document.getElementById("pro_qty-"+pro_arr[1]).innerHTML = qty;

});