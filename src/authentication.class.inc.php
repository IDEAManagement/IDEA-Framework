<?php

namespace ideamanagement\library;

use ideamanagement\library\app_error;
use ReflectionClass;

/**
 * Base Authentication Functions
 * 
 * This gets extended by another class which
 * sets up the db pointers and processing as required
 */

class authentication
{
	const MD5 = "md5";
	const SHA = "sha1";
	
	public $token_col = "username";
	public $secret_col = "password";
	public $primary_key = "id_user";
	
	protected $hash = authentication::MD5;
	
	public $dbo = null;
	public $type = "mysql";
	public $table = "";
	
	static function secret()
	{	
		$reflect = new ReflectionClass('authentication');
		$props = $reflect->getDefaultProperties();
		return $props['secret_col'];
	}
	
	static function hash($input)
	{
		$reflect = new ReflectionClass('authentication');
		$props = $reflect->getDefaultProperties();
		return call_user_func($props['hash'],$input);
	}
	/**
	 * Name and Key are equivalant to username password
	 * but chosen other words as this could pertain to more.
	 * 
	 * @param string $name
	 * @param string $key
	 * @return bool $success
	 */
	public function auth($token, $secret)
	{
		switch($this->type)
		{
			case 'mysql':
				return $this->auth_mysql($token, $secret);
				break;
		}
	}
	
	/**
	 * Use a sql query to check for and authentiate user
	 */
	public function auth_mysql($token, $secret)
	{
		if( $this->table == "" )
		{
			throw new app_error("No Table Selected for Authentication.");
		}
				
		$token = $this->dbo->real_escape_string($token);
		$secret = call_user_func($this->hash,$secret);
		
		$this->dbo->query("INSERT INTO `audit_login` SET `class`='".__CLASS__."::".__METHOD__."', `token`='{$token}', `secret`='{$secret}', `_SERVER`='".$this->dbo->real_escape_string(var_export($_SERVER,true))."', `_REQUEST`='".$this->dbo->real_escape_string(var_export($_REQUEST,true))."', `URI`=''");
		
		$query = "SELECT count(*) as matches, {$this->primary_key} FROM {$this->table} WHERE {$this->token_col}='$token' AND {$this->secret_col}='{$secret}';";
		$result = $this->dbo->query($query);
		if( $result == false )
		{
			return false;
		}
		$response = $result->fetch_array( MYSQLI_ASSOC );
		$result->free();
		if( $response['matches'] == 0) return false;
		if( $response['matches'] > 1) throw new app_error("Too Many Matches {$response['matches']})");
		if( $response['matches'] == 1 ) return $response[$this->primary_key];
	}
}
