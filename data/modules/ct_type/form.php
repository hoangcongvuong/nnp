<?php

class form
{
	
	public $onlyField = false;
	
	public $setting = array(
							'break_field'	=> '',
							'fieldTemplate'	=> '<div class="vnp-field" id="%s">%s</div>',
							'ajax'			=> true
							);
							
	public $formName = '';
	private $fieldNumber = 0;
	
	private $fielData = array();
	
	private $returnedForm = array();
	
	public $labelTemplate	= '<label class="vnp-label" for="%s">%s</label>';
	public $inputTemplate	= '<input class="vnp-input %s" type="%s" name="contentField[%s]" id="%s" value="%s" %s %s />';
	public $fileTemplate	= '<input class="vnp-input %s" type="%s" name="%s" id="%s" value="%s" %s %s %s />';
	public $imageTemplate	= '<input class="vnp-input %s" type="text" name="contentField[%s]" id="%s" value="%s" %s />
								<button data-toggle="modal" data-target="#myModal" id="browse-button-%s" class="btn btn-primary browse-file" refererfield="%s">Browse file</button>
								<div id="preview-%s" class="preview-image"></div>';
	public $textareaTemplate = '<textarea class="vnp-input %s" name="contentField[%s]" id="%s" %s %s>%s</textarea>';
	public $selectTemplate	= '<select class="vnp-input %s" name="contentField[%s]" id="%s" %s %s>%s</select>';
	
	public $formAttributes = array();
	
	public function __construct($formName = 'vnp-form',$formAttributes = array() )
	{
		$this->formName = $formName;
		$this->formAttributes = $formAttributes;
	}
	
