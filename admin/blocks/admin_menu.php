<?php

if( !defined('VNP') ) die( 'Error!!!');

$AdminMenu = array();

if( !function_exists('admin_menu') )
{
	function admin_menu()
	{
		global $template, $r;
		
		$menuArray = array();
		
		$menuArray['home'] = array('link' => BASE_DIR, 'title' => 'Home', 'noajax' => 'noajax' );
		$menuArray['admin'] = array('link' => BASE_DIR . 'admin.php', 'title' => 'Admin', 'noajax' => 'noajax="true"' );
		
		$pageSubs['add_page'] = array('link' => BASE_DIR . 'admin.php?module=pages&op=add_page', 'title' => 'Add page');
		$menuArray['pages'] = array('link' => BASE_DIR . 'admin.php?module=pages', 'title' => 'Pages', 'sub' => $pageSubs);
		
		
		$submenuCt_type['add_ct_type'] = array('link' => BASE_DIR . 'admin.php?module=ct_type&op=add_ct_type', 'title' => 'Add content type');
		$submenuCt_type['add_taxonomy'] = array('link' => BASE_DIR . 'admin.php?module=ct_type&op=add_taxonomy', 'title' => 'Add taxonomy');
		$submenuCt_type['setting'] = array('link' => BASE_DIR . 'admin.php?module=ct_type&op=setting', 'title' => 'Setting');
		$menuArray['ct_type'] = array('link' => BASE_DIR . 'admin.php?module=ct_type', 'title' => 'Content type', 'sub' => $submenuCt_type);
		$menuArray['theme'] = array('link' => BASE_DIR . 'admin.php?ctl=theme', 'title' => 'Themes');
		
		
		$submenuSetting['add_config_field'] = array('link' => BASE_DIR . 'admin.php?module=setting&op=add_config_field', 'title' => 'Add config field');
		$menuArray['setting'] = array('link' => BASE_DIR . 'admin.php?module=setting', 'title' => 'Setting', 'sub' => $submenuSetting);
		
		$menuArray['user'] = array('link' => BASE_DIR . 'admin.php?ctl=user', 'title' => 'Users');
		
		$menuMedia['upload'] = array('link' => BASE_DIR . 'admin.php?module=media&op=upload', 'title' => 'Upload');
		$menuArray['media'] = array('link' => BASE_DIR . 'admin.php?module=media', 'title' => 'Media', 'sub' => $menuMedia);
		$menuArray['tool'] = array('link' => BASE_DIR . 'admin.php?ctl=tool', 'title' => 'Tool');
		$menuArray['logout'] = array('link' => BASE_DIR . 'admin.php?module=user&op=logout', 'title' => 'Logout', 'noajax' => 'noajax');
		
									
		$tpl = $template->file('template/admin_menu.tpl');
		
		$currentCtl = $r->get('ctl');
		foreach( $menuArray as $key => $menu )
		{
			if( $key == $currentCtl ) $menu['class'] = 'active';
			else $menu['class'] = '';
			
			$tpl->assign('menu', $menu);
			
			if( isset($menu['sub']) && !empty($menu['sub']) )
			{
				foreach( $menu['sub'] as $sub )
				{
					$tpl->assign('sub', $sub);
					$tpl->parse('main.menu.sub_menu.loop');
				}
				$tpl->parse('main.menu.sub_menu');
			}
			$tpl->parse('main.menu');
		}
		$tpl->parse('main');
		return $tpl->text('main');
	}
}
$blockContent = admin_menu();

?>