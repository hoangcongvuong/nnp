<?php

class request
{
	public $homeUrl;
	public $protocol;
	public $domainName;
	public $relativeUrl;
	
	public function __construct()
	{
		$this->detectUrl();
	}
	
	public function get($query, $defaultValue = '')
	{
		if( isset($_GET[$query]) && !empty($_GET[$query]) )
		return $_GET[$query];
		else return $defaultValue;
	}
	
	public function post($query, $defaultValue = '')
	{
		if( isset($_POST[$query]) && !empty($_POST[$query]) )
		return $_POST[$query];
		else return $defaultValue;
	}
	
	public function detectUrl()
	{
		global $theme;
		
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		
		$this->protocol = $protocol;
		$this->domainName = $_SERVER['HTTP_HOST'];
		$this->relativeUrl = $_SERVER['REQUEST_URI'];
		$this->homeUrl = $protocol . $_SERVER['HTTP_HOST'] . BASE_DIR;
		$this->currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}


?>