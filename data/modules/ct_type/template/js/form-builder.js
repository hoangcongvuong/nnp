
(function($){

    $.fn.formBuilder = function(options) {
		
		var obj = this;
		
		var settings = $.extend({
				fieldTypeSelector:	'',
				containerSelector:	'',
				baseUrl:			''
			}, options);
		
		$(settings.fieldTypeSelector, obj).click(function(e) {
			var idArray = new Array();
           	var fieldTypeIdString = $(this).attr('id');
		   	idArray = fieldTypeIdString.split('-');
			
			var fieldType = idArray.pop();
			var ct_type_id = getParameterByName('ct_type_id');
			$.ajax({
				type: 'POST',
				url: settings.baseUrl + fieldType + '&ct_type_id=' + ct_type_id,
				beforeSend: showLoading,
				success: function(data){
					$(settings.containerSelector).append(data);
					hideLoading();
				}
			});
			return false;
        });
    }
}(jQuery));

$(document).ready(function(e) {
    $('#vnp-form-builder').formBuilder({
		fieldTypeSelector: 'a.vnp-form-field-type',
		containerSelector:	'#ct_field-container',
		baseUrl: base + add_field_post_url
	});
	if( $('.panel').length > 1 )
	{
		$('.panel').each(function(index, element) {
			var panel = $(this);
			$('.item-edit', panel).removeClass('opened');
			$('.panel-body', panel).css('display', 'none');
		});
	}
	$('#ct_field-container').sortable({
		items: '.panel',
		handle: '.panel-heading',
		cursor: 'move',
		update: function(){
			var ct_type_id = getParameterByName('ct_type_id');
			var order = $('#ct_field-container').sortable('serialize');
			$.ajax({
				type: 'POST',
				url: base + sort_field_post_url + ct_type_id + '&' + order,
				beforeSend: showLoading,
				success: function(data){
					if(data == 'not') alert('Sort failed, please try again');
					hideLoading();
				}
			})
		} 
	});
	$("#ct_field-container").disableSelection();
});

