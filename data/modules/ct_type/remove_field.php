<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$db->where['ct_field_id'] = $r->post('ct_field_id');
if( $db->delete('ct_fields')->affected_rows != 1 ) $template->body_data = 'not';
else $template->body_data = 'ok';

?>