<!-- BEGIN: main -->
<div class="vnp-medias">
    <a class="btn btn-success fl" href="javascript:window.history.go(-1);" >Library</a>
    <div class="clear"></div>
    <form name="vnp_upload" action="/nnp/admin.php?module=media&op=save_file" method="post" enctype="multipart/form-data">
        <div class="vnp-field" id="vnp_upload-field-1">
            <label class="vnp-label" for="vnp_file[]">Choose file</label>
            <input class="vnp-input " type="file" name="vnp_file[]" id="vnp_file" value=""  multiple  />
        </div>
        <div class="vnp-field" id="vnp_upload-field-2">
            <input class="vnp-input btn btn-primary" type="button" name="contentField[submit_upload]" id="field-submit_upload" value="Upload"   />
        </div>
    </form>
    <div id="files-list"></div>
    <div class="clear"></div>
    <a id="insert-to-post" class="btn btn-primary" >Insert to post</a>
    <div class="clear"></div>
</div>
<!-- END: main -->