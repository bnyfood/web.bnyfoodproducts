var edit_id = 0;
var delete_target = "";

class UI_usergroup {
  addToList(data) {
    const list = document.getElementById("usergroup-list");
    // Create tr element
    const row = document.createElement("li");
    // Insert cols
    const id_en = document.getElementById("id_en").value;

    var url_del = hostname_site+"/"+"config_system/usergroup/move_usergroup_map/"+data.BNYCustomerID+"/"+id_en;

    row.innerHTML = `
      <li class="list-group-item">
        <div class="media">
          <div class="pr-20">
            <a class="avatar avatar-online" href="javascript:void(0)">
              <img class="img-fluid" src="../../../global/portraits/11.jpg">
              <i></i>
            </a>
          </div>
          <div class="media-body">
            <h5 class="mt-0 mb-5 hover">${data.Name}
            </h5>
            <small>${data.CompanyName}</small>
          </div>
          <div class="pt-10 pb-10 p-0">

            <button class="btn btn-sm btn-icon btn-flat btn-danger" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="${url_del}">
              <i class="icon icon-xs wb-trash mr-0"></i>
            </button>
          
          </div>
        </div>
      </li>`;

    list.appendChild(row);
  }

  /*
    <button class="btn btn-icon btn-danger btn-outline btn-round btn-xs" type="button" name="button" value="delete" id="${data.BNYCustomerID}">
              <i class="icon icon-xs wb-trash mr-0"></i>
            </button>
  */

  deleteAll() {
    const elem = document.querySelectorAll("#usergroup-list li");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}

// DOM Load Event
//document.addEventListener("DOMContentLoaded", displayUsergroup);

function displayUsergroup() {
  // const companies = Store.getCompanies();
  const id_en = document.getElementById("id_en").value;

  var urls = hostname_site+"/"+"config_system/usergroup/get_usergroup_ajax";

  $.ajax({
    type: "POST",
    url: urls,
    data:  {id_en: id_en}, 
    dataType: "json"
  })
  .done(function( data ) {  
    const arr_usergroups = data.arr_usergroups;
    const ui = new UI_usergroup();
    //console.log(arr_usergroups);
    ui.deleteAll();
    arr_usergroups.forEach(function (arr_usergroup) {
      // Add company to UI
      ui.addToList(arr_usergroup);
    });

  });
}

/*document.getElementById("usergroup-list").addEventListener("click", function (e) {

  alert(e.target.value);
  edit_id = e.target.id;
  if (e.target.value === "delete") {
    const id_en = document.getElementById("id_en").value;

    var urls = hostname_site+"/"+"config_system/usergroup/move_usergroup_ajax";

    $.ajax({
      type: "POST",
      url: urls,
      data:  {user_id: edit_id,id_en:id_en}, 
      dataType: "json",
      success: function( data ) {
         // alert('yes');
            displayUsergroup();
          }
    });
  }
});*/

$('input[name="set_permission[]"]').click(function(){
      //alert($(this).val());
      //alert($(this).attr('id'));
      var is_name = $(this).attr('id');
      //alert(is_name);
      var id_en = $("#id_en").val();
      var is_check = $(this).prop('checked');
      var is_chk_val = 0;
      if(is_check){
        is_chk_val = 1;
      }

        var urls = hostname_site+"/"+"config_system/usergroup/group_permission";

      $.ajax({
        type: "POST",
        url: urls,
        data:  {id_en:id_en,is_name:is_name,is_chk_val:is_chk_val}, 
        dataType: "json"
      })
        .done(function( data ) {  
        
           
     });

  });

$('#user_sel').on('change', function() {

  //alert( this.value );
  var user_id_val = this.value

  if(user_id_val == "0"){
    document.getElementById("add_usertogroup").style.display = "none";
  }else{
    document.getElementById("add_usertogroup").style.display = "block";
  }
  
});
