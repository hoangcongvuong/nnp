<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$addField = ( $this->ct_types[$ct_type_id]['ct_type_field'] == '' ) ? true : false;
$fields = $r->post('field');
if( !empty($fields) && is_array($fields) )
{
	$i = 1;
	foreach($fields as $_field_id => $field)
	{
		if( in_array($field['ct_field_type'], array('radio', 'checkbox', 'select')) )
		{
			if( isset($field['option']) && is_array($field['option']) )
			{
				$_options = array();
				
				foreach( $field['option'] as $optKey => $__opt )
				{
					$_options[$__opt['value']] = $__opt['title'];
				}
				$field['option'] = $_options;
				unset($_options);
			}
		}
		
		$_field_data = $field;
		$_field_data['weight'] = $i;
		
		$_field = array(
						'ct_field_id'	=> $_field_id,
						'ct_field_name' => $field['ct_field_name'],
						'ct_field_label' => $field['ct_field_label'],
						'ct_field_type' => $field['ct_field_type'],
						'ct_field_data' => encodeArray($_field_data),
						);
		$db->where['ct_field_id'] = $_field_id;
		$_updateField = $db->update('ct_fields', $_field);
		if( !$_updateField->status ) $updateResult[] = 'Update fail: ' . $field['field_name'];
		$i++;
		$ct_type_field[$_field_id] = $_field;
	}
	if($addField) $this->addColumn($ct_type_id, $ct_type_field);
	else $this->rebuildTable($ct_type_id, $ct_type_field);
	$ct_fields = $this->get_ct_fields($ct_type_id);
	
	$_ct_type = array('ct_type_field' => encodeArray($ct_type_field));
	$db->where['ct_type_id'] = $r->post('ct_type_id');
	$db->update('ct_types', $_ct_type);
}
if( empty($updateResult) )
$template->body_data .= alert( 'Update successed!', 'success' );
else $template->body_data .= alert( implode('<br />', $updateResult), 'danger' );

?>