<?php

class vnp
{
	public	$ct_type_id = 0;
	public	$content_id = 0;
	public	$taxonomy_id = 0;
	public	$is_home = 0;
	
	public function __construct()
	{
		global $db, $template;
		$db->set_cache = false;
		
		$this->setGlobalVariables();
		//$this->installTheme();
		$this->loadTheme();
		$this->setMetaTags();
		require CONTROLLER_PATH . 'crypt/Hashids.php';
		//$this->registerDemoMenu();
		//n($db->log);
		//$template->body_data = '<pre>' . print_r($db->log, true) . '</pre>';
	}
	
	private function installTheme()
	{
		global $db, $template;
		$theme_info = getThemeInfo(THEME_PATH, 'vnp');
		
		$_theme = array(	'theme_name'	=> $theme_info['info']['name'],
							'theme_layout'	=> serialize($theme_info['layouts']),
							'theme_block'	=> serialize($theme_info['blocks']),
							'info'			=> serialize($theme_info['info']),
							'dir'			=> 'vnp'
						);
		$db->replace('themes', $_theme);		
		
		foreach( $theme_info['blocks'] as $_blockName => $blockData)
		{
			$_block['block_name'] = $blockData['name'];
			$_block['block_file'] = $blockData['file'];
			$_block['execute_function'] = $blockData['function'];
			$_block['block_type'] = 'theme';
			$_block['type_value'] = $theme_info['info']['name'];
			$_block['layout'] = $blockData['layout'];
			$_block['block_area'] = $blockData['area'];
			$_block['block_order'] = $blockData['order'];
			$db->insert('blocks', $_block);
		}
	}
	
	public function setGlobalVariables()
	{
		global $global, $theme, $config, $db, $template, $modules;
		
		//$config = $db->get('global_config','config_name')->result;
		$config = array_merge($config, getConfigValue());
		$config['db_cache'] = false;
		get_ct_type(false);
		
		$global['taxonomy'] = $db->get('taxonomy', 'taxonomy_alias')->result;
		
		$theme['default_theme'] = $config['default_theme'];
		$theme['default_layout'] = $config['default_layout'];
		
		$theme['theme_root'] = THEME_PATH . $theme['default_theme'] . '/';
		$theme['theme_dir'] = THEME_DIR . $theme['default_theme'] . '/';
		$config['show_execute_time'] = false;
		$template->body_data = '';
		// Load activated modules
		$modules = $db->get('modules', 'module_file')->result;
	}
	
	public function loadTheme()
	{
		global $db, $theme, $template;
		
		/*$db->where['layout'] = array('IN' => array($theme['default_layout'],'all'));
		$db->where['block_type'] = array('!=' => 'admin');
		
		$db->where("`global` = 1 OR `block_id`=2");
		
		$get_agrs = array(	'fieldKey'	=> 'block_id',
							'orderby'	=> 'block_order',
							'order'		=> 'ASC'
						);
		$blocks = $db->get('blocks',$get_agrs);
		$theme['block'][$theme['default_layout']] = $blocks;*/
	}
	
	public function setMetaTags()
	{
		global $global, $template, $theme, $db, $session, $r, $config;
		
		$template->header_tag['title'] = $config['site_name'];
		$template->header_tag['meta']['description'] = '<meta name="description" content="' . $config['site_description'] . '" />';
		$template->header_tag['meta']['http-equiv="Content-Type"'] = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$template->header_tag['meta']['og:title'] = '<meta property="og:title" content="' . $config['site_name'] . '" />';		
		$template->header_tag['meta']['og:image'] = '<meta property="og:image" content="' . $config['site_image'] . '" />';
		$template->header_tag['meta']['og:site_name'] = '<meta property="og:site_name" content="' . $r->domainName . '" />';
		$template->header_tag['meta']['og:description'] = '<meta property="og:description" content="' . $config['site_description'] . '" />';
		$template->header_tag['meta']['og:type'] = '<meta property="og:type" content="blog" />';
		$template->jsHeader(STATIC_DIR . 'js/jquery-1.8.3.min.js', 'file');
		$template->jsHeader($theme['theme_dir'] . 'js/site.js', 'file');
		//$template->jsHeader(STATIC_DIR . 'pretty/scripts/jquery.syntaxhighlighter.min.js', 'file');
		
		//$jsContent = '$.SyntaxHighlighter.init();';
		//$template->jsHeader($jsContent, 'ready');
		
		$template->header_tag['meta']['vnp-script'] = '<script type="text/javascript">var ajaxMarker = ' . json_encode($template->ajax_marker) . '
			;var base = "' . DEFAULT_STATE . '";</script>';	
		if( SITE_AJAX )
		{
			$template->jsHeader(STATIC_DIR . 'js/jquery.address-1.5.min.js', 'file');
			$template->jsHeader(STATIC_DIR . 'js/state.js', 'file');
		}
		if( IS_ADMIN )
		{
			if( $session->get('enable_design_mod') == 'on' )
			{
				$db->where['theme_name'] = $theme['default_theme'];
				$_theme = $db->get('themes', 'theme_name')->result;
				$_theme = $_theme[$theme['default_theme']]['theme_layout'];
				$_theme = unserialize($_theme);
				$theme['block_areas'] = $_theme[$theme['default_layout']]['block'];
			}
			$template->header_tag['link']['admin_bar'] = '<link rel="stylesheet" type="text/css" media="screen" href="' . STATIC_DIR . 'css/admin_bar.css" />';
			$template->jsHeader(STATIC_DIR . 'js/admin_bar.js', 'file');
		}
	}
	
	public function registerDemoMenu()
	{
		global $theme, $db;
	}
}

?>