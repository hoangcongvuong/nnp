<?php

define('VNP_ADMIN', true);
define('VNP', true);
include './ini.php';

include( FUNCTION_PATH . '/common.php' );
include( VNP_ROOT . SOURCES_DIR . '/core/vnp_loader.php' );
include( VNP_ROOT . ADMIN_DIR . '/admin_loader.php' );
$vnp		= &admin_loader::instance();


?>
