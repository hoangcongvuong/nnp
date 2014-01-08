<?php

// mysqli wrapper for VNP

class vnp_db
{
	public $ThrowExceptions = true;
	public $error_desc = '';
	public $error	= array();
	public $log		= array(
								'total_query' => 0,
								'query' => array()
							);
	static public $instance;
	private $config	= array(	'db_host' => 'localhost',
							 	'db_user' => NULL,
								'db_pass' => NULL,
								'db_name' => NULL,
								'db_port' => NULL,
								'charset' => 'utf8',
								'debug'	=> 0
							);
	
	protected $obj;
	protected $_where = array();
	protected $_whereTypeList;
	protected $_paramTypeList;
	protected $_bindParams = array('');
	protected $stmt;
	
	public function __construct($db_config = array(), $auto_connect = true)
	{
		$this->config = array_merge( $this->config, $db_config );
		if( $auto_connect )  $this->open();
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	public function open()
	{
		$this->reset_error();
		if( $this->config['db_host'] !== NULL &&
			$this->config['db_user'] !== NULL &&
			$this->config['db_pass'] !== NULL &&
			$this->config['db_name'] !== NULL )
		{
			if( $this->config['db_port'] == NULL )
			{
				if( $port = ini_get('mysqli.default_port') )
				{
					$this->config['db_port'] = $port;
					unset($port);
				}
			}
			$this->obj = new mysqli($this->config['db_host'], $this->config['db_user'], $this->config['db_pass'], $this->config['db_name'], $this->config['db_port']) or die('Cannot connect to db server');;
			if( $this->obj->connect_errno )
			{
				$this->set_error( $this->obj->connect_errno, $this->obj->connect_error );
			}
			else $this->obj->set_charset($this->config['charset']);
			self::$instance = $this;
		}
	}
	
	/**
     * Reset states after an execution
     *
     * @return object Returns the current instance.
     */
    protected function reset()
    {
        $this->_where = array();
        $this->_bindParams = array(''); // Create the empty 0 index
        unset($this->_query);
        unset($this->_whereTypeList);
        unset($this->_paramTypeList);
    }
	
	public function close()
	{
		$this->obj->close();
	}
	
	public function is_connected()
	{
		if( $this->obj->ping() ) return true;
		else return false;
	}
	
	/**
     * Method attempts to prepare the SQL query
     * and throws an error if there was a problem.
     *
     * @return mysqli_stmt
     */
    protected function _prepareQuery()
    {
        if( !$stmt = $this->obj->prepare($this->_query))
		{
            trigger_error('Problem preparing query (' . $this->_query . ') ' . $this->obj->error, E_USER_ERROR);
        }
		$this->stmt = $stmt;
        return $stmt;
    }
	
	/**
     * This helper method takes care of prepared statements' "bind_result method
     * , when the number of variables to pass is unknown.
     *
     * @param mysqli_stmt $stmt Equal to the prepared statement object.
     *
     * @return array The results of the SQL fetch.
     */
    protected function _dynamicBindResults(mysqli_stmt $stmt)
    {
        $parameters = array();
        $results = array();

        $meta = $stmt->result_metadata();

        $row = array();
        while( $field = $meta->fetch_field() )
		{
            $row[$field->name] = null;
            $parameters[] = & $row[$field->name];
        }

        call_user_func_array(array($stmt, 'bind_result'), $parameters);

        while($stmt->fetch())
		{
            $x = array();
            foreach( $row as $key => $val )
			{
                $x[$key] = $val;
            }
            array_push($results, $x);
        }
        return $results;
    }
	
	/**
     * Abstraction method that will compile the WHERE statement,
     * any passed update data, and the desired rows.
     * It then builds the SQL query.
     *
     * @param int   $numRows   The number of rows total to return.
     * @param array $tableData Should contain an array of data for updating the database.
     *
     * @return mysqli_stmt Returns the $stmt object.
     */
    protected function _buildQuery( $numRows = null, $tableData = null )
    {
        $hasTableData = is_array($tableData);
        $hasConditional = !empty($this->_where);

        // Did the user call the "where" method?
        if(!empty($this->_where))
		{

            // if update data was passed, filter through and create the SQL query, accordingly.
            if( $hasTableData )
			{
                $pos = strpos($this->_query, 'UPDATE');
                if( $pos !== false )
				{
                    foreach ($tableData as $prop => $value)
					{
                        // determines what data type the item is, for binding purposes.
                        $this->_paramTypeList .= $this->_determineType($value);

                        // prepares the reset of the SQL query.
                        $this->_query .= ($prop . ' = ?, ');
                    }
                    $this->_query = rtrim($this->_query, ', ');
                }
            }

            //Prepair the where portion of the query
            $this->_query .= ' WHERE ';
            foreach($this->_where as $column => $value)
			{
                // Determines what data type the where column is, for binding purposes.
                $this->_whereTypeList .= $this->_determineType($value);

                // Prepares the reset of the SQL query.
                $this->_query .= ($column . ' = ? AND ');
            }
            $this->_query = rtrim($this->_query, ' AND ');
        }

        // Determine if is INSERT query
        if($hasTableData)
		{
            $pos = strpos($this->_query, 'INSERT');

            if($pos !== false)
			{
                //is insert statement
                $keys = array_keys($tableData);
                $values = array_values($tableData);
                $num = count($keys);

                // wrap values in quotes
                foreach ($values as $key => $val)
				{
                    $values[$key] = "'{$val}'";
                    $this->_paramTypeList .= $this->_determineType($val);
                }

                $this->_query .= '(' . implode($keys, ', ') . ')';
                $this->_query .= ' VALUES(';
                while ($num !== 0)
				{
                    $this->_query .= '?, ';
                    $num--;
                }
                $this->_query = rtrim($this->_query, ', ');
                $this->_query .= ')';
            }
        }

        // Did the user set a limit
        if(isset($numRows))
		{
            $this->_query .= ' LIMIT ' . (int)$numRows;
        }

        // Prepare query
        $stmt = $this->_prepareQuery();

        // Prepare table data bind parameters
        if($hasTableData)
		{
            $this->_bindParams[0] = $this->_paramTypeList;
            foreach ($tableData as $prop => $val)
			{
                array_push($this->_bindParams, $tableData[$prop]);
            }
        }
        // Prepare where condition bind parameters
        if($hasConditional)
		{
            if ($this->_where)
			{
                $this->_bindParams[0] .= $this->_whereTypeList;
                foreach ($this->_where as $prop => $val)
				{
                    array_push($this->_bindParams, $this->_where[$prop]);
                }
            }
        }
        // Bind parameters to statment
        if($hasTableData || $hasConditional)
		{
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($this->_bindParams));
        }
		
		$this->log['total_query']++;
		$this->log['query'][] = array(	'status' => 1,
										'string' => $this->_query,
										'exc_time' => 0
									);

        return $stmt;
    }
	
