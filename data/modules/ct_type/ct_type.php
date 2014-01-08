<?php

define('CT_TYPE_MODULE', true);

class ct_type
{
	public $ct_types = array();
	public $externalModuleData = array();
	
	public function __construct()
	{
		global $template, $db;
		
		//$this->ct_types = $db->get('ct_types', 'ct_type_id')->result;
		$this->ct_types = get_ct_type();
		/*$ct_fields = $db->get('ct_fields', 'ct_field_id')->result;
		foreach( $ct_fields as $_field )
		{
			$_u['ct_field_data'] = base64_decode($_field['ct_field_data']);
			$db->where['ct_type_id'] = $_field['ct_type_id'];
			$db->update('ct_fields', $_u);
		}*/
	}
	
	public function main()
	{
		global $template, $db;
		
		$template->header_tag['title'] = lang('List content type');
		
		$tpl = $template->file('template/list_ct_type.tpl');
		
		foreach( $this->ct_types as $_ct_type )
		{
			$_ct_type['list_content'] = BASE_DIR . 'admin.php?module=ct_type&op=list_content&ct_type_id=' . $_ct_type['ct_type_id'];
			$_ct_type['add_content'] = BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $_ct_type['ct_type_id'];
			$_ct_type['edit_ct_type'] = BASE_DIR . 'admin.php?module=ct_type&op=add_ct_type&ct_type_id=' . $_ct_type['ct_type_id'];
			$_ct_type['add_ct_field'] = BASE_DIR . 'admin.php?module=ct_type&op=add_ct_type_field&ct_type_id=' . $_ct_type['ct_type_id'];
			$_ct_type['content_setting'] = BASE_DIR . 'admin.php?module=ct_type&op=content_setting&ct_type_id=' . $_ct_type['ct_type_id'];
			$_ct_type['comment_management'] = BASE_DIR . 'admin.php?module=comment&op=comment_management&ct_type_id=' . $_ct_type['ct_type_id'];
			$tpl->assign('CT_TYPE', $_ct_type);
			$tpl->parse('main.loop');
		}
		
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');
	}
	
	public function add_ct_type()
	{
		global $template, $r;	
		require MODULE_PATH . 'ct_type/add_ct_type.php';
	}
	
	private function save_content_type($ct_type)
	{
		global $r, $db, $template;	
		require MODULE_PATH . 'ct_type/save_ct_type.php';
	}
	
	public function add_ct_type_field($_ctTypeID = -1)
	{
		global $r, $template, $db;
		require MODULE_PATH . 'ct_type/add_ct_type_field.php';
	}
	
	private function addColumn($ct_type_id, $ct_type_field = array())
	{
		global $db;
		require MODULE_PATH . 'ct_type/addColumn.php';
	}
	
	private function rebuildTable($ct_type_id, $newFields)
	{
		global $db, $template;
		require MODULE_PATH . 'ct_type/rebuildTable.php';
	}
	
	public function field_builder()
	{
		global $template, $r, $db;
		require MODULE_PATH . 'ct_type/field_builder.php';
	}
	
	public function field_template($fieldData)
	{
		global $template;
		$_fieldTemplate = '';
		require MODULE_PATH . 'ct_type/field_template.php';
		return $_fieldTemplate;
	}
	
	public function get_ct_fields($ct_type_id)
	{
		global $db, $template;
		require MODULE_PATH . 'ct_type/get_ct_fields.php';	
		return sortArray($ctTypeField, $sortedArray);
	}
	
	public function add_content()
	{
		global $template, $db, $r, $modules;
		require MODULE_PATH . 'ct_type/add_content.php';	
	}
	
	public function save_content($tableName, $ct_type_id, $fieldContent, $content_id)
	{
		global $db, $template;
		require MODULE_PATH . 'ct_type/save_content.php';
	}
	
	public function sort_field()
	{
		global $r, $template, $db;
		require MODULE_PATH . 'ct_type/sort_field.php';		
	}
	
	public function remove_field()
	{
		global $r, $template, $db;
		require MODULE_PATH . 'ct_type/remove_field.php';
	}
	
	public function remove_ct_type()
	{
		global $db, $template, $r;
		require MODULE_PATH . 'ct_type/remove_ct_type.php';
	}
	
	public function content_setting()
	{
		global $db, $template, $r;
		require MODULE_PATH . 'ct_type/content_setting.php';
	}
	
	public function list_content($ct_type_id = 0)
	{
		global $db, $r, $template;
		require MODULE_PATH . 'ct_type/list_content.php';
	}
	
	public function remove_content()
	{
		global $db, $template, $r;
		require MODULE_PATH . 'ct_type/remove_content.php';	
	}
	
	public function get_field_selectbox($ct_type_id = 0, $ct_field_id = 0, $return = false)
	{
		global $template, $r;
		require MODULE_PATH . 'ct_type/get_field_selectbox.php';
		if( $return ) return implode(PHP_EOL, $options);
		else $template->body_data .= implode(PHP_EOL, $options);		
	}
	
	public function add_taxonomy()
	{
		global $template, $r;
		require MODULE_PATH . 'ct_type/add_taxonomy.php';
	}
	
	public function ajax_update_content()
	{
		global $r, $db;
		require MODULE_PATH . 'ct_type/add_taxonomy.php';		
	}
	
	public function add_ct_field_submit($ct_type_id)
	{
		global $r, $db, $template;
		require MODULE_PATH . 'ct_type/add_ct_field_submit.php';		
	}
	
	public function add_ct_field_template($ct_type_id = NULL, $addFieldSetting = array(), $__fieldsData = array(), $__returnForm = false )
	{
		global $r, $db, $template, $config;
		
		$default_setting = array(
					'label'					=> 'Field types',
					'save_field_submit_url'	=> '',
					'add_field_post_url'	=> '/admin.php?ajax=string&module=ct_type&op=field_builder&field_type=',
					'sort_field_post_url'	=> '/admin.php?ajax=string&module=ct_type&op=sort_field&ct_type_id=',
					'remove_field_url'		=> '/admin.php?module=ct_type&op=remove_field&ajax=string',
					'form_action'			=> BASE_DIR . 'admin.php?module=ct_type&op=add_ct_type_field&ct_type_id=' . $ct_type_id
				);
		$addFieldSetting = array_merge($default_setting, $addFieldSetting);
		
		$_generatedForm = '';
		require MODULE_PATH . 'ct_type/add_ct_field_template.php';	
		
		if( $__returnForm ) return 	$_generatedForm;
		else $template->body_data .= $_generatedForm;
	}
}

?>