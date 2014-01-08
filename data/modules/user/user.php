<?php

class user extends ct_type
{
	public function __construct()
	{
		global $db;
		$this->ct_types = get_ct_type();
	}
	
	public function admin_add_content($ct_type_id, $content_id)
	{
		global $template, $r, $db;
		
		$tpl = $template->file('template/add_user.tpl');
		$fieldContent = $r->post('contentField');
		$tableName = alias($this->ct_types[$ct_type_id]['ct_type_name']);
		// Save content when submit
		if( isset($fieldContent['save_content'])  && $fieldContent['save_content'] == lang('Save') )
		{
			if( !empty($fieldContent['password']) && $fieldContent['password'] == $fieldContent['re_password'])
			{
				$pass = passGenerator( $fieldContent['password'] );
				$fieldContent['password'] = $pass['string'];
				$fieldContent['salt'] = $pass['salt'];
				unset($fieldContent['re_password']);
				parent::save_content($tableName, $ct_type_id, $fieldContent, $content_id);
			}
			else $template->body_data .= alert('Password does not match!', 'error');
		}
		else
		{
			if( $content_id > 0 )
			{
				$db->where[$tableName . '_id'] = $content_id;
				$fieldContent = $db->get($tableName)->result;
				$fieldContent = $fieldContent[0];
			}
		}
		
		// Show add form
		$ct_fields = parent::get_ct_fields($ct_type_id);
		
		if($content_id > 0 ) $stringCtID = '&content_id=' . $content_id;
		else $stringCtID = '';
		$formArgs = array(	'action' => BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id . $stringCtID,
							'method' => 'POST',
							'id'	 => 'add_content_' . $ct_type_id
						);
		$form = new form('add_content_' . $ct_type_id, $formArgs);
		$form->setting['ajax'] = false;
		$form->addTag('clear');
		
		if( !empty($ct_fields) )
		{
			foreach( $ct_fields as $fieldID => $ct_field )
			{
				if( $ct_field['ct_field_name'] == 'salt' ) continue;
				$fieldData = $ct_field['ct_field_data'];
				$ct_field = array_merge($fieldData, $ct_field);
				
				unset($ct_field['ct_field_data']);
				if( !isset($ct_field['default_value']) ) $ct_field['default_value'] = '';
				//$template->body_data .= p($ct_field);
				
				if( isset($fieldContent[$ct_field['ct_field_name']]) )
				{
					$ct_field['default_value'] = $fieldContent[$ct_field['ct_field_name']];
				}
				$ct_field['field_name'] = $ct_field['ct_field_name'];
				$ct_field['field_label'] = $ct_field['ct_field_label'];
				$ct_field['field_type'] = $ct_field['ct_field_type'];
				
				if( $ct_field['ct_field_type'] == 'referer' )
				{
					$refererCtType = $this->ct_types[$ct_field['referer_ct_type']];
					$refererCtField = $refererCtType['ct_type_field'];
					$refererTitleField = $refererCtField[$ct_field['referer_title_field']]['ct_field_name'];
					$tableName = $refererCtType['ct_type_name'];
					$args = array(	'fields'	=> array($tableName . '_id', $refererTitleField),
									'fieldKey'	=> $tableName . '_id'
								);
					$_options = $db->get($tableName, $args)->result;
					if( !$ct_field['require'] )
					{
						$ct_field['option'][] = array($tableName . '_id' => '', $refererTitleField => 'None');
						$ct_field['option'] = array_merge($ct_field['option'], $_options);
					}
					else $ct_field['option'] = $_options;
					$ct_field['field_type'] = $ct_field['referer_display'];
					$ct_field['option_value'] = $tableName . '_id';
					$ct_field['option_title'] = $refererTitleField;
				}
				if( $ct_field['ct_field_type'] == 'html' )
				{
					//$ct_field['default_value'] = html_entity_decode(stripslashes($ct_field['default_value']));
					//$template->body_data .= p($ct_field);
				}
				
				$form->addField($ct_field);
				$form->addTag('clear');
			}
		}

		$field_data = array(
							'field_name'	=> 'save_content',
							'default_value'	=> lang('Save'),
							'field_label'	=> '',
							'field_type'	=> 'submit',
							'class'			=> 'btn btn-primary',
							'id'			=> 'save_content',
							'attribute'		=> array()
							);
		$form->addField($field_data);
		$tpl->assign('FORM_DATA', $form->create());
		
		$setting['list_content'] = BASE_DIR . 'admin.php?module=ct_type&op=list_content&ct_type_id=' . $ct_type_id;
		$setting['add_content'] = BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id;
		$setting['profile_setting'] = BASE_DIR . 'admin.php?module=ct_type&op=content_setting&ct_type_id=' . $ct_type_id;
		$tpl->assign('SETTING', $setting );
		$tpl->assign('CONTENT_TYPE_NAME', $this->ct_types[$ct_type_id]['ct_type_name']);
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');
	}
	
	public function logout()
	{
		global $session;
		
		$session->end();
		header('LOCATION: ' . BASE_DIR . 'admin.php');
	}
}


?>