<!-- BEGIN: main -->
<div id="shellnavigation" class="unselectable">
	<div id="shellnavigation-inner">
        <h1>{block_title}</h1>
        <ul id="admin-menu" class="vnp-feature">
            <!-- BEGIN: menu -->
            <li class="{menu.class}">
                <a href="{menu.link}" title="{menu.title}" class="{menu.noajax}">{menu.title}</a>
                <!-- BEGIN: sub_menu -->
                <ul class="sub-menu">
                    <!-- BEGIN: loop -->
                    <li><a href="{sub.link}" class="{sub.noajax}" title="{sub.title}" >{sub.title}</a></li>
                    <!-- END: loop -->
                </ul>
                <!-- END: sub_menu -->
            </li>
            <!-- END: menu -->
        </ul>
   	</div>
</div>
<!-- END: main -->