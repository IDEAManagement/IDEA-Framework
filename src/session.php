<?php

/**
 * Classes and Methods for handling session data
 */

class session
{
	public $dbo = "";
	public $session_id = "";
	public $table = "session";
	
	function __construct($dbo,$session_id="")
	{
		$this->dbo = $dbo;
		if($session_id == "" )
		{
			if( isset( $_COOKIE['PHPSESSID']) )
				$this->session_id = $_COOKIE['PHPSESSID'];
			else
				$this->session_id = md5($_SERVER['SERVER_NAME'].$_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_PORT']);
		} else {
			$this->session_id = $session_id;
		}
		
		session_id($this->session_id);
		
		
		session_set_save_handler(
		    array(&$this, 'open'),
		    array(&$this, 'close'),
		    array(&$this, 'read'),
		    array(&$this, 'write'),
		    array(&$this, 'destroy'),
		    array(&$this, 'gc')
		);
		register_shutdown_function('session_write_close');
		
		session_start();
		
		
	}
	
	public function close ( )
	{
		return false;
	}
	
	public function destroy ( $session_id )
	{
		$result = $this->dbo->query("DELETE FROM {$this->table} WHERE session_id='{$session_id}'");
	}
	
	public function gc ( $maxlifetime )
	{
		$result = $this->dbo->query("DELETE FROM {$this->table} WHERE expiry < NOW()");
	}
	
	public function open ( $save_path , $name )
	{
		return true;
	}
	
	public function read ( $session_id )
	{
		$res = $this->dbo->read("SELECT data FROM {$this->table} WHERE session_id='{$session_id}';");
		if( $res )
			return $res['data'];
			
		return '';
	}
	
	public function write ( $session_id , $session_data )
	{
		$expiry = time() + get_cfg_var('session.gc_maxlifetime')-1;
		$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];
		$domain = $_SERVER['HTTP_HOST'];
		$last_REQUEST = serialize($_SERVER);
		$result = $this->dbo->query("REPLACE {$this->table} 
			SET session_id='$session_id',
				expiry='$expiry',
				data= '$session_data',
				ip='$IP_ADDRESS',
				domain='{$domain}'");
	   	return (bool) $result;
	}
}
