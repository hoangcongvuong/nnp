<?php

if( !defined('VNP') ) die( 'Error!!!');


if( !function_exists('block_content') )
{
	function block_content($blockData)
	{
		global $template, $db, $global;
		
		$a = array( 'ct_type_id' => 7,
					'groud_id'	=> 2
					);
		
		$tpl = $template->file('template/block_content.tpl');
		
		$ct_type_id = $blockData['ct_type_id'];
		$group_id	= $blockData['group_id'];
		$num_rows	= 10;
		$tableName	= $global['ct_types'][$ct_type_id]['ct_type_name'];
		
		$get_args = array(	'fieldKey'	=> $tableName . '_id',
							'orderby'	=> $tableName . '_id',
							'fields'	=> 'tutorial_id,description,alias,image,title,category,add_time,author',
							'order'		=> 'DESC',
							'limit'		=> $num_rows
						);
		$db->where['group'] = array('REGEXP' => $group_id);
		$listNews = $db->get($tableName, $get_args)->result;
		
		if( !empty($listNews) )
		foreach( $listNews as $_news )
		{
			$_news['add_time'] = date('d/m/y',$_news['add_time']);
			$_news['image'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . $_news['image'] . '&h=60&w=60';
			$_news['link'] = content_url( $_news['alias'] . '-' . encrypt($ct_type_id, $_news['tutorial_id']) );
			$tpl->assign('TT', $_news);
			$tpl->parse('main.loop');
		}
		
		$tpl->parse('main');
		return $tpl->text('main');
	}
}

$blockContent = block_content($blockData);

?>