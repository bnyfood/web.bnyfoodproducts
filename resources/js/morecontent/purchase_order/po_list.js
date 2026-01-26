var edit_id = 0;

$(window).on('load', function(){ 
    authen_user();
});


class UI_MORECONTENT {
  addToList(list_data) {
    const list = document.getElementById("content-list");
    // Create tr element
    const row = document.createElement(`tr`);

    var id_tr_del = list_data.web_purchase_order_id+'_tr_del';
    row.setAttribute("id", id_tr_del);


    
    //var url_del = hostname_site+"purchase_order/del_action/"+list_data.web_purchase_order_id;

    var status_icon = '<i class="icon wb-large-point" aria-hidden="true" style="color:red"></i>';

    if(list_data.status === "Active"){
        status_icon = '<i class="icon wb-large-point" aria-hidden="true" style="color:orange"></i>';
    }else if(list_data.status === "Approve"){
        status_icon = '<i class="icon wb-large-point" aria-hidden="true" style="color:green"></i>';
    }

    const val_print = list_data.po_number+"_"+list_data.web_supplier_id;


    //row.setAttribute("onMouseOver", `this.bgColor = '#F4F4F5'`);
    //row.setAttribute("onMouseOut", `this.bgColor = '#FFFFFF'`);
    // Insert cols
    row.innerHTML = `
    <td>${list_data.po_number}</td>
    <td>${status_icon}</td>
    <td>${list_data.supplier_name}</td>
    <td>${list_data.po_cdate}</td>
    <td>
      <button id="${val_print}" class="btn btn-sm btn-icon btn-flat btn-default icon wb-print" value="print_poaction" type="button" ></button>
      <button id="${val_print}" class="btn btn-sm btn-icon btn-flat btn-default icon wb-edit" value="manage_po" type="button" ></button>
      <button id="${list_data.web_purchase_order_id}" class="btn btn-sm btn-icon btn-flat btn-default icon wb-close" data-target="#ModalDelPo" data-toggle="modal" value="del_po" type="button" ></button>
    </td>
    `;

    list.appendChild(row);
  }

  addNullList(){
    const list = document.getElementById("content-list");
    // Create tr element
    const row = document.createElement(`tr`);

    row.innerHTML = `
    <td colspan='4' style="text-align: center;">NO more Data</td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#content-list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

 }

function loadcontent(offset,sortby,sorttype,is_new_load){

  authen_user();
	//alert(offset);

	var po_search = document.getElementById("po_search").value;
  var po_status = document.getElementById("po_status").value;
  var daterange = document.getElementById("daterange").value;
	//alert(brand_search);

  $('#spinner-div').show();

	var urls = hostname_site+"/"+"purchase_order/loaddata_more_ajax";
	      $.ajax({
	        type: "POST",
	        url: urls,
	        data:  {po_search: po_search,po_status:po_status,daterange:daterange,offset:offset,sortby:sortby,sorttype:sorttype}, 
	        dataType: "json",
          success: function (data) {
               //On success do something....
               const ui = new UI_MORECONTENT();

              if(is_new_load == 1){
                ui.deleteAll();
              }

              const list_data = data.list_data;
              //console.log(list_data);

              if(list_data === null){
                ui.deleteAll();
                ui.addNullList();
                $('#spinner-div').hide();
              }else{

                list_data.forEach(function (list_data) {
                  // Add company to UI
                  ui.addToList(list_data);
                });   
              }

            },
            complete: function () {
              //do_highligh();
              $('#spinner-div').hide();//Request is complete so hide spinner
            }
	    });
	

}

document.getElementById("content-list").addEventListener("click", function (e) {

  authen_user();

  if (e.target.value === "del_po") {
    // Instantiate UI

    edit_id = e.target.id;

  } else if(e.target.value === "print_poaction"){

    var print_id = e.target.id;
    var url_print = hostname_site+"purchase_order/print_po/"+print_id;
    //window.location.href = url_print;
    //console.log(url_print);

    window.open(url_print,'_blank', "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,top=500,left=500,width=1200,height=800");

  } else if(e.target.value === "manage_po"){

    var print_id = e.target.id;
    var url_print = hostname_site+"purchase_order/manage_po/"+print_id;
    //window.location.href = url_print;
    //console.log(url_print);

    window.open(url_print,'_top');

  }

  e.preventDefault();
});

document.getElementById("del_po_btn").addEventListener("click", function (e) {

  authen_user();

  var urls = hostname_site+"/"+"purchase_order/delete_po";
        $.ajax({
          type: "POST",
          url: urls,
          data:  {web_purchase_order_id: edit_id}, 
          dataType: "json",
          success: function (data) {
                
            deleteRow(edit_id+'_tr_del');

          },
          complete: function () {
            //do_highligh();
              $('#spinner-div').hide();//Request is complete so hide spinner
          }
      });

  
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

$(function() {
  $('input[name="daterange"]').daterangepicker({

    autoUpdateInput: false,
    opens: 'left'
  });

  $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
  });

  $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });

});




