<?php

namespace ideamanagement\library;

use debug_log_file;
use debug_log_sapi;
use debug_log_sendmail;
use debug_log_systemlog;

/**
 * This Class should be used to aid in application debugging
 * 
 * Used to Catch all errors and report them to a system rather than to a users
 * 
 * This will also allow for run-time debuging rather than ?debug=true outputting data
 * @author Thaddaeus
 *
 *
 * USES bool error_log ( string $message [, int $message_type = 0 [, string $destination [, string $extra_headers ]]] )
 */

class debug_log
{
	const DEBUG_SYSTEMLOG = 0;
	const DEBUG_SENDMAIL = 1;
	const DEBUG_FILE = 3;
	const DEBUG_SAPI = 4;
	
	var $debug_mode;
	var $debug_obj;
	
	function __construct($log_type = 0)
	{
		$this->debug_mode = (int) $log_type;
		
		switch( $log_type )
		{
			case self::DEBUG_SYSTEMLOG:
				include_once 'debug/debug_log_systemlog.class.inc.php';
				$this->debug_obj = new debug_log_systemlog();
				break;
			
			case self::DEBUG_SENDMAIL:
				include_once 'debug/debug_log_sendmail.class.inc.php';
				$this->debug_obj = new debug_log_sendmail();
				break;
				
			case self::DEBUG_FILE:
				include_once 'debug/debug_log_file.class.inc.php';
				$this->debug_obj = new debug_log_file( func_get_arg(1),func_get_arg(2) );
				break;
			
			case self::DEBUG_SAPI:
				include_once 'debug/debug_log_sapi.class.inc.php';
				$this->debug_obj = new debug_log_sapi();
				break;
		}
		
	}

	public function __call( $name , $arguments  )
	{
		return $this->debug_obj->$name($arguments);
	}
	
	
// 	function __toString();
// 	function __debugInfo();
}
