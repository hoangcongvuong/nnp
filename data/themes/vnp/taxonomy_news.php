<?php

if( !defined('VNP') ) die( 'Error!!!');

$tpl = $template->file('template/taxonomy_news.tpl');

foreach( $items as $_news )
{
	$_news['link'] = content_url($_news['alias'] . '-' . encrypt($ct_type_id, $_news['news_id']), 'post');
	$tpl->assign('NEWS', $_news);
	$tpl->parse('main.loop');
}

$base = content_url($aliasString, 'page');
$paging = paging( $base, $db->total_rows, $num_rows, $page*$num_rows, $config['rewrite']);
$tpl->assign('TAXONOMY', $taxonomy);
$tpl->assign('PAGING', $paging);
$tpl->parse('main');
$page_content .= $tpl->text('main');

?>