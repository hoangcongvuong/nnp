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
	private $config	= array(	'db_host'	=> 'localhost',
							 	'db_user'	=> NULL,
								'db_pass'	=> NULL,
								'db_name'	=> NULL,
								'db_port'	=> NULL,
								'charset'	=> 'utf8',
								'debug'		=> 1
							);
	public $logic = 'AND';
	static public $instance;
	
	private		$obj;
	private		$_returnObj = array(	'result'		=> array(),
										'affected_rows'	=> 0,
										'num_rows'		=> 0,
										'insert_id'		=> 0
									);
	protected	$_bind_params = array('');
	protected	$whereFromFunction = array();
	public		$where = array();
	public		$sql = '';
	public		$stmt;
	public		$num_rows = 0;
	public		$total_rows = 0;
	public		$affected_rows = 0;
	public		$insert_id = 0;
	public		$result = array();
	public		$set_cache = false;
	public		$db_cache_dir = VNP_ROOT;
	public		$prefix = '';
	public		$customCondition = array();
	
	public function __construct($db_config = array(), $auto_connect = true)
	{
		if( isset($db_config['prefix']) && $db_config['prefix'] != '' )
		$this->prefix = $db_config['prefix'] . '_';
		$this->db_cache_dir = VNP_ROOT . DATA_DIR . '/cache/db/';
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
			else
			{
				$this->obj->set_charset($this->config['charset']);
				$this->obj->query("SET NAMES utf8");
			}
			self::$instance = $this;
		}
	}
	
	protected function reset_pre_condition()
	{
		$this->where = array();
		$this->whereFromFunction = array();
		$this->customCondition = '';
	}
	
	protected function reset_result()
	{
		$this->_returnObj = array(	'result'		=> array(),
									'affected_rows'	=> 0,
									'num_rows'		=> 0,
									'insert_id'		=> 0,
									'status'		=> false
								);
		$this->result = array();
		$this->affected_rows = 0;
		$this->num_rows = 0;
		$this->insert_id = 0;
        $this->_bind_params = array(''); // Create the empty 0 index
		//$this->stmt->close();
        unset($this->sql);
	}
	
	/**
     * Reset states after an execution
     *
     * @return object Returns the current instance.
     */
    protected function reset()
    {
        $this->reset_pre_condition();
		$this->reset_result();		
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
	
	protected function buildQueryGetFields($fields = '*')
	{
		if( $fields == '*' ) return $fields;
		elseif( is_array($fields) )
		{
			$return = array();
			foreach( $fields as $field )
			{
				$return[] = '`' . trim($field) . '`';
			}
			return implode( ', ', $return );
		}
		elseif( $fields != '' )
		{
			$_fields = explode(',', $fields);
			$return = array();
			foreach( $_fields as $field )
			{
				$return[] = '`' . trim($field) . '`';
			}
			return implode( ', ', $return );
		}
	}
	
	public function where($customQuery)
	{
		$this->whereFromFunction[] = $customQuery;
	}
	
	protected function buildWhereConditions()
	{
		$where_conditions = $this->whereFromFunction;
		if( !empty($this->where) && is_array($this->where) )
		{
			$inClause = $regexpClause = '';
			foreach($this->where as $cdt_field => $cdt_value)
			{
				$compareClause = '=';
				if( is_array($cdt_value) )
				{
					foreach( $cdt_value as $cdtKey => $cdtValue )
					{
						$compareClause = $cdtKey;	
						$prepareValue = array();			
						if( strtoupper($compareClause) == 'IN' )
						{
							$inClause .= '`' . $cdt_field . '` IN (';
							if( !is_array($cdtValue) ) $cdtValue = explode(',', $cdtValue);
							foreach( $cdtValue as $val )
							{
								$_fieldType = $this->getFieldType($val);
								$this->_bind_params[0] .= $_fieldType;
								$_field_value_escaped = $this->escape($val);
								array_push($this->_bind_params, $_field_value_escaped);	
								$prepareValue[] = '?';
							}
							$inClause .= implode(',', $prepareValue);
							$inClause .= ')';
							
							if( !empty($inClause) ) $where_conditions[] = $inClause;
						}
						elseif( strtoupper($compareClause) == 'REGEXP' )
						{
							$_value = $this->escape($cdtValue, true);
							$regexpClause .= "(`" . $cdt_field . "`=? OR `" . $cdt_field . "` REGEXP '^" . $_value . "\\\,' OR `" . $cdt_field . "` REGEXP '\\\," . $_value . "\\\,' OR `" . $cdt_field . "` REGEXP '\\\," . $_value . "\$')";
							
							$_fieldType = $this->getFieldType($_value);
							$this->_bind_params[0] .= $_fieldType;
							$_field_value_escaped = $this->escape($_value);
							array_push($this->_bind_params, $_field_value_escaped);
							
							if( !empty($regexpClause) ) $where_conditions[] = $regexpClause;
						}
						else
						{
							$cdt_value = $cdtValue;
							$where_conditions[] = '`' . $cdt_field . '`' . $compareClause . ' ? ';
							$_fieldType = $this->getFieldType($cdt_value);
							$this->_bind_params[0] .= $_fieldType;
							$_field_value_escaped = $this->escape($cdt_value);
							array_push($this->_bind_params, $_field_value_escaped);	
						}
					}
				}
				else
				{
					$where_conditions[] = '`' . $cdt_field . '`' . $compareClause . ' ? ';
					$_fieldType = $this->getFieldType($cdt_value);
					$this->_bind_params[0] .= $_fieldType;
					$_field_value_escaped = $this->escape($cdt_value);
					array_push($this->_bind_params, $_field_value_escaped);	
				}
			}
		}
		if( !empty($where_conditions) )
		{			
			$__cdts = '(' . implode(' ' . $this->logic . ' ', $where_conditions) . ')';
			
			if( !empty( $this->customCondition ) )
			{
				$__customCdts = $this->customCondition;
				return ' WHERE ' . $__customCdts . ' ' . $__cdts;
			}
			else return ' WHERE ' . $__cdts;
		}
		else return '';
	}
	
	protected function setLimitRow($limitData = '')
	{
		if( !empty($limitData) && is_array($limitData) )
		{
			return ' LIMIT ' . $limitData[0] . ', ' . $limitData[1];
		}
		elseif( is_numeric($limitData) || ( is_string($limitData) && strlen($limitData) > 2) )
		{
			return ' LIMIT ' . $limitData;
		}
		else return '';
	}
	
	protected function getFieldType($value)
	{
		switch( gettype($value) )
		{
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
	
	public function escape($str, $escape = false)
    {
		$str = $this->nl2br( $str );
		
        if( $escape ) return $this->obj->real_escape_string($str);
		else return $str;
    }
	
	public function nl2br( $text, $replacement = '' )
	{
		if( empty( $text ) ) return '';
	
		return strtr( $text, array(
			"\r\n" => $replacement,
			"\r" => $replacement,
			"\n" => $replacement
		) );
	}
	
	/**
	 * nv_br2nl()
	 *
	 * @param string $text
	 * @return
	 */
	public function br2nl( $text )
	{
		if( empty( $text ) ) return '';
	
		return preg_replace( '/\<br(\s*)?\/?(\s*)?\>/i', chr( 13 ) . chr( 10 ), $text );
	}
	
	protected function _prepare()
	{
		if( $this->stmt = $this->obj->prepare($this->sql) ) $this->query_status = true;
		else $this->query_status = false;
		if( $this->config['debug'] == 1 )
		{
			$this->log['total_query']++;
			$this->log['query'][] = array('status' => $this->query_status, 'sql' => $this->sql, 'error' => $this->obj->error);
		}
		return $this->query_status;
	}
	
	protected function bind_param()
	{
		if( sizeof($this->_bind_params) > 1 )
		call_user_func_array( array($this->stmt, 'bind_param'), $this->refValues($this->_bind_params) );
	}
	
	protected function _bind_result($fieldKey = '')
	{
		$results = array();

		$meta = $this->stmt->result_metadata();
		$row = array();
		while( $field = $meta->fetch_field() )
		{
			$row[$field->name] = NULL;
			$parameters[] = &$row[$field->name];
		}
		
		call_user_func_array(array($this->stmt, 'bind_result'), $parameters);
		
		$this->stmt->store_result();

		while( $this->stmt->fetch() )
		{
			$x = array();
			foreach( $row as $key => $val )
			{
				//$x[$key] =  html_entity_decode(stripslashes($val), ENT_QUOTES,'UTF-8');
				$x[$key] =  stripslashes($val);
			}
			if( $fieldKey != '' && isset($x[$fieldKey ]) )
			$results[$x[$fieldKey ]] = $x;
			else array_push($results, $x);
		}
		$this->result = $results;
		//$this->stmt->close();
		return $results;
	}
	
	protected function execute()
	{
		$this->stmt->execute();
		$this->stmt->store_result();
	}
	
	protected function refValues($arr)
    {
        //Reference is required for PHP 5.3+
        if(strnatcmp(phpversion(), '5.3') >= 0 )
		{
            $refs = array();
            foreach($arr as $key => $value)
			{
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }
	
	protected function _query()
	{
		if( $this->_prepare() )
		{
			$this->bind_param();
			$this->execute();
			$this->affected_rows = $this->stmt->affected_rows;
			$this->insert_id = $this->stmt->insert_id;
			$this->num_rows = $this->stmt->num_rows;
			return true;
		}
		else return false;
	}
	
	public function prepare($sql)
	{
		if( $_stmt = $this->obj->prepare($sql) ) $this->query_status = true;
		else $this->query_status = false;
		if( $this->config['debug'] == 1 )
		{
			$this->log['total_query']++;
			$this->log['query'][] = array('status' => $this->query_status, 'string' => $sql, 'error' => $this->obj->error);
		}
		if( $this->query_status ) return $_stmt;
		else return false;
	}
	
	public function query($sql)
	{
		$_qr = $this->obj->query($sql);
		if( $this->obj->errno == 0 ) $this->query_status = true;
		else $this->query_status = false;
		
		if( $this->config['debug'] == 1 )
		{
			$this->log['total_query']++;
			$this->log['query'][] = array('status' => $this->query_status, 'string' => $sql, 'error' => $this->obj->error);
		}
		
		if( $this->query_status ) return $_qr;
		else return false;
	}
	
	/* Dynamic bind result and return result for get query */
	public function bind_result($stmt)
	{
		$stmt->execute();
		$parameters = array();
		$results = array();
	
		$meta = $stmt->result_metadata();
	
		$row = array();
		while( $field = $meta->fetch_field() )
		{
			$row[$field->name] = NULL;
			$parameters[] = & $row[$field->name];
		}
		
	
		call_user_func_array(array($stmt, 'bind_result'), $parameters);
		//$this->stmt->store_result();
	
		while($stmt->fetch())
		{
			$x = array();
			foreach($row as $key => $val)
			{
				$x[$key] = $val;
			}
			array_push($results, $x);
		}
		return $results;
	}
	
	public function get( $table, $args_or_fieldKey = NULL, $limit = array() )
	{
		$this->reset_result();
		$get_agrs = array(	'fieldKey'	=> '',
							'orderby'	=> '',
							'order'		=> 'DESC',
							'limit'		=> array(),
							'fields'	=> '*',
							'paged'		=> false
						);
		if( is_array($args_or_fieldKey) )
		{
			$get_agrs = array_merge($get_agrs, $args_or_fieldKey);
		}
		elseif( $args_or_fieldKey != '' )
		{
			$get_agrs['fieldKey']	= $args_or_fieldKey;
			$get_agrs['limit']		= $limit;
		}
		
		//$cacheString = hash( 'sha256', md5($table . serialize($limit) . serialize($field) . serialize($this->where)));
		//$cacheString = md5($table . serialize($get_agrs['limit']) . serialize($get_agrs['fields']) . serialize($this->where));
		$cacheString = $table . '_' . md5($table . serialize($get_agrs) . serialize($this->where)) . '.cache';
		
		if($this->set_cache && $result = $this->get_db_cache($cacheString))
		{
			$this->reset_pre_condition();
			return $result;
		}
		else
		{
			
			$get_agrs['paged'] ? $sql_calc_found_row = ' SQL_CALC_FOUND_ROWS ' : $sql_calc_found_row = '';
			
			$getFields = $this->buildQueryGetFields($get_agrs['fields']);
			$this->sql = 'SELECT ' . $sql_calc_found_row . $getFields . ' FROM `' . $this->prefix . $table . '`';
			$this->sql .= $this->buildWhereConditions();
			
			if( $get_agrs['orderby'] != '' )
			$this->sql .= ' ORDER BY `' . $get_agrs['orderby'] . '` ' . $get_agrs['order'];
			elseif( $get_agrs['fieldKey'] != '' ) $this->sql .= ' ORDER BY `' . $get_agrs['fieldKey'] . '` ' . $get_agrs['order'];
			
			$this->sql .= $this->setLimitRow($get_agrs['limit']);
			if( $this->_query() )
			{
				$this->_returnObj['affected_rows']	= $this->affected_rows;
				$this->_returnObj['insert_id']		= $this->insert_id;
				$this->_returnObj['num_rows']		= $this->num_rows;	
				
				
				if($get_agrs['paged'])
				{
					$result = $this->query('SELECT FOUND_ROWS()');
					$rowData = $result->fetch_row();
					$this->total_rows = $rowData[0];
				}
				else $this->total_rows = $this->num_rows;
				$this->_returnObj['total_rows']	= $this->total_rows;
				
				$this->_bind_result($get_agrs['fieldKey']);
				
				if( $this->stmt->num_rows > 0 )
				{
					$this->num_rows = $this->stmt->num_rows;
					$this->affected_rows = $this->stmt->affected_rows;
					
					$this->_returnObj['affected_rows']	= $this->stmt->affected_rows;
					$this->_returnObj['num_rows']		= $this->stmt->num_rows;
					$this->_returnObj['result'] = $this->result;
					$this->_returnObj['status'] = true;
				}
				else $this->_returnObj['result'] = $this->_returnObj['status'] = false;
			}
			else
			{
				$this->_returnObj['result'] = false;
				$this->_returnObj['status'] = false;
			}
			$this->reset_pre_condition();
			
			$_rt = (object) $this->_returnObj;
			if($this->set_cache) $this->save_db_cache($cacheString, $_rt);
			return $_rt;
		}
	}
	
	public function insert( $table, $tableData = array() )
	{
		$this->reset_result();
		$prepareField = $prepareValue = array();
		
		foreach( $tableData as $fieldName => $fieldValue )
		{
			$prepareField[] = '`' . $fieldName . '`';
			$prepareValue[] = '?';
			
			$_fieldType = $this->getFieldType($fieldValue);
			$this->_bind_params[0] .= $_fieldType;
			$_field_value_escaped = $this->escape($fieldValue);
			array_push($this->_bind_params, $_field_value_escaped);	
		}
		$this->sql = 'INSERT INTO `' . $this->prefix . $table . '` (' . implode(',', $prepareField) . ') VALUES (' . implode(',', $prepareValue) . ')';
		if( $this->_query() )
		{
			$this->_returnObj['affected_rows']	= $this->affected_rows;
			$this->_returnObj['insert_id']		= $this->insert_id;
			$this->_returnObj['num_rows']		= $this->num_rows;
			$this->_returnObj['status'] = true;
		}
		else $this->_returnObj['status'] = false;
		$this->reset_pre_condition();
		return (object) $this->_returnObj;
	}
	
	public function replace( $table, $tableData = array() )
	{
		$this->reset_result();
		$prepareField = $prepareValue = array();
		
		foreach( $tableData as $fieldName => $fieldValue )
		{
			$prepareField[] = '`' . $fieldName . '`';
			$prepareValue[] = '?';
			
			$_fieldType = $this->getFieldType($fieldValue);
			$this->_bind_params[0] .= $_fieldType;
			$_field_value_escaped = $this->escape($fieldValue);
			array_push($this->_bind_params, $_field_value_escaped);	
		}
		$this->sql = 'REPLACE INTO `' . $this->prefix . $table . '` (' . implode(',', $prepareField) . ') VALUES (' . implode(',', $prepareValue) . ')';
		if( $this->_query() )
		{
			$this->_returnObj['affected_rows']	= $this->affected_rows;
			$this->_returnObj['insert_id']		= $this->insert_id;
			$this->_returnObj['num_rows']		= $this->num_rows;
			$this->_returnObj['status'] = true;
		}
		else $this->_returnObj['status'] = false;

		$this->reset_pre_condition();
		return (object) $this->_returnObj;
	}
	
	public function update( $table, $tableData = array() )
	{
		$this->reset_result();
		$prepareField = $prepareValue = $prepareBind_param = array();
		
		foreach( $tableData as $fieldName => $fieldValue )
		{
			$prepareField[] = '`' . $fieldName . '`=?';
			
			$_fieldType = $this->getFieldType($fieldValue);
			$this->_bind_params[0] .= $_fieldType;
			$_field_value_escaped = $this->escape($fieldValue);
			array_push($this->_bind_params, $_field_value_escaped);	
		}
		$this->sql = 'UPDATE `' . $this->prefix . $table . '` SET ' . implode(',', $prepareField);
		$this->sql .= $this->buildWhereConditions();
		if( $this->_query() )
		{
			$this->_returnObj['affected_rows']	= $this->affected_rows;
			$this->_returnObj['insert_id']		= $this->insert_id;
			$this->_returnObj['num_rows']		= $this->num_rows;
			$this->_returnObj['status'] = true;
		}
		else $this->_returnObj['status'] = false;

		$this->reset_pre_condition();
		return (object) $this->_returnObj;
	}
	
	public function delete( $table )
	{
		$this->reset_result();
		$this->sql = 'DELETE FROM `' . $this->prefix . $table . '`';
		$this->sql .= $this->buildWhereConditions();
		if( $this->_query() )
		{
			$this->_returnObj['affected_rows']	= $this->affected_rows;
			$this->_returnObj['insert_id']		= $this->insert_id;
			$this->_returnObj['num_rows']		= $this->num_rows;
			$this->_returnObj['status'] = true;
		}
		else $this->_returnObj['status'] = false;

		$this->reset_pre_condition();
		return (object) $this->_returnObj;
	}
	
	public function dropColumn($tableName,$column = '')
	{
		global $db;
		
		$this->reset_result();
		if( is_array($column) )
		{
			$alterString = array();
			foreach($column as $_column)
			{
				$alterString[] = 'DROP `' . $_column . '`';
			}
			$alterString = implode(',', $alterString);
		}
		else $alterString = 'DROP `' . $column . '`';
		
		$this->sql = "ALTER TABLE `" . $db->prefix . $tableName . "` " . $alterString;
		if( $this->_query() )
		{
			$this->_returnObj['affected_rows']	= $this->affected_rows;
			$this->_returnObj['insert_id']		= $this->insert_id;
			$this->_returnObj['num_rows']		= $this->num_rows;
			$this->_returnObj['status'] = true;
		}
		else $this->_returnObj['status'] = false;

		$this->reset_pre_condition();
		return (object) $this->_returnObj;
	}
	
	public function addColumn($tableName, $column = '')
	{
		$this->reset_result();
		$dataType = array(	'text'		=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'password'	=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'image'		=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'file'		=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'hidden'	=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'select'	=> ' INT(11) NOT NULL AFTER `status`',
							'radio'		=> ' INT(11) NOT NULL AFTER `status`',
							'checkbox'	=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'number'	=> ' INT(11) NOT NULL AFTER `status`',
							'referer'	=> ' VARCHAR(255) NOT NULL AFTER `status`',
							'textarea'	=> ' MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL AFTER `status`',
							'html'		=> ' MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL AFTER `status`',
						);
		$dataType = array(	'text'		=> ' VARCHAR(255) NOT NULL',
							'password'	=> ' VARCHAR(255) NOT NULL',
							'image'		=> ' VARCHAR(255) NOT NULL',
							'file'		=> ' VARCHAR(255) NOT NULL',
							'hidden'	=> ' VARCHAR(255) NOT NULL',
							'select'	=> ' INT(11) NOT NULL',
							'radio'		=> ' INT(11) NOT NULL',
							'checkbox'	=> ' VARCHAR(255) NOT NULL',
							'number'	=> ' INT(11) NOT NULL',
							'referer'	=> ' VARCHAR(255) NOT NULL',
							'textarea'	=> ' MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL',
							'html'		=> ' MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL',
						);
		if( is_array($column) )
		{
			$alterString = array();
			foreach($column as $_column)
			{
				$alterString[] = 'ADD COLUMN `' . $_column['ct_field_name'] . '`' . $dataType[$_column['ct_field_type']];
			}
			$alterString = implode(',', $alterString);
		}
		else $alterString = 'ADD COLUMN `' . $column['ct_field_name'] . '`' . $dataType[$column['ct_field_type']];
		
		$this->sql = "ALTER TABLE `" . $this->prefix . $tableName . "` " . $alterString;
		if( $this->_query() )
		{
			$this->_returnObj['affected_rows']	= $this->affected_rows;
			$this->_returnObj['insert_id']		= $this->insert_id;
			$this->_returnObj['num_rows']		= $this->num_rows;
			$this->_returnObj['status'] = true;
		}
		else $this->_returnObj['status'] = false;

		$this->reset_pre_condition();
		return (object) $this->_returnObj;
	}
	
	public function nextInsertID($tableName)
	{
		$result = $this->query("SHOW TABLE STATUS LIKE '" . $this->prefix . $tableName . "'");
		$rowData = $result->fetch_assoc();
		return $rowData['Auto_increment'];
	}
		
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
	
	protected function get_db_cache($cacheFileName)
	{
		return get_cache($cacheFileName);
	}
	
	protected function save_db_cache($cacheFileName, $resource)
	{
		set_cache($cacheFileName, $resource, 'db');
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