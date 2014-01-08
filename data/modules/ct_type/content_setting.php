<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$ct_type_id = $r->get('ct_type_id');
if( empty($ct_type_id) ) $ct_type_id = $r->post('ct_type_id');

$form = new form();

$formArgs = array(	'action' => BASE_DIR . 'admin.php?module=ct_type&op=content_setting&ct_type_id=' . $r->get('ct_type_id'),
					'method' => 'POST',
					'id'	 => 'content_setting'
				);
if( $ct_type_id > 0 )
{
	$ctType = $this->ct_types[$ct_type_id];
	$contentField = $r->post('contentField');
	if( isset($contentField['save_setting']) && $contentField['save_setting'] == lang('Save') )
	{
		unset($contentField['save_setting']);
		$ct_type_data['ct_type_setting'] = encodeArray($contentField);
		$db->where['ct_type_id'] = $ct_type_id;
		if( $db->update('ct_types', $ct_type_data)->status )
		$template->body_data .= alert(lang('Update success: ' . $ctType['ct_type_name'] . '!'), 'success');
		else $template->body_data .= alert(lang('Update fail!'), 'error');
	}
	else
	{
		$contentField = $ctType['ct_type_setting'];
	}

	$form = new form('add_ct_type', $formArgs);
	$form->addTag('clear');
	$options = array();
	
	
	$Fields = $ctType['ct_type_field'];
	
	foreach( $Fields as $_field )
	{
		$options[$_field['ct_field_id']] = $_field['ct_field_label'];
	}
	
	//$template->body_data .= p($contentFields);
	$field_data = array(
						'field_name'	=> 'show_fields',
						'field_label'	=> 'Showed fields',
						'field_type' 	=> 'checkbox',
						'option'		=> $options,
						'default_value'	=> isset($contentField['show_fields']) ? $contentField['show_fields'] : ''
						);
	$form->addField($field_data);
	$form->addTag('clear');
	$field_data = array(
						'field_name'	=> 'num_rows',
						'field_label'	=> lang('Number item per page'),
						'default_value'	=> isset($contentField['num_rows']) ? $contentField['num_rows'] : ''
						);
	$form->addField($field_data);
	$form->addTag('clear');
	$field_data = array(
						'field_name'	=> 'use_comment',
						'field_label'	=> lang('Use comment'),
						'field_type' 	=> 'select',
						'option'		=> array('No', 'Yes'),
						'default_value'	=> isset($contentField['use_comment']) ? $contentField['use_comment'] : 1
						);
	$form->addField($field_data);
	$form->addTag('clear');
		
	$field_data = array(
						'field_name'	=> 'save_setting',
						'field_type' 	=> 'submit',
						'class'			=> 'btn btn-primary',
						'default_value'	=> lang('Save')
						);
	$form->addField($field_data);
	$template->body_data .= $template->collapseBox($form->create(), $ctType['ct_type_name'], 'Content type setting');
}

?>