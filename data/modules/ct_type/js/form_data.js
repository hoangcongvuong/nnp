
$(document).ready(function(e) {
    $('.browse-file').click(function(e) {
		$(this).modal();
    });
	
	$('#myModal').on('hidden.bs.modal', function () {
		$('.modal-backdrop').remove();
	})
});