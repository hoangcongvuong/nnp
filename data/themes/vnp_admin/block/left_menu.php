<?php

if( !defined('VNP') ) die( 'Error!!!');
if( !function_exists('leftMenu') )
{
	function leftMenu()
	{
		return
		'<ul class="vnp-feature">
			<li class="active">About</li>
			<li>General</li>
			<li>Apperance</li>
			<li>Performance</li>
			<li>Devices</li>
			<li>Network</li>
			<li>Security</li>
			<li>Easy of access</li>
		</ul>';
	}
}
$executeFunction = 'leftMenu';

?>