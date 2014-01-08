<?php

class comment
{
	public function __construct()
	{
	}
	
	public function form()
	{
		global $template, $r, $global;
		
		$ct_type_id = $r->post('ct_type_id', 0);
		$content_id = $r->post('content_id', 0);
		if( $ct_type_id > 0 && $content_id > 0 )
		{
			if( isset($global['ct_type'][$ct_type_id]) )
			{
				$parent_id = $r->post('parent_id', 0);
				$template->body_data .= $this->show_form($ct_type_id, $content_id, $parent_id);
			}
		}
	}
	
	public function reply_form()
	{
		global $template, $r, $global;
		
		$ct_type_id = $r->post('ct_type_id', 0);
		$content_id = $r->post('content_id', 0);
		if( $ct_type_id > 0 && $content_id > 0 )
		{
			if( isset($global['ct_type'][$ct_type_id]) )
			{
				$parent_id = $r->post('parent_id', 0);
				$form = $this->reply($ct_type_id, $content_id, $parent_id);
				
				$template->body_data .= json_encode(array('status' => 'ok', 'content' => $form ) );
			}
			else $template->body_data .= json_encode(array('status' => 'not', 'content' => 'invalid action!' ) );
		}
		else $template->body_data .= json_encode(array('status' => 'not', 'content' => 'invalid action!' ) );
		echo $template->body_data;
		exit();
	}
	
	public function show_form($ct_type_id, $content_id, $parent_id = 0)
	{
		global $template, $r, $db;
		
		$tpl = $template->file('template/form.tpl');
		
		$template->header_tag['link']['sceditor'] = '<link rel="stylesheet" href="' . MODULE_DIR . 'editor/sceditor/minified/themes/default.min.css" type="text/css" media="all" />';
		$template->jsHeader(MODULE_DIR . 'editor/sceditor/minified/jquery.sceditor.bbcode.min.js', 'file');
		$jsString = '
		$("form#add_comment textarea").sceditor({
			emoticonsRoot: "' . MODULE_DIR . 'editor/sceditor/",
			plugins: \'bbcode\',
			style: "' . MODULE_DIR . 'editor/sceditor/minified/themes/default.min.css",
			toolbar: "bold,italic,underline|source,strike,subscript,superscript,left,center,right,justify|code,quote,link,unlink,emoticon,source,pastetext",
		});';
		$template->jsHeader($jsString);
		
		$cm = array('ct_type_id'	=> $ct_type_id,
					'content_id'	=> $content_id,
					'parent_id'		=> $parent_id
					);
		$tpl->assign('CM', $cm);
		$tpl->assign('ACTION', content_url('save', 'module', 'comment') );
		$tpl->assign('REPLY_FORM', content_url('reply_form', 'module', 'comment') );
		//$tpl->assign('ACTION', BASE_DIR . 'index.php?module=comment&op=save' );
		$tpl->parse('main');
		return $tpl->text('main');
	}
	
	public function reply($ct_type_id, $content_id, $parent_id = 0)
	{
		global $template, $r, $db;
		
		$tpl = $template->file('template/reply.tpl');
		
		$cm = array('ct_type_id'	=> $ct_type_id,
					'content_id'	=> $content_id,
					'parent_id'		=> $parent_id
					);
		$tpl->assign('CM', $cm);
		$tpl->assign('ACTION', content_url('save', 'module', 'comment') );
		$tpl->assign('REPLY_FORM', content_url('reply_form', 'module', 'comment') );
		$tpl->parse('main');
		return $tpl->text('main');
	}
	
	public function save()
	{
		global $r, $template, $db;
		
		$cm['ct_type_id'] = $r->post('ct_type_id', 0);
		$cm['content_id'] = $r->post('content_id', 0);
		$cm['parent_id'] = $r->post('parent_id', 0);
		
		if( $cm['ct_type_id'] > 0 && $cm['content_id'] > 0 )
		{
			$error = array();
			$cm['author_name'] = $r->post('author_name', '');
			$cm['author_email'] = $r->post('author_email', '');
			$cm['content'] = $r->post('content', '');
			if( $cm['author_name'] == '' || $cm['author_name'] == '' ) $error[] = 'Tên hoặc và email không thể để trống';
			if( $cm['content'] == '' ) $error[] = 'Nội dung bình luận không thể để trống';
			
			$cm['post_time'] = TIME;
			
			if( empty($error) )
			{
				if( $cm['parent_id'] == 0 )
				{
					$cm['id_string'] = str_pad($db->nextInsertID('comment'), 9, 0, STR_PAD_LEFT);
				}
				else
				{
					$db->where['comment_id'] = $cm['parent_id'];
					$parent_comment = $db->get('comment', 'comment_id')->result;
					$parent_comment = $parent_comment[$cm['parent_id']];
					$cm['id_string'] = $parent_comment['id_string'] . '.' . str_pad($db->nextInsertID('comment'), 9, 0, STR_PAD_LEFT);
				}
				$db->insert('comment', $cm);
				$template->body_data .= json_encode(array('status' => 'ok', 'content' => $this->get_list_comment($cm['ct_type_id'], $cm['content_id'] ) ) );
			}
			else $template->body_data .= json_encode(array('status' => 'not', 'content' => implode(PHP_EOL, $error)) );
		}
		echo $template->body_data;
		exit();
	}
	
