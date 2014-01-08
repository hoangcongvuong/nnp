<?php

class admin_loader
{
	public	$loader = array();
	public	$mod = array();
	
	public function __construct($_loaderOptions = array())
	{
		$defaultOptions =
			array( 
				'debug_mod'				=> 0,
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
			$vnp = new admin_loader();
		}
		return $vnp;
	}
	
	private function init()
	{
		global $db, $session;
		
		$this->preparing_system();
		$this->error_handler();
		$this->database_connecting();
		
		include SOURCE_PATH . 'core/class.session.php';
		$session = new Session();
		$session->start();
			
		if($this->check_login())
		{
			define('IS_ADMIN', true);
			$this->load_controllers();
			$this->admin_functions();
			$this->system_cleaning();
			$this->template_handler();
		}
		else $this->login_form();
	}
	
	private function check_login()
	{
		global $db, $session;
		
		if($session->get('is_admin')) return true;
		else return false;
	}
	
	private function login_form()
	{
		global $db, $r, $session;
		
		if( $r->post('login_submit') == 'Login' )
		{
			$username = $r->post('username');
			$password = $r->post('password');
			$db->where['username'] = $username;
			$db->where['group'] = array('IN' => ADMIN_GROUP );
			$u = $db->get('user', 'username')->result;
			$u = $u[$username];
			if( passChecker( $password, $u['salt'], $u['password'] ) )
			{
				$session->set('is_admin', true);
				$session->set('admin_info', $u);
				header('LOCATION: ' . $r->currentUrl);
			}
		}
		$loginForm = '<form method="post"><center>
		<table>
			<tr><td>Username:</td><td><input type="text" name="username" /></td></tr>
			<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
			<tr><td colspan="2" align="center"><input type="submit" name="login_submit" value="Login" /></td></tr>
		</table></center>';
		echo $loginForm;		
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
		
		$ajaxMod = $r->get('ajax') ? $r->get('ajax') : $r->post('ajax');
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
	
	protected function database_connecting()
	{
		global $db, $db_config;
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
	}
	
	protected function load_controllers()
	{
		global $global, $db, $theme, $request, $config, $db_config, $template;
		
		//Load template controller
		$template_controller = $this->loader['options']['template_controller'];
		if( file_exists(CONTROLLER_PATH . $template_controller . '/' . $template_controller . '.php') )
		{
			include(CONTROLLER_PATH . $template_controller . '/' . $template_controller . '.php');
			$template = $this->loader[$template_controller] = new $template_controller();
		}
		else trigger_error('Cannot load template controller, please check your system!!!');
		
		//Load vnp controller
		$vnp_controller = $this->loader['options']['vnp_controller'];
		if( file_exists(ADMIN_CONTROLLER_PATH . $vnp_controller . '/' . $vnp_controller . '.php') )
		{
			include(ADMIN_CONTROLLER_PATH . $vnp_controller . '/' . $vnp_controller . '.php');
			$this->loader[$vnp_controller] = new $vnp_controller();
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
	
	protected function admin_functions()
	{
		global $r, $modules, $template;
		
		include MODULE_PATH . 'ct_type/ct_type.php';
		include(MODULE_PATH . 'ct_type/form.php');
		
		$module = $r->get('module');
		$module_keys = array_keys($modules);
		
		if($module != '')
		{
			if( in_array($module, $module_keys) )
			{
				if( file_exists( MODULE_PATH . $module . '/' . $module . '.php' ) )
				{
					if( $module != 'ct_type' )
					include MODULE_PATH . $module . '/' . $module . '.php';
					
					$this->mod[$module] = new $module();
					$method = $r->get('op');
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
	
	protected function template_handler()
	{
		global $template, $r;
		
		if( IS_AJAX )
		{
			$template->ajaxOutput($r->get('ajax') ? $r->get('ajax') : $r->post('ajax'));
		}
		else $template->output();
	}
}

?>