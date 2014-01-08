<?php


class template
{
	private $XiTpl;
	private $Tpl;
	private $main_block = array(
									'header'	=> 'main_header',
									'content'	=> 'main_content',
									'footer'	=> 'main_footer',
								);
								
	public $header_tag = array( 'title' => '',
								'meta' => array(),
								'script' => array(),
								'link' => array()
								);
	public $ajax_marker = array(	'left_sidebar' => 'vnp-left',
									'main_content' => 'vnp-main'
								);
	public $body_data;
	public $hook = array(	'before_header' => array(),
							'after_header' => array(),
							'before_body' => array(),
							'after_body' => array()
						);
									
	public $error = array();
	
	public $currentBlockTemplatePath;
	
	public $template_cache_dir;
	
	public $jsHeader = array(	'ready' => array(),
								'file'	=> array(),
								'custom'=> array()
							);
	public $jsFooter = array(	'ready' => array(),
								'file'	=> array(),
								'custom'=> array()
							);
	public $ajaxJsHeader = array(	'ready' => array(),
								'file'	=> array(),
								'custom'=> array()
							);
	public $ajaxJsFooter = array(	'ready' => array(),
								'file'	=> array(),
								'custom'=> array()
							);
	public $cssHeader = array(	'inline'=> array(),
								'file'	=> array()
							);
	public $cssFooter = array(	'inline' => array(),
								'file'	=> array()
							);
	public $ajaxCssHeader = array(	'inline'=> array(),
								'file'	=> array()
							);
	public $ajaxCssFooter = array(	'inline' => array(),
								'file'	=> array()
							);
	public $mergeFile = false;
	
	public function __construct()
	{
		$this->template_cache_dir = HTML_CACHE_DIR;
		$this->LoadTemplate();
		$this->XiTemplateIntergrated();
		include CONTROLLER_PATH . 'template/simple_html_dom.php';
		$this->mergeFile = true;
		//$this->output();
	}
	
	public function LoadTemplate()
	{
	}
	
	private function XiTemplateIntergrated()
	{
		if( file_exists( CONTROLLER_PATH . 'template/class.XiTemplate.php' ) )
		{
			include( CONTROLLER_PATH . 'template/class.XiTemplate.php' );
			//$this->XiTpl = new XiTemplate();
		}
	}
	
	public function get_body_content()
	{
		global $r;
		
		//return $this->body_data;
		
		$html = str_get_html($this->body_data);
		if( !empty($html) && !defined('VNP_ADMIN') )
		{
			foreach( $html->find('a') as $aTag )
			{
				$_d = getDomainName($aTag->href);
				if( !empty($_d) && $_d != $r->domainName ) $aTag->class .= ' noajax';
			}
			return $html->save();
		}
		else return $this->body_data;
	}
	
	private function optimization($html)
	{
		global $theme;
		
		if( $this->mergeFile && !defined('VNP_ADMIN') )
		{
			$html = str_get_html($html);
			
			$scriptCachedFile = $this->template_cache_dir . 'script.js';
			$styleCachedFile = $this->template_cache_dir . 'style.css';
			
			$scriptString = $styleString = '';
			
			foreach( $html->find('script') as $element )
			{
				$src = $element->src;
				
				if( !empty($src) && $src != NULL )
				{
					if( BASE_DIR == '/' ) $src = substr(VNP_ROOT, 0, -1) . $src;
					else $src = str_replace(BASE_DIR, VNP_ROOT, $src);
					$scriptString[] = file_get_contents($src);
				}
				elseif( !empty($element->innertext) ) $scriptString[] = $element->innertext;
				
				$element->outertext = '';
			}
			
			$scriptString = implode(';', $scriptString);
			
			foreach( $html->find('link') as $element )
			{
				$href = $element->href;
				if( BASE_DIR == '/' ) $href = substr(VNP_ROOT, 0, -1) . $href;
				else $href = str_replace(BASE_DIR, VNP_ROOT, $href);
				if( !empty($href) && $href != NULL )
				{
					$styleString .= file_get_contents($href);
					$element->outertext = '';
				}
			}
			
			foreach( $html->find('style') as $element )
			{
				if( !empty($element->innertext) ) $styleString .= $element->innertext;
				$element->outertext = '';
			}
			
			file_put_contents($scriptCachedFile, $scriptString);
			
			if( !empty($styleString) )
			{
				$baseURI = $theme['theme_dir'];
				$styleString = str_replace('../', '', $styleString);
				$styleString = preg_replace('/url\(\s*[\'"]?\/?(.+?)[\'"]?\s*\)/i', 'url(' . $baseURI . '$1)', $styleString);
				file_put_contents($styleCachedFile, $styleString);
			}
			
			$scriptLink = BASE_DIR . DATA_DIR . '/cache/template/script.js';
			$styleLink = BASE_DIR . DATA_DIR . '/cache/template/style.css';
			foreach( $html->find('head') as $element )
			{
				$element->innertext .= '<script type="text/javascript" src="' . $scriptLink . '"></script>';
				$element->innertext .= '<link type="text/css" rel="stylesheet" href="' . $styleLink . '" />';
			}
			return $html->save();
		}
		else return $html;
	}
	
