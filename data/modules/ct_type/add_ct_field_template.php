<?php

if( !defined('CT_TYPE_MODULE') ) die('Access denied!');

if( $addFieldSetting['save_field_submit_url'] == '' )
{
	$submit_url = "$('#form-field').attr('action') + '&ajax=state-main'";
}
else $submit_url = "'" . $addFieldSetting['save_field_submit_url'] . "'";

$template->jsHeader(STATIC_DIR . 'js/jquery-ui.js', 'file');
$template->cssHeader(MODULE_DIR . 'ct_type/template/css/form-builder.css', 'file');

$ajaxFormEnable = false;
if( defined('IS_ADMIN') && $config['admin_ajax'] ) $ajaxFormEnable = true;
elseif( !defined('IS_ADMIN') && $config['site_ajax'] ) $ajaxFormEnable = true;
if( $ajaxFormEnable )
{
	$template->jsHeader(STATIC_DIR . 'js/jquery.form.min.js', 'file');
	$jsContent = "var options = {beforeSubmit:showLoading,success:ajax_state_handler,dataType:  'json',url: " . $submit_url . "};$('#form-field').ajaxForm(options);";
	$template->jsHeader($jsContent);
}

$template->header_tag['script']['add-ct-field'] = "<script type=\"text/javascript\">var add_field_post_url = '" . $addFieldSetting['add_field_post_url'] . "'; var sort_field_post_url = '" . $addFieldSetting['sort_field_post_url'] . "'; var remove_field_url = '" . $addFieldSetting['remove_field_url'] . "';</script>";
$template->jsHeader(MODULE_DIR . 'ct_type/template/js/form-builder.js', 'file');
$tpl = $template->file('template/field_type.tpl');

//$ct_fields = decodeArray($ctType['ct_type_field']);
//$template->body_data .= p($ct_fields);

if( isset($ct_type_id) && $ct_type_id > 0 )
{
	$ct_fields = $this->get_ct_fields($ct_type_id);
}
elseif( $ct_type_id == NULL && !empty($__fieldsData) )
{
	$ct_fields = $__fieldsData;
}
if( !empty($ct_fields) )
{
	if( !empty($ct_fields) )
	{
		foreach( $ct_fields as $ct_field )
		{
			unset($ct_field['ct_field_data']);
			$tpl->assign('FIELD', $this->field_template($ct_field));
			$tpl->parse('main.ct_field');
		}
	}
	$tpl->assign('ACTION', $addFieldSetting['form_action']);
	$tpl->assign('CT_TYPE_ID', $ct_type_id);
}

$tpl->assign('LABEL', $addFieldSetting['label']);
$tpl->parse('main');

$_generatedForm .= $tpl->text('main');

?>