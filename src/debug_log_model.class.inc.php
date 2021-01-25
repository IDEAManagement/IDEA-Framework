<?php
namespace ideamanagement\library;

use IteratorAggregate;

/**
 * Debug Log Model to ensure that public functions are the same for all set/get
 */


Abstract Class debug_log_model implements IteratorAggregate
{
	var $message_type = '';
	 
	protected  $array = array();
	
	function log($message)
	{
		
	}
	
	function getLast()
	{
		
	}
	
	function write_log()
	{
		
	}
	
	function __toString()
	{
		
	}
	
	public function  __get($name)
	{
		if( method_exists($this,"get$name") )
		{
			$method = "get$name";
			return $this->$method();
		} else {
			if( array_key_exists($name, $this->array) )
				return $this->array[$name];
			elseif( $name == 'array'){
				return $this->array;
			} else
				return null;
		}
	}
	
	public function  __set($name,$value)
	{
		if( method_exists($this,"set$name") )
		{
			$method = "set$name";
			$this->$method($value);
		} else {
			$this->array[$name] = $value;
		}
	}
	
	public function getIterator()
	{
		return new ArrayIterator($this->array);
	}
}