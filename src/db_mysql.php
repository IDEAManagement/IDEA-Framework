<?php
namespace ideamanagement\library;

use \mysqli;

/**
 * Object for accessing a mysql database
 * 
 */

class db_mysql extends mysqli
{
	private $db = '';
	
	public $db_name = ''; /* Used mostly for table operations */
	function __construct($host = "",$user="",$pass="",$db_name="")
	{
		$this->db_name = $db_name;
		parent::__construct($host,$user,$pass,$db_name);
	}
	
	function __destruct(){	$this->close();	}
	function getdbname(){	return $this->db_name;	}
	
	
	/**
	 * Deprecated!
	 * @param unknown $query
	 * @return Array
	 */
	function read($query)
	{
		if( defined("DEBUG_MODE") && DEBUG_MODE == true) {
			$_debug = debug_backtrace(); $debug = array();
			foreach( $_debug as $entry => $array){	$debug[$entry] = $array['line']." ".$array['file'];	}
			trigger_error(__METHOD__." in ".__FILE__." <pre>".print_r($debug,true).'</pre>',  E_USER_DEPRECATED);
		}
		
		$result = $this->query($query);
		$return = $result->fetch_array( MYSQLI_ASSOC);
		$result->free();
		
		return $return;
	}
	
	function create($row){ }
	function update($row){ }
	function delete($row){ }
	
	/**
	 * Audit
	 */
	function audit($type=null,$query){
	
		try {
			$this->dbo->query("INSERT INTO `audit` SET `type`='".$this->dbo->real_escape_string($type)."', `query`='".$this->dbo->real_escape_string($query)."', `who`='".$this->dbo->real_escape_string($_SESSION['user'])."';");
		} catch (Exception $e)  {
			if( DEBUG_MODE == true ) {
				echo "<pre>INSERT AUDIT FAILED: Check that db audit exists</pre>";
			}
		}
	}

}
