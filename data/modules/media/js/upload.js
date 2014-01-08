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
			
			var $table = $('<table class="table">');
			$table.append('<thead>').children('thead')
			.append('<tr />').children('tr').append('<th>File Name</th><th>Progress</th><th>Status</th><th>Feature</th>');
			var $tbody = $table.append('<tbody />').children('tbody');
			
			for( var i = 0; i < numberFiles; i++ )
			{
				$tbody.append('<tr><td>' + mediaFiles.files[i].name + '</td>\
				<td><div id="progress-file-' + i + '" class="progress active"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"><span class="sr-only">0% Complete</span></div></div></td>\
				<td><div id="status-file-' + i + '"></div></td>\
				<td></td>\
				</tr>');
			}
			
			
			$table.appendTo(settings.fileList, obj);
		}
			
		$(settings.fileField, obj).change(function(e) {		
			mediaFiles = $(this)[0];
			numberFiles = mediaFiles.files.length;
			prepareMediaFile(mediaFiles);
			return false;
        });
		
		var upload = function(fileID) {
			
			if( fileID <= numberFiles )
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
						$('#status-file-' + currentFile).html(data.status);
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