	public function output($is_cache = false, $cache_dir = VNP_ROOT)
	{
		global $theme, $config, $db, $r;
		
		$content = $this->vnp_content();
		
		$_tpl = $this->vnp_header();
		$_tpl .= $content;	
		if($config['show_execute_time'] == 1)
		{
			$now = microtime();
			$_tpl .= $now - $config['start_time'];
		}
		$_tpl .= $this->vnp_footer();
		
		/*include CONTROLLER_PATH . 'template/format.php';
		$format = new Format;
		$formatted_html = $format->HTML($_tpl);
		echo preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $formatted_html);;*/
		
		$output = $this->optimization($_tpl);

		if( $is_cache )
		{
			$result = base64_encode(serialize($cacheValue));
			$cacheString = md5($r->relativeUrl);
			file_put_contents($cache_dir . $cacheString,$output);
		}
		
		echo $output;
		unset($output);
		exit();
	}
	
	public function ajaxOutput($ajaxMod = 'state-main')
	{
		global $theme, $config, $db;
		
		$data = $hook = '';
		$hook .= $this->cssContent($this->ajaxCssHeader);
		$hook .= $this->cssContent($this->ajaxCssFooter);
		
		if( !empty($this->header_tag['script']) )
		$hook .= implode(PHP_EOL, $this->header_tag['script']);
		$hook .= $this->jsContent($this->ajaxJsHeader);
		$hook .= $this->jsContent($this->ajaxJsFooter);
		
		$return = '';
		if($ajaxMod == 'state-main')
		{
			$data = $this->get_body_content();
			$hook = array('header' => $hook );
			$jsonPrepare = array('title' => $this->header_tag['title'], 'data' => $data, 'hook' => $hook);
			$return = json_encode($jsonPrepare);
		}
		elseif($ajaxMod == 'string')
		{
			$return = $hook;
			$return .= $this->get_body_content();
		}
		echo $return;
		exit();
	}
	
	private function TemplateFile($filePath)
	{
		if( file_exists($filePath) )
		$this->Tpl = $this->XiTpl = new XiTemplate($filePath);
		$this->Tpl->nullValue = '';
	}
	
	private function BlockTemplateFile($filePath)
	{
		if( file_exists($filePath) )
		return new XiTemplate($filePath);
	}
	
	public function assign($TplVar, $value)
	{
		$this->Tpl->assign($TplVar, $value);
	}
	
	public function parse($blockName)
	{
		$this->Tpl->parse($blockName);
	}
	
	public function text($blockName)
	{
		return $this->Tpl->text($blockName);
	}
	
	public function out($blockName)
	{
		$this->Tpl->out($blockName);
	}
	
	private function exception_handler($exception)
	{
		$this->error($exception->getMessage());
	}
	