	protected function execute()
	{
		$this->stmt->execute();
	}
	
	/**
     * Pass in a raw query and an array containing the parameters to bind to the prepaird statement.
     *
     * @param string $query      Contains a user-provided query.
     * @param array  $bindParams All variables to bind to the SQL statment.
     *
     * @return array Contains the returned rows from the query.
     */
    public function rawQuery($query, $bindParams = null)
    {
        $this->_query = filter_var($query, FILTER_SANITIZE_STRING);
        $stmt = $this->_prepareQuery();

        if (is_array($bindParams) === true)
		{
            $params = array(''); // Create the empty 0 index
            foreach ($bindParams as $prop => $val)
			{
                $params[0] .= $this->_determineType($val);
                array_push($params, $bindParams[$prop]);
            }

            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        }

        $stmt->execute();
        $this->reset();

        return $this->_dynamicBindResults($stmt);
    }
	
	/**
     *
     * @param string $query   Contains a user-provided select query.
     * @param int    $numRows The number of rows total to return.
     *
     * @return array Contains the returned rows from the query.
     */
    public function query($query, $numRows = null)
    {
        $this->_query = filter_var($query, FILTER_SANITIZE_STRING);
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->reset();

        return $this->_dynamicBindResults($stmt);
    }
	
	/**
     * A convenient SELECT * function.
     *
     * @param string  $tableName The name of the database table to work with.
     * @param integer $numRows   The number of rows total to return.
     *
     * @return array Contains the returned rows from the select query.
     */
    public function get($tableName, $numRows = null)
    {
        $this->_query = 'SELECT * FROM ' . $tableName;
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->reset();

        return $this->_dynamicBindResults($stmt);
    }
	
