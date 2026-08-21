$(document).ready(function() {
	$( "#submit_payout_add" ).click(function() {
	 

	 map_id = $("#map_id").val();
	 amount_transfer = $("#amount_transfer").val();
	 transfer_date = $("#transfer_date").val();
	 transfer_approved = $('input[name="transfer_approved"]:checked').val();

		var arr_path = window.location.pathname.split("/");
        var urls = 'http://'+window.location.hostname+"/"+"adminbny/payout/payout_edit";

         $.ajax({
			  type: "POST",
			  url: urls,
			  data:  {map_id:map_id,amount_transfer: amount_transfer,transfer_date:transfer_date,transfer_approved:transfer_approved}, 
			  dataType: "json",
			  async: false,
			  success: function() {
			  		//parent.jQuery.fancybox.close();
			  		parent.location.reload(true);
    			}
			})

	});
							
});		