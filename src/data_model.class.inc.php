<?php
namespace ideamanagement\library;

/**
 * data_model.class.inc.php
 * 
 * Defines basic required methods for any data resources
 * 
 * Allows for a basic function set to be used by other classes and  functions
 */

interface data_model
{
	function create($what);
	
	function read($what);
	
	function update($what);
	
	function delete($what);
}
