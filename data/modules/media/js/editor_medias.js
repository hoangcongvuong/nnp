$(document).ready(function(e) {
	
	if( is_single_field ) insertToField();
	else insertToEditor();
	
	
	$('.save_file_info').click(function(e) {
        var parent = $(this).parent();
		var file_id = parent.attr('file_id');
		var file_name = $('.file_name', parent).val();
		var file_title = $('.file_title', parent).val();
		var file_alt = $('.file_alt', parent).val();
		
		$('.file_name', parent).attr('disabled', 'disabled');
		$('.file_title', parent).attr('disabled', 'disabled');
		$('.file_alt', parent).attr('disabled', 'disabled');
		
		$.ajax({
			type: 'POST',
			url: base + '/admin.php?module=media&op=save_file_info&ajax=string',
			data: {file_id: file_id, file_name: file_name, file_title: file_title, file_alt: file_alt},
			dataType: 'json',
			success: function(data) {
				$('.file_name', parent).removeAttr('disabled');
				$('.file_title', parent).removeAttr('disabled');
				$('.file_alt', parent).removeAttr('disabled');
			}
		})
    });
});

function objToString (obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '::' + obj[p] + '\n';
        }
    }
    return str;
}

function insertToEditor()
{
	var selectedMedias = new Object();
	
	$(document).on('click', 'ul#media-files li.img-item img.media-file', function(){
	//$('ul#media-files li.img-item img.media-file').click(function(e) {
		var media_id = $(this).attr('mediaid');
		var media_src = $(this).attr('imagesrc');
		
		if( $('#media-file-' + media_id).hasClass('selected') )
		{
			$('#media-file-' + media_id).removeClass('selected');
			delete selectedMedias[media_id];
		}
		else
		{
			$('#media-file-' + media_id).addClass('selected');
			
			var media_alt = $('#media-file-' + media_id + ' .file_alt').val();
			var media_title = $('#media-file-' + media_id + ' .file_title').val();
			selectedMedias[media_id] = {alt: media_alt, title: media_title, src: media_src};
		}
		
		
		var image_src = $(this).attr('imagesrc');
		var image = '<img src="' + image_src + '" />';
		//window.parent.tinymce.activeEditor.execCommand('mceInsertContent', false, image);
		//top.tinymce.activeEditor.windowManager.close();
	});
	
	$('#insert-to-post').click(function(e) {		
		var imageString = '';
		
		for( var _media_id in selectedMedias )
		{ 
		   if( selectedMedias.hasOwnProperty(_media_id) )
		   {
			   var mediaObj = selectedMedias[_media_id];
			   imageString += '<img src="' + mediaObj.src + '" alt="' + mediaObj.alt + '" alt="' + mediaObj.title + '" />';
		   }
		}
		window.parent.tinymce.activeEditor.execCommand('mceInsertContent', false, imageString);
		top.tinymce.activeEditor.windowManager.close();
	});
}

function insertToField()
{
	$('ul#media-files li.img-item img.media-file').click(function(e) {
		var media_src = $(this).attr('imagesrc');
		window.parent.document.getElementById(field).value = media_src;
		window.parent.document.getElementById('preview-' + field).innerHTML = '<img src="' + media_src + '" width="80" height="80" />';
		window.parent.$('#myModal').modal('hide')
	});
}