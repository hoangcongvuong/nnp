<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

if( $ct_type_id == 0 ) $ct_type_id = $r->get('ct_type_id');
$ctType = $this->ct_types[$ct_type_id];

if( $ct_type_id > 0 )
{
	$sortedArray = $ctType['ct_field_sort'];
	$contentFields = $ctType['ct_type_field'];
	$settings = $ctType['ct_type_setting'];
	$tableName = $ctType['ct_type_name'];
	
	$getFields = array();
	if( !empty($settings['show_fields']) )
	foreach( $settings['show_fields'] as $field_id )
	{
		$getFields[$field_id] = $contentFields[$field_id]['ct_field_name'];
	}
	//$getFields = sortArray($getFields, $sortedArray);
	$page = $r->get('page', 1) - 1;
	$getFields[] = $tableName . '_id';
	$args = array(	'fields'	=> $getFields,
					'limit'		=> array( $page*$settings['num_rows'], $settings['num_rows'] ),
					'order'		=> 'DESC',
					'paged'		=> true
				);
	$_ctList = $db->get($tableName, $args);
	$contentList = $_ctList->result;
	if( !empty($contentList) )
	$total_rows = $_ctList->total_rows;
	else $total_rows = 0;
	
	$tpl = $template->file('template/list_content.tpl');
	
	array_pop($getFields);
	
	$refererData = array();
	
	foreach( $contentFields as $field )
	{
		if( in_array($field['ct_field_name'], $getFields) )
		{
			if( $field['ct_field_type'] == 'referer')
			{
				$_fieldData = $field;
				
				$refererCtType = $this->ct_types[$_fieldData['referer_ct_type']];
				$refererCtFields = $refererCtType['ct_type_field'];
				$refererTitleField = $refererCtFields[$_fieldData['referer_title_field']]['ct_field_name'];
				$_tableName = $refererCtType['ct_type_name'];
				$args = array(	'fields'	=> array($_tableName . '_id', $refererTitleField),
								'fieldKey'	=> $_tableName . '_id'
							);
				$refererData[$field['ct_field_name']] = array( 'data' => $db->get($_tableName, $args)->result, 'title_field' => $refererTitleField );
			}					
			
			$tpl->assign('FIELD', $field);
			$tpl->parse('main.thead');
		}
	}
	
	if( !empty($contentList) )
	foreach( $contentList as $row )
	{
		//$tpl->assign('ROW', $row);
		foreach( $getFields as $fieldID => $fieldName )
		{
			if( in_array($contentFields[$fieldID]['ct_field_type'], array('radio', 'checkbox', 'select') ))
			{
				$fieldData = $contentFields[$fieldID]['ct_field_data'];
				$colData = $fieldData['option'][$row[$fieldName]];
			}
			elseif( $contentFields[$fieldID]['ct_field_type'] == 'referer' )
			{
				$_rfData = $refererData[$fieldName]['data'];
				$_title_field = $refererData[$fieldName]['title_field'];
				
				if( !empty($row[$fieldName]) )
				{
					$refIDs = explode(',', $row[$fieldName]);
					$_colData = array();
					foreach( $refIDs as $_rfid )
					{
						if( isset($_rfData[$_rfid][$_title_field]) ) $_colData[] = $_rfData[$_rfid][$_title_field];
					}
					$colData = implode(', ', $_colData);
				}
				else $colData = '';
			}
			elseif( $contentFields[$fieldID]['ct_field_type'] == 'image')
			{
				$colData = '<img src="' . $row[$fieldName] . '" height="50" width="50" />';
			}
			else $colData = $row[$fieldName];
			$tpl->assign('CONTENT_ID', $row[$tableName . '_id']);
			$tpl->assign('COLDATA', $colData);
			$tpl->parse('main.row.col');
		}
		
		$feature['edit_link'] = BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id . '&content_id=' . $row[$tableName . '_id'];
		
		$featureString = '
		<span class="ct_type_feature">
			<a class="vnp-edit-ct-type glyphicon glyphicon-pencil noajax" noajax="true" title="Edit content" href="%s"></a>
			<span class="vnp-remove-ct-type glyphicon glyphicon-remove" title="Remove content type" onclick="removeContent(' . $ct_type_id . ', ' . $row[$tableName . '_id'] . '); return false;"></span>
		</span>
		';
		
		$tpl->assign('FEATURE', sprintf($featureString, $feature['edit_link']) );
		$tpl->parse('main.row');
	}
	
	$setting['add_content'] = BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id;
	$setting['profile_setting'] = BASE_DIR . 'admin.php?module=ct_type&op=content_setting&ct_type_id=' . $ct_type_id;
	$tpl->assign('SETTING', $setting );
	
	if( $total_rows > 0 )
	{
		$base = BASE_DIR . 'admin.php?module=ct_type&op=list_content&ct_type_id=' . $ct_type_id;
		$paging = paging( $base, $total_rows, $settings['num_rows'], $page*$settings['num_rows']);
		$tpl->assign('PAGING', $paging);
	}
	
	$tpl->assign('CT_TYPE_ID', $ct_type_id);
	$tpl->parse('main');
	$template->body_data .= $tpl->text('main');
}

?>