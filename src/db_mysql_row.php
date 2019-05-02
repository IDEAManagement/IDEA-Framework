<?php

/**
 * Used to allow Row specifc settings to be utilized
 * 
 * Also allows it to be iterated over using only Array Data
 */

Abstract Class db_mysql_row extends db_mysql_table
{	
	protected $form = null;
	protected $overload = array();
	
	function _form()
	{
		//Lets Add in the main form object and add the overloads needed
		$this->_getColumnsExtended();
		if( $this->form == null )
		{
			$this->overload("_form_mysql");
			$this->form = new form($this);
		}
		return $this->form;
	}
	
	public function overload($class)
	{
		$this->overload[$class] = $class;
	}
	
	public function __call( $name, $arguments )
	{
		foreach( $this->overload as $class_name => $class)
		{
			if(method_exists($class, $name))
			{
				return call_user_func($class."::{$name}",$arguments,$this); //{$class}::{$name};
			}
		}
	}
}