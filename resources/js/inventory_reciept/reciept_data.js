$('#reciept_print').click(function(){
  var w=1200;
  var h=800;
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);

  var po_number=$("#po_number").val();
  
  var urls = hostname_site+"store_manage/inventory_reciept/print_reciept/"+po_number;
  //alert(urls);
  return window.open(urls, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  return false;
});