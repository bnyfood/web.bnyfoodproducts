class UI_MORECONTENT {
  addToList(list_data) {
    const list = document.getElementById("content-list");
    const row = document.createElement(`tr`);

    var url_edit = hostname_site+"/"+"webs/domains/domain_edit_form/"+list_data.web_domain_id;
    var url_del = hostname_site+"/"+"webs/domains/del_action/"+list_data.web_domain_id;

    row.innerHTML = `
      <td>${list_data.web_domain_name}</td>
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
    const elem = document.querySelectorAll("#content-list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }
}

per_page = 5;

function loadcontent(offset,sortby,sorttype,is_new_load){
	var domain_search = document.getElementById("domain_search").value;
	var urls = hostname_site+"/"+"webs/domains/loaddata_more_ajax";
  $('#spinner-div').show();
	      $.ajax({
	        type: "POST",
	        url: urls,
	        data:  {domain_search: domain_search,offset:offset,sortby:sortby,sorttype:sorttype}, 
	        dataType: "json",
          success: function (data) {
               const ui = new UI_MORECONTENT();

              if(is_new_load == 1){
                ui.deleteAll();
              }

              const list_data = data.list_data;

              list_data.forEach(function (list_data) {
                ui.addToList(list_data);
              });   

            },
            complete: function () {
                $('#spinner-div').hide();
            }
	      });
}
