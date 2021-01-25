<?php
namespace ideamanagement\library;

use Exception;

/**
 * Implementing a Custom DB Error Class for any AJAX requests
 */


class database_error extends Exception
{
	const E_DUPLICATE = 1062;
	
	function __construct($message=null,$code=null,$previous=null)
	{
		$code_translation = $this->code_to_string($code);
		
		parent::__construct($code_translation.": ".$message, $code, $previous);
	}
	
	function code_to_string($code=null)
	{
		switch($code)
		{
			case self::E_DUPLICATE:
				return 'Non Unique Value';
				break;
				
			default: return "[{$code}]";
		}
	}
}