<?php

if( !defined('VNP') ) die( 'Error!!!');

$tpl = $template->file('template/taxonomy_tutorial.tpl');

foreach( $items as $_tuts )
{
	$_tuts['link'] = content_url($_tuts['alias'] . '-' . encrypt($ct_type_id, $_tuts['tutorial_id']), 'post');
	
	$_tuts['image'] = BASE_DIR . DATA_DIR . '/timthumb.php?src=' . $_tuts['image'] . '&h=105&w=105';
	$tpl->assign('TUTS', $_tuts);
	$tpl->parse('main.loop');
}

$base = content_url($aliasString, 'page');
$paging = paging( $base, $db->total_rows, $num_rows, $page*$num_rows, $config['rewrite']);
$tpl->assign('TAXONOMY', $taxonomy);
$tpl->assign('PAGING', $paging);
$tpl->parse('main');
$page_content .= $tpl->text('main');

?>