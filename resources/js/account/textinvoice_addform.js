$( document ).ready(function() {
	$('input[type=radio][name=invoice_type]').change(function() {
		if (this.value == 1) {
			document.getElementById("headoffice").style.display = "none";
			document.getElementById("branch_number").style.display = "none";
	    }else if (this.value == 2) {
	        document.getElementById("headoffice").style.display = "contents";
	    }
	});

	$('input[type=radio][name=is_head_office]').change(function() {
    	if (this.value == 1) {
    		document.getElementById("branch_number").style.display = "none";
    	}else if (this.value == 2) {
    		document.getElementById("branch_number").style.display = "contents";
    	}
    });	
});