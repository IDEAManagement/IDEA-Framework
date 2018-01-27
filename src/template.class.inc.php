<?php

/**
 * Templating Engine for IDEA Framework
 * 
 * The plan
 * 	1) Simple Define tmpls to be pulled in as text
 * 	2) Allow Class Views to define their own overides
 */

class template 
{
	/**
	 * Containes the Keys and replacements
	 * @var array('KEY Name' => Value)
	 */
	var $replace = array();
	var $tpath = '';
	
	var $is_object = false;
	
	var $template_string = '';
	
	function __construct(&$object)
	{
		if( is_object($object) )
		{
			$this->is_object = true;
			$this->tpath = $object->template_path;
		} else {
			$this->tpath = $object;
		}
	}
	
	
	/**
	 * String Processing Methods
	 */
	
	
	function addTerm($key,$value)
	{
		$this->replace[$key] = $value;
	}
	
	function loadTerms($array)
	{
		foreach($array as $key => $value)
		{
			if( !is_array($value) )
				$this->addTerm($key, $value);
		}
	}
	
	function  loadFile()
	{
		$this->loadString( file_get_contents($this->tpath) );
	}
	
	function loadString($input)
	{
		$this->template_string = $input;
	}
	
	function replace()
	{
		foreach( $this->replace as $key => $value)
		{
			$key = strtoupper($key);
			$match[] = "/(\[\'{$key}\'\])/";
			$replace[] = "{$value}";
		}
		
		//First Pass replaces with values
		$first_pass = preg_replace($match,$replace,$this->template_string);
		//Second pass removes variables
		return preg_replace("/(\[\'.*?\'\])/","",$first_pass);
	}
}