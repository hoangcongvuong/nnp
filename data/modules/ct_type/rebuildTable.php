<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$ctType = $this->ct_types[$ct_type_id];
$currentFields = $ctType['ct_type_field'];

$db->where['ct_type_id'] = $ct_type_id;
$ct_fields = $db->get('ct_fields', 'ct_field_id')->result;

		
$removeFieldIDs = array_diff(array_keys($ct_fields), array_keys($newFields));
$dropFieldIDs = array_diff(array_keys($currentFields), array_keys($newFields));
$addFieldIDs = array_diff(array_keys($newFields), array_keys($currentFields));
$insertFieldIDs = array_diff(array_keys($newFields), array_keys($ct_fields));

$removeFields = array();
$dropFields = array();
$addFields = array();
$insertFields = array();

foreach( $removeFieldIDs as $fieldID )
{
	$removeFields[] = $ct_fields[$fieldID]['ct_field_name'];
}
foreach( $dropFieldIDs as $fieldID )
{
	$dropFields[] = $currentFields[$fieldID]['ct_field_name'];
}	
foreach( $addFieldIDs as $fieldID )
{
	$_tempField = decodeArray($newFields[$fieldID]['ct_field_data']);
	$addFields[] = $_tempField;
}
foreach( $insertFieldIDs as $fieldID )
{
	$_tempField = $newFields[$fieldID];
	$_tempField['ct_type_id'] = $ct_type_id;
	$insertFields[] = $_tempField;
}

//$template->body_data .= p(array_keys($currentFields));

/*** Delete removed field ***/
if( !empty($removeFields) )
{
	$db->where['ct_field_id'] = array('IN' => $removeFieldIDs );
	$db->delete('ct_fields');
}
if( !empty($dropFields) )
{
	$db->dropColumn(alias($this->ct_types[$ct_type_id]['ct_type_name'], '_'), $dropFields);
}
/*** Delete removed field ***/

/*** Add new field ***/
if( !empty($addFields) )
{
	$db->addColumn(alias($this->ct_types[$ct_type_id]['ct_type_name'], '_'), $addFields);
}
if( !empty($insertFields) )
{
	foreach($insertFields as $field_data)
	{
		$db->insert('ct_fields', $field_data);
	}
}

?>