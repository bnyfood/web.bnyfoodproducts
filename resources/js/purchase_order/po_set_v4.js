var edit_id = 0;
var row_index = "";
var arr_po_add = [];
var arr_po_add_val = [];
var arr_po_qty_id = [];
var arr_po_index = 0;
var num_tr_id = 0;
$(document).ready(function() {
  $( function() {

  var urls = hostname_site+"/"+"purchase_order/material_search";

  $( "#material_txt" ).autocomplete({
      source: function( request,response ) {
        $.ajax({
          type: "POST",
          url: urls,
          data:  {krysearch: request.term,web_supplier_id:$("#web_supplier_id").val()}, 
          dataType: "json",
          success: function( data ) {
            response( data.availableTags );
          }
        });
      },
      minLength: 3,
      messages: {
                  noResults: '',
                  results : function(resultsCount) {}
                },
                  select: function( event, ui ) {
                  res_auto(ui.item.value);          
                }
    });
  });

  
  $("#po_qty").keyup(function(event){

    var qty = $(this).val();
    var price = $("#po_price").val();

    var total = 0;
    total = price * qty;
    $("#po_total").val(total);

  });

  $("#po_make_btn").click(function(event) {

    var urls_add = hostname_site+"/"+"purchase_order/sent_data_make_po";
      $.ajax({
      type: "POST",
      url: urls_add,
      data:  {arr_po_add: arr_po_add,arr_po_qty_id:arr_po_qty_id}, 
      dataType: "json",
      success: function (data) {



         $("#material_txt").val(""); 
         $("#po_qty").val("");
         $("#po_price").val("");
         $("#po_total").val("");

      }
    });


  });


  $("#po_add_btn").click(function(event) {

    const ui_modelpricedata = new UI_pricedata();
    ui_modelpricedata.deleteAllModel();
    document.getElementById("compare_div").style.display = "none";

    var web_supplier_id = $("#web_supplier_id").val();
    var web_material_id = $("#web_material_id").val();
    var po_qty = $("#po_qty").val();
    var ran_num_pocode = $("#ran_num_pocode").val();
    //var po_price = $("#po_price").val();
    

    var urls_add = hostname_site+"/"+"purchase_order/po_make";
    $('#spinner-div').show();
      $.ajax({
      type: "POST",
      url: urls_add,
      data:  {web_supplier_id: web_supplier_id,web_material_id:web_material_id,po_qty:po_qty,ran_num_pocode:ran_num_pocode,po_price:99}, 
      dataType: "json",
      success: function (data) {

        document.getElementById("po_print").style.display = "block";

        document.getElementById("po_make_btn").style.display = "block";

        document.getElementById("po_add_btn").style.display = "none";
        //$("#div_po_print").css({"display":"block"});
        //$("#div_po_print").attr("style", "display:block");
        
        const arr_model_datas = data.arr_po_mats;
        const arr_mats = data.arr_mats;
        const arr_po_to_add = data.arr_mats['web_material_id'];
        const arr_suppliers = data.arr_suppliers;

        //console.log(arr_suppliers);


       // arr_po_add_val = [];

        //const arr_po_val_name = "mat_val_name_"+arr_po_to_add;

       // arr_po_add_val[arr_po_val_name] = ({'id': arr_po_to_add,'qty':0});

       const data_po_arr = ({'id': arr_po_to_add,'qty':0});

        arr_po_add.push(data_po_arr);

        arr_po_index = arr_po_add.length-1;

        console.log("index=>"+arr_po_index);
        console.log(arr_po_add);

        supplier_manage(arr_mats,arr_suppliers,arr_po_index);

        /*const ui_modeldata = new UI_modeldata();
        //console.log(arr_usergroups);
        ui_modeldata.deleteAllModel();
        arr_model_datas.forEach(function (arr_model_data) {
          // Add company to UI
          ui_modeldata.addToList(arr_model_data);
          
        });*/

         $("#material_txt").val(""); 
         //$("#web_material_id").val("");
         $("#po_qty").val("");
         $("#po_price").val("");
         $("#po_total").val("");

      },
      complete: function () {
        //do_highligh();
          $('#spinner-div').hide();//Request is complete so hide spinner
      }
    });

  });

$(document).on('keyup','[name="qty[]"]', function() {
  //value = $("#textbox1").val();   

  //var qty_v = $('input[name="qty[]"]').val();

  var qty_v = $(this).val();
  var qty_id_val = $(this).attr('id');


  const qty_arr = qty_id_val.split("_");

  var qty_met_id = qty_arr[1];

  const qty_mat_id_name = "qty_mat_id_"+qty_met_id;

  //arr_po_qty_id[qty_mat_id_name] = qty_v;

  //arr_po_add[row_index]["mat_val_name_"+qty_met_id]['qty'] = qty_v;
  arr_po_add[row_index]['qty'] = qty_v;

  console.log("-------qty[]--------");
  console.log(arr_po_add);

  var sum_id = "";
  var price_id = "";
  var cal_price = "";
  var price_value = "";

  for (let i = 2; i < qty_arr.length; i++) {

    price_id = qty_arr[0]+'_'+qty_arr[i];
    sum_id = qty_arr[0]+'_sum_'+qty_arr[i];
//....[..]
    //price_value = $('.'+price_id).val();

    //price_value = json_access(arr_mat_json,qty_arr[0],i-1);
    price_value = json_access(arr_mat_json,qty_arr[0],qty_arr[i]);

    if (price_value === undefined) {
      price_value = 0;
    }

    cal_price = price_value*qty_v;

    //alert($('#'+price_id).html());

    //alert(sum_id+"_"+cal_price);

    $('#'+sum_id).html(cal_price);
   
  }

  
  //alert(qty_arr[1]);

});

/*  $(".po_all_price").keydown(function(e) {
    // Sets the color when the key is down...
    //alert('yes');
    if(e.which === 13) {
      $(this).css("background-color", "red");
    }
});
$(".po_all_price").keyup(function() {
  //alert('no');
    // Removes the color when any key is lifted...
    $(this).css("background-color", "");
});*/



$(document).on('keyup','[name="po_all_price[]"]', function() {

   // alert('yes');
    $(this).css("background-color", "red");
    $(this).css("color", "#fff");
   // var new_mat_price_v1 = $(this).val();

   var price_id_v1 = $(this).attr('id');
   const arr_price_id_v1 = price_id_v1.split("_");

   var json_val = json_access(arr_mat_json,arr_price_id_v1[0],arr_price_id_v1[1]);
        
    console.log("id>>"+price_id_v1+" val="+json_val);

   /*var urls_ma = hostname_site+"/"+"purchase_order/test_json";

   $.ajax({
      type: "POST",
      url: urls_ma,
      data:  {}, 
      dataType: "json",
      success: function (data) {

        const arr_new = data.arr_new; 

        var json_val = json_access(arr_new,0,2);
        
        console.log(json_val);


      }
    });*/


  });

$(document).on('change','[name="po_all_price[]"]', function() {

   // alert('yes');

   var sup_id_mat_id = $(this).attr('id');

   var mat_price_class_name = $(this).attr("class").get(0);

   const sup_mat_arr = sup_id_mat_id.split("_");

   var met_id = sup_mat_arr[0];
   var sup_id = sup_mat_arr[1];
   var new_mat_price = $(this).val();

   console.log(met_id+"--"+sup_id+"--"+new_mat_price+"--"+mat_price_class_name);

   var urls_ma = hostname_site+"/"+"purchase_order/change_material_price";

   $.ajax({
      type: "POST",
      url: urls_ma,
      data:  {web_material_id: met_id,web_supplier_id:sup_id,new_mat_price:new_mat_price}, 
      dataType: "json",
      success: function (data) {

        const arr_new_price = data.arr_new_price; 

        console.log(arr_new_price);
        
        $(this).css("background-color", "green");
        $(this).css("color", "#000");


      },
      complete: function () {
        console.log("Change price Done");
        //do_highligh();
          //$('#spinner-div').hide();//Request is complete so hide spinner
      }
    });
  });



});

  function res_auto(value){
    //alert(value);
    var web_supplier_id = $("#web_supplier_id").val();

    var urls_ma = hostname_site+"/"+"purchase_order/material_search_by_name";
    //$('#spinner-div').show();
    $.ajax({
      type: "POST",
      url: urls_ma,
      data:  {txt_ma: value,web_supplier_id:web_supplier_id}, 
      dataType: "json",
      success: function (data) {
        
        const arr_mat = data.arr_mat;  

        //console.log("web_material_id_main"+arr_mat['web_material_id_main']);
        //alert(arr_mat['web_material_id_main']);
        $("#web_material_id").val(arr_mat['web_material_id_main']);
        document.getElementById("po_add_btn").style.display = "block";
        //$("#po_price").val(arr_mat['unit_price']);

       /* const arr_po_compares = data.arr_compares;
        const ui_modelpricedata = new UI_pricedata();
        //alert(arr_po_compares.length);
        //console.log(arr_usergroups);
        if(arr_po_compares.length > 0){
          document.getElementById("compare_div").style.display = "block";
          ui_modelpricedata.deleteAllModel();
          arr_po_compares.forEach(function (arr_po_compare) {
            // Add company to UI
            ui_modelpricedata.addToList(arr_po_compare);
          });
        }else{
          const ui_modelpricedata = new UI_pricedata();
          ui_modelpricedata.deleteAllModel();
          document.getElementById("compare_div").style.display = "none";
        }*/

      },
      complete: function () {
        console.log("Auto complete Done");
        //do_highligh();
          //$('#spinner-div').hide();//Request is complete so hide spinner
      }
    });
  }

  var edit_id = 0;
  class UI_modeldata {
  addToList(data) {
    const list = document.getElementById(`list-model-data`);
    // Create tr element
    const row = document.createElement("tr");
    // Insert cols
    var img_name = 'noimage.jpeg';
    var po_total_row = data.material_unit_price * data.qty;

    //var url_del = hostname_site+"/"+"purchase_order/del_action/"+data.web_purchase_material_id;

    row.innerHTML = `
    <td>${data.material_name}</td>
    <td>${data.material_unit_price}</td>
    <td>${data.qty}</td>
    <td>${po_total_row}</td>
    <td>
      <button type="button" id="${data.web_purchase_material_id}" class="btn btn-dark" style="font-size:1em;" value="del_material" data-toggle="modal" data-target="#ModalDelMaterial" title="Delete"><i class="far fa-edit"></i></button></td>
    </td>
    `;

    list.appendChild(row);
  }

  deleteAllModel() {
    const elem = document.querySelectorAll(`#list-model-data tr`);
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}

class UI_pricedata {
  addToList(data) {
    const list = document.getElementById(`list-model-pricedata`);
    // Create tr element
    const row = document.createElement("tr");
    // Insert cols
    var img_name = 'noimage.jpeg';

    row.innerHTML = `
    <td>${data.supplier_name}</td>
    <td>${data.material_name}</td>
    <td>${data.material_unit_price}</td>
    `;

    list.appendChild(row);
  }

  deleteAllModel() {
    const elem = document.querySelectorAll(`#list-model-pricedata tr`);
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}



function openModal() {
  const modal = document.querySelector(".modal");
  const overlay = document.querySelector(".overlay");
  const openModalBtn = document.querySelector(".btn-open");
  const closeModalBtn = document.querySelector(".btn-close"); 

  modal.classList.remove("hidden");
  overlay.classList.remove("hidden");
};

function openModel_v1(){
  // Get the modal
  var modal = document.getElementById("myModal_v1");

  // Get the button that opens the modal
  //var btn = document.getElementById("myBtn");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("closev1")[0];

  var close_btn = document.getElementById("close_btn");

  var change_supplier_btn = document.getElementsByClassName("change_supplier_btn")[0];

  modal.style.display = "block";

  span.onclick = function() {
    modal.style.display = "none";
  }

  close_btn.onclick = function() {
    modal.style.display = "none";
  }

  change_supplier_btn.onclick = function() {

    modal.style.display = "none";
    clear_po();
    
  }

}


$("#web_supplier_id").change(function(event) {

  var web_supplier_id = $("#web_supplier_id").val();
  var web_supplier_id_tmp = $("#web_supplier_id_tmp").val();

  if(web_supplier_id == "0"){
    document.getElementById("mng_material_div").style.display = "none";
  }else{
    document.getElementById("mng_material_div").style.display = "block";
  }

  console.log("tmp->"+web_supplier_id_tmp+" >> id-> "+web_supplier_id);

  if(web_supplier_id == "0"){
    console.log("no1");
  }else{  
    if(web_supplier_id_tmp != "0"){

      if(web_supplier_id != web_supplier_id_tmp){

        console.log("yes");
        $("#web_supplier_id_tmp").val(web_supplier_id);
        openModel_v1();
        
      }

    }else{
      console.log("no2");
      $("#web_supplier_id_tmp").val(web_supplier_id);
    }
  }

  });


function clear_po(){

  document.getElementById("po_print").style.display = "none";
  
  var ran_num_pocode = $("#ran_num_pocode").val();

  var urls_del = hostname_site+"/"+"purchase_order/po_del_by_code";

    $.ajax({
    type: "POST",
    url: urls_del,
    data:  {ran_num_pocode:ran_num_pocode}, 
    dataType: "json"
  })
  .done(function( data ) {  
    
    const ui_modeldata = new UI_modeldata();
    ui_modeldata.deleteAllModel();

  });

}

$('#po_print').click(function(){
  var w=1200;
  var h=800;
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);

  var ran_num_pocode=$("#ran_num_pocode").val();
  
  var urls = hostname_site+"purchase_order/print_po/"+ran_num_pocode;
  //alert(urls);
  return window.open(urls, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  return false;
});


document.getElementById("list-model-data").addEventListener("click", function (e) {

  if (e.target.value === "del_material") {
    // Instantiate UI

    edit_id = e.target.id;
    delete_target = e;

  } 

  e.preventDefault();
});

/*document.getElementById("del_material_btn2").addEventListener("click", function (e) {
  //console.log(edit_id);

  // Instantiate UI
  const ui_modeldata = new UI_modeldata();
  var ran_num_pocode = $("#ran_num_pocode").val();

  var url_del_material = hostname_site+"/"+"purchase_order/del_material_action/"+edit_id+"/"+ran_num_pocode;
  $('#spinner-div').show();
    $.ajax({
      type: "POST",
      url: url_del_material,
      dataType: "json",
      success: function (data) {

        hideModal("#ModalDelMaterial");
        //$('#ModalDelMaterial').modal('hide');

        const arr_model_datas = data.arr_po_mats;
        const ui_modeldata = new UI_modeldata();
        //console.log(arr_usergroups);
        ui_modeldata.deleteAllModel();
        arr_model_datas.forEach(function (arr_model_data) {
          // Add company to UI
          ui_modeldata.addToList(arr_model_data);
        });

      },
      complete: function () {
        //do_highligh();
          $('#spinner-div').hide();//Request is complete so hide spinner
      }
    });
});*/


function hideModal(cla) {
  $(".close").click();
  $(cla).hide();
  edit_id = 0;
  delete_target = "";
}

function supplier_manage(arr_mats,arr_suppliers,arr_po_index){

  const ui_sup_man_data = new UI_supmandata();

  //var num_price =1;
  //console.log(arr_usergroups);
  //ui_sup_man_data.deleteAllModel();

  /*arr_mats.forEach(function (arr_mat) {
    // Add company to UI
    ui_sup_man_data.addToList(arr_mat,arr_suppliers,num_price);
    num_price = num_price+1;
  });*/

  ui_sup_man_data.addToList(arr_mats,arr_suppliers,arr_po_index);

}

var arr_mat_json = [];
var arr_mat_json_tmp = [];

class UI_supmandata {

  

  addToList(data,arr_suppliers,arr_po_index) {

    var tr_id = "tr_"+num_tr_id;

    arr_mat_json_tmp = [];

    const list = document.getElementById(`list-sup-data`);
    // Create tr element
    const row = document.createElement("tr");
    //var id_tr_del = arr_po_index+'_tr_del';
    row.setAttribute("id", tr_id);
    // Insert cols
    //var url_del = hostname_site+"/"+"purchase_order/del_action/"+data.web_purchase_material_id;
    const arr_prices = data.data_price;
    //console.log(arr_suppliers);
    var td_price = ``;
    var td_sum = ``;
    var id_del = "tr_"+num_tr_id;
    //var id_qty_price = 'qty_'+arr_po_index;
    var id_qty_price = num_tr_id+'_'+data.web_material_id;

    arr_mat_json_tmp.push(data.material_name);

    var num_price_id = 1;
    arr_suppliers.forEach(function (arr_supplier) {

    var id_price_val = num_tr_id+"_"+num_price_id;

    var var_matid_supid =  data.web_material_id+"_"+arr_supplier.web_supplier_id;

    //console.log(arr_supplier.supplier_name);
    var var_price = arr_prices[arr_supplier.supplier_name]['unit_price'];
    var id_price = num_tr_id+'_'+arr_supplier.supplier_name;
    var id_sum_price = num_tr_id+'_sum_'+arr_supplier.supplier_name;
    id_qty_price +=  '_'+arr_supplier.supplier_name;

    if (var_price == '0'){
        var_price = "-";
      }

      arr_mat_json_tmp.push(var_price);

      //td_price = td_price + `<td id='${id_price}'>${var_price}</td>`;

      td_price = td_price + `<td id='${id_price}'><input type='number' name='po_all_price[]' id='${id_price_val}' value='${var_price}' class='${id_price_val} form-control'></td>`;

      td_sum += `<td id='${id_sum_price}'></td>`;

      num_price_id = num_price_id+1;
    });

    row.innerHTML = `
    <td>${data.material_name}</td>
    <td><input type='number' name='qty[]' id='${id_qty_price}' class='form-control'> </td>
    ${td_price}
    ${td_sum}
    <td>
      <button type="button" id="${id_del}" class="btn btn-dark" style="font-size:1em;" value="del_material" data-toggle="modal" data-target="#ModalDelMaterial" title="Delete">
      <i class="far fa-edit"></i></button>
    </td>
    `;

    list.appendChild(row);

    /*const data_po_arr_tmp = (
      data.material_name
    );*/

        arr_mat_json.push(arr_mat_json_tmp);

        console.log(arr_mat_json);

        num_tr_id = num_tr_id+1;
  }

  deleteAllModel() {
    const elem = document.querySelectorAll(`#list-sup-data tr`);
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}

document.getElementById("del_material_btn").addEventListener("click", function (e) {
  console.log(edit_id);
 // alert($(this).index());

  //alert(row_index);

  const arr_val_id = edit_id.split("_");
  const id_index_del = arr_val_id[0];

  //console.log(id_index_del);

 
  //document.getElementById("list-sup-data").deleteRow(row_index);
  //delete arr_po_add[id_index_del];
  
  deleteRow(edit_id);

  arr_po_add.splice(row_index,1);

  console.log("------json------");
  console.log(arr_po_add);
  console.log("------json------");

  var json_data_po = JSON.stringify(arr_po_add);


  var urls_data_po = hostname_site+"/"+"purchase_order/sent_data_make_po";
    //$('#spinner-div').show();
    $.ajax({
      type: "POST",
      url: urls_data_po,
      data:  {arr_po_add:arr_po_add}, 
      dataType: "json"
    })
    .done(function( data ) {  
      
      console.log('success send arr data po');

    });
  
});

document.getElementById("list-sup-data").addEventListener("click", function (e) {

// alert(e.target.id);
  edit_id = e.target.id;

 //row_index =  e.rowIndex

 //alert("row_index="+row_index);

 //var id_row_tr = e.parentNode.parentNode.id;
 //alert(id_row_tr);

//var rowJavascript = e.parentNode.parentNode;

var rowJavascript = e.target.parentNode.parentNode;
row_index = rowJavascript.rowIndex - 2;

//var rowjQuery = $(e).closest("tr");
//alert("JavaScript Row Index : " + (rowJavascript.rowIndex - 2));

//alert( "jQuery Row Index : " + (rowjQuery[0].rowIndex));

});


function deleteRow(rowid)  
{   
    var row = document.getElementById(rowid);
    var table = row.parentNode;
    while ( table && table.tagName != 'TABLE' )
        table = table.parentNode;
    if ( !table )
        return;
    table.deleteRow(row.rowIndex);
}

function delrow2() {
  document.getElementById("myTable").deleteRow(0);
}

function insert_row() {
  var table = document.getElementById("myTable");
  var row = table.insertRow(0);
  var cell1 = row.insertCell(0);
  var cell2 = row.insertCell(1);
  cell1.innerHTML = "<input type='checkbox' name='aaa' >";
  cell2.innerHTML = '<input type="text" name="tese">';
}

function json_access(data,row,col){

  //const obj = JSON.parse(data);


  //console.log(obj[row]);
  const obj2 = Object.values(data[row]);


  return obj2[col];

}






