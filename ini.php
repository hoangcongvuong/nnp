<?php

$config = '';

$config['start_time'] = microtime();

define( 'VNP_ROOT', pathinfo( str_replace( DIRECTORY_SEPARATOR, '/', __file__ ), PATHINFO_DIRNAME ) . '/' );
define( 'SOURCES_DIR', 'sources' );
define( 'DATA_DIR', 'data' );
define( 'THEME_DIR_NAME', 'themes' );
define( 'ADMIN_DIR', 'admin' );
define( 'UPLOAD_DIR_NAME', 'uploads' );

define( 'SOURCE_PATH', VNP_ROOT . SOURCES_DIR . '/' );
define( 'CONTROLLER_PATH', VNP_ROOT . SOURCES_DIR . '/core/controller/' );
define( 'FUNCTION_PATH', VNP_ROOT . SOURCES_DIR . '/core/function/' );
define( 'THEME_PATH', VNP_ROOT . DATA_DIR . '/' . THEME_DIR_NAME . '/' );
define( 'CONFIG_FILE', VNP_ROOT . 'config.php' );

define( 'ADMIN_ROOT', VNP_ROOT . ADMIN_DIR . '/' );
define( 'ADMIN_CONTROLLER_PATH', VNP_ROOT . ADMIN_DIR . '/controllers/' );
define( 'ADMIN_MODULE_PATH', VNP_ROOT . ADMIN_DIR . '/modules/' );
define( 'MODULE_PATH', VNP_ROOT . DATA_DIR . '/modules/' );
define( 'UPLOAD_PATH', VNP_ROOT . DATA_DIR . '/' . UPLOAD_DIR_NAME . '/' );

if( pathinfo( $_SERVER['PHP_SELF'], PATHINFO_DIRNAME ) != '/' )
{
	define( 'DEFAULT_STATE', pathinfo( $_SERVER['PHP_SELF'], PATHINFO_DIRNAME ) );
	define( 'BASE_DIR', pathinfo( $_SERVER['PHP_SELF'], PATHINFO_DIRNAME ) . '/' );
	define( 'BASE_ADMIN', pathinfo( $_SERVER['PHP_SELF'], PATHINFO_DIRNAME ) . '/' . ADMIN_DIR . '/' );
}
else
{
	//define( 'DEFAULT_STATE', 'http://nguyenngocphuong.com' );
	define( 'BASE_DIR', '/' );
	define( 'BASE_ADMIN', '/' . ADMIN_DIR . '/' );
}
define( 'THEME_DIR', BASE_DIR . DATA_DIR . '/' . THEME_DIR_NAME . '/' );
define( 'STATIC_DIR', BASE_DIR . SOURCES_DIR . '/static/' );
define( 'MODULE_DIR', BASE_DIR . DATA_DIR . '/modules/' );
define( 'UPLOAD_DIR', BASE_DIR . DATA_DIR . '/' . UPLOAD_DIR_NAME . '/' );


// Initializing constant
define( 'ADMIN_GROUP', '1,2' );
define( 'ADMIN_AJAX', false );
define( 'SITE_AJAX', false );
define( 'TIME', isset( $_SERVER['REQUEST_TIME'] ) ? $_SERVER['REQUEST_TIME'] : time() );

//global variables
$global = $config = $modules = array();
$db = $theme = $r = $db_config = $template = $session = $vnp = '';

$global['admin']['is_admin'] = 1;


// location setting
date_default_timezone_set('Asia/Bangkok');

//Cache setting
define('DB_CACHE_DIR', VNP_ROOT . DATA_DIR . '/cache/db/' );
define('HTML_CACHE_DIR', VNP_ROOT . DATA_DIR . '/cache/template/' );
define('XML_CONFIG_FILE', VNP_ROOT . DATA_DIR . '/site_config.xml' );

?>
