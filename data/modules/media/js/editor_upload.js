(function($){

    $.fn.uploader = function(options) {
		
		var obj = this;
		var mediaFiles;
		var filesData;
		var numberFiles;
		var currentFile = 0;
		
		var settings = $.extend({
				fileField:	'',
				fileList:	'',
				uploadButton: '',
			}, options);
			
		var prepareMediaFile = function(mediaFiles) {
			
			var $mediaList = $('<ul class="media-files clear" id="media-files">');
			for( var i = 0; i < numberFiles; i++ )
			{				
				var imgItem = '\
				<li id="media-file-' + i + '" class="img-item">\
            		<img class="media-file" src="" mediaid="' + i + '" imagesrc="" width="80" height="80"/>\
					<div id="progress-file-' + i + '" class="progress active">\
						<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">\
							<span class="sr-only">0% Complete</span>\
						</div>\
					</div>\
        		</li>\
				';
				$mediaList.append(imgItem);
			}
			
			
			//$table.appendTo(settings.fileList, obj);
			$mediaList.appendTo(settings.fileList, obj);
			//$(settings.fileList, obj).append(mediaList);
		}
			
		$(settings.fileField, obj).change(function(e) {		
			mediaFiles = $(this)[0];
			numberFiles = mediaFiles.files.length;
			prepareMediaFile(mediaFiles);
			return false;
        });
		
		var upload = function(fileID) {
			
			if( fileID < numberFiles )
			{
				var url = base + '/admin.php?module=media&op=save_file&ajax=string';
				var formData = new FormData();
				formData.append('vnp_file', filesData.files[currentFile]);
				$.ajax({
					type:	'POST',
					url:	url,
					processData: false,
					contentType: false,
					data:	formData,
					dataType: 'json',
					xhr: function()
					{
						//var xhr = new window.XMLHttpRequest();
						if (window.XMLHttpRequest) xhr = new XMLHttpRequest();
						else xhr = new ActiveXObject("Microsoft.XMLHTTP");
						
						xhr.upload.addEventListener("progress", function(evt){
							if (evt.lengthComputable) {  
								var percentComplete = (evt.loaded / evt.total)*100;
								//Do something with upload progress
								$('#progress-file-' + currentFile + ' .progress-bar').css({width: percentComplete + '%'});
								
								//if( percentComplete == 100 ) $('#progress-file-' + currentFile).removeClass('active');
							}
						}, false); 
						return xhr;
					},
					success: function(data){
						//$('#status-file-' + currentFile).html(data.mediainfo.status);
						$('#media-file-' + currentFile + ' img').attr('src', data.thumb);
						$('#media-file-' + currentFile + ' img').attr('imagesrc', data.image);
						currentFile++;
						upload(currentFile);
					},
					error: function(requestObject, error, errorThrown)
					{
						$('#status-file-' + currentFile).html(requestObject.responseText);
						currentFile++;
						upload(currentFile);
					}
				});
			}
		}
		
		$(settings.uploadButton).click(function(e) {    
			filesData = $(settings.fileField).get(0);  
			upload(currentFile);	
        });
    }
}(jQuery));

function objToString (obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '::' + obj[p] + '\n';
        }
    }
    return str;
}

$(document).ready(function(e) {
    $('form[name="vnp_upload"]').uploader({
		fileField: '#vnp_file',
		fileList: '#files-list',
		uploadButton: '#field-submit_upload'
	});
})