<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$ct_type_id = $r->post('ct_type_id', 0);
$content_id = $r->post('content_id', 0);
$mod		= $r->post('mod', '');
if( $ct_type_id > 0 && $content_id > 0 )
{
	$content = $r->post('content', '');
	
	$updateData = array('body_text' => $content);
	$ctType = $this->ct_types[$ct_type_id];
	$tableName = $ctType['ct_type_name'];
	$db->where[$tableName . '_id'] = $content_id;
	if( $db->update($tableName, $updateData)->status ) echo 'ok';
	else echo 'not';
	exit();
}

?>