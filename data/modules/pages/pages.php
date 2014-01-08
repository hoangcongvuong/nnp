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
			$addTypeUrls = array(	'dynamic'	=>	$r->currentUrl . '&page_type=dynamic&content=single',
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
		
		//$template->cssHeader(MODULE_DIR . 'ct_type/css/modal.css', 'file');
		//$template->jsHeader(MODULE_DIR . 'ct_type/js/modal.js', 'file');
		if( $page_type == 'static' ) return $this->staticPageTemplate();
		elseif( $page_type == 'dynamic' ) return $this->dynamicPageTemplate();
	}
	
	private function dynamicPageTemplate()
	{
		global $template, $global, $r;
		
		/*$db->where['config_name'] = 'config_fields';
		$configFields = $db->get('global_config', 'config_name')->result['config_fields']['config_value'];
		$configFields = decodeArray($configFields);
		
		foreach( $configFields as $fieldKey => $_cfField )
		{
			$configFields[$fieldKey] = array_merge($configFields[$fieldKey], decodeArray($_cfField['ct_field_data']) );
		}*/
		
		$template->jsHeader(MODULE_DIR . 'pages/template/js/page_content.js', 'file');
		
		$currentPageContentType = $r->get('content');
		
		if( $currentPageContentType == 'single' )
		{
			$_pageAttrs = array(	'label'	=> 'Single content page',
									'class'	=> array('single' => 'btn-danger', 'list' => '' )
									);
		}
		else
		{
			$_pageAttrs = array(	'label'	=> 'List content page',
									'class'	=> array('single' => '', 'list' => 'btn-danger' )
									);
		}
		
		$tpl = $template->file('template/dynamic_page.tpl');
		
		foreach( $global['ct_types'] as  $_ct_Type )
		{
			$tpl->assign('CT_TYPE', $_ct_Type);
			$tpl->parse('main.ct_type');
		}
		
		$pageContentType = 
			array(	'single'	=>	BASE_DIR . 'admin.php?module=pages&op=add_page&page_type=dynamic&content=single',
					'list'		=>	BASE_DIR . 'admin.php?module=pages&op=add_page&page_type=dynamic&content=list'
				);
								
		$tpl->assign('PAGE_LINK', $pageContentType);
		$tpl->assign('PAGE_ATTRIBUTE', $_pageAttrs);
		
		$tpl->parse('main');
		return $tpl->text('main');
	}
	
	public function get_content_type_field()
	{
		global $r, $db, $global;
		
		$content = $r->get('content', 'single');
		$ct_type_id = $r->get('ct_type_id', 0);
		
		if( array_key_exists($ct_type_id, $global['ct_types']) )
		{
			$db->where['ct_field_id'] = $ct_type_id;
			$ctFields = $db->get('ct_fields', 'ct_field_id' )->result;
			$return = array( 'status' => 'ok', 'fields' => $ctFields );
		}
		else $return = array( 'status' => 'not', 'fields' => 'Invalid content type' );
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