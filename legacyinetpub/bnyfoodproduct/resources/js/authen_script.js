function authen_user(){
	
	var urls = hostname_site+"/"+"users/chk_authen_user_login";
    $.ajax({
      type: "POST",
      url: urls,
      dataType: "json",
      success: function (data) {
            
        //console.log(data.is_popup);

        if(data.is_popup){
        	//console.log('popup');
        	openModal('ModalLogin');
        }

      },
      complete: function () {

        console.log('Authen Complete');

      }
  	});

}

function openModal(modal_id) {
  $('#'+modal_id).modal('show');
};

function closeModal(modal_id) {
  $('#'+modal_id).modal('hide');
};

$("#pop_login_btn").click(function(event) {

	var bnyusername = $("#bnyusername").val();
	var bnypassword = $("#bnypassword").val();

	//var recaptcha = $("#g-recaptcha-response").val();
	var cha_response = grecaptcha.getResponse();

	var urls = hostname_site+"/"+"users/user_login_logined";
    $.ajax({
      type: "POST",
      url: urls,
      data:  {bnyusername:bnyusername,bnypassword:bnypassword,from_pop_login:'1',cha_response:cha_response}, 
      dataType: "json",
      success: function (data) {
            
        $('#ModalLogin').modal('hide');

      },
      complete: function () {

        console.log('Login Complete');

      }
  	});

});

function close_pop(){
  console.log("close pop");
  //closeModal('ModalLogin');
  //$('#ModalLogin').modal('hide');
}