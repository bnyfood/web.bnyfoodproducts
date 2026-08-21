class UI_MORECONTENT {
  addToList(list_data) {
    const list = document.getElementById("content-list");
    const row = document.createElement(`tr`);

    var url_edit = hostname_site+"/"+"manufacture/material_unit/edit_material_unit_form/"+list_data.web_material_unit_id;

    row.innerHTML = `
    <td>${list_data.material_unit}</td>
    <td class="text-nowrap">
      <a href="${url_edit}" data-toggle="tooltip" data-original-title="Edit"> 
        <i class="icon wb-wrench" aria-hidden="true"></i>
      </a>
    </td>
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
	//alert(offset);

	var material_unit_search = document.getElementById("material_unit_search").value;

  $('#spinner-div').show();

	var urls = hostname_site+"/"+"manufacture/material_unit/loaddata_more_ajax";
	      $.ajax({
	        type: "POST",
	        url: urls,
	        data:  {material_unit_search: material_unit_search,offset:offset,sortby:sortby,sorttype:sorttype}, 
	        dataType: "json",
          success: function (data) {
               //On success do something....
               const ui = new UI_MORECONTENT();

              if(is_new_load == 1){
                ui.deleteAll();
              }

              const list_data = data.list_data;
              //console.log(list_data);

              list_data.forEach(function (list_data) {
                // Add company to UI
                ui.addToList(list_data);
              });   

            },
            complete: function () {
              //do_highligh();
                $('#spinner-div').hide();//Request is complete so hide spinner
            }
	    });
	

}

