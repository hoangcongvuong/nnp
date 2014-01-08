<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$db->where['ct_type_id'] = $ct_type_id;
$ctType = $db->get('ct_types', 'ct_type_id')->result;
//$ctType = $this->ct_types[$ct_type_id];
$ctType = $ctType[$ct_type_id];
$ctType['ct_type_field'] = decodeArray($ctType['ct_type_field']);
$ctType['ct_field_sort'] = decodeArray($ctType['ct_field_sort']);

$ctTypeField = $ctType['ct_type_field'];
$sortedArray = $ctType['ct_field_sort'];

//$ct_fields = $db->get('ct_fields', 'ct_field_id')->result;


?>