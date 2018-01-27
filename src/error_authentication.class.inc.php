<?php
/*
 * Simply used to type cast Authentication Errors
 * 
 * Useful when we want to redirect a user who is invalid rather than break the app
 * 
 */

class error_authentication extends Exception{

	public function __construct ($message = "",$code = 0,$previous = NULL)
	{
		return $this;
	}
}