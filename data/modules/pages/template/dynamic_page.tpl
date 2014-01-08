<!-- BEGIN: main -->
<div class="choose-page-featured">
    <button data-toggle="modal" data-backdrop="static" data-target="#fieldsList" id="browse-button-%s" class="btn btn-primary browse-file" refererfield="%s">Data fields</button> 
    <button data-toggle="modal" data-backdrop="static" data-target="#listContent" id="browse-button-%s" class="btn btn-primary browse-file" refererfield="%s">List content</button>
</div>
<div class="page-fatured-container">
	{ADD_PAGE_FIELD}
</div>

<div class="modal fade" id="fieldsList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content" style="width:100%">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Choose page field</h4>
            </div>
            <div class="modal-body clearfix">
                {ADD_PAGE_FIELD}
            </div>
        </div>
    </div>
</div>
<!-- END: main -->