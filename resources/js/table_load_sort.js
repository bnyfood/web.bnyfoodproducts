var offset = 0;
var sortby = "";
var sorttype = "";
var per_page = 10;

$(window).scroll(function() {
  if($(window).scrollTop() >= ($(document).height() - $(window).height())-1) {

     offset = parseInt(offset)+per_page;

     $("#offset").val(offset);

     loadcontent(offset,sortby,sorttype,0);

  }
});
/*
$(window).scroll(function() {
  console.log($(window).scrollTop());
           console.log($(document).height() - $(window).height());
    if($(window).scrollTop() >= ($(document).height() - $(window).height())-1) {
           // ajax call get data from server and append to the div

           console.log($(window).scrollTop());
           console.log($(document).height() - $(window).height());
    }
});*/


//---hilite text search
//do_highligh();
function do_highligh(){
  var src_str = $("#highlighting").html();
  var term = $("#material_search").val();
  term = term.replace(/(\s+)/,"(<[^>]+>)*$1(<[^>]+>)*");
  var pattern = new RegExp("("+term+")", "gi");

  src_str = src_str.replace(pattern, "<mark>$1</mark>");
  src_str = src_str.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/,"$1</mark>$2<mark>$4");

  $("#highlighting").html(src_str);
}
//---hilite text search

function tablesort(v_sortby,v_sorttype,numclass){

  //document.getElementById("sortby").value = v_sortby;
  //document.getElementById("sorttype").value = v_sorttype;
  sortby = v_sortby;
  sorttype = v_sorttype;
  offset = 0;

  //console.log(v_sortby+"---"+v_sorttype);

 // 
  const arrgrays = Array.from(
    document.getElementsByClassName('asssort')
  );

  arrgrays.forEach(arrgray => {
    arrgray.style.color = '#ccc';
  });

  document.getElementsByClassName("asssort")[numclass].style.color = "#000000";

  loadcontent(0,v_sortby,v_sorttype,1);



}

$(document).on('click','#btn_search', function() {

  sortby = "";
  sorttype = "";
  offset = 0;
  loadcontent(offset,sortby,sorttype,1);

});