var edit_id = 0;
var delete_target = "";
var price_id = "";

//document.addEventListener("DOMContentLoaded", displaySupplier);
  class UI_supplier {
  addToList(value,text) {
    const list = document.getElementById(`supplier_price`);
    // Create tr element
    const row = document.createElement("tr");
    // Insert cols
    row.innerHTML = `
      <td>${text}</td><td><input type="text" class="form-control" name="supprice_${value}" id='supprice_${value}'  autocomplete="off"></td>`;



    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#supplier_price tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}

  function displaySupplier() {
  
    const ui = new UI_supplier();
    price_id = "";
    //console.log(arr_usergroups);
    ui.deleteAll();

    for (let i = 0; i < 3; i++) {
      ui.addToList(i);
    }


  }

  const select = document.getElementById('web_supplier_id');


  select.addEventListener('change', () => {

    const ui = new UI_supplier();
    var price_tmp = "";
    var num =1;

    //console.log(arr_usergroups);
    ui.deleteAll();

    //const cnt_opt = select.options;

    for (const option of select.options) {
      if (option.selected) {
        //console.log(option.text);
        ui.addToList(option.value,option.text);
        price_tmp = "supprice_"+option.value;
        if(num > 1){
          price_id += "|"+price_tmp;
        }else{
          price_id = price_tmp;
        }
        num = num+1;
      }

    }
    //console.log(price_id);
    document.getElementById("supprice").value = price_id;
    
  });
