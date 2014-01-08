<!-- BEGIN: main -->
<div class="vnp-medias">
	<form action="" method="post" class="fl">
    	<label for="search-file" class="vnp-label" style="width:75px">Search file</label>
        <input class="vnp-input" type="text" id="search-file" />
        <input type="submit" class="btn btn-primary" value="Search" />
    </form>
    <a class="btn btn-success fl" href="{UPLOAD_LINK}" title="Upload" >Upload</a>
    <div class="clear"></div>
    <ul class="media-files" id="media-files">
        <!-- BEGIN: loop -->
        <li id="media-file-{MEDIA.media_id}" class="img-item">
            <img class="media-file" src="{MEDIA.thumb}" mediaid="{MEDIA.media_id}" imagesrc="{MEDIA.link}" width="80" height="80"/>
            <div class="media-info" file_id="{MEDIA.media_id}">
            	<input class="vnp-input file_name" type="text" value="{MEDIA.media_name}" /><br />
                <input class="vnp-input file_title" type="text" value="{MEDIA.media_title}" placeholder="Title" /><br />
                <input class="vnp-input file_alt" type="text" value="{MEDIA.media_alt}" placeholder="Alt" /><br />
                <button type="button" class="btn btn-primary save_file_info">Save</button>
            </div>
        </li>
        <!-- END: loop -->
    </ul>
	<div class="clear"></div>
    <a id="insert-to-post" class="btn btn-primary" >Insert to post</a>
    <div class="clear"></div>
    {PAGING}
</div>
<style type="text/css">
::-webkit-scrollbar {
	width:8px;
	height:8px;
	-webkit-border-radius:4px;
	z-index: 999;
}
::-webkit-scrollbar-track, .modal-body ::-webkit-scrollbar-track-piece {
	background-color:transparent
}
::-webkit-scrollbar-thumb {
	background-color:rgba(53,57,71,0.3);
	width:6px;
	height:6px;
	-webkit-border-radius:4px
}
</style>
<!-- END: main -->