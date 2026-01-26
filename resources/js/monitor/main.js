$(document).ready(function() {

	setInterval(function(){

	var arr_path = window.location.pathname.split("/");
    var urls = 'http://'+window.location.hostname+"/"+"adminbny/accounting/chk_api_order_lazada";

	$.ajax({
		  type: "POST",
		  url: urls,
		  dataType: "json",
		  async:false
		})
	  	.done(function( data_re ) {	

	  	//alert(data_re);
	  	if(!data_re){
	  		
	  		
	  	document.getElementById("txt_order_lazada_alert").innerHTML = 	"Check Data";
	  	document.getElementById('order_lazada').style = 'animation: 1000ms ease-in-out infinite color-change';

	  	//var alert_2 =	setInterval(function(){ document.getElementById('order_lazada').style = 'background-color:white'; }, 2000);

	  	}else{
	  		//alert('true');

	  		document.getElementById("txt_order_lazada_alert").innerHTML = "Data OK";
	  		document.getElementById('order_lazada').style = 'background-color:white';

	  	}
		  	

        });

	  }, 10000);
	  	
	  	
});