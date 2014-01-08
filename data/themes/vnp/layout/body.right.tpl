<!-- BEGIN: main_content -->
<body>
	<header id="header">
    	<div class="header-wrapper main-width">
            <nav id="top-nav">
                <div class="main-width">
                    {top}
                </div>
            </nav>
            <div class="main-width main-header">
            	<div class="logo">
                	<h1>
                    	<a href="{BASE_DIR}" title="Cherry">
                        	<img src="{BASE_DIR}data/images/logo-vietcode.png" alt="Cherry">
                     	</a>
                 	</h1>
                </div>
                <div class="ads">
                	<img src="{BASE_DIR}data/images/ads.gif" />
                </div>
                {header}
            </div>
     	</div>
    </header>
    <div class="main-width clear">
        <div class="main-content" id="vnp-main">
            {VNP_ERROR}
            {VNP_MAIN_CONTENT}
       	</div>
        <aside class="sidebar">
        	{right}
        </aside>
    </div>
    <footer class="ct-footer">
    {footer}
    </footer>
</body>
<!-- END: main_content -->