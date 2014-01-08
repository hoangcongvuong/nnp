<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

if( defined('IS_AJAX') )
{
	$ct_type_id = $r->get('ct_type_id');
	$field_type = $r->get('field_type');
	$supportedField = array('text', 'image', 'file', 'hidden', 'referer', 'textarea', 'checkbox', 'select', 'radio', 'number', 'html', 'password');
	if( $ct_type_id > 0 && in_array($field_type, $supportedField) )
	{
		$field_data = array('ct_type_id'	=> $ct_type_id,
							'ct_field_name'	=> '',
							'ct_field_label'=> '',
							'ct_field_type'	=> $field_type,
							'ct_field_data'	=> encodeArray()
							);
		$insertField = $db->insert('ct_fields', $field_data);
		$ct_field_id = $insertField->insert_id;
		$field_data['ct_field_id'] = $ct_field_id;
		$field_data['ct_field_name'] = $ct_field_id . '_' . $field_type;
		$field_data['ct_field_label'] = $ct_field_id . '_' . $field_type;
		$db->where['ct_field_id'] = $ct_field_id;
		$db->update('ct_fields', $field_data);
		$template->body_data = $this->field_template($field_data);
	}
	else $template->body_data = '';
}

?>