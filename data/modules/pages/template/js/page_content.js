$(document).ready(function(e) {
    $('#vnp-page-builder .content-type').click(function(e) {
			e.preventDefault();
			alert('ok');
			var idArray = new Array();
           	var contentTypeIdString = $(this).attr('id');
		   	idArray = fieldTypeIdString.split('-');
			
			var ct_type_id = idArray.pop();
			var content = getParameterByName('content');
			alert(ct_type_id);
			$.ajax({
				type: 'POST',
				url: base + '/admin.php?module=pages&op=get_content_type_field&content=' + content + '&ct_type_id=' + ct_type_id,
				beforeSend: showLoading,
				success: function(data){
					//$(settings.containerSelector).append(data);
					hideLoading();
				}
			});
			return false;
        });
});