<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

if( $ct_type_id == 0 )
$ct_type_id = $r->post('ct_type_id');

$ctType = $this->ct_types[$ct_type_id];
$ctFields = $ctType['ct_type_field'];

$options = array();
foreach( $ctFields as $_field )
{
	( $ct_field_id == $_field['ct_field_id'] ) ? $selected = 'selected="selected"' : $selected = '';
	$options[] = '<option ' . $selected . ' value="' . $_field['ct_field_id'] . '">' . $_field['ct_field_label'] . '</option>';
}

?>