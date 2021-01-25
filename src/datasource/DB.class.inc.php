<?php

namespace ideamanagement\library;

use ideamanagement\library\db_mysql;

class DB
{
	function _default()
	{
		return new db_mysql(\DATABASE\Root\host,\DATABASE\Root\user,\DATABASE\Root\pass,\DATABASE\Root\db_name);
	}
	
	function site()
	{
		return new db_mysql(\DATABASE\Site\host,\DATABASE\Site\user,\DATABASE\Site\pass,\DATABASE\Site\db_name);
	}
	
	function test()
	{
		return new db_mysql(\DATABASE\Test\host,\DATABASE\Test\user,\DATABASE\Test\pass,\DATABASE\Test\db_name);
	}
	
	function controller()
	{
		return new db_mysql(\DATABASE\Controller\host,\DATABASE\Controller\user,\DATABASE\Controller\pass,\DATABASE\Controller\db_name);
	}
}