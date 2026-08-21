var edit_id = 0;
var row_index = "";
var arr_po_add_val = [];
var arr_po_qty_id = [];
var arr_po_index = 0;
var num_tr_id = 0;
var arr_mat_json = [];
var arr_mat_json_tmp = [];
var supplier_point = 0; 
var table_point_row = 0;
var table_point_col = 0;
var table_point_row_befor = 0;
var table_point_col_befor = 0;
var tablt_first_col = 1;
var tablt_last_col = 5;

var table_point = "";
var tbl_row_length = 0;
var tbl_col_length = 0;

var data_mat_json = "";


$(document).ready(function() {

  $(function() {
    const urls = hostname_site+"/"+"purchase_order/material_get_all";
    $.ajax({
        type: "POST",
        url: urls,
        dataType: "json",
        success: function( data ) {
          //console.log(data.arr_pos);

          data_mat_json = data.arr_pos.Data;
        }
      });

  });

  $("#web_supplier_id").change(function(event) {

    document.getElementById("all_material_sup").style.display = "block";

    var web_supplier_id_val = $(this).val();

   // alert(web_supplier_id_val);

   console.log(data_mat_json);

   const ui_mat_alldata = new UI_matdata();
   ui_mat_alldata.deleteAllModel();

   data_mat_json.forEach(function (arr_mat) {
    if(web_supplier_id_val == arr_mat.web_supplier_id){
      console.log(arr_mat.material_name);
      ui_mat_alldata.addToList(arr_mat);

    }
   });

  });


  $(document).on('click','#list-all-mat tr', function() {

    const web_material_id = $(this).attr('value');
    const web_supplier_id = $("#web_supplier_id").val();
    const ran_num_pocode = $("#ran_num_pocode").val();
    //alert(qty_v);

    var urls_add = hostname_site+"/"+"purchase_order/po_make";
    $('#spinner-div').show();
      $.ajax({
      type: "POST",
      url: urls_add,
      data:  {web_supplier_id: web_supplier_id,web_material_id:web_material_id,ran_num_pocode:ran_num_pocode}, 
      dataType: "json",
      success: function (data) {

        document.getElementById("po_print").style.display = "block";

        document.getElementById("po_make_btn").style.display = "block";

        //document.getElementById("tr_mat_"+web_material_id).style.display = "none";

        //$("#div_po_print").css({"display":"block"});
        //$("#div_po_print").attr("style", "display:block");

        console.log(data.arr_mats);
        
        const arr_model_datas = data.arr_po_mats;
        const arr_mats = data.arr_mats;
        const arr_po_to_add = web_material_id;
        const arr_suppliers = data.arr_suppliers;

        //console.log(arr_suppliers);

        supplier_manage(arr_mats,arr_suppliers,web_material_id);


         $("#material_txt").val(""); 
         //$("#web_material_id").val("");

         $("#po_total").val("");

      },
      complete: function () {
        //do_highligh();
          $('#spinner-div').hide();//Request is complete so hide spinner
      }
    });
  });


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

    var urls_data_po = hostname_site+"/"+"purchase_order/sent_data_make_po";
    //$('#spinner-div').show();
    $.ajax({
      type: "POST",
      url: urls_data_po,
      data:  {web_supplier_id:$("#web_supplier_id").val(),arr_po_add:arr_mat_json}, 
      dataType: "json"
    })
    .done(function( data ) {  
      
      console.log('success send arr data po');

    });


  });


  $("#po_add_btn").click(function(event) {

    const ui_modelpricedata = new UI_pricedata();
    ui_modelpricedata.deleteAllModel();

    var web_supplier_id = $("#web_supplier_id").val();
    var web_material_id = $("#web_material_id").val();
    var ran_num_pocode = $("#ran_num_pocode").val();

    

    var urls_add = hostname_site+"/"+"purchase_order/po_make";
    $('#spinner-div').show();
      $.ajax({
      type: "POST",
      url: urls_add,
      data:  {web_supplier_id: web_supplier_id,web_material_id:web_material_id,ran_num_pocode:ran_num_pocode}, 
      dataType: "json",
      success: function (data) {

        document.getElementById("po_print").style.display = "block";

        document.getElementById("po_make_btn").style.display = "block";

        document.getElementById("po_add_btn").style.display = "none";
        //$("#div_po_print").css({"display":"block"});
        //$("#div_po_print").attr("style", "display:block");

        console.log(data.arr_mats);
        
        const arr_model_datas = data.arr_po_mats;
        const arr_mats = data.arr_mats;
        const arr_po_to_add = web_material_id;
        const arr_suppliers = data.arr_suppliers;

        //console.log(arr_suppliers);

        supplier_manage(arr_mats,arr_suppliers,web_material_id);

        
        

        

        /*const ui_modeldata = new UI_modeldata();
        //console.log(arr_usergroups);
        ui_modeldata.deleteAllModel();
        arr_model_datas.forEach(function (arr_model_data) {
          // Add company to UI
          ui_modeldata.addToList(arr_model_data);
          
        });*/

         $("#material_txt").val(""); 
         //$("#web_material_id").val("");

         $("#po_total").val("");

      },
      complete: function () {
        //do_highligh();
          $('#spinner-div').hide();//Request is complete so hide spinner
      }
    });

  });

