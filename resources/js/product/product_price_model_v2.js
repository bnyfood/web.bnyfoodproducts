$(document).ready(function() {
    var intId=0;
    var intId2=0;
    var arr_model = [];
    var arr_model2 = [];
    $("#addfield").click(function() {

    	var lastField = $("#buildyourform div:last");
         intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
         arr_model.push(intId);
         const model_json = JSON.stringify(arr_model);
         $("#arr_model").val(model_json);
         //console.log(arr_model);
        var num1 = 0;
        var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId + "\"/>");
        fieldWrapper.data("idx", intId);
        var fName = $("<input type=\"text\" class=\"fieldname\" name=\"text"+intId+"\" id=\"text"+intId+"\" />");
        var fType = $("<input type=\"file\" name=\"file"+intId+"\" />");
        var removeButton = $("<input type=\"button\" class=\"remove\" value=\"-\" id=\""+intId+"\" />");
        removeButton.click(function() {
            $(this).parent().remove();

            //alert(this.id);
            const index_del = this.id;
            const index_model = arr_model.indexOf(parseInt(index_del));
            if (index_model > -1) {
              arr_model.splice(index_model, 1);
              build_multi_price(arr_model,arr_model2);
              const model_json = JSON.stringify(arr_model);
              $("#arr_model").val(model_json);
              //console.log(arr_model);
            }
        });
        fieldWrapper.append(fName);
        fieldWrapper.append(fType);
        fieldWrapper.append(removeButton);
        $("#buildyourform").append(fieldWrapper);
    });

    $("#addfield2").click(function() {

        var lastField = $("#buildyourform2 div:last");
         intId2 = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
         arr_model2.push(intId2);
         const model_json2 = JSON.stringify(arr_model2);
              $("#arr_model2").val(model_json2);
         //console.log(arr_model2);
        
        var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId2 + "\"/>");
        fieldWrapper.data("idx", intId2);
        var fName = $("<input type=\"text\" class=\"fieldname\" name=\"text2_"+intId2+"\" id=\"text2_"+intId2+"\" />");
        var fType = $("<input type=\"file\" name=\"file2_"+intId2+"\" />");
        var removeButton = $("<input type=\"button\" class=\"remove\" value=\"-\" id=\""+intId2+"\" />");
        removeButton.click(function() {
            $(this).parent().remove();
            
            const index_del2 = this.id;
            const index_model2 = arr_model.indexOf(parseInt(index_del2));
            if (index_model2 > -1) {
              arr_model2.splice(index_model2, 1);
              build_multi_price(arr_model,arr_model2);
              const model_json2 = JSON.stringify(arr_model2);
              $("#arr_model2").val(model_json2);
              //console.log(arr_model2);
            }
        });
        fieldWrapper.append(fName);
        fieldWrapper.append(fType);
        fieldWrapper.append(removeButton);
        $("#buildyourform2").append(fieldWrapper);
    });

    $("#build_multiprice").click(function() {
      build_multi_price();

    });
    $("#variant1_val").change(function(event){

      build_multi_price();

    });
    $("#variant2_val").change(function(event){

      build_multi_price();

    });

   /* $("#variant1").change(function(event){

      build_multi_price();

    });

    $("#variant2").change(function(event){

      build_multi_price();

    });*/

    function build_multi_price(){

      var variant1 = $("#variant1").val();
      var variant2 = $("#variant2").val();
      document.getElementById("variant1_txt").innerHTML = variant1;
      document.getElementById("variant2_txt").innerHTML = variant2;

      var variant1_val = $("#variant1_val").val();
      var variant2_val = $("#variant2_val").val();

      const ui_model = new UI_model_multi();
      ui_model.deleteAll();
      ui_model.deleteAllFile();

      const variant1_arr_val = variant1_val.split(",");
      const variant2_arr_val = variant2_val.split(",");


      num1 = variant1_arr_val.length;
      num2 = variant2_arr_val.length;

      var numfile = 1;
      
      var num4=1;
      if(num1 > 0){
         for (var i = 0; i < num1; i++) {
          var txtval1 = variant1_arr_val[i];
          var txtfile = variant1 + " " + txtval1;
          ui_model.addFileToList(txtfile,numfile);
          numfile = numfile+1;
          var num3=1;
          //alert(txtval1);
              for (var j = 0; j < num2; j++) {
                var txtval2 = "-";
                  if(j >= 0){
                    txtval2 = variant2_arr_val[j];
                  }
                  
                  ui_model.addToList(txtval1,txtval2,num2,num3,num4);
                  num3 = num3+1;
                  //alert(txtval2);

              }

            num4 = num4+1;  
         }
      }
    }

    class UI_model_multi {
      addToList(txtval1,txtval2,num2,num3,num4) {
        const list = document.getElementById(`list-model-data`);
        // Create tr element
        const row = document.createElement("tr");
        const name_price = `price_${num4}_${num3}`
        const name_quantity = `quantity_${num4}_${num3}`
        const name_weight = `weight_${num4}_${num3}`
        var class_price = `${txtval1}_${txtval2}_price`
        var class_quantity = `${txtval1}_${txtval2}_quantity`
        var class_weight = `${txtval1}_${txtval2}_weight`

        var tbl_v1 = "";

        if(num2 == 1 ){
          tbl_v1 = `<td>${txtval1}</td>`;
        }else if(num2 > 1){
          if((num3%num2) == 1){
            tbl_v1 = `<td rowspan="${num2}">${txtval1}</td>`;
          }
        }
        // Insert cols
        row.innerHTML = `
        ${tbl_v1}
        <td>${txtval2}</td>
        <td><input type="text" name="${name_price}" id="pro_price" class="${class_price}"></td>
        <td><input type="text" name="${name_quantity}" id="pro_qty" class="${class_quantity}"></td>
        <td><input type="text" name="${name_weight}" id="pro_weight" class="${class_weight}"></td>
        `;

        list.appendChild(row);

      }

      addFileToList(txtfile,num3){

        const list_file = document.getElementById(`list-model-file`);
        const name_file = `file_${num3}`;
        const row_file = document.createElement("tr");

        row_file.innerHTML = `
        <td> ${txtfile} : <input type="file" name="${name_file}"></td>
        `;

        list_file.appendChild(row_file);

      }

      deleteAll() {
        const elem = document.querySelectorAll(`#list-model-data tr`);
        Array.prototype.forEach.call(elem, function (node) {
          node.parentNode.removeChild(node);
        });
      }

      deleteAllFile() {
        const elem = document.querySelectorAll(`#list-model-file tr`);
        Array.prototype.forEach.call(elem, function (node) {
          node.parentNode.removeChild(node);
        });
      }

    }

    $("#preview").click(function() {
        $("#yourform").remove();
        var fieldSet = $("<fieldset id=\"yourform\"><legend>Your Form</legend></fieldset>");
        $("#buildyourform div").each(function() {
            var id = "input" + $(this).attr("id").replace("field","");
            var label = $("<label for=\"" + id + "\">" + $(this).find("input.fieldname").first().val() + "</label>");
            var input;
            switch ($(this).find("select.fieldtype").first().val()) {
                case "checkbox":
                    input = $("<input type=\"checkbox\" id=\"" + id + "\" name=\"" + id + "\" />");
                    break;
                case "textbox":
                    input = $("<input type=\"text\" id=\"" + id + "\" name=\"" + id + "\" />");
                    break;
                case "textarea":
                    input = $("<textarea id=\"" + id + "\" name=\"" + id + "\" ></textarea>");
                    break;    
            }
            fieldSet.append(label);
            fieldSet.append(input);
        });
        $("body").append(fieldSet);
    });

   $("#Title").change(function(event){
    
    var Title = $(this).val();
    var Skuname = $("#sku_name").val();
    var ran_id = $("#ran_id").val();
    //const ProductDiscountStore = JSON.parse(
      //localStorage.getItem("ProductSkuStore")
    //);

    localStorage.setItem(
      "ProductSkuStore",
      JSON.stringify({
        title:Title,
        sku_name : Skuname,
        ran_id:ran_id
      })
    );
  });  

   $("#sku_name").change(function(event){

    var Skuname = $(this).val();
    var Title = $("#Title").val();
    var ran_id = $("#ran_id").val();
    //const ProductDiscountStore = JSON.parse(
      //localStorage.getItem("ProductSkuStore")
    //);

    localStorage.setItem(
      "ProductSkuStore",
      JSON.stringify({
        title:Title,
        sku_name : Skuname,
        ran_id:ran_id
      })
    );
  }); 

  $("#is_model").change(function(e){
    e.stopPropagation();
    if($(this).is(":checked")){
      document.getElementById("show_model").style.display = "block";
      document.getElementById("show_model_detail").style.display = "block";
    }else{

      document.getElementById("show_model").style.display = "none";
      document.getElementById("show_model_detail").style.display = "none";
    } 
  });  

  // Check required fields
