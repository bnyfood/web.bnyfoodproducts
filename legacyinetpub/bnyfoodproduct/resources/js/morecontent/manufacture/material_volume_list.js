class UI_MORECONTENT {
  addToList(list_data) {
    const list = document.getElementById("content-list");
    // Create tr element
    const row = document.createElement(`tr`);

    var url_edit = hostname_site+"/"+"manufacture/material_volume/edit_material_volume_form/"+list_data.web_material_volume_id;
    var url_del = hostname_site+"/"+"manufacture/material_volume/del_action/"+list_data.web_material_volume_id;


    //row.setAttribute("onMouseOver", `this.bgColor = '#F4F4F5'`);
    //row.setAttribute("onMouseOut", `this.bgColor = '#FFFFFF'`);
    // Insert cols
    row.innerHTML = `
      <td>${list_data.material_name}</td>
      <td>${list_data.web_material_volume_type}</td>
      <td>${list_data.web_material_volume_unit}</td>
    <td class="text-nowrap">
      <a href="${url_edit}" data-toggle="tooltip" data-original-title="Edit"> 
        <i class="icon wb-wrench" aria-hidden="true"></i>
      </a>
      <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="${url_del}"><i class="icon wb-close" aria-hidden="true"></i></button>
    </td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#material-volume-list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

 }

function loadcontent(offset,sortby,sorttype,is_new_load){
	//alert(offset);

	var material_search = document.getElementById("material_search").value;
	//alert(brand_search);

	var urls = hostname_site+"/"+"manufacture/material_volume/loaddata_more_ajax";
  $('#spinner-div').show();
	      $.ajax({
	        type: "POST",
	        url: urls,
	        data:  {material_search: material_search,offset:offset,sortby:sortby,sorttype:sorttype}, 
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