	private function vnp_header()
	{
		global $theme, $global;
		
		$header_file = $theme['theme_root'] . '/header.tpl';
		
		$this->TemplateFile($header_file);
		$header_data = '';
		
		$this->header_tag['link'][] = '<link href="' . $theme['theme_dir'] . 'css/style.css" rel="stylesheet" type="text/css" />';
		
		if( !empty($this->header_tag) )
		{
			$header_data = array();
			if( !empty($this->hook['before_header']) ) $header_data[] = implode( PHP_EOL, $this->hook['before_header']);
			if( !empty($this->header_tag['hook_header']) ) $header_data[] = implode( PHP_EOL, $this->header_tag['meta']);
			if( !empty($this->header_tag['meta']) ) $header_data[] = implode(PHP_EOL, $this->header_tag['meta']);
			if( !empty($this->header_tag['script']) ) $header_data[] = implode(PHP_EOL, $this->header_tag['script']);
			if( !empty($this->header_tag['link']) ) $header_data[] = implode( PHP_EOL, $this->header_tag['link']);
			if( !empty($this->hook['after_header']) ) $header_data[] = implode( PHP_EOL, $this->hook['after_header']);
			$header_data = implode( PHP_EOL, $header_data );
		}
		
		$header_data .= $this->cssContent($this->cssHeader);
		$header_data .= $this->jsContent($this->jsHeader);
		
		//Parse Variables
		$this->assign( 'META_TITLE', $this->header_tag['title'] );
		$this->assign( 'HEADER_DATA', $header_data );
		$this->assign( 'THEME_DIR', $theme['theme_dir'] );
		$this->assign( 'BASE_DIR', BASE_DIR );
		$this->parse($this->main_block['header']);
		return $this->text($this->main_block['header']);
		//$this->out($this->main_block['header']);
	}
	
	private function vnp_footer()
	{
		global $theme, $global;
		
		$footer_file = $theme['theme_root'] . '/footer.tpl';
		$this->TemplateFile($footer_file);
		
		$footer_data = '';
		$footer_data .= $this->cssContent($this->cssFooter);
		$footer_data .= $this->jsContent($this->jsFooter);
		$this->assign( 'FOOTER_DATA', $footer_data );
		$this->parse($this->main_block['footer']);
		return $this->text($this->main_block['footer']);
		//$this->out($this->main_block['footer']);
	}
	
	private function vnp_content()
	{
		global $theme, $global;
		
		$content_file = $theme['theme_root'] . 'layout/' . $theme['default_layout'];
		$this->TemplateFile($content_file);		
		
		$this->LoadBlocks();
		
		$this->assign( 'BASE_DIR', BASE_DIR );
		$this->assign( 'AJAX_MARKER', $this->ajax_marker );
		$this->assign( 'VNP_ERROR', $this->template_error_handler() );
		$this->assign( 'VNP_MAIN_CONTENT', $this->get_body_content() );		
		$this->parse($this->main_block['content']);
		//$this->out($this->main_block['content']);
		return $this->text($this->main_block['content']);
	}
	
	private function LoadBlocks()
	{
		global $theme, $session;
		
		if( !empty($theme['block']) )
		{
			$themeBlocks = array();
			if( !empty($theme['block'][$theme['default_layout']]) )
			foreach( $theme['block'][$theme['default_layout']] as $blockID => $block )
			{
				$blockData = '';
				if( !empty($block) )
				{
					$blockFile = '';
					if( $block['block_type'] == 'theme' )
					{
						$blockFile = $theme['theme_root'] . 'block/' . $block['block_file'];
					}
					elseif( $block['block_type'] == 'admin' )
					{
						$blockFile = ADMIN_ROOT . 'blocks/' . $block['block_file'];
					}
					elseif( $block['block_type'] == 'module' )
					{
						$blockFile = MODULE_PATH . $block['type_value'] . '/' . $block['block_file'];
					}
					$themeBlocks[$block['block_area']][$blockID] = $this->blockContent($blockFile, unserialize($block['block_data'])); 
				}
			}
			if($session->get('enable_design_mod') == 'on')
			{
				$block_areas = explode(',', $theme['block_areas']);
				foreach( $block_areas as $area )
				{
					if( isset($themeBlocks[$area]) )
					{
						$__blocks = array();
						foreach( $themeBlocks[$area] as $__blockID => $__block )
						{
							$__blocks[] = '<div class="template-block-portal" id="template-block-' . $__blockID . '">' . $__block . '</div>';
						}
						$block_content = implode(PHP_EOL, $__blocks);
					}
					else $block_content = '';
					
					$__blockData = '<div class="template-block-area-handler" id="template-area-' . $area . '">' . $block_content . '</div>';
					
					$this->assign( $area, $__blockData );
				}
			}
			else
			{
				foreach( $themeBlocks as $themeBlockArea => $blockAreaData )
				{
					$__blockData = implode(PHP_EOL, $blockAreaData);
					
					$this->assign( $themeBlockArea, $__blockData );
				}
			}
		}
	}
	