	/**
     *
     * @param <string $tableName The name of the table.
     * @param array $insertData Data containing information for inserting into the DB.
     *
     * @return boolean Boolean indicating whether the insert query was completed succesfully.
     */
    public function insert($tableName, $insertData)
    {
        $this->_query = 'INSERT into ' . $tableName;
        $stmt = $this->_buildQuery(null, $insertData);
        $stmt->execute();
        $this->reset();

        return ($stmt->affected_rows > 0 ? $stmt->insert_id : false);
    }
	
	/**
     * Update query. Be sure to first call the "where" method.
     *
     * @param string $tableName The name of the database table to work with.
     * @param array  $tableData Array of data to update the desired row.
     *
     * @return boolean
     */
    public function update($tableName, $tableData)
    {
        $this->_query = 'UPDATE ' . $tableName . ' SET ';

        $stmt = $this->_buildQuery(null, $tableData);
        $stmt->execute();
        $this->reset();

        return ($stmt->affected_rows > 0);
    }
	
	/**
     * Delete query. Call the "where" method first.
     *
     * @param string  $tableName The name of the database table to work with.
     * @param integer $numRows   The number of rows to delete.
     *
     * @return boolean Indicates success. 0 or 1.
     */
    public function delete($tableName, $numRows = null)
    {
        $this->_query = 'DELETE FROM ' . $tableName;

        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->reset();

        return ($stmt->affected_rows > 0);
    }
	
	/**
     * This method allows you to specify multipl (method chaining optional) WHERE statements for SQL queries.
     *
     * @uses $MySqliDb->where('id', 7)->where('title', 'MyTitle');
     *
     * @param string $whereProp  The name of the database field.
     * @param mixed  $whereValue The value of the database field.
     *
     * @return MysqliDb
     */
    public function where($whereProp, $whereValue)
    {
        $this->_where[$whereProp] = $whereValue;
        return $this;
    }
	

	/**
     * This methods returns the ID of the last inserted item
     *
     * @return integer The last inserted item ID.
     */
    public function getInsertId()
    {
        return $this->obj->insert_id;
    }

    /**
     * Escape harmful characters which might affect a query.
     *
     * @param string $str The string to escape.
     *
     * @return string The escaped string.
     */
    public function escape($str)
    {
        return $this->obj->real_escape_string($str);
    }
	
	/**
     * This method is needed for prepared statements. They require
     * the data type of the field to be bound with "i" s", etc.
     * This function takes the input, determines what type it is,
     * and then updates the param_type.
     *
     * @param mixed $item Input to determine the type.
     *
     * @return string The joined parameter types.
     */
    protected function _determineType($item)
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }
	
	/**
     * @param array $arr
     *
     * @return array
     */
    protected function refValues($arr)
    {
        //Reference is required for PHP 5.3+
        if( strnatcmp(phpversion(), '5.3') >= 0 )
		{
            $refs = array();
            foreach ($arr as $key => $value)
			{
                $refs[$key] = & $arr[$key];
            }
            return $refs;
        }
        return $arr;
    }
	
	private function set_error($errno = 0, $error = '')
	{
		try
		{
			if( strlen($error) > 0 || $errno > 0)
			$this->error[] = array( 'errno' => $errno, 'error' => $error );
			
			if($this->is_connected())
			{
				$this->error[] = array( 'errno' => $this->obj->errno, 'error' => $this->obj->error );
			}
		}
		catch( Exception $e )
		{
			$this->error_desc = $e->getMessage();
			$this->error_desc = -999;
		}
		if( $this->ThrowExceptions )
		{
			if( isset( $this->error_desc ) && $this->error_desc  != NULL )
			{
				throw new Exception( $this->error_desc . ' (' . __LINE__ . ')');
			}
		}
	}
	
	private function reset_error()
	{
		$this->error = array();
	}
}

?>