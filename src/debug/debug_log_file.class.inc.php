<?php

class debug_log_file extends debug_log_model
{
	var $message_type = 3;
	var $destination = '';
	var $prefix = '';
	
	function __construct($destination='', $prefix='')
	{
		$this->destination = $destination;
		$this->prefix = $prefix;
		return $this;
	}
	
	function log($message)
	{
		$date = new DateTime();
		$date = $date->format(DateTime::W3C);
		$this->message = $date."\t".$this->prefix.''.$message[0]."\r\n";
		$this->write_log();
	}

	function getLast()
	{

	}
	
	function write_log()
	{
		return error_log($this->message, $this->message_type, $this->destination);
	}

	function __toString()
	{

	}
}