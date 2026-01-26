$(window).scroll(function() {
    if($(window).scrollTop() == $(document).height() - $(window).height()) {
           // ajax call get data from server and append to the div
           var offset = $("#offset").val();
           console.log(offset);

           loadcontent(offset);

           offset = parseInt(offset)+5;

           $("#offset").val(offset);

           
    }
});


class UI_MORECONTENT {
  addToList(list_data) {
    const list = document.getElementById("content-list");
    // Create tr element
    const row = document.createElement(`tr`);


    //row.setAttribute("onMouseOver", `this.bgColor = '#F4F4F5'`);
    //row.setAttribute("onMouseOut", `this.bgColor = '#FFFFFF'`);
    // Insert cols
    row.innerHTML = `
    <td>${list_data.material_brand_name}</td>
    <td class="text-nowrap">
      <input type="hidden" name="contentpage" id="contentpage" value="1">
      <a href="http://bnyfoodproducts.com/manufacture/brand/brand_edit_form/RHM5aS9TY3ZRcHBIbmZFbEdPTmFHQT09" data-toggle="tooltip" data-original-title="Edit"> 
        <i class="icon wb-wrench" aria-hidden="true"></i>
      </a>
      <button class="btn btn-sm btn-icon btn-flat btn-default" data-target="#confirm_delete" data-toggle="modal" type="button" data-href="http://bnyfoodproducts.com/manufacture/brand/del_action/RHM5aS9TY3ZRcHBIbmZFbEdPTmFHQT09"><i class="icon wb-close" aria-hidden="true"></i></button>
    </td>
    `;

    list.appendChild(row);
  }

  deleteAll() {
    const elem = document.querySelectorAll("#province-list tr");
    Array.prototype.forEach.call(elem, function (node) {
      node.parentNode.removeChild(node);
    });
  }

 }

function loadcontent(offset){
	//alert(offset);

	var brand_search = document.getElementById("brand_search").value;
	//alert(brand_search);

	var urls = hostname_site+"/"+"manufacture/brand/loaddata_more_ajax";
	      $.ajax({
	        type: "POST",
	        url: urls,
	        data:  {brand_search: brand_search,offset:offset}, 
	        dataType: "json"
	      })
	      .done(function( data ) {  

	      	const ui = new UI_MORECONTENT();

	      	const list_data = data.list_data;
	      	console.log(list_data);

	      	list_data.forEach(function (list_data) {
            // Add company to UI
            ui.addToList(list_data);
          });		

	    });
	

}