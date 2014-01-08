<?php

if( !defined('VNP') ) die( 'Error!!!');

if( !function_exists('submenu') )
{
	function submenu($parent_item = '', $menuItems)
	{
		global $template;
		
		$tpl = $template->file('template/sub-menu.tpl');
		
		foreach( $menuItems as $_menuIts )
		{
			if( $_menuIts['parent_item'] == $parent_item )
			{
				$_menuIts['menu_link'] = str_replace('{BASE_DIR}', BASE_DIR, $_menuIts['menu_link']);
				$_menuIts['sub'] = submenu($_menuIts['menu_id'], $menuItems);
				$tpl->assign('MENU', $_menuIts);
				$tpl->parse('main.loop');
			}
		}
		$tpl->parse('main');
		return $tpl->text('main');
	}
}

if( !function_exists('menu') )
{
	function menu($blockData)
	{
		global $template, $db, $global, $r;
		
		$db->where['menu_type'] = $blockData['menu_type'];
		$tableName = $global['ct_types'][$blockData['ct_type_id']]['ct_type_name'];
		
		$get_args = array(	'fieldKey'	=> $tableName . '_id',
							'order'		=> 'ASC'
						);
		$menuItems = $db->get($tableName, $get_args)->result;
		
		if( $blockData['menu_type'] == 1 ) $tpl = $template->file('template/top-menu.tpl');
		elseif( $blockData['menu_type'] == 2 ) $tpl = $template->file('template/main-menu.tpl');
		
		if( !empty($menuItems) )
		foreach( $menuItems as $_menuIts )
		{
			if( empty($_menuIts['parent_item']) )
			{
				$_menuIts['sub'] = submenu($_menuIts['menu_id'], $menuItems);
				$_menuIts['menu_link'] = str_replace('{BASE_DIR}', BASE_DIR, $_menuIts['menu_link']);
				if( $r->currentUrl == $_menuIts['menu_link'] || $r->relativeUrl == $_menuIts['menu_link'] )
				$_menuIts['active'] = 'class="active-menu-item"';
				else $_menuIts['active'] = '';
				
				$tpl->assign('MENU', $_menuIts);
				$tpl->parse('main.loop');
			}
		}
		$tpl->parse('main');
		return $tpl->text('main');
	}
}

$blockContent = menu($blockData);

?>