<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

unset($fieldContent['save_content']);
		
foreach( $fieldContent as $_key => $_fieldContent )
{
	if( is_array($_fieldContent) )
	{
		//$_fieldContent[] = 0;
		$_fieldContent = array_unique($_fieldContent);
		$fieldContent[$_key] = implode(',', $_fieldContent);
	}
}

if( $content_id > 0 )
{
	$fieldContent['edit_time'] = TIME;
	$db->where[$tableName . '_id'] = $content_id;
	$_updateTable = $db->update($tableName, $fieldContent);
	if( $_updateTable->status )
	{
		$template->body_data .= alert(lang('Success save'), 'success');
	}
	else $template->body_data .= alert(lang('Error save'), 'error');
}
else
{
	$fieldContent['add_time'] = TIME;
	$_insertFieldContent = $db->insert($tableName, $fieldContent);
	if($_insertFieldContent->insert_id > 0 )
	$template->body_data .= '<meta http-equiv="refresh" content="0;url=' . BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id . '&content_id=' . $_insertFieldContent->insert_id . '" />';
	else $template->body_data .= alert(lang('Error save'), 'error');
	//else $template->body_data .= p($db);
}

?>