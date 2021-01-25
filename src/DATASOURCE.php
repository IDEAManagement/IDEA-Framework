<?php

namespace ideamanagement\library;

use ideamanagement\library\datasource\db;

/**
 * In An Effort to build a more robust system
 * I am adding in the higher definition of a datasource
 * This is so we can use Databases (Mysql) and also
 * include RPC type sources, or HTTP sources
 * or many other API based data layers to gather information
 * 
 * Some have broken this down to make access simple in nominclature
 * such that new 'Datasource("Database",$connection_profile)' would be invoked
 * and return a Databse Connection with the given details and allow for
 * methods to be commone between all sources.
 * 
 * That is dumb from a purely power standpoint.  CRUD with a unique key would make sense
 * to define one simple abstraction layer between the source but in complex database design
 * this would be useless without also redefining query strings in which translations
 * would need to occour and the most efficiant way found to retreive or process the Data being
 * stored or accessed.  OR Better yet, know your data source and create good clean code and queries.
 * 
 * MySQL offer so many good Query features that limiting what it could do simply to be lazy
 * does not make sense.  Nor would it be resonable to compare it to NoSql, OR even RPC commands
 * 
 * This is merely a single access point into datasources to help ground them and their
 * appropriate settings.
 */

class DATASOURCE
{
	static function DB()
	{
		return new db();
	}
}