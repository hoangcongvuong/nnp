<?php

function n($data)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function p($data)
{
	$return = '<pre>' . print_r($data, true) . '</pre>';
	return $return;
}

function mydir()
{
	$backtrace = debug_backtrace();
	n($backtrace);
    if (isset($backtrace[1]['function']) && $backtrace[1]['function'] == 'mydir')
    {
        return $backtrace[1]['function'];
    }
	else return '';
}

function getThemeInfo($theme_root, $theme_dir)
{	
	if( file_exists( $theme_root . $theme_dir . '/ini.xml' ) )
	{
		$theme = array();			
		$ini = new DOMDocument();
		$ini->load($theme_root . $theme_dir . '/ini.xml');
		
		$infos = $ini->getElementsByTagName('info');
		foreach( $infos as $info )
		{
			$name = $info->getElementsByTagName('name');
			$name = $name->item(0)->nodeValue;
	
			$author = $info->getElementsByTagName('author');
			$author = $author->item(0)->nodeValue;
	
			$authorurl = $info->getElementsByTagName('authorurl');
			$authorurl = $authorurl->item(0)->nodeValue;
			
			$themeurl = $info->getElementsByTagName('themeurl');
			$themeurl = $themeurl->item(0)->nodeValue;
	
			$theme['info'] = array(	'name'		=> $name,
									'author'	=> $author,
									'authorurl'	=> $authorurl,
									'themeurl'	=> $themeurl,
									'dir'		=> $theme_dir
										);
		}

		$layouts = $ini->getElementsByTagName('layout');
		foreach( $layouts as $layout )
		{
			$name = $layout->getElementsByTagName('name');
			$name = $name->item(0)->nodeValue;

			$file = $layout->getElementsByTagName('file');
			$file = $file->item(0)->nodeValue;

			$block = $layout->getElementsByTagName('block');
			$block = $block->item(0)->nodeValue;

			$theme['layouts'][$file] = array('name'	=> $name,
										'file'	=> $file,
										'block'	=> $block
										);
		}
		
		$blocks = $ini->getElementsByTagName('blockfile');
		foreach( $blocks as $block )
		{
			$name = $block->getElementsByTagName('name');
			$name = $name->item(0)->nodeValue;

			$file = $block->getElementsByTagName('file');
			$file = $file->item(0)->nodeValue;

			$function = $block->getElementsByTagName('function');
			$function = $function->item(0)->nodeValue;
			
			$layout = $block->getElementsByTagName('layout');
			$layout = $layout->item(0)->nodeValue;
			
			$active = $block->getElementsByTagName('active');
			$active = $active->item(0)->nodeValue;
			
			$area = $block->getElementsByTagName('area');
			$area = $area->item(0)->nodeValue;
			
			$order = $block->getElementsByTagName('order');
			$order = $order->item(0)->nodeValue;

			$theme['blocks'][$name] = array(
										'name'	=> $name,
										'file'	=> $file,
										'function'	=> $function,
										'type' => 'theme_block',
										'layout' => $layout,
										'active' => $active,
										'area' => $area,
										'order' => $order
										);
		}
		return $theme;
	}
	else
	{
		trigger_error('Template error: Missing config file!!!');
		return false;
	}
}

function random( $length = 10 )
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string = '';    
	for( $n = 0; $n < $length; $n++ )
	{
		$string .= $characters[mt_rand( 0, strlen( $characters ) )];
	}
	return $string;
}

function alias( $string, $spaceReplace = '-' )
{
	$unicode = array( 
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ', 
            'd'=>'đ', 
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ', 
            'i'=>'í|ì|ỉ|ĩ|ị', 
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ', 
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự', 
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ', 
			'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ', 
            'D'=>'Đ', 
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ', 
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị', 
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ', 
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự', 
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ); 
	foreach( $unicode as $nonUnicode=>$uni )
	{
		$string = preg_replace("/($uni)/i", $nonUnicode, $string ); 
	}
	$string = str_replace( ' ', $spaceReplace, $string );
	return strtolower( $string ); 
} 

function passGenerator( $string, $addSalt = true )
{
	if( !empty( $string ) )
	{
		$salt = '';
		if( $addSalt )
		{
			$salt = random();
			$string = hash( 'sha256', md5( $string . $salt ) );
			return array(	'salt'		=> $salt,
							'string'	=> $string
						);
		}
		else return hash( 'sha256', md5( $string . $salt ) );
	}
	else return '';
}

