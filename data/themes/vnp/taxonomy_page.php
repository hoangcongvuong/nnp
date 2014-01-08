<?php

$tpl = $template->file('template/page_content.tpl');

$tpl->assign('PAGE', $item);
$tpl->parse('main');
$page_content = $tpl->text('main');


?>