<?php

class theme
{
	function __construct()
	{
	}
	
	public function enableDesignMod()
	{
		global $session, $r;
		
		if( $session->get('enable_design_mod', '') == 'on' ) $stt = 'off';
		else $stt = 'on';
		$session->set('enable_design_mod', $stt);
	}
	
	public function loadAdminBar()
	{
		global $session;
		$adminBar = '
		<div id="admin-bar">
			<div style="width: 980px; margin: 0 auto">
				<a class="design-mod" href="#">' . $session->get('enable_design_mod') . '</a>
				<a href="' . BASE_DIR . 'admin.php" class="back-to-admin" noajax="true">Admin control panel</a>
				<a href="javascript:enableDesignMode(); return flase;" noajax="true">Switch design mode</a>
			</div>
		</div>';
		echo $adminBar;
		exit();
	}
}

?>