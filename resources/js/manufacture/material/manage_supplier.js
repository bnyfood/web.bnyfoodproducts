var edit_id = 0;
var delete_target = "";
document
  .getElementById("add_supplier")
  .addEventListener("click", function (e) {


    // Get form values
    const ran_num_sup = document.getElementById("ran_num_sup").value;
    const web_supplier_id = document.getElementById("web_supplier_id").value;

    // Instantiate company
    //const company = new Company(company_name);

    // Instantiate UI
    //const ui = new UI_Company();

        var urls = hostname_site+"/"+"manufacture/material/add_supplier";

        $.ajax({
        type: "POST",
        url: urls,
        data:  {ran_num_sup: ran_num_sup,web_supplier_id:web_supplier_id}, 
        dataType: "json"
      })
      .done(function( data ) {  
        displaySupplier();
      });

  });

  class UI_supplier {
  addToList(data) {
    const list = document.getElementById("supplier-list");
    // Create tr element
    const row = document.createElement("li");
    // Insert cols
    row.innerHTML = `
      <li class="list-group-item">
                <div class="media">
                  <div class="media-body">
                    <h5 class="mt-0 mb-5 hover">${data.supplier_name}
                    </h5>
                  </div>
                  <div class="pt-10 pb-10 p-0">
                    <button class="btn btn-icon btn-danger btn-outline btn-round btn-xs" type="button" name="button" value="delete" id="${data.material_map_supplier_id}">
                      <i class="icon icon-xs wb-trash mr-0"></i>
                    </button>
                  </div>
                </div>
              </li>`;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#supplier-list li");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}

  function displaySupplier() {
  // const companies = Store.getCompanies();
  const ran_num_sup = document.getElementById("ran_num_sup").value;

  var urls = hostname_site+"/"+"manufacture/material/get_supplier_ajax";

  $.ajax({
    type: "POST",
    url: urls,
    data:  {ran_num_sup: ran_num_sup}, 
    dataType: "json"
  })
  .done(function( data ) {  
    const arr_suppliers = data.arr_suppliers;
    const ui = new UI_supplier();
    //console.log(arr_usergroups);
    ui.deleteAll();
    arr_suppliers.forEach(function (arr_supplier) {
      // Add company to UI
      ui.addToList(arr_supplier);
    });

  });
}

document.getElementById("supplier-list").addEventListener("click", function (e) {

  //alert(e.target.value);
  edit_id = e.target.id;
  if (e.target.value === "delete") {

    var urls = hostname_site+"manufacture/material/move_supplier_ajax";

    $.ajax({
      type: "POST",
      url: urls,
      data:  {map_id: edit_id}, 
      dataType: "json"
    })
    .done(function(  ) {  

      displaySupplier();

    });
  }
});