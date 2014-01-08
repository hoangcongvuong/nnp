<?php


class pages
{
	public $allowedPageTypes = array('dynamic', 'static');
	public function __construct()
	{
		//include MODULE_PATH . 'ct_type/form.php';
	}
	
	public function main()
	{
		global $template, $db, $r, $config;
		
		
		$template->body_data = 'ok';
	}
	
	public function add_page()
	{
		global $r, $db, $template;
		
		$tpl = $template->file('template/add_page.tpl');
		
		$page_type = $r->get('page_type');
		if( empty($page_type) )
		{
			$addTypeUrls = array(	'dynamic'	=>	$r->currentUrl . '&page_type=dynamic',
									'static'	=>	$r->currentUrl . '&page_type=static'
								);
			$tpl->assign('PAGE_TYPE', $addTypeUrls );
			$tpl->parse('main.choose_page_type');
		}
		else
		{
			$tpl->assign('PAGE_TEMPLATE', $this->add_page_template($page_type) );
			$tpl->parse('main.page_template');
		}
		
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');
	}
	
	public function edit_page()
	{
		$page_id = $this->is_edit_page();
	}
	
	private function add_page_template($page_type)
	{
		global $template;
		
		$template->cssHeader(MODULE_DIR . 'ct_type/css/modal.css', 'file');
		$template->jsHeader(MODULE_DIR . 'ct_type/js/modal.js', 'file');
		if( $page_type == 'static' ) return $this->staticPageTemplate();
		elseif( $page_type == 'dynamic' ) return $this->dynamicPageTemplate();
	}
	
	private function dynamicPageTemplate()
	{
		global $template;		
		
		$template->currentBlockTemplatePath = MODULE_PATH . 'ct_type/';
		$ctType = new ct_type();
		
		/*$db->where['config_name'] = 'config_fields';
		$configFields = $db->get('global_config', 'config_name')->result['config_fields']['config_value'];
		$configFields = decodeArray($configFields);
		
		foreach( $configFields as $fieldKey => $_cfField )
		{
			$configFields[$fieldKey] = array_merge($configFields[$fieldKey], decodeArray($_cfField['ct_field_data']) );
		}*/
		
		$configFields = array();
		
		$add_field_setting = array(
					'label'					=> 'Page fields',
					'save_field_submit_url'	=> BASE_DIR . 'admin.php?module=setting&op=add_config_field&ajax=state-main',
					'add_field_post_url'	=> '/admin.php?ajax=string&module=setting&op=field_builder&field_type=',
					'sort_field_post_url'	=> '/admin.php?ajax=string&module=setting&op=sort_field&ct_type_id=',
					'remove_field_url'		=> '/admin.php?ajax=string&module=setting&op=remove_field',
					'form_action'			=> BASE_DIR . 'admin.php?module=setting&op=add_config_field'
				);
		$_addPageFields = $ctType->add_ct_field_template(NULL, $add_field_setting, $configFields, true);
		
		$template->currentBlockTemplatePath = MODULE_PATH . 'pages/';
		$tpl = $template->file('template/dynamic_page.tpl');
		$tpl->assign('ADD_PAGE_FIELD', $_addPageFields);
		$tpl->parse('main');
		return $tpl->text('main');
	}
	
	private function is_edit_page()
	{
		global $r;
		
		$pageID = $r->get('page_id', 0);
		if( $pageID == 0 ) $pageID = $r->post('page_id', 0);
		return $pageID;
	}
}

?>