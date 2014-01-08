<?php


class setting
{
	protected $DefaultConfigFileds = array(
									'site_name',
									'site_image',
									'site_description',
									'rewrite',
									'default_theme',
									'default_layout',
									'default_editor',
									'default_admin_theme',
									'default_admin_layout'
								);	
	public function __construct()
	{
		//include MODULE_PATH . 'ct_type/form.php';
	}
	
	public function main()
	{
		global $template, $db, $r, $config;
		
		$args = array(	'action'	=> BASE_DIR . 'admin.php?module=setting',
						'method'	=> 'post'
					);
		$form = new form('setting', $args);
		
		$contentField = $r->post('contentField');
		
		if( isset($contentField['save_setting']))
		{	
			$__configValue = array();
			unset($contentField['save_setting']);
			$__configValue = $contentField;
			$db->where['config_name'] = 'config_values';
			$_updateConfigField = $db->update('global_config', array('config_value' => encodeArray($__configValue) ) );
			
			if( $_updateConfigField->status )
			{
				if( $_updateConfigField->affected_rows == 1 )
				{
					if( updateXmlConfigFile($__configValue) ) $msg = alert('Update successed!', 'success');
					else $msg = alert('Update failed!', 'error');
					$config = array_merge($config, getConfigValue());
				}
				else $msg = alert('Site config hadn\'t changed, nothing to update!', 'info');
			}
			else $msg = alert('Update failed!', 'error');
			$template->body_data .= $msg;
		}
		
		$db->where['config_name'] = 'config_fields';
		$configData			= $db->get('global_config','config_name');
		$configField		= decodeArray($configData->result['config_fields']['config_value']);
		
		foreach( $configField as $_configName => $_config )
		{
			$_config = array_merge($_config, decodeArray($_config['ct_field_data']) );
			$settingField = array(	'field_name'	=> $_config['ct_field_name'],
									'field_label'	=> $_config['ct_field_label'],
									'default_value'	=> isset($config[$_configName]) ? $config[$_configName] : '',
									'field_type'	=> $_config['ct_field_type']
								);
			if( isset($_config['option']) ) $settingField['option'] = $_config['option'];
			unset($_config);
			$form->addField($settingField);
			$form->addTag('clear');					
		}
		
		$submitField = array(	'field_type'	=> 'submit',
								'field_name'	=> 'save_setting',
								'default_value'	=> 'Save setting',
								'class'			=> 'btn btn-primary'
							);
		$form->addField($submitField);
		
		$template->body_data .= $form->box('Global Setting', $form->create() );
	}
	
	static function configFileToArray()
	{
	}
	
	public function add_config_field()
	{
		global $template, $global, $db, $r;
		
		if( $r->post('save_ct_type') == 1 )
		{
			$fields = $r->post('field');
			if( !empty($fields) && is_array($fields) )
			{
				$this->save_field($fields);
			}
		}
		
		//include MODULE_PATH . 'ct_type/ct_type.php';
		$template->currentBlockTemplatePath = MODULE_PATH . 'ct_type/';
		$ctType = new ct_type();
		
		$db->where['config_name'] = 'config_fields';
		$configFields = $db->get('global_config', 'config_name')->result['config_fields']['config_value'];
		$configFields = decodeArray($configFields);
		
		foreach( $configFields as $fieldKey => $_cfField )
		{
			$configFields[$fieldKey] = array_merge($configFields[$fieldKey], decodeArray($_cfField['ct_field_data']) );
		}
		
		$add_field_setting = array(
					'label'					=> 'Setting fields',
					'save_field_submit_url'	=> BASE_DIR . 'admin.php?module=setting&op=add_config_field&ajax=state-main',
					'add_field_post_url'	=> '/admin.php?ajax=string&module=setting&op=field_builder&field_type=',
					'sort_field_post_url'	=> '/admin.php?ajax=string&module=setting&op=sort_field&ct_type_id=',
					'remove_field_url'		=> '/admin.php?ajax=string&module=setting&op=remove_field',
					'form_action'			=> BASE_DIR . 'admin.php?module=setting&op=add_config_field'
				);
		$ctType->add_ct_field_template(NULL, $add_field_setting, $configFields);
		//n($template->body_data);
	}
	
	public function field_builder()
	{
		global $template, $r;
		
		$template->currentBlockTemplatePath = MODULE_PATH . 'ct_type/';
		$ctType = new ct_type();
		
		if( defined('IS_AJAX') )
		{
			$ct_type_id = round(microtime(true));
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
				
				$ct_field_id = round(microtime(true));
				$field_data['ct_field_id'] = $ct_field_id;
				$field_data['ct_field_name'] = $ct_field_id . '_' . $field_type;
				$field_data['ct_field_label'] = $ct_field_id . '_' . $field_type;
				$template->body_data = $ctType->field_template($field_data);
			}
			else $template->body_data = '';
		}
	}
	
	public function sort_field()
	{
		global $template, $r;
	}
	
	public function remove_field()
	{
		global $template, $r;
		
		$ct_field_id = $r->post('ct_field_id');
		
		if( !in_array($ct_field_id, $this->DefaultConfigFileds ))
		{
			$template->body_data = 'ok';
		}
		else $template->body_data = 'not';
	}
	
	protected function save_field($fields)
	{
		global $r, $template, $db;
		
		$detectFields = array();
		
		$i = 1;
		foreach($fields as $_field_id => $field)
		{
			if( in_array($field['ct_field_type'], array('radio', 'checkbox', 'select')) )
			{
				if( isset($field['option']) && is_array($field['option']) )
				{
					$_options = array();
					
					foreach( $field['option'] as $optKey => $__opt )
					{
						$_options[$__opt['value']] = $__opt['title'];
					}
					$field['option'] = $_options;
					unset($_options);
				}
			}
			
			$_field_data = $field;
			$_field_data['weight'] = $i;
			
			$_field = array(
							'ct_field_id'	=> $field['ct_field_name'],
							'ct_field_name' => $field['ct_field_name'],
							'ct_field_label' => $field['ct_field_label'],
							'ct_field_type' => $field['ct_field_type'],
							'ct_field_data' => encodeArray($_field_data),
							);
			$i++;
			$ct_type_field[$field['ct_field_name']] = $_field;
			
			$detectFields[] = $field['ct_field_name'];
		}
		
		$__ct_type_field = encodeArray($ct_type_field);
		
		
		$lostFields = array_diff( $this->DefaultConfigFileds, $detectFields );
		if( count($lostFields) == 0 )
		{	
			$db->where['config_name'] = 'config_fields';
			$configFields = array( 'config_value'	=> $__ct_type_field );
			$updateConfigFields = $db->update('global_config', $configFields);
			
			if( $updateConfigFields->status )
			{
				if( $updateConfigFields->affected_rows == 1 )
				{
					$msg = alert('Update successed!', 'success');
				}
				else $msg = alert('Site config fields hadn\'t changed, nothing to update!', 'info');
			}
			else $msg = alert('Update failed!', 'error');
			$template->body_data .= $msg;
		}
		else $template->body_data .= alert('Invalid action!', 'error');
	}
}

?>