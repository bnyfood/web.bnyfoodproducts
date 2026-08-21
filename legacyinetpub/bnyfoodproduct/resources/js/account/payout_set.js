$( document ).ready(function() {
//$("input[name='statement_number[]']").live('click', function(event){
$("input[name='TransactionID[]']").on('click', function(event){	
		if($(this).is(":checked")) {
            var is_chk = 1;
        } else {
        	var is_chk = 0;
        }
        //alert(is_chk);
		TransactionID = $(this).attr('id');
		//alert(user_id);
		//alert(cat_id);
		payout_id = $("#payout_id").val();
		
		var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/adminbny/payout/payout_map_set";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {TransactionID: TransactionID,payout_id:payout_id,is_chk:is_chk}, 
			  dataType: "json",
			  async:false
			});
		
	});	
});