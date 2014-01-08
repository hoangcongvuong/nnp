<!-- BEGIN: main -->
<div class="panel panel-default" id="fields-{FIELD.ct_field_id}">
	<div class="panel-heading">
    	<span class="item-title">Add page</span>
        <span class="item-controls">
			<span class="item-type">{FIELD.ct_field_type} field</span>
			<span class="item-edit opened">Edit Field Item</span>
		</span>
    </div>
    
    <div class="panel-body clearfix">
        <!-- BEGIN: choose_page_type -->
        <div class="choose-page-type">
        	<a class="btn btn-primary" href="{PAGE_TYPE.dynamic}">Dynamic page</a>  
            <a class="btn btn-primary" href="{PAGE_TYPE.static}">Static page</a>
        </div>
        <!-- END: choose_page_type -->
        
        <!-- BEGIN: page_template -->
        {PAGE_TEMPLATE}
        <!-- END: page_template -->
    </div>
</div>
<!-- END: main -->