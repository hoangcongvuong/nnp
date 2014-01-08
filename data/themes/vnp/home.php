<?php

if( !defined('VNP') ) die( 'Error!!!');


$tpl = $template->file('home.tpl');

/****************** Home slider ******************/
/*

$template->header_tag['link']['slide_css'] = '<link href="' . $theme['theme_dir'] . 'css/slide.css" rel="stylesheet" type="text/css" />
';
$template->jsHeader($theme['theme_dir'] . 'js/jquery.cycle.all.min.js', 'file');
//$template->jsHeader($theme['theme_dir'] . 'js/cherry-scripts.js', 'file');
$template->body_data .= '<script type="text/javascript" src="' . $theme['theme_dir'] . 'js/cherry-scripts.js"></script>
';

$ct_type_id = 7;
$group_id	= 1;
$num_rows	= 7;
$tableName	= $global['ct_type'][$ct_type_id]['ct_type_name'];

$get_args = array(	'fieldKey'	=> $tableName . '_id',
					'orderby'	=> $tableName . '_id',
					'fields'	=> 'tutorial_id,description,alias,image,title,category,add_time,author',
					'order'		=> 'DESC',
					'limit'		=> $num_rows
				);
$db->where['group'] = array('REGEXP' => $group_id);
$listNews = $db->get($tableName, $get_args);

if( !empty($listNews) )
foreach( $listNews as $_news )
{
	$_news['add_time'] = date('d/m/y',$_news['add_time']);
	$_news['thumb'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . $_news['image'] . '&h=78&w=78';
	$_news['link'] = content_url( $_news['alias'] . '-' . encrypt($ct_type_id, $_news['tutorial_id']) );
	$tpl->assign('TT', $_news);
	$tpl->parse('slider.loop');
	$tpl->parse('slider.thumb');
}

$tpl->parse('slider');
$template->body_data .= $tpl->text('slider');*/
/****************** Home slider ******************/


$ct_type_id = 7;
$num_rows	= 5;
$page = $r->get('page', 1) - 1;
$tableName	= $global['ct_types'][$ct_type_id]['ct_type_name'];

$get_args = array(	'fieldKey'	=> $tableName . '_id',
					'orderby'	=> $tableName . '_id',
					'fields'	=> 'tutorial_id,description,alias,image,title,category,add_time,author',
					'order'		=> 'DESC',
					'limit'		=> array($page*$num_rows,$num_rows),
					'paged'		=> true
				);
$listNews = $db->get($tableName, $get_args)->result;

if( !empty($listNews) )
foreach( $listNews as $_news )
{
	$_news['add_time'] = date('d/m/y',$_news['add_time']);
	$_news['link'] = content_url( $_news['alias'] . '-' . encrypt($ct_type_id, $_news['tutorial_id']) );
	$tpl->assign('NEWS', $_news);
	$tpl->parse('main.loop');
}


$paging = paging( content_url('', 'page'), $db->total_rows, $num_rows, $page*$num_rows, $config['rewrite']);
$tpl->assign('PAGING', $paging);
$tpl->parse('main');
$template->body_data .= $tpl->text('main');

?>