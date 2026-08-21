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
      build_multi_price(arr_model,arr_model2);
    });

    function build_multi_price(arr1,arr2){

        var variant1 = $("#variant1").val();
        var variant2 = $("#variant2").val();
        document.getElementById("variant1_val").innerHTML = variant1;
        document.getElementById("variant2_val").innerHTML = variant2;

        const ui_model = new UI_model_multi();
        ui_model.deleteAll();
        num1 = arr1.length;
        num2 = arr2.length;
        var num3=1;
        if(num1 > 0 && num2 >0){
           for (var i = 0; i < num1; i++) {
            var txtname1 = "#text"+arr1[i];
            var txtval1 = $(txtname1).val();
            //alert(txtval1);
                for (var j = 0; j < num2; j++) {
                    var txtname2 = "#text2_"+arr2[j];
                    var txtval2 = $(txtname2).val();
                    ui_model.addToList(txtval1,txtval2,num2,num3);
                    num3 = num3+1;
                    //alert(txtval2);

                }
           }
        }
    }

    class UI_model_multi {
      addToList(txtval1,txtval2,num2,num3) {
        const list = document.getElementById(`list-model-data`);
        // Create tr element
        const row = document.createElement("tr");
        const name_price = `price_${num3}`
        const name_quantity = `quantity_${num3}`
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
        <td><input type="text" name="${name_price}"></td>
        <td><input type="text" name="${name_quantity}"></td>
        `;

        list.appendChild(row);
      }

      deleteAll() {
        const elem = document.querySelectorAll(`#list-model-data tr`);
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

   $("#sku_name").change(function(event){

    var Skuname = $(this).val();
    //const ProductDiscountStore = JSON.parse(
      //localStorage.getItem("ProductSkuStore")
    //);

    localStorage.setItem(
      "ProductSkuStore",
      JSON.stringify({
        sku_name : Skuname
      })
    );
  }); 

$('#tokenfield').tokenfield({
  autocomplete: {
    source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
    delay: 100
  },
  showAutocompleteOnFocus: true
})



});
