<?php
namespace ideamanagement\library;

use db_mysql;

/**
 * Contains Accessors for the Default DB
 * 
 * AS well as functions to setup site specific DB from applied settings
 * for us in other included functions and objects
 */

global $db_main_read, $db_site,$db_test, $db_application;
class db_default
{
	
}


$db_main_read = new db_mysql(DATABASE\Root\host,\DATABASE\Root\user,\DATABASE\Root\pass,\DATABASE\Root\db_name);
function getDefaultDB(){ global $db_main_read; return $db_main_read;}

$db_site = "";
function getSiteDB()
{
	global $db_site;
	
	if( $db_site == "" )
	{
		$db_site = new db_mysql(DATABASE\Site\host,\DATABASE\Site\user,\DATABASE\Site\pass,\DATABASE\Site\db_name);
	}
	
	return $db_site;
}

$db_test = '';
function getTestDB()
{
	global $db_test;

	if( $db_test == '' )
	{
		$db_test = new db_mysql(DATABASE\Test\host,\DATABASE\Test\user,\DATABASE\Test\pass,\DATABASE\Test\db_name);
	}

	return $db_test;
}

function getApplicationDB()
{
	global $db_application;

	if( $db_application == '' )
	{
		$db_application = new db_mysql(DATABASE\Application\host,\DATABASE\Application\user,\DATABASE\Application\pass,\DATABASE\Application\db_name);
	}

	return $db_application;
}
