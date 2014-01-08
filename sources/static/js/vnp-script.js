var init = true,
    state = window.history.pushState !== undefined;

// Handles response
var ajax_state_handler = function(respondedObj){
    $('title').html(respondedObj.title);
	//$('#hook-header').html(respondedObj.hook.header);
	$('#' + ajaxMarker.main_content).fadeOut(0);
   	$('#' + ajaxMarker.main_content).html(respondedObj.data).fadeIn(200);
	$('html,body').animate({scrollTop: $('body').offset().top},'slow');
	document.getElementById('hook-header').innerHTML = respondedObj.hook.header;
	$('#hook-header').append(respondedObj.hook.header);
	hideLoading();
}

var ajax_error_handler = function(error_str)
{
	$('#' + ajaxMarker.main_content).fadeOut(0);
   	$('#' + ajaxMarker.main_content).html(error_str).fadeIn(300);
	hideLoading();
}

var showLoading = function(){
	loadingArea = 'body';
	var loader = '<div id="vnp-loading" class="vnp-loading"></div>';
	$(loadingArea).append(loader);
}

var hideLoading = function(){
	$('.vnp-loading').each(function(index, element) {
        $(this).fadeOut(600, 'swing', function(){
			$(this).remove();
		});
    })
}

$.address.state(base).init(function () {
	
	$('a').click(function() {  
		if( $(this).parent('.mce-tinymce.mce-container.mce-panel').length > 0 ) return false;
		else $.address.value($(this).attr('href'));
	}); 
    // Initializes the plugin
    //$('a:not([noajax])').address();

}).change(function (event) {
    // Selects the proper navigation link
    $('a:not([noajax])').each(function () {
        if( $(this).attr('href') == ($.address.state() + $.address.path() + '?' + $.address.queryString()) )
		{
            $(this).parent().addClass('active');
        }
		else
		{
            $(this).parent().removeClass('active');
        }
    });

    if (state && init) {

        init = false;

    } else {

        // Loads the page content and inserts it into the content area
		var ajaxPath = $.address.state() + $.address.path() + '?&ajax=state-main&' + $.address.queryString();
        $.ajax({
            url: ajaxPath,
			dataType: 'json',
			beforeSend: showLoading,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                ajax_error_handler(XMLHttpRequest.responseText);
            },
            success: function (data, textStatus, XMLHttpRequest) {
                ajax_state_handler(data);
            }
        });
    }

});

if (!state) {

    // Hides the page during initialization
    //document.write('<style type="text/css"> .page { display: none; } </style>');
}

/************ Toggle panel ************/
$(function(){
	$(document).on('click','.item-edit',function(){
		var mainPanel = $(this).parent().parent().parent();
		if( $(this).hasClass('opened') )
		{
			$('.panel-body', mainPanel).animate({height: '0'}, 50, function(){
				$('.panel-body', mainPanel).css('display', 'none');
			})
			$(this).removeClass('opened');
		}
		else
		{
			$('.panel-body', mainPanel).animate({height: '100%'}, 50, function(){
				$('.panel-body', mainPanel).css('display', 'block');
			})
			$(this).addClass('opened');
		}
		return false;
	});
	return false;
});

// Add option
$(document).on('click','.vnp-add-option',function(){
	var idString = new Array();
	idString = $(this).attr('id').split('-');
	var fieldOptionID = idString.pop();
	var optionCount = $('#field-option-' + fieldOptionID + ' .input-group').length - 1;
	var optionNo = parseInt(optionCount) + 1;
	var optionTemplate = '\
	<div class="input-group">\
		<span class="input-group-addon">\
			<input type="radio" class="f-df" value="option_' + optionNo + '" name="field[' + fieldOptionID + '][default_value]">\
		</span>\
		<input value="" type="text" name="field[' + fieldOptionID + '][option][option_' + optionNo + '][value]" class="vnp-input f-value">\
		<input value="" type="text" name="field[' + fieldOptionID + '][option][option_' + optionNo + '][title]" class="vnp-input f-title">\
		<span class="vnp-remove-option" id="remove-opt-' + fieldOptionID + '"><span class="glyphicon glyphicon-remove"></span></span>\
	</div>'
	$(optionTemplate).appendTo('#field-option-' + fieldOptionID);
	$('input[name="field[' + fieldOptionID + '][option][option_' + optionNo + '][value]"]').focus()
})

// Remove option
$(document).on('click', '.vnp-remove-option', function(){

	if( confirm('Are you sure to delete this option?') )
	{
		$(this).parent().remove();
		
		var idString = new Array();
		idString = $(this).attr('id').split('-');
		var fieldOptionID = idString.pop();
		$('#field-option-' + fieldOptionID + ' .input-group').each(function(index, element) {
			var obj = $(this);
			$('.f-df', obj).attr('value', 'option_' + index);
			$('.f-value', obj).attr('name', 'field[' + fieldOptionID + '][option][option_' + index + '][value]');
			$('.f-title', obj).attr('name', 'field[' + fieldOptionID + '][option][option_' + index + '][title]');
		});
	}
})

// Remove field
$(document).on('click', '.vnp-remove-field', function(){
	if( confirm('Are you sure to delete?') )
	{
		var idString = new Array();
		idString = $(this).attr('id').split('-');
		var fieldID = idString.pop();
		$.ajax({
			type: 'POST',
			data: {ct_field_id : fieldID },
			url: base + '/admin.php?module=ct_type&op=remove_field&ajax=string',
			beforeSend: showLoading,
			success: function(data){
				if(data == 'not') alert('Cannot remove this field, please try again');
				else $('#fields-' + fieldID).remove();
				hideLoading();
			}
		})
	}
})

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

$(document).ready(function(e) {
    if($( '#vnp-left').height() < $(document).height() ) $('#vnp-sidebar').css({'position': 'fixed'});
	else $('#vnp-sidebar').css({'position': 'relative'});
});

function removeCtType(ct_type_id)
{
	if(confirm('Are you sure to delete this content type, all referent data wil be deleted and you cannot restore them!'))
	{
		$.ajax({
			type: 'POST',
			data: {ct_type_id : ct_type_id },
			dataType: 'json',
			url: base + '/admin.php?module=ct_type&op=remove_ct_type&ajax=state-main',
			beforeSend: showLoading,
			success: function (data, textStatus, XMLHttpRequest) {
                ajax_state_handler(data);
            }
		})
	}
}

function removeContent(ct_type_id, content_id)
{
	if(confirm('Are you sure to delete this content, all referent data wil be deleted and you cannot restore them!'))
	{
		$.ajax({
			type: 'POST',
			data: {ct_type_id : ct_type_id, content_id: content_id},
			dataType: 'json',
			url: base + '/admin.php?module=ct_type&op=remove_content&ajax=state-main',
			beforeSend: showLoading,
			success: function (data, textStatus, XMLHttpRequest) {
                ajax_state_handler(data);
            }
		})
	}
}

function getCTfield(obj, container)
{
	var ct_type_id = obj.options[obj.selectedIndex].value;
	if( ct_type_id != '' )
	{
		$.ajax({
			type: 'POST',
			data: {ct_type_id : ct_type_id},
			url: base + '/admin.php?module=ct_type&op=get_field_selectbox&ajax=string',
			beforeSend: showLoading,
			success: function (data, textStatus, XMLHttpRequest) {
                $(container).html(data);
				hideLoading();
            }
		})
	}
}
