<?php

if( !defined('VNP') ) die( 'Error!!!');

$tpl = $template->file('template/news_content.tpl');
$tpl->assign('NEWS', $content);
$tpl->parse('main');
$page_content = $tpl->text('main');

?>