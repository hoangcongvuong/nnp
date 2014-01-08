// JavaScript Document
$(document).ready(function(e) {
    var distance = $('#for-newbie').offset().top,
    $window = $(window);

	$window.scroll(function() {
		if ( $window.scrollTop() >= distance ) {
			$('#for-newbie').css({top: '15px', width: '310px'}).addClass('fixed');
		}
		else $('#for-newbie').removeClass('fixed');
	});
	
	/*** Submit comment ***/
	$(document).on('submit', '.add_comment', function(){
	//$('.add_comment').submit(function(e) {
        url = $(this).attr('action');
		var form_data = {	ct_type_id: $('#ct_type_id', $(this) ).val(),
							content_id: $('#content_id', $(this) ).val(),
							parent_id: $('#parent_id', $(this) ).val(),
							author_name: $('#author-name', $(this) ).val(),
							author_email: $('#author-email', $(this) ).val(),
							author_name: $('#author-name', $(this) ).val(),
							content: $('#comment-content', $(this) ).val(),
							ajax: 'state-main'
		};
		
		var options = {
				type: 'POST',
				url:	url,
				data: form_data,
				dataType: 'json',
				success: function(t) {
					if( t.status == 'ok' )
					{
						if( 1 )
						{
							$('#list-comment').html(t.content);
							$('#comment-content' ).val('');
						}
					}
					else alert(t.status);
				},
				error: function(a,b,c)
				{
					alert(b);
				}
		};
		ajax(options);
		return false;
    });
	/*** Submit comment ***/
	
	/*** Load form reply ***/
	$(document).on('click', '.reply-comment', function(){
	//$('.reply-comment').bind('click',function(e) {
		var obj = $('#add_comment');
		var id_string = $(this).attr('id');
		id_string = id_string.split('-');
		var parent_id = id_string.pop();
		var form_info = {	ct_type_id: $('#ct_type_id', obj ).val(),
							content_id: $('#content_id', obj ).val(),
							parent_id: parent_id,
							ajax: 'state-main'
		};
        var options = {
			type: 'POST',
			url: $('#add_comment #reply_form').val(),
			data: form_info,
			dataType: 'json',
			success: function(data) {
					if( data.status == 'ok' )
					{
						$('#comment-' + parent_id).append(data.content);
					}
					else alert(data.content);
				}			
		}
		ajax(options);
		return false;
    });
	/*** Load form reply ***/
});

function ajax(options)
{
	$.ajax(options);
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

function inlineEditorUpdateContent(ct_type_id, content_id, tinymceObj)
{
	var data = {
					mod:		'inline_editor',
					ct_type_id: ct_type_id,
					content_id: content_id,
					content:	tinymceObj.activeEditor.getContent()
	};
	var url = base + '/admin.php?module=ct_type&op=ajax_update_content';
	
	var options = {
			type: 'POST',
			data: data,
			url: url,
			success: function(data) {
				if( data == 'ok' ) window.location = window.location;
				else alert('Error when updating data, please try again!');
			}
	}
	ajax(options);
}