<?php


class crypt
{
	public function __construct()
	{
		require CONTROLLER_PATH . 'crypt/Hashids.php';
	}
	
	static function encrypt() {
	}
	
	static function decrypt($hash) {
	}
}


?>