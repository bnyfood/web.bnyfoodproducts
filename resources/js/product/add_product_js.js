var edit_id = 0;
var delete_target = "";
var edit_model_id = 0;
var delete_model_target = "";

var arr_path_pic = window.location.pathname.split("/");
  var pic_urls = 'http://'+window.location.hostname+"/"+arr_path_pic[1]+"/"+"uploads/products/";


class UI_model {
  addToList(data,method) {
    const list = document.getElementById(`list-model`);
    // Create tr element
    const row = document.createElement("tr");
    // Insert cols
    row.innerHTML = `
    <td>${data.Name}</td>
    <td>
    <button class="btn btn-icon btn-primary btn-outline btn-round btn-xs icon icon-xs wb-edit mr-0" type="button" name="button" value="edit" id="${data.ProductModelGroupID}" data-toggle="modal"
                    data-target="#Modal_add_model">

    </button>
    <button class="btn btn-icon btn-danger btn-outline btn-round btn-xs icon icon-xs wb-trash mr-0" type="button" name="button" value="delete" id="${data.ProductModelGroupID}" data-toggle="modal"
                    data-target="#ModalDel">
    </button>
    </td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll(`#list-model tr`);
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

}

class UI_modeldata {
  addToList(data) {
    const list = document.getElementById(`list-model-data`);
    // Create tr element
    const row = document.createElement("tr");
    // Insert cols
    var img_name = 'noimage.jpeg';
    if(data.icon1 != null){
      img_name = data.icon1;
    }
    row.innerHTML = `
    <td>
      <div class="avatar avatar-online" style="width:100px">
      <img src="${pic_urls}${img_name}" alt="..." style="border-radius:0px">
    </div>
    </td>
    <td>${data.model1}</td>
    <td>${data.title1}</td>
    <td>${data.model2}</td>
    <td>${data.title2}</td>
    <td>${data.price}</td>
    <td>
    <button class="btn btn-icon btn-primary btn-outline btn-round btn-xs icon icon-xs wb-edit mr-0" type="button" name="button" value="edit" id="${data.ProductModelID}" data-toggle="modal"
                    data-target="#Modal_add_model_data">

    </button>
    <button class="btn btn-icon btn-danger btn-outline btn-round btn-xs icon icon-xs wb-trash mr-0" type="button" name="button" value="delete" id="${data.ProductModelID}" data-toggle="modal"
                    data-target="#ModalDelData">
    </button>
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

function removeOptions(selectElement) {
  var i,
    L = selectElement.options.length - 1;
  for (i = L; i >= 1; i--) {
    selectElement.remove(i);
  }
}

function setListbox(eleid) {

  var arr_path = window.location.pathname.split("/");
  var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/get_product_model_ajax";
  const shop_id_en = document.getElementById("shop_id_en").value;
  $.ajax({
    type: "POST",
    url: urls,
    data:  {shop_id_en: shop_id_en}, 
    dataType: "json"
  })
  .done(function( data ) {  
    removeOptions(document.getElementById(eleid));
    const arr_models = data.arr_models;

    arr_models.forEach(function (model_data) {
      const list_opt = document.getElementById(eleid);
      // Create tr element
      const opt = document.createElement("option");

      opt.value = model_data.ProductModelGroupID;
      opt.text = model_data.Name;

      list_opt.appendChild(opt);
    });

  });
}

document
  .getElementById("add_model_data")
  .addEventListener("click", function (e) {

    setListbox('model1');
    setListbox('model2');

  });

// DOM Load Event
document.addEventListener("DOMContentLoaded", displayModel);
document.addEventListener("DOMContentLoaded", displayModelData);

function displayModel() {
  // const companies = Store.getCompanies();
  const shop_id_en = document.getElementById("shop_id_en").value;
  var arr_path = window.location.pathname.split("/");
  var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/get_product_model_ajax";

  $.ajax({
    type: "POST",
    url: urls,
    data:  {shop_id_en: shop_id_en}, 
    dataType: "json"
  })
  .done(function( data ) { 
    const arr_models = data.arr_models;
    const ui = new UI_model();
    //console.log(arr_usergroups);
    ui.deleteAll();
    arr_models.forEach(function (arr_model) {
      // Add company to UI
      ui.addToList(arr_model);
    });

  });
}

function displayModelData() {
  // const companies = Store.getCompanies();
  const product_id = 1;
  var arr_path = window.location.pathname.split("/");
  var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/get_product_model_data_ajax";

  $.ajax({
    type: "POST",
    url: urls,
    data:  {product_id: product_id}, 
    dataType: "json"
  })
  .done(function( data ) {  
    const arr_model_datas = data.arr_model_datas;
    const ui_modeldata = new UI_modeldata();
    //console.log(arr_usergroups);
    ui_modeldata.deleteAllModel();
    arr_model_datas.forEach(function (arr_model_data) {
      // Add company to UI
      ui_modeldata.addToList(arr_model_data);
    });

  });
}

document.getElementById("list-model").addEventListener("click", function (e) {

  //alert(e.target.value);
  edit_id = e.target.id;

  if (e.target.value === "delete") {
    delete_target = e;
  } else if (e.target.value === "edit") {
    //console.log(e.target.id);

    //document.getElementById("locModalLabel").innerHTML = "Edit Organization";

    var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/get_model_by_id";

      $.ajax({
        type: "POST",
        url: urls,
        data:  {mogel_group_id: edit_id}, 
        dataType: "json"
      })
      .done(function( data ) {  
        const arr_model_data = data.arr_model;
        document.getElementById("model_name").value = arr_model_data.Name;

      });
  }
});

document.getElementById("list-model-data").addEventListener("click", function (e) {

  //alert(e.target.value);
  edit_model_id = e.target.id;

  if (e.target.value === "delete") {
    delete_model_target = e;
  } else if (e.target.value === "edit") {
    //console.log(e.target.id);

    //document.getElementById("locModalLabel").innerHTML = "Edit Organization";
    setListbox('model1');
    setListbox('model2');
    var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/get_model_data_by_id";

      $.ajax({
        type: "POST",
        url: urls,
        data:  {model_id: edit_model_id}, 
        dataType: "json"
      })
      .done(function( data ) {  
        const arr_model_data_val = data.arr_model_data;
        document.getElementById("title1").value = arr_model_data_val.title1;
        document.getElementById("title2").value = arr_model_data_val.title2;
        document.getElementById("model_price").value = arr_model_data_val.price;
        document.getElementById("model1").value = arr_model_data_val.ProductModelGroupID1;
        document.getElementById("model2").value = arr_model_data_val.ProductModelGroupID2;
      });
  }
});

document
  .getElementById("add_model_btn")
  .addEventListener("click", function (e) {
    //validate
    const model_name_id = document.getElementById("model_name");

    const model_name = document.getElementById("model_name").value;
    const shop_id_en = document.getElementById("shop_id_en").value;

    const re_error = checkRequired([model_name_id]);

    //console.log(re_error);
    var arr_path = window.location.pathname.split("/");
    // Validate
    if (re_error) {
      if (edit_id == 0) {
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/add_product_model";

        $.ajax({
        type: "POST",
        url: urls,
        data:  {model_name: model_name,shop_id_en:shop_id_en}, 
        dataType: "json"
        })
        .done(function( data ) {  
        
          displayModel();
          hideModal("#Modal_add_model");
        });
      } else {
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/edit_product_model";

        $.ajax({
        type: "POST",
        url: urls,
        data:  {model_name: model_name,mogel_group_id: edit_id}, 
        dataType: "json"
        })
        .done(function( data ) {  
        
          displayModel();
          hideModal("#Modal_add_model");
        });
      }
    }

    e.preventDefault();
  });

document.getElementById("delete_model_btn").addEventListener("click", function (e) {
  //console.log(edit_id);

    var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/del_product_model";

    $.ajax({
      type: "POST",
      url: urls,
      data:  {mogel_group_id: edit_id}, 
      dataType: "json"
    })
    .done(function(  ) {  

      displayModel();
      hideModal("#ModalDel");

    });
  
});

document
  .getElementById("add_model_data_btn")
  .addEventListener("click", function (e) {
    //validate
    const title1_id = document.getElementById("title1");

    const model1 = document.getElementById("model1").value;
    const model2 = document.getElementById("model2").value;
    const title1 = document.getElementById("title1").value;
    const title2 = document.getElementById("title2").value;
    const model_price = document.getElementById("model_price").value;
    const ren_id = document.getElementById("ren_id").value;

    const file_input1 = document.getElementById("fileUpload1");
    const files1 = file_input1.files[0];
    const body1 = new FormData();
    body1.append("file1", files1);
    //console.log(body1);
    const file_input2 = document.getElementById("fileUpload2");
    const files2 = file_input2.files[0];
    const body2 = new FormData();
    body2.append("file2", files2);

    const re_error = checkRequired([title1_id]);

    var form_model_add = new FormData($('#form_model_add')[0]);

    //console.log(re_error);
    var arr_path = window.location.pathname.split("/");
    // Validate
    if (re_error) {
      if (edit_model_id == 0) {
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/add_product_model_data";

        $.ajax({
        type: "POST",
        url: urls,
        //data:  {model1: model1,model2:model2,title1:title1,title2:title2,model_price:model_price,body1:body1,body2:body2,ren_id:ren_id}, 
        data:  form_model_add,
        //dataType: "text",
        cache: false,
        contentType: false,
        processData: false,
        })
        .done(function( data ) {  
        
          displayModelData();
          hideModal("#Modal_add_model_data");
        });
      } else {
        var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/edit_product_model_data";

        $.ajax({
        type: "POST",
        url: urls,
        data:  {model1: model1,model2:model2,title1:title1,title2:title2,model_price:model_price,edit_model_id:edit_model_id}, 
        dataType: "json"
        })
        .done(function( data ) {  
        
          displayModelData();
          hideModal("#Modal_add_model_data");
        });
      }
    }

    e.preventDefault();
  });

  document.getElementById("delete_model_data_btn").addEventListener("click", function (e) {
  //console.log(edit_id);

    var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+arr_path[1]+"/"+"product/del_product_model_data";

    $.ajax({
      type: "POST",
      url: urls,
      data:  {edit_model_id: edit_model_id}, 
      dataType: "json"
    })
    .done(function(  ) {  

      displayModelData();
      hideModal("#ModalDelData");

    });
  
});

function checkRequired(inputArr) {
  var err_status = true;
  inputArr.forEach(function (input) {
    if (input.value.trim() === "") {
      ///console.log(">>"+input.value);
      showError(input, `${getFieldName(input)} is required`);
      err_status = false;
    } else {
      showSuccess(input);
      //console.log(input.value);
      if (err_status) {
        err_status = true;
      } else {
        err_status = false;
      }
    }
  });
  return err_status;
}

function showError(input, message) {
  const formControl = input.parentElement;
  // console.log(formControl);

  const small = formControl.querySelector("#err_valid");
  small.innerText = message;
}

function showSuccess(input) {
  const formControl = input.parentElement;

  const small = formControl.querySelector("#err_valid");
  small.innerText = "";
}

function hideModal(cla) {
  $(cla).removeClass("in");
  $(".modal-backdrop").remove();
  $("body").removeClass("modal-open");
  $("body").css("padding-right", "");
  $(cla).hide();
  edit_id = 0;
  delete_target = "";
  edit_model_id = 0;
  delete_model_target = "";
}
