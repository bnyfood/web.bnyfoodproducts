$(window).scroll(function() {
    if($(window).scrollTop() == $(document).height() - $(window).height()) {
           // ajax call get data from server and append to the div
           var offset = $("#offset").val();
           //console.log(offset);
           var sortby = $("#sortby").val();
           var sorttype = $("#sorttype").val();

           offset = parseInt(offset)+5;

           $("#offset").val(offset);

           loadcontent(offset,sortby,sorttype,0);
    }
});

function tablesort(v_sortby,v_sorttype,numclass){

  document.getElementById("sortby").value = v_sortby;
  document.getElementById("sorttype").value = v_sorttype;

  //console.log(v_sortby+"---"+v_sorttype);

 // 
  const arrgrays = Array.from(
    document.getElementsByClassName('asssort')
  );

  arrgrays.forEach(arrgray => {
    arrgray.style.color = '#ccc';
  });

  document.getElementsByClassName("asssort")[numclass].style.color = "#000000";

  //$("#offset").val(0);
  document.getElementById("sortby").value = v_sortby;

  loadcontent(0,v_sortby,v_sorttype,1);



}