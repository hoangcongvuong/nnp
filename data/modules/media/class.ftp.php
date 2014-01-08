<?php


class ftp
{
	
	public $connection;
	public $error;
	public function __construct($host, $port = 21, $timeout = 90 )
	{
		$this->connect($host);
	}
	
	public function connect($host, $port = 21, $timeout = 90 )
	{
		$this->connection = @ftp_connect($host, $port);
	}
	
	public function login($username = 'root', $password = '' )
	{
		$result = @ftp_login($this->connection, $username, $password);
	}
	
	public function delete($path)
    {
        $result = @ftp_delete($this->connection, $path);
        
        if($result === false) $this->error = 'Unable to delete!';

        return $result;
    }
}


?>