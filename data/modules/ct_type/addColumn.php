<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$acceptedCtFields = $ct_type_field;
		
$addFields = array();
foreach( $acceptedCtFields as $fieldID => $fieldData)
{
	$_tempField = decodeArray($fieldData['ct_field_data']);
	$addFields[] = $_tempField;
}	
/*** Add new field ***/
if( !empty($addFields) )
{
	$tableName = alias($this->ct_types[$ct_type_id]['ct_type_name'], '_');
	//$db->addColumn($tableName, $addFields);
}

?>