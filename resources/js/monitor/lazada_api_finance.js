
var urls_f = hostname_site+"/"+"monitor/api_order_log/2";
var edit_f_id = 0;
class UI_LAZADA_F_API {
  addApiToFList(dataf_api) {
    const listf = document.getElementById("lazada_api_finance_list");
    // Create tr element
    const rowf = document.createElement("tr");
    // Insert cols
    var iconf_status = "";
    if(dataf_api.log_status === 1){
      iconf_status = '<i class="fab fas fa-exclamation-circle mr-2 text-danger font-16"></i>';
    }else if(dataf_api.log_status === 2){
      iconf_status = '<i class="fab fas fa-check-circle mr-2 text-success font-16"></i>';
    }  
    rowf.innerHTML = `
      <td>${iconf_status}</td>
      <td>${dataf_api.log_note}</td>
      <td>${dataf_api.cdate}</td>
      <td>
      <button type="button" id="${dataf_api.id}" class="btn btn-dark" style="font-size:1em;" value="change_status" data-toggle="modal" data-target="#ModalChangeStatus" title="Delete"><i class="far fa-edit"></i></button></td>
      </td>
    `;

    listf.appendChild(rowf);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#lazada_api_finance_list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }
}


function displayLazadaFApi() {

  $.ajax({
      type: "POST",
      url: urls_f,
      dataType: "json",
      async:false
    })
      .done(function( dataf ) { 
        const uif = new UI_LAZADA_F_API();
        uif.deleteAll();
        const dataf_apis = dataf.data_apis;
        dataf_apis.forEach(function (dataf_api) {
          // Add company to UI
          uif.addApiToFList(dataf_api);
        });

      });

}

// DOM Load Event
document.addEventListener("DOMContentLoaded", displayLazadaFApi);

document.getElementById("lazada_api_finance_list").addEventListener("click", function (e) {
  // Instantiate UI
  const ui = new UI_LAZADA_F_API();

  if (e.target.value === "change_status") {
    // Instantiate UI

    edit_f_id = e.target.id;
    delete_target = e;
  } 

  e.preventDefault();
});

document.getElementById("change_status_btn").addEventListener("click", function (e) {
  //console.log(edit_id);

  // Instantiate UI
  const ui = new UI_LAZADA_F_API();

  var urls_change_f_status = hostname_site+"/"+"monitor/change_status_log/"+edit_f_id;

    $.ajax({
      type: "POST",
      url: urls_change_f_status,
      dataType: "json",
      async:false
    })
      .done(function( data ) { 

        displayLazadaFApi();
        hideModal("#ModalChangeStatus");
      });
});

setInterval(function(){displayLazadaFApi()}, 60000);