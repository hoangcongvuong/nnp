<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$db->where['ct_type_id'] = $r->get('ct_type_id');
$_updateCtType = $db->update('ct_types', array('ct_field_sort' => encodeArray($r->get('fields'))));
if( $_updateCtType->status != 1 ) $template->body_data = 'not';
else $template->body_data = 'ok';

?>