<?php

class vnp_loader
{
	public	$loader = array();
	
	public function __construct($_loaderOptions = array())
	{
		$defaultOptions =
			array( 
				'debug_mod'				=> 0,
				'cache_template'		=> false,
				'cache_dir'				=> VNP_ROOT . DATA_DIR . '/sites/',
				'allow_apps'			=> true,
				'template_controller'	=> 'template',
				'database_controller'	=> 'database',
				'request_controller'	=> 'request',
				'vnp_controller'		=> 'vnp',
				'controllers'			=> array('security')
			);
			
		$this->loader['options']	= array_merge( $defaultOptions, $_loaderOptions );
		unset($_loaderOptions);
		$this->init();
	}
	
	static public function &instance() 
	{
		static $vnp;
		if( empty( $vnp ) ) 
		{
			$vnp = new vnp_loader();
		}
		return $vnp;
	}
	
	private function init()
	{		
		$this->preparing_system();		
		$this->check_login();
		$this->template_init();
		
		$buildHtml = true;
		
		if( $this->loader['options']['cache_template'] )
		{
			$cache = $this->load_cache();
			if( $cache )
			{
				echo $cache;
				$buildHtml = false;
				exit();
			}
		}
		
		if($buildHtml)
		{
			$this->error_handler();
			$this->load_controllers();
			$this->system_cleaning();
			$this->site_function();
			$this->template_output();
			$this->set_cache();
		}
	}
	
	private function check_login()
	{
		global $session;
		include SOURCE_PATH . 'core/class.session.php';
		$session = new Session();
		$session->start();
		if($session->get('is_admin'))
		{
			define('IS_ADMIN', true);
		}
		else define('IS_ADMIN', false);
	}
	
	private function load_cache()
	{
		global $r, $template;
		
		$cacheString = md5($r->relativeUrl);
		$cacheFile = $this->loader['options']['cache_dir'] . $cacheString;
		if(file_exists( $cacheFile ))
		{
			$result = file_get_contents( $cacheFile );
			return $result;
			//return base64_decode($result);
		}
		return false;
	}
	
	private function preparing_system()
	{
		global $db, $r, $config, $db_config;
		
		include CONFIG_FILE;
		//Load request controller
		$request_controller = $this->loader['options']['request_controller'];
		if( file_exists(CONTROLLER_PATH . $request_controller . '/' . $request_controller . '.php') )
		{
			include(CONTROLLER_PATH . $request_controller . '/' . $request_controller . '.php');
			$r = $this->loader[$request_controller] = new $request_controller();
		}
		
		if( !defined('DEFAULT_STATE') ) define('DEFAULT_STATE', $r->protocol . $r->domainName);
		
		$ajaxMod = $r->post('ajax', '');
		if( $ajaxMod != '' ) define('IS_AJAX', true);
		else define('IS_AJAX', false);
		include VNP_ROOT . SOURCES_DIR . '/core/prepare.php';
	}
	
	private function system_cleaning()
	{
	}
	
	private function error_handler()
	{
		global $global;
		if( $global['admin']['is_admin'] == 1 || $this->loader['options']['debug_mod'] == 1 )
		{
			error_reporting(E_ALL);
			set_error_handler(array($this, 'vnp_error'));
			register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
		}
		else error_reporting(0);
	}
	
	public function vnp_error($level, $msg, $error_file, $error_line, $context = array())
	{
		global $template;
		$template->error[$level][] = array(	'msg' => $msg,
											'file' => $error_file, 
											'line' => $error_line,
											'context' => $context
											);
	}
	
