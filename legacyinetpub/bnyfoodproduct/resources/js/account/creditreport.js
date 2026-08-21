$( document ).ready(function() {
  $('#search').click(function(){
     var w=1200;
     var h=800;
     var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var platform=$("select#platform").val();
    var ordernumber=$("#ordernumber").val();
    if(ordernumber=="")
    {
     ordernumber="none"; 
    }
    var daterange=$("#daterange").val();
    
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace("/", "sl");
      daterange = daterange.replace(" ", "sp");
      daterange = daterange.replace(" ", "sp");
      daterange = daterange.replace("-", "hp");
    

    return window.open(hostname_site+'accounting/creditreport/creditreport_make/'+platform+'/'+daterange, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    
    return false;
  });

  $('.cn_by_date').click(function(){
    console.log(this.id);
  });
});