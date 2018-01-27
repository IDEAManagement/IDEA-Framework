<?php

/**
 * App Error v1 - Throws an error via Exception
 * @author Thaddaeus
 *
 * This is being deprecated as of version 4 to silently log errors
 * Logging for users should be doen via the user_error class that will be subdefined in v1 for 
 * backward compatability with warnings.
 */

class app_error extends Exception{

	public function __construct ($message = "",$code = 0,$previous = NULL)
	{
		exit($message);
	}
}
