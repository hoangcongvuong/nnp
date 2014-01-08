<!-- BEGIN: main_content -->
<body>
	<div id="hook-header">
    </div>
    <div class="vnp-wrapper" id="vnp-wrapper">
    	<div class="vnp-sidebar w150" id="vnp-sidebar">
        	<!--
        	<div class="nav-button">
            	<a href="#" onClick="history.go(-1); return false" title="Back"><span class="glyphicon glyphicon-circle-arrow-left"></span></a>
                <a href="#" title="Back"><span class="glyphicon glyphicon-repeat"></span></a>
                <a href="#" title="Back"><span class="glyphicon glyphicon-circle-arrow-right"></span></a>
            </div>
            -->
        	<div id="{AJAX_MARKER.left_sidebar}">
        	{left}
            </div>
        </div>
        <div class="vnp-main-content">
        	{VNP_ERROR}
        	<div id="{AJAX_MARKER.main_content}">
        	{VNP_MAIN_CONTENT}
            </div>
        </div>
    </div>
</body>
<!-- END: main_content -->