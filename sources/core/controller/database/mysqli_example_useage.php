<?php


/* BUILD IN QUERY */

/* Get rows from a table with/without conditions, with/without limition 'array(0,20): LIMIT 0,20' */
	//$db->where['username'] = 'ngocphuongnb';
	//$db->where['userid'] = array('REGEXP' => 1);
	//$db->where['userid'] = array('REGEXP' => 'ng');
	//$db->where['userid'] = array('IN' => '1,2,3');
	//$db->where['userid'] = array('IN' => array('1','2','3') );
	//$db->where['userid'] = array('>' => 1);
	//$db->where("`block_type` = 'admin' OR `block_id`=2");
	//$results = $db->get('users');
	
	/*
	$get_agrs = array(	'fieldKey'	=> 'config_name',
						'orderby'	=> 'config_id',
						'order'		=> 'ASC',
						'limit'		=> array(0,10), //'0,10',
						'fields'	=> 'config_name,config_id, config_value',
						'paged'		=> false
					);
	$config = $db->get('global_config');
	$config = $db->get('global_config', $get_agrs);
	$config = $db->get('global_config', 'config_name');
	$config = $db->get('global_config', 'config_name', '1,3');
	$config = $db->get('global_config', 'config_name', array(1,3));
	*/
	
	// number of rows
	// echo $db->found_row;
/* End get rows */

/* Update table rows, conditions like above */
	//$user = array('username' => 'Ngọc Phương update', 'image' => 'image 1', 'rights' =>' 111');
	//$db->update('users', $user);
	// affected rows
	// echo $db->affected_rows;
/* End update rows */

/* Insert row */
	//$user = array('username' => 'Ngọc Phương update', 'image' => 'image 1', 'rights' =>' 111');
	//$db->insert('users', $user);
/* End insert row */

/* Delete row */
	//$db->where['userid'] = 15;
	//$db->delete('users');
/* End delete row */


/* CUSTOM QUERY */

/* Custom query, return result in an array: $result */

	/********** Type 1, stmt **********/
	/*
	$userid = 1;
	$stmt = $db->prepare("SELECT `username` FROM `users` WHERE `userid`=?");
	$userid = 1;
	$stmt->bind_param('i', $userid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($username);
	while( $stmt->fetch() )
	{
		$result[] = $username;
	}
	*/
	
	
	/********** Type 2, query **********/
	/*
	$result = $db->query("SELECT * FROM `users` WHERE `userid`=1");
	while( $obj = $result->fetch_object() )
	{
		$result[] = $obj;
        printf ("%s (%s)\n", $obj->username, $obj->userid);
    }
	*/
	
/* End custom query */

/* Database query log */
	//n($db->log);
/* End database query log */

?>