	public function fatalErrorShutdownHandler()
	{
		$last_error = error_get_last();
		$this->vnp_error($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
	}
	
	protected function template_init()
	{
		global $template;
		
		//Load template controller
		$template_controller = $this->loader['options']['template_controller'];
		if( file_exists(CONTROLLER_PATH . $template_controller . '/' . $template_controller . '.php') )
		{
			include(CONTROLLER_PATH . $template_controller . '/' . $template_controller . '.php');
			$template = $this->loader[$template_controller] = new $template_controller();
		}
		else trigger_error('Cannot load template controller, please check your system!!!');
	}
	
	protected function load_controllers()
	{
		global $global, $db, $theme, $request, $config, $db_config, $template, $vnp;
		
		//Load database controller
		$database_controller = $this->loader['options']['database_controller'];
		if( file_exists(CONTROLLER_PATH . $database_controller . '/' . $db_config['dbtype'] . '.php') )
		{
			include(CONTROLLER_PATH . $database_controller . '/' . $db_config['dbtype'] . '.php');
			
			$db_config	= array('db_host'	=> $db_config['dbhost'],
							 	'db_user'	=> $db_config['dbuser'],
								'db_pass'	=> $db_config['dbpass'],
								'db_name'	=> $db_config['dbname'],
								'prefix'	=> $db_config['prefix']
							);
			$db = $this->loader[$database_controller] = new vnp_db($db_config);
		}
		else trigger_error('Cannot load database controller, please check your system!!!');
		
		//Load vnp controller
		$vnp_controller = $this->loader['options']['vnp_controller'];
		if( file_exists(CONTROLLER_PATH . $vnp_controller . '/' . $vnp_controller . '.php') )
		{
			include(CONTROLLER_PATH . $vnp_controller . '/' . $vnp_controller . '.php');
			$this->loader[$vnp_controller] = $vnp = new $vnp_controller();
		}
		else trigger_error('Cannot load vnp controller, please check your system!!!');
		
		//Load other controllers
		foreach( $this->loader['options']['controllers'] as $controller )
		{
			if( file_exists( CONTROLLER_PATH . $controller . '/' . $controller . '.php' ) )
			{
				include(CONTROLLER_PATH . $controller . '/' . $controller . '.php');
				$this->loader[$controller] = new $controller();
			}
			else trigger_error('Cannot load ' . $controller . ' controller, please check your system!!!');
		}
	}
	
	protected function site_function()
	{
		global $template, $r, $theme, $db, $global, $config, $modules, $vnp;
		
		$taxonomyKeys = array_keys($global['taxonomy']);
		$aliasString = '';
		
		if( $r->currentUrl == $r->homeUrl || $r->get('p') == '' && $r->get('module') == '' )
		{
			$vnp->is_home = 1;
			$template->currentBlockTemplatePath = $theme['theme_root'];
			include $theme['theme_root'] . 'home.php';
		}
		elseif( $r->get('p') != '' || $r->post('p') != '' )
		{
			$aliasString = $r->get('p', '');
			if( $aliasString == '' ) $aliasString = $r->post('p', '');
			if(in_array($aliasString, $taxonomyKeys))
			{
				$vnp->taxonomy_id = $global['taxonomy'][$aliasString]['taxonomy_id'];
				$template->currentBlockTemplatePath = MODULE_PATH . 'content/';
				include MODULE_PATH . 'content/taxonomy.php';
			}
			else
			{	
				$urlVariables = explode('-',$r->get('p'));
				$postIDstring = array_pop($urlVariables);
				$aliasString = implode('-',$urlVariables);	
				$contentInfo = decrypt($postIDstring);
				if( !empty($contentInfo) && sizeof($contentInfo) == 2 )
				{
					$vnp->ct_type_id = $contentInfo[0];
					$vnp->content_id = $contentInfo[1];
					
					$template->currentBlockTemplatePath = MODULE_PATH . 'content/';
					include MODULE_PATH . 'content/content.php';
				}
				else $template->body_data .= '<h1>404 NOT FOUND!</h1>';
			}
		}
		elseif( $r->get('module') != '' || $r->post('module') != '' )
		{
			$module = $r->get('module') ? $r->get('module') : $r->post('module');
			$module_keys = array_keys($modules);
			
			if($module != '')
			{
				if( in_array($module, $module_keys) )
				{
					if( file_exists( MODULE_PATH . $module . '/' . $module . '.php' ) )
					{
						include MODULE_PATH . $module . '/' . $module . '.php';
						
						$this->mod[$module] = new $module();
						$method = $r->get('op') ? $r->get('op') : $r->post('op');
						$template->currentBlockTemplatePath = MODULE_PATH . $module . '/';
						if( method_exists($this->mod[$module], $method) )
						{
							call_user_func_array(array($this->mod[$module], $method), array());
						}
						else $this->mod[$module]->main();
					}
					else trigger_error('Cannot load ' . $module . ' module, please check your system!!!');
				}	
				else trigger_error('Invalid module!!!');
			}
		}
	}
	
	protected function load_blocks()
	{
		global $db, $theme, $vnp;
		
		$db->customCondition = "`layout` IN ('" . $theme['default_layout'] . "', 'all') AND `block_type`!= 'admin' AND ";
		
		$db->where['ct_type_id'] = array('REGEXP' => $vnp->ct_type_id );
		$db->where['global'] = 1;
		$db->where['in_home'] = $vnp->is_home;
		
		$get_agrs = array(	'fieldKey'	=> 'block_id',
							'orderby'	=> 'block_order',
							'order'		=> 'ASC'
						);
		$db->logic = 'OR';
		$blocks = $db->get('blocks',$get_agrs)->result;
		
		$theme['block'][$theme['default_layout']] = $blocks;
	}
	
	protected function template_output()
	{
		global $template, $r;
		
		$template->body_data .= "<script type=\"text/javascript\">
		  (function (i, s, o, g, r, a, m) {
				i['GoogleAnalyticsObject'] = r;
				i[r] = i[r] || function () {
					(i[r].q = i[r].q || []).push(arguments)
				}, i[r].l = 1 * new Date();
				a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
				a.async = 1;
				a.src = g;
				a.id = 'analytic-code';
				m.parentNode.insertBefore(a, m)
			})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
		
			ga('create', 'UA-45404047-1', 'vietcode.vn');
			ga('send', 'pageview');
		</script>";
		
		$this->load_blocks();
		if( IS_AJAX )
		{
			$ajaxMod = $r->post('ajax', '');
			$template->ajaxOutput($ajaxMod);
		}
		else $template->output($this->loader['options']['cache_template'], $this->loader['options']['cache_dir']);
	}
}

?>