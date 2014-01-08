var showLoading = function(o){
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

function objToString (obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '::' + obj[p] + '\n';
        }
    }
    return str;
}

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
	//tinymce.execCommand('mceAddControl', true, "field-body_text");
}

var ajax_error_handler = function(error_str)
{
	//alert(objToString(error_str));
	$('#' + ajaxMarker.main_content).fadeOut(0);
   	$('#' + ajaxMarker.main_content).html(error_str.responseText).fadeIn(300);
	hideLoading();
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
			url: base + remove_field_url,
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

function removeContent(ct_type_id, content_ids)
{
	if(confirm('Are you sure to delete this content, all referent data wil be deleted and you cannot restore them!'))
	{
		$.ajax({
			type: 'POST',
			data: {ct_type_id : ct_type_id, content_ids: content_ids},
			dataType: 'json',
			url: base + '/admin.php?module=ct_type&op=remove_content&ajax=state-main',
			beforeSend: showLoading,
			success: function (data, textStatus, XMLHttpRequest) {
                ajax_state_handler(data);
            }
		})
	}
}

function removeComment(listIDs)
{
	if(confirm('Are you sure to delete this content, all referent data wil be deleted and you cannot restore them!'))
	{
		$.ajax({
			type: 'POST',
			data: {ids: listIDs},
			dataType: 'json',
			url: base + '/admin.php?module=comment&op=remove_comment&ajax=state-main',
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

function removeMedia(mediaID)
{
	if(confirm('Are you sure to delete this content, all referent data wil be deleted and you cannot restore them!'))
	{
		$.ajax({
			type: 'POST',
			data: {media_id: mediaID},
			dataType: 'json',
			url: base + '/admin.php?module=media&op=remove_media&ajax=state-main',
			beforeSend: showLoading,
			success: function (data, textStatus, XMLHttpRequest) {
                ajax_state_handler(data);
            }
		})
	}
}

function getUnique(obj){
   var u = {}, a = [];
   for(var i = 0, l = obj.length; i < l; ++i){
      if(u.hasOwnProperty(obj[i])) {
         continue;
      }
      a.push(obj[i]);
      u[obj[i]] = 1;
   }
   return a;
}

(function($){
	
    $.fn.InputToggle = function(options) {

        var settings = $.extend({
            childInput         	: '',
			storageVar			: 'checkedInputs',
			featureAction		: []
        }, options);
		
		var featureNums = settings.featureAction.length;
		for( var i = 0; i < featureNums; i++ )
		{
			var feature = settings.featureAction[i];
			$(feature.container).attr('onclick', feature.callback + ';return false;');
		}
		
		var toggleAllID = $(this).attr('id');
		$(this).click(function(e) {
            if( $('input#' + toggleAllID + ':checked').val() == 1 )
			{
				$(settings.childInput).each(function() {
                    $(this).attr('checked', 'checked');
                });
			}
			else
			{
				$(settings.childInput).each(function() {
                    $(this).removeAttr('checked');
                });
			}
			updateCheckedList();
        });
		$(settings.childInput).click(function(e) {
            if($('input:checkbox:checked' + settings.childInput).length === ($('input:checkbox' + settings.childInput)).length)
			{
				$('input#' + toggleAllID).attr('checked', 'checked');
			}
			else
			{
				$('input#' + toggleAllID).removeAttr('checked');
			}
			updateCheckedList();
        });
		
		function updateCheckedList()
		{
			var _checkedInputs = new Array();
			$(settings.childInput).each(function() {
				if( $(this).is(':checked') )
				{
					_checkedInputs.push( $(this).val() );
				}
			});
			window[settings.storageVar] = String( getUnique(_checkedInputs) );
		}
		return updateCheckedList();
    }
}(jQuery));
