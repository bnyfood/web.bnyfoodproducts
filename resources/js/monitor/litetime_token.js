$(document).ready(function() {

	chk_laz_token();
	chk_sho_token();

	//30 second
	setInterval(chk_laz_token, 300000);
	setInterval(chk_sho_token, 300000);


	function chk_laz_token(){
	
    var urls = hostname_site+"/"+"admintoken/token_litetime";
    //document.getElementById("laz_token").innerHTML = "";

	$.ajax({
		  type: "POST",
		  url: urls,
		  dataType: "json",
		  async:false
		})
	  	.done(function( data_re ) {	
	  		var data_token = data_re['data_token'];
	  	//alert(data_re);
	  	if(data_token['litetime'] < 2){
	  		
	  	document.getElementById("txt_order_lazada_alert").innerHTML = "Expire in "+data_token['litetime']+" day";	

	  	document.getElementById('order_lazada').style = 'animation: 1000ms ease-in-out infinite color-change';

	  
		var a = document.createElement('a');
	      //var linkText = document.createTextNode("Click to renew token");
	      //a.appendChild(linkText);
	      //a.title = "Click to renew token";
	      //a.href = "https://auth.lazada.com/oauth/authorize?response_type=code&redirect_uri=https://www.bnyfoodproducts.com/lazcallback&force_auth=true&client_id=123793";
	      //a.target="_TOP";
	      //document.getElementById("laz_token").appendChild(a);

		  	//var alert_2 =	setInterval(function(){ document.getElementById('order_lazada').style = 'background-color:white'; }, 2000);

		  	}else{
		  		//alert('true');

		  		document.getElementById("txt_order_lazada_alert").innerHTML = "Expire in "+data_token['litetime']+" day";
		  		document.getElementById('order_lazada').style = 'background-color:white';

		  		//document.getElementById("laz_token").innerHTML = "Not expire";
		  	}
			  	

	        });
	}

	function chk_sho_token(){
	
    var url_shopee = hostname_site+"/"+"admintoken/shopee_token_litetime";
    //document.getElementById("laz_token").innerHTML = "";

	$.ajax({
		  type: "POST",
		  url: url_shopee,
		  dataType: "json",
		  async:false
		})
	  	.done(function( data_re ) {	
	  		var data_token = data_re['data_token'];
		  	//alert(data_re);
		  	if(data_token['lessthan'] < 3600){
		  		
		  	document.getElementById("txt_order_shopee_alert").innerHTML = "Expire in "+data_token['litetime'];	

		  	document.getElementById('order_shopee').style = 'animation: 1000ms ease-in-out infinite color-change';

		  	}else{
		  		//alert('true');

		  		document.getElementById("txt_order_shopee_alert").innerHTML = "Expire in "+data_token['litetime'];
		  		document.getElementById('order_shopee').style = 'background-color:white';

		  	}
		  	
        });
	}

	  	
});