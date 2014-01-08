<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$template->header_tag['title'] = lang('Add content type');
		
$ct_type_id = $r->get('ct_type_id');

$formArgs = array(	'action' => BASE_DIR . 'admin.php?module=ct_type&op=add_ct_type&ct_type_id=' . $r->get('ct_type_id'),
					'method' => 'POST',
					'id'	 => 'add_ct_type'
				);
$ct_type = array(	'ct_type_name' => '',
					'ct_type_title' => '' );
if( $ct_type_id > 0 )
{
	global $db;
	
	$db->where['ct_type_id'] = $ct_type_id;
	$ct_type = $this->ct_types[$ct_type_id];
}
if( $r->post('contentField') )
{
	$ct_type = $r->post('contentField');
	$this->save_content_type($ct_type);
}
$form = new form('add_ct_type', $formArgs);
$form->addTag('clear');
$field_data = array(
					'field_name'	=> 'ct_type_name',
					'default_value'	=> $ct_type['ct_type_name'],
					'field_label'	=> 'Content type name'
					);
if( $ct_type_id > 0 ) $field_data['disabled'] = 'readonly';
$form->addField($field_data);
$field_data = array(
					'field_name'	=> 'ct_type_title',
					'default_value'	=> $ct_type['ct_type_title'],
					'field_label'	=> 'Content type title'
					);
$form->addField($field_data);
$form->addTag('clear');
$field_data = array(
					'field_name'	=> 'ct_type_submit',
					'default_value'	=> lang('Submit'),
					'field_label'	=> $ct_type['ct_type_name'],
					'class'			=> 'btn btn-primary',
					'field_type'	=> 'submit'
					);
$form->addField($field_data);

$tpl = $template->file('template/add_ct_type.tpl');
$tpl->assign('ADD_CT_TYPE_FORM', $form->create());
$tpl->parse('main');
$template->body_data .= $tpl->text('main');

?>