function passChecker( $inputString, $salt = '', $authString )
{
	$_authTemp = hash( 'sha256', md5( $inputString . $salt ) );
	
	//echo $_authTemp; die();
	
	if( $_authTemp === $authString )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function encode($string)
{
	return base64_encode($string);
}

function decode($string)
{
	return base64_decode($string);
}

function lang($string)
{
	return $string;
}
function noty($msg, $type = 'info')
{
}
	
function alert($msg, $type = 'default')
{
	return '<div class="alert alert-' . $type . '">' . $msg . '</div>';
}

function sortArray($inputArray = array(), $sortedKeyArray = array())
{
	$tempArray = array();
	if( !empty($inputArray) && !empty($sortedKeyArray) )
	{
		foreach( $sortedKeyArray as $index => $mainArrayKey )
		{
			if( isset($inputArray[$mainArrayKey]) )
			{
				$tempArray[] = $inputArray[$mainArrayKey];
				unset($inputArray[$mainArrayKey]);
			}
		}
		return array_merge($tempArray,$inputArray);
	}
	else return $inputArray;
}

function encodeArray($array = array())
{
	return serialize($array);
	return base64_encode(serialize($array));
}

function decodeArray($arrayString)
{
	return unserialize($arrayString);
	return unserialize(base64_decode($arrayString));
}

function encrypt()
{
	$hashids = new Hashids('vnp_salt', 4);
	
	$numbers = func_get_args();
	return call_user_func_array( array($hashids, 'encrypt'), $numbers);
	//$hashids->encrypt();
}
	
function decrypt($hash)
{
	$hashids = new Hashids('vnp_salt', 4);
	return $hashids->decrypt($hash);
}

/**
 * paging()
 *
 * @param string $base_url
 * @param integer $num_items
 * @param integer $per_page
 * @param integer $start_item
 * @param bool $add_prevnext_text
 * @param bool $onclick
 * @param string $js_func_name
 * @param string $containerid
 * @return
 */
function paging( $base_url, $num_items, $per_page, $start_item, $rewrite = 0, $add_prevnext_text = true, $onclick = false, $js_func_name = 'nv_urldecode_ajax', $containerid = 'vnp_page' )
{

	$total_pages = ceil( $num_items / $per_page );

	if( $total_pages == 1 ) return '';

	$on_page = @floor( $start_item / $per_page ) + 1;
	

	if( ! is_array( $base_url ) )
	{
		if( $rewrite )
		{
			$amp = preg_match('/\/{1,}$/',$base_url) ? "" : "/";
			$amp .= "page/";
		}
		else
		{
			$amp = preg_match( "/\?/", $base_url ) ? "&amp;" : "?";
			$amp .= "page=";
		}
	}
	else
	{
		$amp = $base_url['amp'];
		$base_url = $base_url['link'];
	}

	$page_string = '';

	if( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;

		for( $i = 1; $i <= $init_page_max; ++$i )
		{
			$href = $i;
			if( $rewrite ) $href = $href ? $base_url . $amp . $href . '.html' : $base_url;
			else $href = $href ? $base_url . $amp . $href : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('" . rawurlencode( htmlspecialchars_decode( $href ) ) . "','" . $containerid . "')\"";
			$page_string .= ( $i == $on_page ) ? "<li class=\"active\"><a>" . $i . "</a></li>" : "<li><a " . $href . ">" . $i . "</a></li>";

		}

		if( $total_pages > 3 )
		{
			if( $on_page > 1 && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? " ... " : ", ";
				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for( $i = $init_page_min - 1; $i < $init_page_max + 2; ++$i )
				{
					$href = $i;
					if( $rewrite ) $href = $href ? $base_url . $amp . $href . '.html' : $base_url;
					else $href = $href ? $base_url . $amp . $href : $base_url;
					$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('" . rawurlencode( htmlspecialchars_decode( $href ) ) . "','" . $containerid . "')\"";
					$page_string .= ( $i == $on_page ) ? "<li class=\"active\"><a>" . $i . "</a></li>" : "<li><a " . $href . ">" . $i . "</a></li>";

					if( $i < $init_page_max + 1 )
					{
						$page_string .= ", ";
					}
				}

				$page_string .= ( $on_page < $total_pages - 4 ) ? " ... " : ", ";
			}
			else
			{
				$page_string .= " ... ";
			}

			for( $i = $total_pages - 2; $i < $total_pages + 1; ++$i )
			{
				$href = $i;
				if( $rewrite ) $href = $href ? $base_url . $amp . $href . '.html' : $base_url;
				else $href = $href ? $base_url . $amp . $href : $base_url;
				$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('" . rawurlencode( htmlspecialchars_decode( $href ) ) . "','" . $containerid . "')\"";
				$page_string .= ( $i == $on_page ) ? "<li class=\"active\"><a>" . $i . "</a></li>" : "<li><a " . $href . ">" . $i . "</a></li>";

			}
		}
	}
	else
	{
		for( $i = 1; $i < $total_pages + 1; ++$i )
		{
			$href = $i;
			if( $rewrite ) $href = $href ? $base_url . $amp . $href . '.html' : $base_url;
			else $href = $href ? $base_url . $amp . $href : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('" . rawurlencode( htmlspecialchars_decode( $href ) ) . "','" . $containerid . "')\"";
			$page_string .= ( $i == $on_page ) ? "<li class=\"active\"><a>" . $i . "</a></li>" : "<li><a " . $href . ">" . $i . "</a></li>";

		}
	}

	if( $add_prevnext_text )
	{
		if( $on_page > 1 )
		{
			$href = ( $on_page - 1 );
			if( $rewrite ) $href = $href ? $base_url . $amp . $href . '.html' : $base_url;
			else $href = $href ? $base_url . $amp . $href : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('" . rawurlencode( htmlspecialchars_decode( $href ) ) . "','" . $containerid . "')\"";
			$page_string = "<li><a " . $href . ">Prev</a></li>" . $page_string;
		}

		if( $on_page < $total_pages )
		{
			$href = $on_page + 1;
			if( $rewrite ) $href = $href ? $base_url . $amp . $href . '.html' : $base_url;
			else $href = $href ? $base_url . $amp . $href : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('" . rawurlencode( htmlspecialchars_decode( $href ) ) . "','" . $containerid . "')\"";
			$page_string .= "<li><a " . $href . ">Next</a></li>";
		}
	}

	return '<ul class="pagination pagination-sm">' . $page_string . '</ul>';
}

function content_url($aliasString, $type = 'post', $module = '')
{
	global $config;
	
	$url = '';
	if( $type == 'post' )
	{
		if( $config['rewrite'] )
		$url = BASE_DIR . $aliasString . '.html';
		else $url = BASE_DIR . 'index.php?p=' . $aliasString;
	}
	elseif( $type == 'taxonomy' )
	{
		if( $config['rewrite'] )
		$url = BASE_DIR . $aliasString . '.html';
		else $url = BASE_DIR . 'index.php?p=' . $aliasString;
	}
	elseif( $type == 'page' )
	{
		if( $config['rewrite'] )
		$url = BASE_DIR . $aliasString;
		else $url = BASE_DIR . 'index.php?p=' . $aliasString;
	}
	elseif( $type == 'module' )
	{
		if( $config['rewrite'] )
		$url = BASE_DIR . 'module/' . $module . '/' . $aliasString . '.html';
		else $url = BASE_DIR . 'index.php?module=' . $module . '&op=' . $aliasString;
	}
	return $url;
}

function box($header, $content)
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

function addClassToLink($html, $class_name)
{
	global $r;

	if( defined('VNP_ADMIN') )  return $html;
	
	$domain = $r->domainName;
	$base = BASE_DIR;
	$follow_list = array($domain);
	
	$html = preg_replace('%(<a\s*[^a-zA-Z](?!\bclass=)[^>]*)%', '$1 class="noclasslink"', $html);
	
	$html = preg_replace('/<a([^>]*?)(class\s*\=\s*\"[^\"]*?\")([^>]*?>)/','<a $2$1$3',$html);
	/*$html = preg_replace('%<a\s*[^a-zA-Z](.*)(class\s*\=\s*\"[^\"]*?\")([^>]*?>)%','<a $2$1$3',$html);*/

	return preg_replace('%<a(.*)class="([^"]*)"(.*)(href="https?://)((?!(?:(?:www.)?'.implode('|(?:www\.)?',$follow_list).'))[^"]+)"([^>]*)(?:[^>]*)>%',
'<a$1$3$4$5"$6 class="$2 haha">',$html);

	//return $html = preg_replace('/(<a\s*[^a-zA-Z](?!\bhref=http://localhost)[^>]*)/', '$1 class="noclasslink"', $html);
	
}

function addClassToLink1($html, $class_name)
{
	global $r;
	
	if( defined('VNP_ADMIN') )  return $html;
	
	$domain = $r->domainName;
	$base = BASE_DIR;
	$follow_list = array($domain);
	
	$out = preg_replace('/(<a[^>]*?)(class\s*\=\s*\"[^\"]*?\")([^>]*?>)/','$1$3',$html);
	
	return preg_replace('%(<a\s*[^>]*)(href="https?://)((?!(?:(?:www.)?'.implode('|(?:www\.)?',$follow_list).'))[^"]+)"([^>]*)(?:[^>]*)>%',
'$1$2$3"$4 class="$5 ' . $class_name . ' ">',$out);

}

function getDomainName($url)
{
	$parse = parse_url($url);
	return $parse['host'];
}

function write_file($fileLocation, $fileContent, $writeMod = 'replace')
{
	if($writeMod == 'append')
	{
		file_put_contents($fileLocation, $fileContent, FILE_APPEND | LOCK_EX);
	}
	else file_put_contents($fileLocation, $fileContent, LOCK_EX);
}

function read_file($fileLocation)
{
	return file_get_contents($fileLocation);
}

function set_cache($cacheFileName, $resource, $cacheType = 'db', $encodeType = 'serialize')
{
	if( $cacheType == 'html' ) $cacheContent = $resource;
	else
	{
		if( $encodeType == 'json' ) $cacheContent = json_encode($resource);
		else $cacheContent = serialize($resource);
	}
	
	if( $cacheType == 'db' )
	{
		write_file( DB_CACHE_DIR . $cacheFileName, $cacheContent);
	}
	elseif( $cacheType == 'html' )
	{
		write_file( HTML_CACHE_DIR . $cacheFileName, $cacheContent);
	}
}

function get_cache($cacheFileName, $cacheType = 'db', $encodeType = 'serialize')
{
	if( $cacheType == 'db' )
	{
		$cacheFile = DB_CACHE_DIR . $cacheFileName;
	}
	elseif( $cacheType == 'html' )
	{
		$cacheFile = HTML_CACHE_DIR . $cacheFileName;
	}
	
	if( file_exists($cacheFile) )
	{
		$cacheContent = read_file($cacheFile);
		
		if( $cacheType == 'html' ) return $cacheContent;
		else
		{
			if( $encodeType == 'json' ) return json_decode($cacheContent);
			else return unserialize($cacheContent);
		}
	}
	else return '';
}

function get_ct_type($return = true)
{
	global $config, $global, $db;
	
	$contentTypeCacheName = 'ct_type_dbdata.cache';
	$contentTypeInfoCacheName = 'ct_type_info.cache';

	$genCache = true;
	if( $config['db_cache'] && $_ctType = get_cache($contentTypeInfoCacheName) )
	{
		$global['ct_types'] = $_ctType;
		if( $return )
		{
			if( $_r = get_cache($contentTypeCacheName) ) return $_r;
		}
		else $genCache = false;
	}
	
	if( $genCache )
	{
		$_contentTypeData = array();
		$_ct_type_cache = $db->get('ct_types', 'ct_type_id')->result;
		$_temp = array();
		foreach( $_ct_type_cache as $_id => $_ct_type )
		{
			if( !empty($_ct_type['ct_type_field']) )
			{
				$_ct_type_field = decodeArray($_ct_type['ct_type_field']);
	
				foreach( $_ct_type_field as $_fieldID => $_field )
				{
					$_ct_type_field[$_fieldID] = array_merge($_ct_type_field[$_fieldID], decodeArray($_field['ct_field_data']));
					unset($_ct_type_field[$_fieldID]['ct_field_data']);
				}
			}
			else $_ct_type_field = array();
			
			$_ct_type_cache[$_id]['ct_type_field'] = $_ct_type_field;
			$_ct_type_cache[$_id]['ct_type_setting'] = decodeArray($_ct_type['ct_type_setting']);
			$_ct_type_cache[$_id]['ct_field_sort'] = decodeArray($_ct_type['ct_field_sort']);
			
			$_contentTypeData[$_id] = array(
											'ct_type_id'		=> $_id,
											'ct_type_name'		=> $_ct_type['ct_type_name'],
											'ct_type_title'		=> $_ct_type['ct_type_title'],
											'ct_type_image'		=> $_ct_type['ct_type_image'],
											'ct_type_setting'	=> $_ct_type_cache[$_id]['ct_type_setting'],
											'status'			=> $_ct_type['status']
										);
		}
		$global['ct_types'] = $_contentTypeData;
		unset($_contentTypeData);
		if( $config['db_cache'] )
		{
			set_cache($contentTypeCacheName, $_ct_type_cache);
			set_cache($contentTypeInfoCacheName, $global['ct_types']);
		}
		return $_ct_type_cache;
	}
}

function getConfigValue()
{
	if( file_exists( XML_CONFIG_FILE ) )
	{
		$config = array();			
		$ini = new DOMDocument();
		$ini->load( XML_CONFIG_FILE );

		$configs = $ini->getElementsByTagName('config');
		foreach( $configs as $_config )
		{
			$_configName	= $_config->getAttribute('name');
			$_configValue	= $_config->nodeValue;
			$config[$_configName] = $_configValue;
		}			
		return $config;
	}
	else return false;
}

function updateXmlConfigFile($configData = array())
{
	$configFile = new DOMDocument('1.0', 'utf-8' );
	$configFile->formatOutput = true;
	$sites = $configFile->createElement('sites');
	
	foreach( $configData as $_configName => $_configValue )
	{
		$configNode = $configFile->createElement('config');
		$configNode->setAttribute('name', $_configName);
		$configNode->nodeValue = $_configValue;
		$configNode = $sites->appendchild($configNode);
	}
	$sites = $configFile->appendchild( $sites );
	if( $configFile->save(XML_CONFIG_FILE) ) return true;
	else return false;
}

?>