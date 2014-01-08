<?php

define('VNP', true);
include './ini.php';
include( FUNCTION_PATH . 'common.php' );
include( VNP_ROOT . SOURCES_DIR . '/core/vnp_loader.php' );

//echo encrypt(20,1000000);
$instance		= &vnp_loader::instance();
//echo sprintf("<br />Elapsed:  %f", $now-$then);

?>