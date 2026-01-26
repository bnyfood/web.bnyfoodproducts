$(document).ready(function() {

	make_sku_list_edit();


	function make_sku_list_edit(){

		//console.log('h1');

		//const ProductSkuStore = JSON.parse(
	     // localStorage.getItem("ProductSkuStore")
	    //);

	    product_id_en = $("#product_id_en").val();

		var arr_path = window.location.pathname.split("/");
		var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"sku/get_sku_by_product_id";
		$.ajax({
			type: "POST",
			url: urls,
			data:  {product_id_en:product_id_en}, 
			dataType: "json"
		})
		.done(function( data ) {  

			document.getElementById("div_product_set").style.display = "block";

			const arr_sku_datas = data.arr_list_sku;
			//console.log(arr_sku_datas);
			const ui_sku_model = new UI_SKU_LIST();
      		ui_sku_model.deleteSkuAll();

      		var cnt_pro = arr_sku_datas.length;
      		var num_pro = 0;

      		var sum_cost_price =  0;
	        var sum_qty = 0;
	        var sum_price = 0;
	        var is_finish = 0;

			arr_sku_datas.forEach(function (arr_sku_data) {
		      // Add company to UI

		      const total_cost_price = arr_sku_data.Cost_price * arr_sku_data.quantity;
		      const total_price = arr_sku_data.Price * arr_sku_data.quantity;

		      sum_qty = sum_qty+arr_sku_data.quantity;
		      sum_price = sum_price + total_price;
		      sum_cost_price = sum_cost_price+total_cost_price;

		      num_pro = num_pro+1;
		      
		      
		      ui_sku_model.addToList(arr_sku_data);

		      if(num_pro == cnt_pro){
		      	ui_sku_model.addToListTotal(sum_qty,sum_price,sum_cost_price);
		      }
		    });

		});

	}

	class UI_SKU_LIST {
      addToList(data,cnt_pro,is_finish,sum_qty,sum_price,sum_cost_price) {
        const list = document.getElementById(`list-sku-data`);
        // Create tr element
        const row = document.createElement("tr");

        const cal_cost_price = data.Cost_price * data.quantity;
        const cal_price = data.Price * data.quantity;


        // Insert cols
        row.innerHTML = `
        <td>Pic</td>
        <td>${data.Title}</td>
        <td>${data.Description}</td>
        <td>${data.Price}</td>
        <td>${data.quantity}</td>
        <td>${cal_price}</td>
        <td>${data.Cost_price}</td>
        <td>${cal_cost_price}</td>
        `;

        list.appendChild(row);

      }

      addToListTotal(sum_qty,sum_price,sum_cost_price) {
        const list = document.getElementById(`list-sku-data`);
        // Create tr element
        const row = document.createElement("tr");

        	row.innerHTML = `
	        <td colspan="4" style="text-align: center;">รวม</td>
	        <td>${sum_qty}</td>
	        <td>${sum_price}</td>
	        <td></td>
	        <td>${sum_cost_price}</td>
	        `;

	        list.appendChild(row);

        

      }

      deleteSkuAll() {
        const elem = document.querySelectorAll(`#list-sku-data tr`);
        Array.prototype.forEach.call(elem, function (node) {
          node.parentNode.removeChild(node);
        });
      }

    }


});