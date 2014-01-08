<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$tpl = $template->file('template/add_taxonomy.tpl');
		
$taxonomy_id = $r->get('taxonomy_id',0);
if( $taxonomy_id == 0 ) $taxonomy_id = $r->post('taxonomy_id');
if( $taxonomy_id > 0 ) $taxonomyIDString = '&taxonomy_id=' . $taxonomy_id;
else $taxonomyIDString = '';

$args = array(	'action'	=> BASE_DIR . 'admin.php?module=ct_type&op=add_taxonomy' . $taxonomyIDString,
				'method'	=> 'post');
$form = new form('add_tax', $args);

$fieldData = array(	'field_name'	=> 'taxonomy_name',
					'field_label'	=> 'Taxonomy name',
				);
$form->addField($fieldData);
$form->addTag('clear');
$fieldData = array(	'field_name'	=> 'taxonomy_alias',
					'field_label'	=> 'Taxonomy alias',
				);
$form->addField($fieldData);
$form->addTag('clear');

$fieldData = array(	'field_name'	=> 'ct_type_id',
					'field_label'	=> 'Content type',
					'field_type'	=> 'select',
					'option'		=> $this->ct_types,
					'option_value'	=> 'ct_type_id',
					'option_title'	=> 'ct_type_title'
				);
$form->addField($fieldData);
$form->addTag('clear');
$fieldData = array(	'field_name'	=> 'refered_ct_type',
					'field_label'	=> 'Refered content type',
					'field_type'	=> 'checkbox',
					'option'		=> $this->ct_types,
					'option_value'	=> 'ct_type_id',
					'option_title'	=> 'ct_type_title'
				);
$form->addField($fieldData);


$form->addTag('clear');
$fieldData = array(	'field_name'	=> 'save_taxonomy',
					'field_label'	=> 'Save Taxonomy',
					'field_type'	=> 'submit',
					'class'			=> 'btn btn-primary',
					'default_value'	=> 'Save Taxonomy'
				);
$form->addField($fieldData);

$tpl->assign('FORM_DATA', $form->create());
$tpl->parse('main');
$template->body_data .= $tpl->text('main');

?>