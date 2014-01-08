<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$ct_type_id = $r->post('ct_type_id');
$ct_type = $this->ct_types[$ct_type_id];

$db->where['ct_type_id'] = $ct_type_id;
if($db->delete('ct_types')->affected_rows > 0)
{
	$db->where['ct_type_id'] = $ct_type_id;
	$db->delete('ct_fields');
	
	$sql_dropTable = "DROP TABLE " . $db->prefix . alias($ct_type['ct_type_name']);
	$stmt = $db->prepare($sql_dropTable);
	$stmt->execute();
	$stmt->store_result();
	
	$template->body_data = alert('Success delete content type: ' . $ct_type['ct_type_name'], 'success');
}
else $template->body_data = alert('Cannot delete content type: ' . $ct_type['ct_type_name'], 'error');
$this->ct_types = $db->get('ct_types', 'ct_type_id');
$this->main();

?>