function checkRequired(inputArr) {
  var err_status = true;
  inputArr.forEach(function (input) {
    if (input.value.trim() === "") {
      ///console.log(">>"+input.value);
      showError(input, `${getFieldName(input)} is required`);
      err_status = false;
    } else {
      showSuccess(input);
      //console.log(input.value);
      if (err_status) {
        err_status = true;
      } else {
        err_status = false;
      }
    }
  });
  return err_status;
}

// Show input error message.
function showError(input, message) {
  const formControl = input.parentElement;
  // console.log(formControl);

  const small = formControl.querySelector("#err_valid");
  small.innerText = message;
}

// Show success outline
function showSuccess(input) {
  const formControl = input.parentElement;

  const small = formControl.querySelector("#err_valid");
  small.innerText = "";
}

$('.tokenfield').tokenfield({

})

$('.tokenfield2').tokenfield({

})


$("#Price").keyup(function(event){

  var PriceMain = $(this).val();
  var PriceSaleVal = $("#Pricesale").val();
  var sale_per = 0;

  /*if(PriceSaleVal == ""){
    PriceSaleVal = 0;
  }else{
  sale_per = (PriceSaleVal-PriceMain)*100/PriceMain;
    $("#Pricesale_per").val(sale_per+"%");
  }*/

  cal_dis_to_per(PriceMain,PriceSaleVal);


});

