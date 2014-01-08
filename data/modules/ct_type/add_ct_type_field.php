<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$template->header_tag['title'] = lang('Add content field');

$ct_type_id = $r->get('ct_type_id');
if( empty($ct_type_id) ) $ct_type_id = $r->post('ct_type_id');


$addField = ( $this->ct_types[$ct_type_id]['ct_type_field'] == '' ) ? true : false;

$updateResult = array();
$ct_type_field = array();
if( $r->post('save_ct_type') == 1 )
{
	$this->add_ct_field_submit($ct_type_id);
}

$this->add_ct_field_template($ct_type_id);

?>