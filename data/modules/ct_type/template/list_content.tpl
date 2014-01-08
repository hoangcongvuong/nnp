<!-- BEGIN: main -->
<div class="fl" style="width: 100%">
    <div class="panel clearfix panel-default" id="list-content">
        <div class="panel-heading">
            <span class="item-title">List content</span>
            <span class="ct_type_feature">
                <a href="{SETTING.add_content}" class="ct-featured noajax" noajax="true" title="Add content"><span class="glyphicon glyphicon-plus"></span></a>
                <a href="{SETTING.profile_setting}" class="ct-featured" title="Profile setting"><span class="glyphicon glyphicon-wrench"></span></a>
                <a href="#" id="remove-content" class="ct-featured" title="Profile setting"><span class="glyphicon glyphicon-remove"></span></a>
            </span>
            <span class="item-controls">
                <span class="item-type">Collapse</span>
                <span class="item-edit opened">Edit</span>
            </span>
        </div>
        <div class="panel-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                    	<th><input id="toggle-all" value="1" type="checkbox" /></th>
                    	<!-- BEGIN: thead -->
                        <th>{FIELD.ct_field_label}</th>
                        <!-- END: thead -->
                        <th>Feature</th>
                    </tr>
                </thead>
                <tbody>
                	<!-- BEGIN: row -->
                    <tr>
                    	<td><input value="{CONTENT_ID}" class="item-toggle" type="checkbox" /></td>
                    	<!-- BEGIN: col -->
                        <td>{COLDATA}</td>
                        <!-- END: col -->
                        <td>{FEATURE}</td>
                    </tr>
                    <!-- END: row -->
                </tbody>
            </table>
       	</div>
    </div>
    {PAGING}
</div>
<script type="text/javascript">
$(document).ready(function() {
	var checkedInputs = new Array();
	$('#toggle-all').InputToggle({
		childInput: '.item-toggle', 
		storageVar: 'checkedInputs',
		featureAction: [
			{container: '#remove-content', callback: "removeContent({CT_TYPE_ID}, checkedInputs)" }
		]
	});
})
</script>
<!-- END: main -->