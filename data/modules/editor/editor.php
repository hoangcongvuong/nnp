<?php

class editor
{
	public function __construct()
	{
	}
	
	public function main()
	{
	}
	
	public function setting()
	{
		global $template, $config;
		
		include(MODULE_PATH . 'ct_type/form.php');
		
		$formAgrs = array(	'action' => BASE_DIR . 'admin.php?module=editor&op=setting',
							'method' => 'POST',
							'id'	 => 'editor_setting'
						);
		
		$form = new form('editor-setting', $formAgrs);
		
		$_field = array('field_label'	=> 'Default editor',
						'default_value'	=> $config['default_editor'],
						'field_name'	=> 'default_editor'
						);
		$form->addField($_field);
		$form->addTag('clear');
		$_field = array('field_type'	=> 'submit',
						'default_value'	=> 'Save',
						'field_name'	=> 'save_setting',
						'class'			=> 'btn btn-primary',
						'field_label'	=> 'Save setting'
						);
		$form->addField($_field);
		$template->body_data .= $form->create();
	}
	
	static function replace($textareaSelector)
	{
		global $template, $config;
		
		if( $config['default_editor'] == 'tinymce' )
		{
			$jsContent = '
			var vnp_editor = tinymce.init({
				selector: "' . $textareaSelector . '",
				theme: "modern",
				plugins: [
					"advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table contextmenu directionality",
					"emoticons template paste textcolor vnp_image"
				],
				toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | vnp_image",
				toolbar2: "print preview media | forecolor backcolor emoticons | fontselect | fontsizeselect | template",
				image_advtab: true,
				templates: [
					{title: \'Test template 1\', content: \'Test 1\'},
					{title: \'Test template 2\', content: \'Test 2\'}
				],
				height: 500,
				width: 900,
				entity_encoding : "raw",
				theme_advanced_font_sizes: ["10px,12px,13px,14px,16px,18px,20px"],
    			font_size_style_values: ["12px,13px,14px,16px,18px,20px"],
			 });';
			$template->jsHeader(MODULE_DIR . 'editor/tinymce/tinymce.min.js', 'file');
			$template->jsHeader($jsContent);
		}
	}
	
	static function inlineEditor($textareaSelector)
	{
		global $template, $config, $vnp;
		
		if( $config['default_editor'] == 'tinymce' )
		{
			$jsContent = '
			tinymce.init({
				selector: "' . $textareaSelector . '",
				inline: true,
				plugins: [
					"advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table contextmenu directionality",
					"emoticons template paste textcolor vnp_image"
				],
				toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
				toolbar2: "print preview media | forecolor backcolor emoticons | link image | vnp_image | update_content",
				image_advtab: true,
				setup: function(editor) {
					editor.addButton("update_content", {
						text: "Update content",
						icon: false,
						onclick: function() {
							inlineEditorUpdateContent(' . $vnp->ct_type_id . ', ' . $vnp->content_id . ', tinymce);
						}
					});
				}
			});
			';
			$template->jsHeader(MODULE_DIR . 'editor/tinymce/tinymce.min.js', 'file');
			$template->jsHeader($jsContent);
		}
	}
}



?>