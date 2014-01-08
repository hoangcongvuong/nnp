<!-- BEGIN: main -->
<ul class="bs-glyphicons">
	<!-- BEGIN: loop -->
	<li>
    	<a href="{CT_TYPE.list_content}" class="ct-featured" title="List content"><span class="glyphicon glyphicon-list"></span></a>
    	<a href="{CT_TYPE.add_content}" class="ct-featured noajax" noajax="true" title="Add content"><span class="glyphicon glyphicon-plus"></span></a>
        <a href="{CT_TYPE.content_setting}" class="ct-featured" title="Profile setting"><span class="glyphicon glyphicon-wrench"></span></a>
        <div class="clear"></div>
        {CT_TYPE.ct_type_title}
        <span class="ct_type_feature">
        	<span class="vnp-remove-ct-type glyphicon glyphicon-remove" title="Remove content type" onclick="removeCtType({CT_TYPE.ct_type_id}); return false;"></span>
        	<a class="vnp-edit-ct-type glyphicon glyphicon-pencil" title="Edit content type" href="{CT_TYPE.edit_ct_type}"></a>
            <a class="vnp-add-field glyphicon glyphicon-plus-sign" title="Add field" href="{CT_TYPE.add_ct_field}"></a>
            <a class="vnp-add-field glyphicon glyphicon-comment" title="Comment" href="{CT_TYPE.comment_management}"></a>
        </span>
   	</li>
    <!-- END: loop -->
</ul>
<!-- END: main -->