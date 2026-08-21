$( document ).ready(function() {

  $('#search').click(function(){
     var w=1200;
     var h=800;
     var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);

    var platform=$("select#platform").val();


    var daterange=$("#datesearch").val();
    
      /*daterange = daterange.replace("/", "sl");
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace(" ", "sp");
      daterange = daterange.replace(" ", "sp");
      daterange = daterange.replace("-", "hp");*/
    
    var urls = hostname_site+"accounting/trackingreport/trackingreport_make/"+platform+"/"+daterange;
    //alert(urls);
    return window.open(urls, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    
    return false;
  });


});







        