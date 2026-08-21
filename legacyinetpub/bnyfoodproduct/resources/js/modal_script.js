$(document).ready(function() {
	$('#confirm_delete').on('show.bs.modal', function(e) {
        var gotodel_url = $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	});	
});