	public function fieldBuilder($fieldData = array())
	{
		global $template;
		
		$field_data = array(
							'field_name'	=> '',
							'default_value'	=> '',
							'field_label'	=> '',
							'field_type'	=> 'text',
							'class'			=> '',
							'id'			=> '',
							'disabled'		=> '',
							'attribute'		=> array(),
							'option_value'	=> '',
							'option_title'	=> ''
							);
		
		$fieldData = array_merge($field_data, $fieldData);
							
		
		$this->fieldNumber++;
		
		$formAttribute = '';
		if( !empty($fieldData['attribute']) && is_string($fieldData['attribute']) ) $formAttribute = $fieldData['attribute'];
		elseif( is_array($fieldData['attribute']) ) $formAttribute = implode(' ', $fieldData['attribute']);
		
		$fieldID = $this->formName . '-field-' . $this->fieldNumber;
		
		$inputFields = array('text', 'email', 'number', 'tel', 'url');
		if( in_array($fieldData['field_type'], $inputFields) )
		{		
			$_ip = sprintf(	$this->labelTemplate . $this->inputTemplate, 
							$fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_type'],
							$fieldData['field_name'],
							'field-' . $fieldData['field_name'],
							$fieldData['default_value'],
							$fieldData['disabled'],
							$formAttribute
						);
		}
		elseif( $fieldData['field_type'] == 'password' )
		{		
			$_ip = sprintf(	$this->labelTemplate . $this->inputTemplate, 
							$fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_type'],
							$fieldData['field_name'],
							'field-' . $fieldData['field_name'],
							$fieldData['default_value'],
							$fieldData['disabled'],
							$formAttribute
						);
			$_ip .= '<div style="clear: both"></div><br />';
			$_ip .= sprintf(	$this->labelTemplate . $this->inputTemplate, 
							$fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_type'],
							're_' . $fieldData['field_name'],
							'field-re_' . $fieldData['field_name'],
							$fieldData['default_value'],
							$fieldData['disabled'],
							$formAttribute
						);
		}
		elseif( $fieldData['field_type'] == 'file' )
		{		
			$_ip = sprintf(	$this->labelTemplate . $this->fileTemplate, 
							$fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_type'],
							$fieldData['field_name'],
							$fieldData['field_id'],
							$fieldData['default_value'],
							$fieldData['disabled'],
							$fieldData['multiple'],
							$formAttribute
						);
		}
		elseif( $fieldData['field_type'] == 'image' )
		{
			$template->cssHeader(MODULE_DIR . 'ct_type/css/modal.css', 'file');
			$template->jsHeader(MODULE_DIR . 'ct_type/js/modal.js', 'file');
			$template->jsHeader(MODULE_DIR . 'ct_type/js/form_data.js', 'file');
			
			$previewImg = empty($fieldData['default_value']) ? '' : '<img src="' . BASE_DIR . DATA_DIR . '/timthumb.php?src=' . $fieldData['default_value'] . '&w=80&h=80" height="80" width="80" />';
			$this->imageTemplate	= 
			'<input class="vnp-input %s" type="text" name="contentField[%s]" id="%s" value="%s" %s />
			<button data-toggle="modal" data-backdrop="static" data-target="#myModal" id="browse-button-%s" class="btn btn-primary browse-file" refererfield="%s">Browse file</button>
			<div id="preview-%s" class="preview-image">' . $previewImg . '</div>';
				
			$modal = '
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content" style="width:610px">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title">VNP Media management</h4>
						</div>
						<div class="modal-body">
							<iframe src="' . BASE_DIR . 'admin.php?module=media&op=field_medias&field=' . $fieldData['field_name'] . '&ajax=string" width="608px" height="498px"></iframe>
						</div>
					</div>
				</div>
			</div>';
								
			$_ip = sprintf(	$this->labelTemplate . $this->imageTemplate, 
							$fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_name'],
							$fieldData['field_name'],
							$fieldData['default_value'],
							$formAttribute,
							$fieldData['field_name'],
							$fieldData['field_name'],
							$fieldData['field_name']
						);
			$_ip .= $modal;
		}
		elseif(in_array($fieldData['field_type'], array('button', 'submit', 'hidden')))
		{
			$_ip = sprintf(	$this->inputTemplate,
							$fieldData['class'],
							$fieldData['field_type'],
							$fieldData['field_name'],
							'field-' . $fieldData['field_name'],
							$fieldData['default_value'],
							$fieldData['disabled'],
							$formAttribute
						);
		}
		elseif(in_array($fieldData['field_type'], array('textarea')))
		{
			$_ip = sprintf(	$this->labelTemplate . $this->textareaTemplate,
							'field-' . $fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_name'],
							'field-' . $fieldData['field_name'],
							$formAttribute,
							$fieldData['disabled'],
							$fieldData['default_value']
						);
		}
		elseif($fieldData['field_type'] == 'html' )
		{
			include MODULE_PATH . 'editor/editor.php';
			$_ip = sprintf(	$this->labelTemplate,
							'field-' . $fieldData['field_name'],
							$fieldData['field_label']
							);
			$_ip .= '<div style="clear:both"></div>';
			$_ip .= sprintf($this->textareaTemplate,
							$fieldData['class'],
							$fieldData['field_name'],
							'field-' . $fieldData['field_name'],
							$formAttribute,
							$fieldData['disabled'],
							$fieldData['default_value']
						);
			editor::replace('#field-' . $fieldData['field_name']);
		}
		elseif(in_array($fieldData['field_type'], array('select')))
		{
			$option = array();
			if( isset($fieldData['option']) && is_array($fieldData['option']) )
			{
				foreach( $fieldData['option'] as $optionKey => $_option )
				{
					if( !empty($fieldData['option_title']) && !empty($fieldData['option_value']) )
					{
						$__option['value'] = $_option[$fieldData['option_value']];
						$__option['title'] = $_option[$fieldData['option_title']];
					}
					else
					{
						$__option['value'] = $optionKey;
						$__option['title'] = $_option;
					}
					$_option = $__option;
					( $_option['value'] == $fieldData['default_value'] ) ? $selected = 'selected="selected"' : $selected = '';
					$option[] = '<option value="' . $_option['value'] . '" ' . $selected . '>' . $_option['title'] . '</option>';
				}
			}
			
			$_ip = sprintf(	$this->labelTemplate . $this->selectTemplate,
							'field-' . $fieldData['field_name'],
							$fieldData['field_label'],
							$fieldData['class'],
							$fieldData['field_name'],
							'field-' . $fieldData['field_name'],
							$fieldData['disabled'],
							$formAttribute,
							implode(PHP_EOL, $option)
						);
		}
		elseif(in_array($fieldData['field_type'], array('radio')))
		{
			$option = array();
			if( isset($fieldData['option']) && is_array($fieldData['option']) )
			{
				foreach( $fieldData['option'] as $optionKey => $_option )
				{
					if( !empty($fieldData['option_title']) && !empty($fieldData['option_value']) )
					{
						$__option['value'] = $_option[$fieldData['option_value']];
						$__option['title'] = $_option[$fieldData['option_title']];
					}
					else
					{
						$__option['value'] = $optionKey;
						$__option['title'] = $_option;
					}
					$_option = $__option;
					( $_option['value'] == $fieldData['default_value'] ) ? $checked = 'checked="checked"' : $checked = '';
					
					$option[] = '
						<label> 
						<input name="contentField[' . $fieldData['field_name'] . ']" type="' . $fieldData['field_type'] . '" value="' . $_option['value'] . '" ' . $checked . '>
						&nbsp;' . $_option['title'] . '&nbsp;&nbsp;&nbsp;&nbsp;
						</label>';

				}
			}
			
			$_ip = '
				<label class="vnp-label">' . $fieldData['field_label'] . '<br /></label>
				<div style="clear: both"></div>' . 
				implode(PHP_EOL, $option);
		}
		elseif(in_array($fieldData['field_type'], array('checkbox')))
		{
			$option = array();
			if( isset($fieldData['option']) && is_array($fieldData['option']) )
			{
				foreach( $fieldData['option'] as $optionKey => $_option )
				{
					if( !empty($fieldData['option_title']) && !empty($fieldData['option_value']) )
					{
						$__option['value'] = $_option[$fieldData['option_value']];
						$__option['title'] = $_option[$fieldData['option_title']];
					}
					else
					{
						$__option['value'] = $optionKey;
						$__option['title'] = $_option;
					}
					$_option = $__option;
					
					if( !empty($fieldData['default_value']) && !is_array($fieldData['default_value']) )
					$fieldData['default_value'] = explode(',', $fieldData['default_value']);
					
					if( is_array($fieldData['default_value']) )
					( in_array($_option['value'], $fieldData['default_value'] ) ) ? $checked = 'checked="checked"' : $checked = '';
					else $checked = '';
					//else ($_option['value'] == $fieldData['default_value'] ) ? $checked = 'checked="checked"' : $checked = '';
					
					$option[] = '
						<label> 
						<input name="contentField[' . $fieldData['field_name'] . '][]" type="' . $fieldData['field_type'] . '" value="' . $_option['value'] . '" ' . $checked . '>
						&nbsp;' . $_option['title'] . '&nbsp;&nbsp;&nbsp;&nbsp;
						</label>';
				}
			}
			
			$_ip = '
				<label class="vnp-label">' . $fieldData['field_label'] . '<br /></label>
				<div style="clear: both"></div>' . 
				implode(PHP_EOL, $option);
		}
		else
		{
			$_ip = '';
		}
		
		return sprintf($this->setting['fieldTemplate'], $fieldID, $_ip);
	}
	