$("#Pricesale").keyup(function(event){

  var PriceSale = $(this).val();
  var PriceMainVal = $("#Price").val();
  var sale_per = 0;

  /*if(PriceMainVal == ""){
    PriceSale = 0;
  }else{
  sale_per = (PriceSale-PriceMainVal)*100/PriceMainVal;
    $("#Pricesale_per").val(sale_per+"%");
  }*/

  cal_dis_to_per(PriceMainVal,PriceSale);
  
});

$("#Pricesale_per").keyup(function(event){

  var PriceMainVal = $("#Price").val();
  var PriceSale = $("#Pricesale").val();
  var sale_per = $(this).val();

  /*if(PriceMainVal == ""){
    PriceSale = 0;
  }else{
  sale_per = (PriceSale-PriceMainVal)*100/PriceMainVal;
    $("#Pricesale_per").val(sale_per+"%");
  }*/

  cal_per_to_dis(PriceMainVal,PriceSale,sale_per);
  
});



//$("#list-model-data tr input").keyup(function(event){
  $('#list-model-data tr input').live('keyup', function (e) {

  var PriceSale = $(this).val();
  //alert(PriceSale);
  var class_name = $(this).attr('class');
  //alert(class_name);

  arr_promodel[product_id] = quan;

  localStorage.setItem(
          "ProductModelStore",
          JSON.stringify({arr_promodel:arr_promodel})
        );
  
  });


  $('#price_model').live('keyup', function (e) {  

    var PriceModel = $(this).val();
    var DiscountVal = $("#discount_model").val();
    var Price_per = $("#price_per_model").val();

    //alert(PriceModel+"-----"+DiscountVal+"------"+Price_per);

    cal_dis_to_per(PriceModel,DiscountVal);

    


  });

  $('#discount_model').live('keyup', function (e) {  

    var PriceModel = $("#price_model").val();
    var DiscountVal = $(this).val();
    var Price_per = $("#price_per_model").val();

    //alert(PriceModel+"-----"+DiscountVal+"------"+Price_per);

    cal_dis_to_per(PriceModel,DiscountVal);

  });

  $('#price_per_model').live('keyup', function (e) {  

    var PriceModel = $("#price_model").val();
    var DiscountVal = $("#discount_model").val();
    var Price_per = $(this).val();

    //alert(PriceModel+"-----"+DiscountVal+"------"+Price_per);

    cal_per_to_dis(PriceModel,DiscountVal,Price_per);

  });

  function cal_dis_to_per(price,discount){

    var sale_per = 0;

    /*if(price == ""){
      price = 0;
    }

    if(discount == ""){
      discount = 0;
    }*/

    if(price!="" && discount!=""){
      sale_per = (discount-price)*100/price;
    }

    $("#Price").val(price);
    $("#Pricesale").val(discount);
    $("#Pricesale_per").val(sale_per);

    $("#price_model").val(price);
    $("#discount_model").val(discount);
    $("#price_per_model").val(sale_per);

  }

  function cal_per_to_dis(price,discount,per){

    //var sale_per = 0;

    /*if(price == ""){
      price = 0;
    }

    if(discount == ""){
      discount = 0;
    }

    if(per == ""){
      per = 0;
    }*/

    per_pos =  Math.abs(per);

    if(price != "" && discount!="" && per != "" && per!= "-"){

      dis_num = (price*per_pos)/100;
      if(per < 0){
        discount = parseInt(price)-parseInt(dis_num);
      }else{
        discount = parseInt(price)+parseInt(dis_num);
      }
      
    }

    $("#Price").val(price);
    $("#Pricesale").val(discount);
    $("#Pricesale_per").val(per);

    $("#price_model").val(price);
    $("#discount_model").val(discount);
    $("#price_per_model").val(per);

  }


});
