$("#btn_submit").click(function(event) {

	var txt_email = $("#txt_email").val();
	var txt_password = $("#txt_password").val();

	//var recaptcha = $("#g-recaptcha-response").val();
	var cha_response = grecaptcha.getResponse();

	var urls = hostname_site+"/"+"users/logined";
    $.ajax({
      type: "POST",
      url: urls,
      data:  {txt_email:txt_email,txt_password:txt_password,cha_response:cha_response}, 
      dataType: "json",
      success: function (data) {
            
        //console.log(data.logon_res);
        if(data.logon_res ==true){
         // console.log('success');
         window.location.replace(hostname_site+"monitor/main");

        }else{
          //console.log('fail');    
          document.getElementById("daibox_login").style.display = "block";
          grecaptcha.reset();
          document.getElementById('btn_submit').disabled = true;  
        }

      }
  	});

});