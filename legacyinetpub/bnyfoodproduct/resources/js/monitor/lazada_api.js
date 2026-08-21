
var urls = hostname_site+"/"+"monitor/api_order_log/1";
var edit_id = 0;
class UI_LAZADA_API {
  addApiToList(data_api) {
    const list = document.getElementById("lazada_api_list");
    // Create tr element
    const row = document.createElement("tr");
    // Insert cols
    var icon_status = "";
    if(data_api.log_status === 1){
      icon_status = '<i class="fab fas fa-exclamation-circle mr-2 text-danger font-16"></i>';
    }else if(data_api.log_status === 2){
      icon_status = '<i class="fab fas fa-check-circle mr-2 text-success font-16"></i>';
    }  
    row.innerHTML = `
      <td>${icon_status}</td>
      <td>${data_api.log_note}</td>
      <td>${data_api.cdate}</td>
      <td>
      <button type="button" id="${data_api.id}" class="btn btn-dark" style="font-size:1em;" value="change_status" data-toggle="modal" data-target="#ModalChangeStatus" title="Delete"><i class="far fa-edit"></i></button></td>
      </td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#lazada_api_list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }
}


function displayLazadaApi() {

  $.ajax({
      type: "POST",
      url: urls,
      dataType: "json",
      async:false
    })
      .done(function( data ) { 
        const ui = new UI_LAZADA_API();
        ui.deleteAll();
        const data_apis = data.data_apis;
        data_apis.forEach(function (data_api) {
          // Add company to UI
          ui.addApiToList(data_api);
        });

      });

}

// DOM Load Event
document.addEventListener("DOMContentLoaded", displayLazadaApi);

document.getElementById("lazada_api_list").addEventListener("click", function (e) {
  // Instantiate UI
  const ui = new UI_LAZADA_API();

  if (e.target.value === "change_status") {
    // Instantiate UI

    edit_id = e.target.id;
    delete_target = e;
  } 

  e.preventDefault();
});

document.getElementById("change_status_btn").addEventListener("click", function (e) {
  //console.log(edit_id);

  // Instantiate UI
  const ui = new UI_LAZADA_API();

  var urls_change_status = hostname_site+"/"+"monitor/change_status_log/"+edit_id;

    $.ajax({
      type: "POST",
      url: urls_change_status,
      dataType: "json",
      async:false
    })
      .done(function( data ) { 

        displayLazadaApi();
        hideModal("#ModalChangeStatus");
      });
});

setInterval(function(){displayLazadaApi()}, 60000);