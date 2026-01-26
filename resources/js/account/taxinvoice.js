
set_search(document.getElementById("search_type"));

var call_couinter=0;
function getlatestrecorddate()
{

    //alert("hello");
    $.ajax({ 
        url: 'https://www.bnyfoodproducts.com/lazcallback/getlatestrecorddate',
        cache: false,
        success: function(response)
        {
            
             $("#res").html(response);
            if(response=="")
            {

              alert("never download");
            }
            else
            {
              alert("use to download:"+response);
            }
         getorders(response);   
        }
  });



}

function set_search(sel){
  //alert(sel.value);
  if(sel.value == '1'){

    document.getElementById("date_search").style.display = "table-cell";
    document.getElementById("order_search").style.display = "none";
    document.getElementById("button_search1").style.display = "flex";
    document.getElementById("button_search2").style.display = "flex";

  }else if(sel.value == '2'){

    document.getElementById("date_search").style.display = "none";
    document.getElementById("order_search").style.display = "table-cell";
    document.getElementById("button_search1").style.display = "flex";
    document.getElementById("button_search2").style.display = "flex";

  }else if(sel.value == '3'){
    
    document.getElementById("date_search").style.display = "table-cell";
    document.getElementById("order_search").style.display = "table-cell";
    document.getElementById("button_search1").style.display = "flex";
    document.getElementById("button_search2").style.display = "flex";

  }else{

    document.getElementById("date_search").style.display = "none";
    document.getElementById("order_search").style.display = "none";
    document.getElementById("button_search1").style.display = "none";
    document.getElementById("button_search2").style.display = "none";
  }
  
}


function  getorders(startdateval){     
$.ajax({ 
        url: 'https://www.bnyfoodproducts.com/lazcallback/getorders',
        cache: false,            
        type : "GET",            
        data: {startdate: $("#ordernumber").val()},
        dataType: 'json',
        success: function(response)
        {
 $("#resault").HTML(response);
              call_couinter++;
            if(response!="done")
            {
                  if(call_couinter<=2)
                  {
                    alert("will download: "+call_couinter+":"+response);
                   getorders(response); 
                   }
            }
        else
        {
            $("#return_div").HTML("DONE");
            
        }
        }
  });
}

$( document ).ready(function() {


$("#platform").val();
$("#ordernumber").val();
  var daterange='';
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sp", " ");
   daterange = daterange.replace("sp", " ");
   daterange = daterange.replace("hp", "-");

$("#daterange").val(daterange);




 
$("#resault").click(function(){
    var daterange=$("#daterange").val();
  
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace("-", "hp");

var formdata = { platform: $("#platform").val(), ordernumber: $("#ordernumber").val(), daterange: daterange };
alert(formdata);
  $.ajax({ 
        method:'POST',
        contentType:'application/json',
        url:'https://www.bnyfoodproducts.com/admin/accounting/taxinvoice/searchtoissue',

        data: JSON.stringify(formdata),
        success:function(response){
        $("#resault").HTML(response);
        }

  });
});



$('#searchcopy').click(function(){
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
  
  var arr_path = window.location.pathname.split("/");
  var urls = 'http://'+window.location.hostname+"/adminbny/accounting/shot_invoice/"+platform+"/"+ordernumber+"/"+daterange+"/copy";
  //alert(urls);
  return window.open(urls, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  return false;
});



$('#search').click(function(){
   var w=1200;
   var h=800;
   var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  var search_type=$("select#search_type").val();
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
  

  var urls = hostname_site+"accounting/taxinvoice/shot_invoice/"+platform+"/"+ordernumber+"/"+search_type+"/original/"+daterange;
  //alert(urls);
  return window.open(urls, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  return false;
});


$('#ftaxinv').click(function(){


    var daterange=$("#daterange").val();
  
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace("-", "hp");
   
//alert($('input[name="taxinvoicetype"]:checked').val());

//================
if($("#ordernumber").val()=="")
{
    

}
else
{
$("#daterange").val();


}


var formdata = {taxinvoicetype:$('input[name="taxinvoicetype"]:checked').val(),platform:$("select#platform").val(),ordernumber:$("#ordernumber").val(),daterange:daterange};

var url='https://www.bnyfoodproducts.com/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype='+$('input[name="taxinvoicetype"]:checked').val()+'&platform='+$("select#platform").val()+'&ordernumber='+$("#ordernumber").val()+'&daterange='+daterange+'&page=1';

//alert(url);
 //return window.open(url, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  //return false;

window.location.href = url;

//================



});






});

function loadbyurl(url)
{


    alert(url);
}

        