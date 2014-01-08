<?php

if( !defined('VNP') ) die( 'Error!!!');

if( !function_exists('taxonomy') )
{
	function taxonomy($aliasString)
	{
		global $global, $db, $template, $r, $config, $theme;
		
		$page_content = '';
		$num_rows	= 5;
		$page = $r->get('page', 1) - 1;
		
		$taxonomy = $global['taxonomy'][$aliasString];
		
		$template->header_tag['title'] = $taxonomy['meta_title'];
		$template->header_tag['meta']['og:title'] = '<meta property="og:title" content="' . $taxonomy['meta_title'] . '" />';
		$template->header_tag['meta']['description'] = '<meta property="og:description" content="' . $taxonomy['meta_description'] . '" />';
		$template->header_tag['meta']['og:description'] = '<meta property="og:description" content="' . $taxonomy['meta_description'] . '" />';
		
		$referedContentTypeIDs = $taxonomy['refered_ct_type'];
		if( !empty($referedContentTypeIDs) )
		$ctTypeIDs = explode(',', $referedContentTypeIDs);
		else $ctTypeIDs = '';
		
		
		$tpl = $template->file('template/taxonomy.tpl');
		$content = array();
		
		if( !empty($ctTypeIDs) )
		foreach( $ctTypeIDs as $ct_type_id )
		{
			$ctType = $global['ct_types'][$ct_type_id];
			$tableName = $ctType['ct_type_name'];
			
			$get_args = array(	'fieldKey'	=> $tableName . '_id',
								'orderby'	=> $tableName . '_id',
								'order'		=> 'DESC',
								'limit'		=> array($page*$num_rows,$num_rows),
								'paged'		=> true
							);
			$db->where[$taxonomy['refered_field']] = array('REGEXP' => $taxonomy['taxonomy_value']);
			$items = $db->get($tableName, $get_args)->result;
			
			if( !empty($items) )
			{
				if( file_exists($theme['theme_root'] . 'taxonomy_' . $ctType['ct_type_name'] . '.php') )
				{
					$template->currentBlockTemplatePath = $theme['theme_root'];
					include $theme['theme_root'] . 'taxonomy_' . $ctType['ct_type_name'] . '.php';
				}
			}
			else $page_content = '<h1>No post!</h1>';
		}
		else
		{
			$ct_type_id = $taxonomy['ct_type_id'];
			$content_id = $taxonomy['taxonomy_value'];
				
			$ctType = get_ct_type();
			$ctType = $ctType[$ct_type_id];
			$ctType['setting'] = $ctType['ct_type_setting'];
			
			if( IS_ADMIN )
			{
				include MODULE_PATH . 'editor/editor.php';
				editor::inlineEditor('#post-content');
			}
			
			if( $ctType['setting']['use_comment'] )
			{
				$template->currentBlockTemplatePath = MODULE_PATH . 'comment/';
				include MODULE_PATH . 'comment/comment.php';
				$cmt = new comment();
				$comment['form'] = $cmt->show_form($ct_type_id, $content_id);
				$comment['list'] = $cmt->list_cmt($ct_type_id, $content_id);
			}
			
			
			
			
			$tableName = $ctType['ct_type_name'];
			
			$get_args = array(	'fieldKey'	=> $tableName . '_id',
								'orderby'	=> $tableName . '_id',
								'order'		=> 'DESC',
								'limit'		=> array($page*$num_rows,$num_rows),
								'paged'		=> true
							);
			$db->where[$taxonomy['refered_field']] = $taxonomy['taxonomy_value'];
			$items = $db->get($tableName, $get_args)->result;

			if( !empty($items) )
			{
				$item = $items[$taxonomy['taxonomy_value']];
				if( file_exists($theme['theme_root'] . 'taxonomy_' . $ctType['ct_type_name'] . '.php') )
				{
					$template->currentBlockTemplatePath = $theme['theme_root'];
					include $theme['theme_root'] . 'taxonomy_' . $ctType['ct_type_name'] . '.php';
				}
				
				
				if( $ctType['setting']['use_comment'] && !empty($comment) )
				{
					$cm = '
					<div class="content">
						<div class="news-content">' . 
						$comment['form'] . $comment['list'] . 
						'</div>
					</div>';
					$page_content .= $cm;
				}
			}
			else $page_content = '<h1>No post!</h1>';
		}
		
		return $page_content;
	}
}
$template->body_data .= taxonomy($aliasString);

?>