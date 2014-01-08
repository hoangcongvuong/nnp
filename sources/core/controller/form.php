<?php

class form
{
	function __construct()
	{
	}
	
	private $formData = array();
	
	public $inputTemplate = '<label>%s</label><input type="%s" name="%s" value="%s" %s />';
	
	function addField($fieldName, $fieldvalue, $fieldType = 'text', $label, $attribute = '')
	{
		if( !empty($attribute) && is_string($attribute) ) $formAttribute = $attribute;
		elseif( is_array($attribute) ) $formAttribute = implode(' ', $attribute);
		$inputFields = array('text', 'password', 'email', 'number', 'tel', 'url');
		if( in_array($fieldType, $inputFields) )
		{		
			$this->formData[] = sprintf($this->inputTemplate, $label, $fieldType, $fieldName, $fieldvalue, $formAttribute);
		}
	}
}


?>