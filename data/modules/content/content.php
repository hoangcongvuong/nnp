<?php

if( !defined('VNP') ) die( 'Error!!!');

if( !function_exists('content') )
{
	function content($contentInfo,$aliasString)
	{
		global $template, $db, $global, $theme;
		
		$ct_type_id = $contentInfo[0];
		$content_id = $contentInfo[1];
		
		$page_content = '';
		
		$ctType = get_ct_type();
		$ctType = $ctType[$ct_type_id];
		$ctType['setting'] = $ctType['ct_type_setting'];
		$tableName = $ctType['ct_type_name'];
		$db->where[$tableName . '_id'] = $content_id;
		
		$content = $db->get($tableName, $tableName . '_id')->result;
		$content = $content[$content_id];
		
		if( $aliasString == $content['alias'] )
		{
			if( IS_ADMIN )
			{
				include MODULE_PATH . 'editor/editor.php';
				editor::inlineEditor('#post-content');
			}
			
			$ctFields = $ctType['ct_type_field'];
			
			$template->header_tag['title'] = $content['meta_title'];
			$template->header_tag['meta']['og:title'] = '<meta property="og:title" content="' . $content['meta_title'] . '" />';
			$template->header_tag['meta']['description'] = '<meta property="description" content="' . $content['meta_description'] . '" />';
			$template->header_tag['meta']['og:description'] = '<meta property="og:description" content="' . $content['meta_description'] . '" />';
			
			if( $ctType['setting']['use_comment'] )
			{
				$template->currentBlockTemplatePath = MODULE_PATH . 'comment/';
				include MODULE_PATH . 'comment/comment.php';
				$cmt = new comment();
				$comment['form'] = $cmt->show_form($ct_type_id, $content_id);
				$comment['list'] = $cmt->list_cmt($ct_type_id, $content_id);
			}
			
			if( file_exists($theme['theme_root'] . $tableName . '_content.php') )
			{
				$template->currentBlockTemplatePath = $theme['theme_root'];
				include $theme['theme_root'] . $tableName . '_content.php';
			}
			else
			{
				$tpl = $template->file('template/content.tpl');
				foreach( $ctFields as $_field )
				{
					$tpl->assign('FIELD_DATA', $content[$_field['ct_field_name']]);
					$tpl->parse('main.field');
				}
				$tpl->parse('main');
				$page_content = $tpl->text('main');
			}
		}
		else $page_content = '<h1>404 NOT FOUND</h1>';
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
		return $page_content;
	}
}

$template->body_data .= content($contentInfo, $aliasString);

?>