/*$(document).on('change','[name="qty[]"]', function() {
//  $(document).bind('keyup focus','[name="qty[]"]', function() {

    //alert('hi');
  //value = $("#textbox1").val();   

  //var qty_v = $('input[name="qty[]"]').val();

  var qty_v = $(this).val();
  var qty_id_val = $(this).attr('id');


  const qty_arr = qty_id_val.split("_");

  var qty_met_id = qty_arr[1];

  arr_mat_json[qty_arr[0]][1] = qty_v;
  var qty_val_json = json_access(arr_mat_json,null,qty_arr[0],1);

  var sum_id = "";
  var price_id = "";
  var cal_price = "";
  var price_value = "";

  for (let i = 2; i < qty_arr.length; i++) {

    const cal_id = i+4;

    price_id = qty_arr[0]+'_'+qty_arr[i];
    sum_id = qty_arr[0]+'_'+cal_id;


    price_value = json_access(arr_mat_json,'price',qty_arr[0],i);
    //price_value = json_access(arr_mat_json,qty_arr[0],qty_arr[i]);

    if (price_value === undefined) {
      price_value = 0;
    }

    cal_price = price_value*qty_val_json;


    //console.log(sum_id+"_"+price_value);

    $('#'+sum_id).html(cal_price);

   //console.log(supplier_name_text);
   
  }

  const supplier_name_text = $("#web_supplier_id option:selected").text();

  $('#sum_sup_name').html(supplier_name_text);

  cal_sum_price();

  document.getElementById("table_sum_po_price").style.display = "block";

  
  //alert(qty_arr[1]);

});*/

$(document).on('click','[name="qty[]"]', function() {
  //alert('fo');
  const table_id_action = $(this).attr('id');
  //console.log("table_id_action>>"+table_id_action);

  const arr_table_id_action = table_id_action.split("_");

  table_point_row = parseInt(arr_table_id_action[0]);
  table_point_col = parseInt(arr_table_id_action[1]);

  console.log("row>>"+arr_table_id_action[0]+" col>>"+arr_table_id_action[1]);

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

    //alert('yes');
   

    $(this).css("background-color", "red");
    $(this).css("color", "#fff");
   // var new_mat_price_v1 = $(this).val();

   var price_id_v1 = $(this).attr('id');
   const arr_price_id_v1 = price_id_v1.split("_");

   arr_mat_json[arr_price_id_v1[0]][arr_price_id_v1[1]]['price']=$(this).val();

   const json_val = json_access(arr_mat_json,'price',arr_price_id_v1[0],arr_price_id_v1[1]);

   const cal_qty = json_access(arr_mat_json,null,arr_price_id_v1[0],1);

   const cal_row = arr_price_id_v1[0];
   const cal_col = parseInt(arr_price_id_v1[1])+4;

   cal_price = cal_qty*json_val;

   //console.log("cal_price=>"+cal_price+" cal_row=>"+cal_row+" cal_col=>"+cal_col);

   $('#'+cal_row+"_"+cal_col).html(cal_price);

   table_point_row = parseInt(arr_price_id_v1[0]);
   table_point_col = parseInt(arr_price_id_v1[1]);

   //console.log("table_point_row>>"+table_point_row+" table_point_col>>"+table_point_col);
        
    //console.log("id>>"+price_id_v1+" val="+json_val);

    //console.log(arr_mat_json);

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

    
   var this_val = $(this);
   var sup_id_mat_id = $(this).attr('id');


   //var mat_price_class_name = $(this).attr("class").get(0);

   const sup_mat_arr = sup_id_mat_id.split("_");

   const met_id = json_access(arr_mat_json,'web_material_id',sup_mat_arr[0],sup_mat_arr[1]);
   var sup_id = json_access(arr_mat_json,'sup_id',sup_mat_arr[0],sup_mat_arr[1]);
   var new_mat_price = json_access(arr_mat_json,'price',sup_mat_arr[0],sup_mat_arr[1]);

   console.log(met_id+"--"+sup_id+"--"+new_mat_price);

   var urls_ma = hostname_site+"/"+"purchase_order/change_material_price";

   $.ajax({
      type: "POST",
      url: urls_ma,
      data:  {web_material_id: met_id,web_supplier_id:sup_id,new_mat_price:new_mat_price}, 
      dataType: "json",
      success: function (data) {

        const arr_new_price = data.arr_new_price; 

        console.log(arr_new_price);
        
        cal_sum_price();


      },
      complete: function () {

        this_val.css("background-color", "green");
        this_val.css("color", "#fff");
        console.log("Change price Done");
        //do_highligh();
          //$('#spinner-div').hide();//Request is complete so hide spinner
      }
    });

  });



});

