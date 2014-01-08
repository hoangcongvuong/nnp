<!-- BEGIN: main -->
<div>
    <div class="panel clearfix panel-default" id="fields-{FIELD.ct_field_id}">
        <div class="panel-heading">
            <span class="item-title">Add content: {CONTENT_TYPE_NAME}</span>
            <span class="ct_type_feature">
            	<a href="{SETTING.list_content}" class="ct-featured" title="List content"><span class="glyphicon glyphicon-list"></span></a>
                <a href="{SETTING.add_content}" class="ct-featured noajax" title="Add content"><span class="glyphicon glyphicon-plus"></span></a>
                <a href="{SETTING.profile_setting}" class="ct-featured" title="Profile setting"><span class="glyphicon glyphicon-wrench"></span></a>
            </span>
            <span class="item-controls">
                <span class="item-type">{FIELD.ct_field_type} field</span>
                <span class="item-edit opened">Edit Field Item</span>
            </span>
        </div>
        <div class="panel-body">
        {FORM_DATA}
        </div>
    </div>
</div>
<style type="text/css">
input[type="text"] {
	width: 400px !important;
}
textarea {
	width: 600px;
	height: 60px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px
}
</style>
<!-- END: main -->