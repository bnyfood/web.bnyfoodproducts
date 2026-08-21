var edit_id = 0;
var delete_target = "";

document.getElementById("add_usertogroup").addEventListener("click", function (e) {
  $('#spinner-div').show();
  const id_en = document.getElementById("id_en").value;
  const user_sel = document.getElementById("user_sel").value;

  var urls = hostname_site + "/" + "config_system/usergroup/add_user_to_group";

  $.ajax({
    type: "POST",
    url: urls,
    data: { id_en: id_en, user_sel: user_sel },
    dataType: "json",
    success: function (data) {
      $("#user_sel").val("0");
      document.getElementById("add_usertogroup").style.display = "none";
      displayUsergroup();
      $('#spinner-div').hide();
    }
  });
});

class UI_usergroup {
  addToList(data) {
    const list = document.getElementById("usergroup-list");
    const row = document.createElement("li");
    row.className = "list-group-item usergroup-user-item";
    const id_en = document.getElementById("id_en").value;
    var url_del = hostname_site + "/" + "config_system/usergroup/move_usergroup_map/" + data.BNYCustomerID + "/" + id_en;

    row.innerHTML = `
      <span class="usergroup-user-name" title="${data.Name || ''}">${data.Name || ''}</span>
      <button class="btn btn-sm btn-icon btn-flat btn-danger" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="${url_del}">
        <i class="icon icon-xs wb-trash mr-0"></i>
      </button>`;

    list.appendChild(row);
  }

  deleteAll() {
    const list = document.getElementById("usergroup-list");
    if (list) {
      list.innerHTML = "";
    }
  }
}

document.addEventListener("DOMContentLoaded", displayUsergroup);

function displayUsergroup() {
  const id_en = document.getElementById("id_en").value;
  var urls = hostname_site + "/" + "config_system/usergroup/get_usergroup_ajax";

  $.ajax({
    type: "POST",
    url: urls,
    data: { id_en: id_en },
    dataType: "json"
  })
    .done(function (data) {
      const arr_usergroups = data.arr_usergroups;
      const ui = new UI_usergroup();
      ui.deleteAll();
      if (arr_usergroups && arr_usergroups.length) {
        arr_usergroups.forEach(function (arr_usergroup) {
          ui.addToList(arr_usergroup);
        });
      }
    });
}

$('input[name="set_permission[]"]').click(function () {
  var is_name = $(this).attr('id');
  var id_en = $("#id_en").val();
  var is_check = $(this).prop('checked');
  var is_chk_val = 0;
  if (is_check) {
    is_chk_val = 1;
  }

  var urls = hostname_site + "/" + "config_system/usergroup/group_permission";

  $.ajax({
    type: "POST",
    url: urls,
    data: { id_en: id_en, is_name: is_name, is_chk_val: is_chk_val },
    dataType: "json"
  })
    .done(function (data) {
    });
});

$('#user_sel').on('change', function () {
  var user_id_val = this.value;

  if (user_id_val == "0") {
    document.getElementById("add_usertogroup").style.display = "none";
  } else {
    document.getElementById("add_usertogroup").style.display = "block";
  }
});
