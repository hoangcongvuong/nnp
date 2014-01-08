<!-- BEGIN: main -->
<div>
    <div class="panel clearfix panel-default" id="fields-{FIELD.ct_field_id}">
        <div class="panel-heading">
            <span class="item-title">Comment list: {CONTENT_TYPE_NAME}</span>
            <span class="ct_type_feature">
            	<a href="{SETTING.list_content}" class="ct-featured" title="List content"><span class="glyphicon glyphicon-list"></span></a>
                <a href="{SETTING.add_content}" class="ct-featured" noajax="true" title="Add content"><span class="glyphicon glyphicon-plus"></span></a>
                <a href="{SETTING.profile_setting}" class="ct-featured" title="Profile setting"><span class="glyphicon glyphicon-wrench"></span></a>
                <a href="#" id="remove-comment" class="ct-featured" title="Profile setting"><span class="glyphicon glyphicon-remove"></span></a>
            </span>
            <span class="item-controls">
                <span class="item-type">field</span>
                <span class="item-edit opened">Edit Field Item</span>
            </span>
        </div>
        <div class="panel-body">
        	<table class="table">
                <thead>
                    <tr>
                    	<th><input id="toggle-all" value="1" type="checkbox" /></th>
                        <th>Author name</th>
                        <th>Author email</th>
                        <th>Author ip</th>
                        <th>Content</th>
                        <th>Feature</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
              		<tr>
                    	<td><input value="{CM.comment_id}" class="item-toggle" type="checkbox" /></td>
                    	<td>{CM.author_name}</td>
                        <td>{CM.author_email}</td>
                        <td>{CM.author_ip}</td>
                        <td>{CM.content}</td>
                        <td>{CM.author_name}</td>
                    </tr>
                    <!-- END: loop -->
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
			{container: '#remove-comment', callback: "removeComment(checkedInputs)" }
		]
	});
})
</script>
<!-- END: main -->