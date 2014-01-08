<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$ct_type_id = $r->post('ct_type_id');
$content_ids = $r->post('content_ids');
$ctType = $this->ct_types[$ct_type_id];

$tableName = $ctType['ct_type_name'];

$db->where[$tableName . '_id'] = array('IN' => $content_ids);
if($db->delete($tableName)->affected_rows > 0)
{
	$db->where = array('ct_type_id' => $ct_type_id);
	$db->where['content_id'] = array('IN' => $content_ids);
	$db->delete('comment');
	$template->body_data = alert('Success delete content', 'success');
	//$template->body_data .= '<meta http-equiv="refresh" content="0;url=' . BASE_DIR . 'admin.php?module=ct_type&op=add_content&ct_type_id=' . $ct_type_id . '&content_id=' . $db->insert_id . '" />';
}
else $template->body_data = alert('Cannot delete content', 'error');

$this->list_content($ct_type_id);

?>