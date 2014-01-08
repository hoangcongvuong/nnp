<?php

class File
{
	public function __construct()
	{
	}
	
	public function delete($filePath)
	{
		if(is_readable($filePath) ) return unlink($filePath);
		else return '';
	}
	
	public function rename($filePath,$newName)
	{
		$fileDir = dirname($filePath);
		return rename($filePath, $fileDir . '/' . $newName);
	}
}

?>