$(document).on('click','[name="po_all_price[]"]', function() {
  //alert('fo');
  const table_id_action_2 = $(this).attr('id');
  //console.log("table_id_action>>"+table_id_action);

  const arr_table_id_action_2 = table_id_action_2.split("_");

  table_point_row = parseInt(arr_table_id_action_2[0]);
  table_point_col = parseInt(arr_table_id_action_2[1]);

  console.log("row>>"+arr_table_id_action_2[0]+" col>>"+arr_table_id_action_2[1]);

  });

function cal_sum_price(){
  /*$('input[name="qty[]"]').each(function(){
    alert($(this).val());
  });*/

  const cnt_all_mat = arr_mat_json.length;
  var sum_price = 0;

  for (let j = 0; j <= cnt_all_mat-1; j++) {

    //0,1 1,1

    const qty_val = json_access(arr_mat_json,null,j,1);
    const mat_del = json_access(arr_mat_json,'is_del',j,0);
    const mat_price = json_access(arr_mat_json,'price',j,supplier_point);
    var price_x_qty = 0;

    if(mat_del == 'no'){

      price_x_qty = mat_price*qty_val;

      sum_price = sum_price+price_x_qty;

    }

    //console.log(qty_val);

  }


$('#sum_po_price').html(sum_price);
  
}

  function res_auto(value){
    //alert(value);
    var web_supplier_id = $("#web_supplier_id").val();

    var urls_ma = hostname_site+"/"+"purchase_order/material_search_by_name";
    $('#spinner-div').show();

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
        //console.log("Auto complete Done");
        //do_highligh();
          $('#spinner-div').hide();//Request is complete so hide spinner
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

class UI_matdata {
  addToList(data) {
    const list = document.getElementById(`list-all-mat`);
    // Create tr element
    const row = document.createElement("tr");

    const tr_mat_id = 'tr_mat_'+data.web_material_id;

    row.setAttribute("id", tr_mat_id);

    row.setAttribute("value", data.web_material_id);
    // Insert cols

    row.innerHTML = `
    <td>${data.material_name}</td>
    <td>${data.material_size} ${data.material_unit}</td>
    `;

    list.appendChild(row);

  }

  deleteAllModel() {
    const elem = document.querySelectorAll(`#list-all-mat tr`);
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


/*$("#web_supplier_id").change(function(event) {

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

  });*/


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

function supplier_manage(arr_mats,arr_suppliers,web_material_id){

  const ui_sup_man_data = new UI_supmandata();

  //var num_price =1;
  //console.log(arr_usergroups);
  //ui_sup_man_data.deleteAllModel();

  /*arr_mats.forEach(function (arr_mat) {
    // Add company to UI
    ui_sup_man_data.addToList(arr_mat,arr_suppliers,num_price);
    num_price = num_price+1;
  });*/

  ui_sup_man_data.addToList(arr_mats,arr_suppliers,web_material_id);

}

function cal_amount(obj){
  //console.log(obj.getAttribute("qty_val"));
  var qty_v = obj.value;
  var qty_id_val = obj.getAttribute("qty_val");



  const qty_arr = qty_id_val.split("_");

  var qty_met_id = qty_arr[1];

  arr_mat_json[qty_arr[0]][1] = qty_v;
  var qty_val_json = json_access(arr_mat_json,null,qty_arr[0],1);

  var sum_id = "";
  var price_id = "";
  var cal_price = "";
  var price_value = "";

  for (let i = 2; i < qty_arr.length; i++) {

    const cal_id = i+4;

    price_id = qty_arr[0]+'_'+qty_arr[i];
    sum_id = qty_arr[0]+'_'+cal_id;


    price_value = json_access(arr_mat_json,'price',qty_arr[0],i);
    //price_value = json_access(arr_mat_json,qty_arr[0],qty_arr[i]);

    if (price_value === undefined) {
      price_value = 0;
    }

    cal_price = price_value*qty_val_json;


    //console.log(sum_id+"_"+price_value);

    $('#'+sum_id).html(cal_price);

   //console.log(supplier_name_text);
   
  }

  const supplier_name_text = $("#web_supplier_id option:selected").text();

  $('#sum_sup_name').html(supplier_name_text);

  cal_sum_price();

  document.getElementById("table_sum_po_price").style.display = "block";
}

function peocess_move(e,obj){
  //console.log(obj.id);

  //console.log(e.keyCode);



  e = e || window.event;

  if (e.ctrlKey && e.keyCode === 38) {
    // up arrow
    if(tbl_row_length > 0){
      if(table_point_row > 0){
        for (let x = table_point_row-1; x >= 0; x--) {
          if( arr_mat_json[x][0]['is_del'] == "no"){
            table_point_row = x;
            break;
          }
        }
      }
    }
  }
  else if (e.ctrlKey && e.keyCode === 40) {
    // down arrow
    if(tbl_row_length > 0){
      if(table_point_row < tbl_row_length-1){
        for (let x = table_point_row+1; x <= tbl_row_length; x++) {
          if( arr_mat_json[x][0]['is_del'] == "no"){
            table_point_row = x;
            break;
          }
        }
      }
    }
  }
  else if (e.ctrlKey && e.keyCode === 37) {
   // left arrow
    if(tbl_col_length > 0){
      if(table_point_col > 0){
        table_point_col = table_point_col-1;

        table_point = table_point_row+"_"+table_point_col;

        const type_input = $("#"+table_point).attr('type');
        if(type_input != "text"){
          table_point_col = table_point_col+1;
        }

      }
    }
  }
  else if (e.ctrlKey && e.keyCode === 39) {
    // right arrow
    if(tbl_col_length > 0){
      if(table_point_col < tbl_col_length-1){
        table_point_col = table_point_col+1;

        table_point = table_point_row+"_"+table_point_col;

        const type_input = $("#"+table_point).attr('type');
        if(type_input != "text"){
          table_point_col = table_point_col-1;
        }
        
      }
    }
  }
  else if (e.ctrlKey && e.keyCode === 46) {
    // right arrow
    if(tbl_col_length > 0){
      if(table_point_col < tbl_col_length-1){
        table_point_col = table_point_col+1;

        table_point = table_point_row+"_"+table_point_col;

        const type_input = $("#"+table_point).attr('type');
        if(type_input != "button"){
          table_point_col = table_point_col-1;
        }

      }
    }
  }

  table_point = table_point_row+"_"+table_point_col;

  console.log("new>"+table_point);



  const type_input = $("#"+table_point).attr('type');
  if(type_input == "text"){

    //console.log("focus>>>"+table_point);
    document.getElementById(table_point).focus();

  }        
      
}

class UI_supmandata {

  addToList(data,arr_suppliers,web_material_id) {

    var tr_id = "tr_"+num_tr_id;

    arr_mat_json_tmp = [];

    const list = document.getElementById(`list-sup-data`);
    // Create tr element
    const row = document.createElement("tr");

    row.setAttribute("id", tr_id);
    // Insert cols
    //var url_del = hostname_site+"/"+"purchase_order/del_action/"+data.web_purchase_material_id;
    const arr_prices = data.data_price;
    //console.log(arr_suppliers);
    var td_price = ``;
    var td_sum = ``;
    var id_del = "tr_"+num_tr_id+"_"+data.web_material_id;

    var qty_id_tbl = num_tr_id+'_1';
    var id_qty_price = num_tr_id+'_'+data.web_material_id;

    const arr_mat_info = ({'material_name': data.material_name,'web_material_id':data.web_material_id,'is_del':'no'});

    arr_mat_json_tmp.push(arr_mat_info);
    arr_mat_json_tmp.push("");

    var num_price_id = 2;
    var num_price_sum_id = 6;

    var supplier_name_option = $("#web_supplier_id option:selected").text();

    arr_suppliers.forEach(function (arr_supplier) {
      if(arr_supplier.supplier_name == supplier_name_option){
        supplier_point = num_price_id;
      }

      //console.log(">>>>"+supplier_point);

    var id_price_val = num_tr_id+"_"+num_price_id;
    var id_price_sum_val = num_tr_id+"_"+num_price_sum_id;

    var var_matid_supid =  data.web_material_id+"_"+arr_supplier.web_supplier_id;

    //console.log(arr_supplier.supplier_name);
    var var_price = arr_prices[arr_supplier.supplier_name]['unit_price'];
    var id_price = num_tr_id+'_'+arr_supplier.supplier_name;
    var id_sum_price = num_tr_id+'_sum_'+arr_supplier.supplier_name;
    id_qty_price +=  '_'+arr_supplier.supplier_name;

    if (var_price === undefined){
        var_price = 0;
      }

      // puse array here-------
      console.log(arr_prices[arr_supplier.supplier_name]);
      const arr_price_sup = ({'price': var_price,'web_material_id':web_material_id,'sup_id':arr_prices[arr_supplier.supplier_name]['web_supplier_id']});


      arr_mat_json_tmp.push(arr_price_sup);

      //td_price = td_price + `<td id='${id_price}'>${var_price}</td>`;

      td_price = td_price + `<td id='${id_price}'><input type='text' name='po_all_price[]' id='${id_price_val}' value='${var_price}' class='edittable form-control' onkeydown="peocess_move(event,this)";></td>`;

      td_sum += `<td id='${id_price_sum_val}'></td>`;

      num_price_id = num_price_id+1;
      num_price_sum_id = num_price_sum_id+1;
    });

    for (let j = 6; j <= 9; j++) {

      arr_mat_json_tmp.push("");
    }

    arr_mat_json.push(arr_mat_json_tmp);

    console.log(arr_mat_json);

    tbl_row_length = arr_mat_json.length;
    tbl_col_length = arr_mat_json[0].length;

    var id_btn = num_tr_id+'_'+tbl_col_length;

    row.innerHTML = `
    <td>${data.material_name} ${data.material_size} ${data.material_unit}</td>
    <td><input type='text' name='qty[]' id='${qty_id_tbl}' qty_val='${id_qty_price}' class='edittable form-control' onkeydown="peocess_move(event,this)"; onkeyup="cal_amount(this)"> </td>
    ${td_price}
    ${td_sum}
    <td>
      <button type="button" id="${id_btn}" idval="${id_del}" class="btn btn-dark" style="font-size:1em;" value="del_material" data-toggle="modal" data-target="#ModalDelMaterial" title="Delete" onkeydown="peocess_move(event,this)";>
      <i class="far fa-edit"></i></button>
    </td>
    `;

    list.appendChild(row);



    /*const data_po_arr_tmp = (
      data.material_name
    );*/

        

        //console.log("tbl_col_length>>"+tbl_col_length);

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
  const id_row_del = arr_val_id[1];
  const web_material_id = arr_val_id[2];

  //document.getElementById("tr_mat_"+web_material_id).style.display = "contents";

  //deleteRow(edit_id);
  //deleteRow(arr_val_id[0]+"_"+arr_val_id[1]);
//del json
  

  arr_mat_json[id_row_del][0]['is_del'] = "yes";


  console.log("------json------");
  console.log(arr_mat_json);
  console.log("------json------");

  var json_data_po = JSON.stringify(arr_mat_json);

  cal_sum_price();

  
});

document.getElementById("list-sup-data").addEventListener("click", function (e) {

 alert(e.target.id);
  edit_id = e.target.idval;

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

function json_access(data,field,row,col){

  //const obj = JSON.parse(data);
  //console.log(obj[row]);
  const obj2 = Object.values(data[row]);

  if(field != null){
    return obj2[col][field];
  }else{
    return obj2[col];
  }
}










//document.getElementById("b2").focus(); 





