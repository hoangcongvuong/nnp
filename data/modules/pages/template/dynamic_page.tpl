<!-- BEGIN: main -->
<div class="choose-page-featured">
    <a href="{PAGE_LINK.single}" class="btn btn-primary {PAGE_ATTRIBUTE.class.single}">Single content</a>
    <a href="{PAGE_LINK.list}" class="btn btn-primary {PAGE_ATTRIBUTE.class.list}">List content</a>
</div>
<div class="page-fatured-container">
	<div class="vnp-box vnp-ct-field-type">
    	<ul class="vnp-fields" id="vnp-page-builder">
            <!-- BEGIN: ct_type -->
            <li><a href="#" class="vnp-form-field-type content-type" id="ct_type-{CT_TYPE.ct_type_id}">{CT_TYPE.ct_type_title}</a></li>
            <!-- END: ct_type -->
       	</ul>
    </div>
    <div class="vnp-box w400 ctfield-ctner">
        <h1>{PAGE_ATTRIBUTE.label}</h1>
    </div>
</div>
<style type="text/css">
.vnp-form-field-type {
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#66CBE573, endColorstr=#66CBE573);
	position: relative;
	display: block;
	margin-right: 8px;
	padding: 4px 10px;
	border: 1px solid rgba(0, 0, 0, 0.54);  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#66CBE573, endColorstr=#66CBE573);
	text-align: left;
	text-decoration: none;
	background: rgba(23, 26, 13, 0.5);
	color: #e1ff80 !important;
}
.vnp-form-field-type:hover {
	border-color: #e1ff80;
}
</style>
<!-- END: main -->