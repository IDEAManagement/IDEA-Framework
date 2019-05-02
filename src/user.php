<?php
/**
 * 
 * @author tadd
 *
 */

class user extends users
{
	
	var $id_user, $id_client, $full_name, $email;
	
	function __construct($user_data = null)
	{
		parent::__construct();
		
		if( is_array($user_data) )
		{
			foreach( $user_data as $property => $value)
				$this->$property = $value;
		} elseif( is_int($user_data) ) {
			$this->loadUser($user_data);
		}
	}
	
	private function loadUser($id_user)
	{
		
		$query = "SELECT `id_client`,`full_name`,`email` FROM {$this->user_table} WHERE id_user = '{$id_user}';";
		
		$result = $this->dbo->query($query);
		
		$user_data = $result->fetch_array( MYSQLI_ASSOC );
		
		foreach($user_data as $name => $value)
			$this->$name = $value;
	}
	
	/**
	 * The only reason these exist is for the __constructor
	 * @param $name
	*/ 
	public function  __get($name)
	{
		if( method_exists($this,"get$name") )
		{
			$method = "get$name";
			return $this->$method();
		} else {
			return $this->$name;
		}
	}
	
	
	/**
	 * The only reason these exist is for the __constructor
	 * @param $name
	 * @param $value
	 */
	public function  __set($name,$value)
	{
		if( method_exists($this,"set$name") )
		{
			$method = "set$name";
			$this->$method($value);
		} else {
			$this->$name = $value;
		}
	}
		
}