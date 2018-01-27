<?php

/**
 * Users Overall Class
 * Requires (for non system adming case) client 
 * List Clients users
 * Add/Edit a clients user
 */

class users extends db_mysql_row
{
	public $id_user;
	public $id_client;
	protected $user_table = "user";
	protected $dbo = '';
	
	function __construct($id_client = null)
	{
		$this->dbo = DATASOURCE::DB()->_default();
		if( DB_USER_TABLE ) $this->user_table = DB_USER_TABLE;
	}
	
	
	function setClientId($id_client)
	{
		$this->id_client = $id_client;
		
		throw error_log('Deprecated Use id_client', E_USER_DEPRECATED);
	}
	
	/**
	 * NOTICE: Why are we doing user class type queries here???
	 * 	That is what the user class is for!
	 * @param unknown $id_user
	 * @return user|boolean
	 */
	function getUser($id_user)
	{
		throw error_log('Deprecated Use user class instead', E_USER_DEPRECATED);
		
		$query = "SELECT * FROM {$this->user_table} 
					   LEFT JOIN {$this->user_table}_address USING (`id_user`)
					   LEFT JOIN {$this->user_table}_contact USING (`id_user`)
					   WHERE {$this->user_table}.id_user='{$id_user}';";
		$result = $this->dbo->query($query);
		if( $result)
		{
			$user = $result->fetch_array( MYSQLI_ASSOC );
			return new user($user);
		} else {
			return false;
		}
	}
	
	function getUsers($start=0,$limit=20)
	{
		$array_users = array();
	
		$query = "SELECT * FROM {$this->user_table} LIMIT {$start},{$limit}";
		$result = $this->dbo->query($query);
		while( ($user = $result->fetch_array( MYSQLI_ASSOC ) ) != null )
		{
			$array_users[$user['id_user']] = new user($user);
		}
	
		$result->free();
	
		return $array_users;
	}
}