<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

//$ct_type_id = !empty($r->get('ct_type_id')) ? $r->get('ct_type_id') : $r->post('ct_type_id');
$ct_type_id = $r->get('ct_type_id');
if( empty($ct_type_id) ) $ct_type_id = $r->post('ct_type_id');
	
if(!isset($ct_type['ct_type_name']) || $ct_type['ct_type_name'] == '')
{
	$template->body_data .= alert(lang('Content type name cannot be empty!'), 'danger');
}
else
{
	unset($ct_type['ct_type_submit']);
	$ct_type_data = array(	'ct_type_name'	=> '',
							'ct_type_title'	=> '',
							'ct_type_data'	=> '',
							'ct_type_image'	=> ''
						);
	$ct_type_data = array_merge($ct_type_data, $ct_type);
	$ct_type_data['ct_type_name'] = alias($ct_type_data['ct_type_name'], '_');
	if( $ct_type_id > 0 )
	{
		$ct_type_data['ct_type_id'] = $ct_type_id;
		$db->where['ct_type_id'] = $ct_type_id;
		if( $db->update('ct_types', $ct_type_data) )
		$template->body_data .= alert(lang('Update success: ' . $ct_type['ct_type_name'] . '!'), 'success');
		else $template->body_data .= alert(lang('Update fail!'), 'error');
	}
	else
	{
		$tableName = alias($ct_type['ct_type_name'], '_');
		$sql_createTable = "
		CREATE TABLE IF NOT EXISTS `" . $db->prefix . $tableName . "` (
			`" . $tableName . "_id` INT(10) NOT NULL AUTO_INCREMENT,
			`add_time` mediumint(8) NOT NULL DEFAULT 0,
			`edit_time` mediumint(8) NOT NULL DEFAULT 0,
			`status` tinyint(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`" . $tableName . "_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		
		$stmt = $db->prepare($sql_createTable);
		$stmt->execute();
		$stmt->store_result();
		
		if( empty($stmt->error) )
		{
			if( $db->insert('ct_types', $ct_type_data) )
			$template->body_data .= alert(lang('Insert success: ' . $ct_type['ct_type_name'] . '!'), 'success');
			else $template->body_data .= alert(lang('Insert fail!'), 'error');
		}
		else $template->body_data .= alert($stmt->error, 'error');
	}
}

?>