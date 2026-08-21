
$(document).ready(function() {
  $( function() {
   /* var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];*/
    $("#material_txt").keyup(function(event){

      var krysearch = $(this).val();
      var web_supplier_id = $("#web_supplier_id").val();

      var urls = hostname_site+"/"+"purchase_order/material_search";

        $.ajax({
          type: "POST",
          url: urls,
          data:  {krysearch: krysearch,web_supplier_id:web_supplier_id}, 
          dataType: "json",
          async: true,
          complete: function (data) {

            const search_data = data.availableTags;
          
            if(search_data.length > 0){
              var availableTags = data.availableTags;
            }else{
              var availableTags = ["No results found"];
            }
            //alert(availableTags);
          
            $( "#material_txt" ).autocomplete({
                source: availableTags,
                minLength:3,
                messages: {
                  noResults: '',
                  results : function(resultsCount) {}
                },
                select: function( event, ui ) {
                  res_auto(ui.item.value);          
                }
            });

          }
        });

       /* .done(function( data ) { 

          const search_data = data.availableTags;
          
          if(search_data.length > 0){
            var availableTags = data.availableTags;
          }else{
            var availableTags = ["No results found"];
          }
          //alert(availableTags);
        
          $( "#material_txt" ).autocomplete({
              source: availableTags,
              minLength:3,
              messages: {
                noResults: '',
                results : function(resultsCount) {}
              },
              select: function( event, ui ) {
                res_auto(ui.item.value);          
              }
            });
        });*/

      });
  });

  
  $("#po_qty").keyup(function(event){

    var qty = $(this).val();
    var price = $("#po_price").val();

    var total = 0;
    total = price * qty;
    $("#po_total").val(total);

  });


  $("#po_add_btn").click(function(event) {

    const ui_modelpricedata = new UI_pricedata();
    ui_modelpricedata.deleteAllModel();
    document.getElementById("compare_div").style.display = "none";

    var web_supplier_id = $("#web_supplier_id").val();
    var web_material_id = $("#web_material_id").val();
    var po_qty = $("#po_qty").val();
    var ran_num_pocode = $("#ran_num_pocode").val();
    var po_price = $("#po_price").val();

    var urls_add = hostname_site+"/"+"purchase_order/po_make";
    $('#spinner-div').show();
      $.ajax({
      type: "POST",
      url: urls_add,
      data:  {web_supplier_id: web_supplier_id,web_material_id:web_material_id,po_qty:po_qty,ran_num_pocode:ran_num_pocode,po_price:po_price}, 
      dataType: "json",
      success: function (data) {

        document.getElementById("po_print").style.display = "block";
        //$("#div_po_print").css({"display":"block"});
        //$("#div_po_print").attr("style", "display:block");
        
        const arr_model_datas = data.arr_po_mats;
        const ui_modeldata = new UI_modeldata();
        //console.log(arr_usergroups);
        ui_modeldata.deleteAllModel();
        arr_model_datas.forEach(function (arr_model_data) {
          // Add company to UI
          ui_modeldata.addToList(arr_model_data);
        });

         $("#material_txt").val(""); 
         $("#web_material_id").val("");
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

});

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

        //console.log(arr_mat['material_unit_price']);
        $("#web_material_id").val(arr_mat['web_material_id']);
        $("#po_price").val(arr_mat['material_unit_price']);

        const arr_po_compares = data.arr_compares;
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
        }

      },
      complete: function () {
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
    var po_total_row = data.unitprice * data.qty;

    //var url_del = hostname_site+"/"+"purchase_order/del_action/"+data.web_purchase_material_id;

    row.innerHTML = `
    <td>${data.material_name}</td>
    <td>${data.unitprice}</td>
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

  console.log(web_supplier_id_tmp+" >> "+web_supplier_id);

  /*if(web_supplier_id == "0"){
    console.log("no1");
  }else{  */
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
//  }

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

document.getElementById("del_material_btn").addEventListener("click", function (e) {
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
});


function hideModal(cla) {
  $(".close").click();
  $(cla).hide();
  edit_id = 0;
  delete_target = "";
}