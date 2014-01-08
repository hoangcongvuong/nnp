<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

$tpl = $template->file('template/field_template.tpl');

if( !empty($fieldData['ct_field_id']) )
{
	if( isset($fieldData['ct_field_name']) && $fieldData['ct_field_name'] != '' )
	$tpl->assign('LABEL', $fieldData['ct_field_name']);
	else $tpl->assign('LABEL', lang('Content type for ') . $fieldData['ct_field_type']);
	$tpl->assign('FIELD', $fieldData);
	
	if( in_array($fieldData['ct_field_type'], array('select', 'radio', 'checkbox')) )
	{
		$i = 1;		
		if( isset($fieldData['option']) && sizeof(array_keys($fieldData['option'])) > 0 )
		foreach( $fieldData['option'] as $optionKey => $option )
		{
			$_option['title'] = $option;
			$_option['value'] = $optionKey;
			$_option['key'] = $optionKey;
			$option = $_option;
			
			if( isset($fieldData['default_value']) && $fieldData['default_value'] == $optionKey )
			{
				$option['checked'] = 'checked="checked"';
			}
			else $option['checked'] = '';
			
			$tpl->assign('OPTION', $option);
			if( $i == 1 ) $tpl->assign('OPTION1', $option);
			else
			{
				$tpl->assign('OPTION', $option);
				$tpl->parse('main.option.other');
			}
			$i++;
		}
		$tpl->parse('main.option');
	}
	elseif( $fieldData['ct_field_type'] == 'referer')
	{
		foreach( $this->ct_types as $_ctType )
		{
			if( isset($fieldData['referer_ct_type']) && $fieldData['referer_ct_type'] == $_ctType['ct_type_id'] )
			{
				$_ctType['selected'] = 'selected="selected"';
				$refererTitleField = $this->get_field_selectbox($fieldData['referer_ct_type'], $fieldData['referer_title_field'], true);
				$tpl->assign('REF_TITLE_FIELD', $refererTitleField);
			}
			else
			{
				$_ctType['selected'] = '';
				//$refererTitleField = '';
				//$tpl->assign('REF_TITLE_FIELD', $refererTitleField);
			}
			
			$display['radio'] = $display['checkbox'] = $display['selectbox'] = $display['checklist'] = '';
			if( $fieldData['referer_display'] == 'radio' ) $display['radio'] = 'selected="selected"';
			if( $fieldData['referer_display'] == 'checkbox' ) $display['checkbox'] = 'selected="selected"';
			if( $fieldData['referer_display'] == 'select' ) $display['select'] = 'selected="selected"';
			if( $fieldData['referer_display'] == 'checklist' ) $display['checklist'] = 'selected="selected"';
			
			$tpl->assign('CTTYPE', $_ctType);
			$tpl->assign('DISPLAY', $display);
			$tpl->parse('main.referer.cttype');
		}
		$tpl->parse('main.referer');
	}
	else $tpl->parse('main.' . $fieldData['ct_field_type']);
}
else $tpl->parse('main.invalid');
$tpl->parse('main');

$_fieldTemplate = $tpl->text('main');

?>