	public function get_list_comment($ct_type_id, $content_id)
	{
		global $template, $db;
		
		$db->where['ct_type_id'] = $ct_type_id;
		$db->where['content_id'] = $content_id;
		
		$get_args = array(	'fieldKey'	=> 'comment_id',
							'orderby'	=> 'id_string',
							'order'		=> 'ASC'
						);
		$list_comment = $db->get('comment', $get_args)->result;
		
		$tpl = $template->file('template/list.tpl');
		
		if( !empty($list_comment) )
		foreach( $list_comment as $comment )
		{
			$level = explode('.', $comment['id_string']);
			$level = sizeof($level) - 1;
			
			if( $level > 0 )
			{
				$comment['padding_left'] = 'style="padding-left: ' . $level*50 . 'px"';
				$comment['sub_class'] = ' sub-comment';
			}
			else
			{
				$comment['padding_left'] = '';
				$comment['sub_class'] = '';
			}
			
			/*********** BB to Html ******************/
			//include MODULE_PATH . 'comment/bbcodetohtml/SBBCodeParser.php';
			//$parser = new \SBBCodeParser\Node_Container_Document();
			//$comment['content'] = $parser->parse($comment['content']);
			/*********** BB to Html ******************/
			
			$tpl->assign('CM', $comment);
			$tpl->parse('main.loop');
		}
		$tpl->parse('main');
		return $tpl->text();
	}
	
	public function list_cmt($ct_type_id, $content_id)
	{
		global $template;
		return '<div id="list-comment">' . $this->get_list_comment($ct_type_id, $content_id) . '</div>';
	}
	
	
	public function comment_management()
	{
		global $r, $template, $db, $global;
		
		$ct_type_id = $r->get('ct_type_id', 0);
		$page = $r->get('page', 1) - 1;
		$per_page = 10;
		
		$tpl = $template->file('template/management.tpl');
		
		$db->where['ct_type_id'] = $ct_type_id;
		$get_args = array(	'fieldKey' => 'comment_id',
							'paged'		=> true,
							'limit'		=> array( $page*$per_page, $per_page )
						);
		$_comment = $db->get('comment', $get_args);
		$comment_list = $_comment->result;
		
		if( !empty( $comment_list) )
		{
			foreach( $comment_list as $comment )
			{
				$tpl->assign('CM', $comment);
				$tpl->parse('main.loop');
			}
		}
		
		$setting['list_content'] = BASE_DIR . 'admin.php?module=ct_type&op=list_content&ct_type_id=' . $ct_type_id;
		$setting['add_content'] = BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id;
		$setting['profile_setting'] = BASE_DIR . 'admin.php?module=ct_type&op=content_setting&ct_type_id=' . $ct_type_id;
		$tpl->assign('SETTING', $setting );
		$tpl->assign('CONTENT_TYPE_NAME', $global['ct_type'][$ct_type_id]['ct_type_name']);
		
		$base = BASE_DIR . 'admin.php?module=comment&op=comment_management&ct_type_id=' . $ct_type_id;
		$paging = paging( $base, $_comment->total_rows, $per_page, $page*$per_page);
		$tpl->assign('PAGING', $paging);
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');
	}
	
	public function remove_comment()
	{
		global $db, $template, $r;
		
		$commentIDs = $r->post('ids', '');
		
		$db->where['comment_id'] = array('IN' => $commentIDs);
		if($db->delete('comment')->affected_rows > 0)
		{			
			$template->body_data = alert('Success delete comment', 'success');
		}
		else $template->body_data = alert('Cannot delete comment', 'error');
		
		$this->comment_management();
	}
}

?>