	public function addField($fieldData = array())
	{
		$this->fielData[$fieldData['field_name']] = $this->fieldBuilder($fieldData);
	}
	
	public function addTag($type)
	{
		$this->fieldNumber++;
		
		if( $type == 'hr' )
		{
			$this->fielData['hr-' . $this->fieldNumber] = '<hr />';
		}
		elseif( $type == 'br' )
		{
			$this->fielData['br-' . $this->fieldNumber] = '<br />';
		}
		elseif( $type == 'clear' )
		{
			$this->fielData['clear-' . $this->fieldNumber] = '<div id="' . $this->formName . '-field-' . $this->fieldNumber . '" style="clear:both"></div>';
		}
	}
	
	public function create()
	{
		global $template, $config;
		
		$ajaxFormEnable = false;
		if( defined('IS_ADMIN') && $config['admin_ajax'] ) $ajaxFormEnable = true;
		elseif( !defined('IS_ADMIN') && $config['site_ajax'] ) $ajaxFormEnable = true;
		if( $ajaxFormEnable )
		{
			$template->jsHeader(STATIC_DIR . 'js/jquery.form.min.js', 'file');
			$jsContent = "var options = {beforeSubmit:showLoading,success:ajax_state_handler,dataType:  'json',url: $('form[name=\"" . $this->formName . "\"]').attr('action') + '&ajax=state-main'};$('form[name=\"" . $this->formName . "\"]').ajaxForm(options);";
			$template->jsHeader($jsContent);
		}
		
		if(!$this->onlyField)
		{
			$attrs ='';
			if(!empty($this->formAttributes) )
			{
				if( is_array($this->formAttributes) )
				{
					$attrs = array();
					foreach( $this->formAttributes as $_attrKey => $_attrValue )
					{
						$attrs[] = $_attrKey . '="' . $_attrValue . '"';
					}
					$attrs = implode(' ', $attrs);
				}
				else $attrs = $this->formAttributes;
			}
			$this->returnedForm[] = '<form name="' . $this->formName . '" ' . $attrs . '>';
			$this->returnedForm[] = implode($this->setting['break_field'], $this->fielData);
			$this->returnedForm[] = '</form>';
		}
		else $this->returnedForm = $this->fielData;
		
		return implode(PHP_EOL, $this->returnedForm);
	}
	
	public function box($header, $content)
	{
		global $template;
		
		$box = '
		<div class="panel clearfix panel-default">
			<div class="panel-heading">
				<span class="item-title">' . $header . '</span>
				<span class="item-controls">
					<span class="item-type">VNP</span>
					<span class="item-edit opened">Edit Field Item</span>
				</span>
			</div>
			<div class="panel-body">
			' . $content . '
			</div>
		</div>';
		
		return $box;
	}
}


?>