<?php

class vnp
{
	public function __construct()
	{
		global $db, $template;
		$db->set_cache = false;
		require CONTROLLER_PATH . 'crypt/Hashids.php';
		$this->setGlobalVariables();
		$this->loadTheme();
		if( !IS_AJAX )
		$this->setMetaTags();
		$template->body_data = '';
		//$template->body_data = '<pre>' . print_r($db->log, true) . '</pre>';
	}
	
	
	public function setGlobalVariables()
	{
		global $global, $theme, $config, $db, $modules, $template;
		
		//$config = $db->get('global_config','config_name')->result;
		$config = array_merge($config, getConfigValue());		
		get_ct_type(false);
		$theme['default_theme'] = $config['default_admin_theme'];
		$theme['default_layout'] = $config['default_admin_layout'];
		
		$theme['theme_root'] = THEME_PATH . $theme['default_theme'] . '/';
		$theme['theme_dir'] = THEME_DIR . $theme['default_theme'] . '/';
		$config['show_execute_time'] = false;
		$template->header_tag['title'] = $config['site_name'];
		$template->body_data = '';
		
		// Load activated modules
		$modules = $db->get('modules', 'module_file')->result;
	}
	
	public function loadTheme()
	{
		global $db, $theme, $template;
		
		$db->where['layout'] = array( 'IN' => array($theme['default_layout'],'all') );
		
		$db->where['block_type'] = 'admin';
		$db->where['type_value'] = 'vnp_admin';
		
		$get_agrs = array(	'fieldKey'	=> 'block_id',
							'orderby'	=> 'block_order',
							'order'		=> 'ASC'
						);
		$blocks = $db->get('blocks',$get_agrs)->result;
		$theme['block'][$theme['default_layout']] = $blocks;
	}
	
	public function setMetaTags()
	{
		global $global, $theme, $template, $config;
		
		$template->header_tag['meta']['http-equiv="Content-Type"'] = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$template->header_tag['meta']['css-reset'] = '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/reset.css" type="text/css" media="all" />';
		$template->header_tag['meta']['potato-menu-css'] = '<link rel="stylesheet" href="' . STATIC_DIR . 'css/jquery.ui.potato.menu.css" type="text/css" media="all" />';
		$template->header_tag['meta']['jquery'] = '<script type="text/javascript" src="' . STATIC_DIR . 'js/jquery-1.8.3.min.js"></script>';
		$template->header_tag['meta']['potato-menu-lib'] = '<script type="text/javascript" src="' . STATIC_DIR . 'js/jquery.ui.potato.menu.min.js"></script>';
		
		$template->header_tag['meta']['vnp-script'] = '<script type="text/javascript">;var ajaxMarker = ' . json_encode($template->ajax_marker) . ';var base = "' . DEFAULT_STATE . '";</script>';	
		
		$template->jsHeader(STATIC_DIR . 'js/admin.js', 'file');
		if( $config['admin_ajax'] )
		{
			$template->jsHeader(STATIC_DIR . 'js/jquery.address-1.5.min.js', 'file');
			$template->jsHeader(STATIC_DIR . 'js/admin_state.js', 'file');
		}
		
		$jsContent = '$(\'#admin-menu\').ptMenu({vertical:true});';
		$template->jsHeader($jsContent);
	}
}

?>