	private function blockContent($blockFile, $blockData)
	{
		global $template;
		
		if( file_exists($blockFile) )
		{
			$this->currentBlockTemplatePath = pathinfo($blockFile, PATHINFO_DIRNAME ) . '/';
			$executeFunction = '';
			$templateFile = '';
			$parameters = array();
			
			include $blockFile;
			if( !isset($blockContent) ) $this->error[] = 'Error block file: ' . $blockFile;
			return $blockContent;
		}
	}
	
	private function template_error_handler()
	{
		$error_array = array();
		
		if( !empty($this->error) )
		{
			//n($this->error);
			foreach( $this->error as $error_type => $_error_list )
			{
				foreach( $_error_list as $key => $_error )
				{
					$_vnp_error = 'Error code ' . str_pad($error_type, 4, '0', STR_PAD_LEFT) . ': <strong>' . $_error['msg'] . '</strong>';
					if( !empty( $_error['file'] ) ) $_vnp_error .= ' in file ' . $_error['file'];
					if( !empty( $_error['line'] ) ) $_vnp_error .= ' online ' . $_error['line'];
					$error_array[] = $_vnp_error;
				}
			}
		}
		return implode(PHP_EOL . '<br />', $error_array);
	}
	
	private function hook_header($hook_header,$position = 'after')
	{
		if($position != 'after') $position = 'before';
	}
	
	
	// Public function to load block template or app template
	
	public function file($templateFile, $outputData = array())
	{
		if( file_exists($this->currentBlockTemplatePath . $templateFile) )
		{
			$tpl = $this->BlockTemplateFile($this->currentBlockTemplatePath . $templateFile);
			return $tpl;
		}
		else return '';
	}
	
	private function jsContent($jsData)
	{
		$js = '';
		if( !empty($jsData['file']) )
		{
			$_js_files = array();
			foreach($jsData['file'] as $_js)
			{
				$_js_files[] = '<script type="text/javascript" src="' . $_js . '"></script>';
			}
			$js .= PHP_EOL . implode(PHP_EOL, $_js_files);
		}
		if( !empty($jsData['ready']) )
		{
			$js .= PHP_EOL . '
			<script type="text/javascript">
			$(document).ready(function(){
				' . implode(PHP_EOL, $jsData['ready']) . '
			})
			</script>';
		}
		if( !empty($jsData['custom']) )
		{
			$js .= PHP_EOL . '
			<script type="text/javascript">
			' . implode(PHP_EOL, $jsData['custom']) . '
			</script>';
		}
		return $js;
	}
	
	private function cssContent($cssData)
	{
		$css = '';
		if( !empty($cssData['file']) )
		{
			$_css_files = array();
			foreach($cssData['file'] as $_css)
			{
				$_css_files[] = '<link rel="stylesheet" href="' . $_css . '" type="text/css" media="all" />';
			}
			$css .= PHP_EOL . implode(PHP_EOL, $_css_files);
		}
		if( !empty($cssData['inline']) )
		{
			$css .= PHP_EOL . '
			<style type="text/css">
			' . implode(PHP_EOL, $cssData['inline']) . '
			</style>';
		}
		return $css;
	}
	
	public function jsHeader($jsContent, $type = 'ready')
	{
		if( IS_AJAX ) $this->ajaxJsHeader[$type][] = $jsContent;
		else $this->jsHeader[$type][] = $jsContent;
	}
	
	public function jsFooter($jsContent, $type = 'ready')
	{
		if( IS_AJAX ) $this->ajaxJsFooter[$type][] = $jsContent;
		else $this->jsFooter[$type][] = $jsContent;
	}
	
	public function cssHeader($cssContent, $type = 'inline')
	{
		if( IS_AJAX ) $this->ajaxCssHeader[$type][] = $cssContent;
		else $this->cssHeader[$type][] = $cssContent;
	}
	
	public function cssFooter($cssContent, $type = 'inline')
	{
		if( IS_AJAX ) $this->ajaxCssFooter[$type][] = $cssContent;
		else $this->cssFooter[$type][] = $cssContent;
	}
	
	public function collapseBox($content, $title = 'Box', $des = '')
	{
		$boxString = '
		<div class="fl">
			<div class="panel clearfix panel-default" id="' . random(10) . '">
				<div class="panel-heading">
					<span class="item-title">' . $title . '</span>
					<span class="item-controls">
						<span class="item-type">' . $des . '</span>
						<span class="item-edit opened">Edit Field Item</span>
					</span>
				</div>
				<div class="panel-body">
				' . $content . '
				</div>
			</div>
		</div>';
		
		return $boxString;
	}
}

?>