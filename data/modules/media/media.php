<?php

class media
{
	public function __construct()
	{
	}
	
	public function main()
	{
		global $template, $db, $r;
		
		$page = $r->get('page',1) - 1;
		$per_page = 10;
		
		$args = array(	'fieldKey'	=> 'media_id',
						'limit'		=> array( $page*$per_page, $per_page ),
						'order'		=> 'DESC',
						'paged'		=> true
					);
		
		$_media = $db->get('media', $args);	
		$medias = $_media->result;
		
		$tpl = $template->file('template/media_list.tpl');
		
		if( !empty($medias) )
		foreach( $medias as $media )
		{
			$media['uploaded_time'] = date( 'd/m/Y', $media['uploaded_time'] );
			$media['link'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . UPLOAD_DIR . $media['media_path'] . $media['media_name'] . '&w=80&h=80';
				
			$media['feature'] = '
			<span class="ct_type_feature">
				<span class="vnp-remove-ct-type glyphicon glyphicon-remove" title="Remove content type" onclick="removeMedia(' . $media['media_id'] . '); return false;"></span>
			</span>
			';
			
			$tpl->assign('MEDIA', $media);
			$tpl->parse('main.loop');
		}
		$tpl->parse('main');
		
		$template->body_data .= box('List media', $tpl->text('main'));
		
		
		$base_url = BASE_DIR . 'admin.php?module=media';
		$template->body_data .= paging( $base_url, $_media->total_rows, $per_page, $page*$per_page );
	}
	
	public function upload()
	{
		global $template, $r;
		
		include MODULE_PATH . 'ct_type/form.php';
		
		$contentFields = $r->post('contentField');
		
		if( isset($contentFields['submit_upload']) )
		{
			$this->save_file();
		}
		
		$args = array(	'action'	=> BASE_DIR . 'admin.php?module=media&op=save_file',
						'method'	=> 'post',
						'enctype'	=> 'multipart/form-data'
					);
		$form = new form('vnp_upload', $args);
		//$form->setting['ajax'] = false;
		
		$fieldData = array(	'field_type'	=> 'file',
							'field_label'	=> 'Choose file',
							'field_name'	=> 'vnp_file[]',
							'field_id'		=> 'vnp_file',
							'multiple'		=> 'multiple'
						);
		$form->addField($fieldData);
		$fieldData = array(	'field_type'	=> 'button',
							'default_value'	=> 'Upload',
							'class'			=> 'btn btn-primary',
							'field_name'	=> 'submit_upload'
						);
		$form->addField($fieldData);
		$template->body_data .= $form->box('Upload', $form->create() . '<div id="files-list"></div>');
		$template->jsHeader(MODULE_DIR . 'media/js/upload.js', 'file');
	}
	
	public function save_file()
	{
		global $db;
		
		$data = $status = '';
		
		include MODULE_PATH . 'media/class.upload.php';
		
		$media = new upload($_FILES['vnp_file']);
		if($media->uploaded)
		{
			$media->process(UPLOAD_PATH);
			if($media->processed)
			{
				$mediaInfo = array(	'media_name'	=> $media->file_dst_name,
									'media_path'	=> '',
									'media_type'	=> $media->file_is_image ? 'image' : '',
									'media_mime'	=> $media->file_src_mime,
									'media_size'	=> $media->file_src_size,
									'media_ext'		=> $media->file_dst_name_ext,
									'uploaded_time'	=> TIME
								);
									
				$db->insert('media',$mediaInfo);
				$media->clean();
				$data = $mediaInfo;
				$status = 'ok';
				
				//$db->where['media_id'] = $db->insert_id;
				//$_mediaInfo = $db->get('media', 'media_id')->result;
				//$mediaInfo = $_mediaInfo[$db->insert_id];
				
				$mediaInfo['thumb'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . UPLOAD_DIR . $mediaInfo['media_path'] . $mediaInfo['media_name'] . '&w=80&h=80';
				$mediaInfo['image'] = UPLOAD_DIR . $mediaInfo['media_path'] . $mediaInfo['media_name'];
				
				echo json_encode($mediaInfo);
			}
			else
			{
				$data = $media->error;
				$status = 'not';
			}
		}
		else
		{
			$data = $media->error;
			$status = 'not';
		}
		
		exit();
	}
	
	public function editor_uploader()
	{
		global $template, $theme;
		
		$template->body_data .= '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/reset.css" type="text/css" media="all" />';
		$template->body_data .= '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/style.css" type="text/css" media="all" />';
		$template->body_data .= '<script type="text/javascript" src="' . STATIC_DIR . 'js/jquery-1.8.3.min.js"></script>';
		$template->body_data .= '<script type="text/javascript">var ajaxMarker = ' . json_encode($template->ajax_marker) . '
			var base = "' . DEFAULT_STATE . '"</script>';	
			
		$template->body_data .= '<script type="text/javascript"> var is_single_field = false;</script>';

		$template->body_data .= '<script type="text/javascript" src="' . MODULE_DIR . 'media/js/editor_upload.js"></script>';
		$template->body_data .= '<script type="text/javascript" src="' . MODULE_DIR . 'media/js/editor_medias.js"></script>';
		
		$tpl = $template->file('template/editor_uploader.tpl');
		
		$tpl->assign('LIBRARY_LINK', BASE_DIR . 'admin.php?module=media&op=editor_medias&ajax=string');
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');
	}
	
	public function remove_media()
	{
		global $db, $template, $r;
		
		$media_id = $r->post('media_id');
		
		$db->where['media_id'] = $media_id;
		$mediaFile = $db->get('media', 'media_id')->result;
		if( !empty($mediaFile) )
		{
			$mediaFile = $mediaFile[$media_id];
			$filePath = UPLOAD_PATH . $mediaFile['media_path'] . $mediaFile['media_name'];

			include MODULE_PATH . 'media/class.file.php';
			$file = new File();
			
			if( $file->delete($filePath) )
			{
				$db->where['media_id'] = $media_id;
				$db->delete('media');
			}
		}
		$this->main();
	}
	
	public function editor_medias()
	{
		global $template, $db, $r, $theme;
		
		$template->body_data .= '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/reset.css" type="text/css" media="all" />';
		$template->body_data .= '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/style.css" type="text/css" media="all" />';
		$template->body_data .= '<script type="text/javascript" src="' . STATIC_DIR . 'js/jquery-1.8.3.min.js"></script>';
		$template->body_data .= '<script type="text/javascript">var ajaxMarker = ' . json_encode($template->ajax_marker) . '
			var base = "' . DEFAULT_STATE . '"</script>';
		$template->body_data .= '<script type="text/javascript"> var is_single_field = false;</script>';
		$template->body_data .= '<script type="text/javascript" src="' . MODULE_DIR . 'media/js/editor_medias.js"></script>';
		
		$page = $r->get('page',1) - 1;
		$per_page = 24;
		
		$args = array(	'fieldKey'	=> 'media_id',
						'limit'		=> array( $page*$per_page, $per_page ),
						'order'		=> 'DESC',
						'paged'		=> true
					);
		$_media = $db->get('media', $args);		
		$medias = $_media->result;
		
		$tpl = $template->file('template/editor_media_list.tpl');
		
		if( !empty($medias) )
		foreach( $medias as $media )
		{
			$media['uploaded_time'] = date( 'd/m/Y', $media['uploaded_time'] );
			$media['link'] = UPLOAD_DIR . $media['media_path'] . $media['media_name'];
			$media['thumb'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . UPLOAD_DIR . $media['media_path'] . $media['media_name'] . '&w=80&h=80';
				
			$media['feature'] = '
			<span class="ct_type_feature">
				<span class="vnp-remove-ct-type glyphicon glyphicon-remove" title="Remove content type" onclick="removeMedia(' . $media['media_id'] . '); return false;"></span>
			</span>
			';
			
			$tpl->assign('MEDIA', $media);
			$tpl->parse('main.loop');
		}		
		$base_url = BASE_DIR . 'admin.php?module=media&op=editor_medias&ajax=string';
		$tpl->assign('PAGING', paging( $base_url, $_media->total_rows, $per_page, $page*$per_page ));
		
		$tpl->assign('UPLOAD_LINK', BASE_DIR . 'admin.php?module=media&op=editor_uploader&ajax=string');
		
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');
	}
	
	public function field_medias()
	{
		global $template, $r, $db, $theme;
		
		$field = $r->get('field');
		
		$template->body_data .= '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/reset.css" type="text/css" media="all" />';
		$template->body_data .= '<link rel="stylesheet" href="' . $theme['theme_dir'] . 'css/style.css" type="text/css" media="all" />';
		$template->body_data .= '<script type="text/javascript" src="' . STATIC_DIR . 'js/jquery-1.8.3.min.js"></script>';
		$template->body_data .= '<script type="text/javascript">var ajaxMarker = ' . json_encode($template->ajax_marker) . '
			var base = "' . DEFAULT_STATE . '"</script>';
		$template->body_data .= '<script type="text/javascript"> var is_single_field = true; field = "' . $field . '";</script>';
		$template->body_data .= '<script type="text/javascript" src="' . MODULE_DIR . 'media/js/editor_medias.js"></script>';
		
		$page = $r->get('page',1) - 1;
		$per_page = 24;
		
		$args = array(	'fieldKey'	=> 'media_id',
						'limit'		=> array( $page*$per_page, $per_page ),
						'order'		=> 'DESC',
						'paged'		=> true
					);
		$_media = $db->get('media', $args);
		$medias = $_media->result;
		
		$tpl = $template->file('template/editor_media_list.tpl');
		
		if( !empty($medias) )
		foreach( $medias as $media )
		{
			$media['uploaded_time'] = date( 'd/m/Y', $media['uploaded_time'] );
			$media['link'] = UPLOAD_DIR . $media['media_path'] . $media['media_name'];
			$media['thumb'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . UPLOAD_DIR . $media['media_path'] . $media['media_name'] . '&w=80&h=80';
				
			$media['feature'] = '
			<span class="ct_type_feature">
				<span class="vnp-remove-ct-type glyphicon glyphicon-remove" title="Remove content type" onclick="removeMedia(' . $media['media_id'] . '); return false;"></span>
			</span>
			';
			
			$tpl->assign('MEDIA', $media);
			$tpl->parse('main.loop');
		}		
		$base_url = BASE_DIR . 'admin.php?module=media&op=field_medias&ajax=string&field=' . $field;
		$tpl->assign('PAGING', paging( $base_url, $_media->total_rows, $per_page, $page*$per_page ));
		
		$tpl->assign('UPLOAD_LINK', BASE_DIR . 'admin.php?module=media&op=editor_uploader&ajax=string');
		
		$tpl->parse('main');
		$template->body_data .= $tpl->text('main');		
	}
	
	public function save_file_info()
	{
		global $r, $db;
		$file_info = array(	'media_name'	=> $r->post('file_name'),
							'media_title'	=> $r->post('file_title'),
							'media_alt'		=> $r->post('file_alt')
						);
		$file_id = $r->post('file_id');
		$db->where['media_id'] = $file_id;
		$mediaFile = $db->get('media', 'media_id')->result;
		if( !empty($mediaFile) )
		{
			$mediaFile = $mediaFile[$file_id];
			$filePath = UPLOAD_PATH . $mediaFile['media_path'] . $mediaFile['media_name'];

			if( $mediaFile['media_name'] != $file_info['file_name'] )
			{
				include MODULE_PATH . 'media/class.file.php';
				$file = new File();
				$file->rename(UPLOAD_PATH . $mediaFile['media_path'] . $mediaFile['media_name'], $file_info['media_name'] );
			}
			
			$db->where['media_id'] = $file_id;
			$db->update('media', $file_info);
		}
		//return json_encode($file_info);
	}
}



?>