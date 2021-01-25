<?php
namespace ideamanagement\library;

/**
 * 
 * 
 */

 namespace library\site_settings

class site_settings extends db_mysql_table
{
	protected $table_name = "site_setting";
	protected $primary_key = "id_site";
	public $dbo;
	
	/**
	 * Values from site_settings table
	 * @var Array
	 */
	
	public $site_settings;
	
	function __construct($setting=NULL)
	{
		$this->dbo = DATASOURCE::DB()->_default();
		parent::__construct($this->dbo, $this->table_name);
		if( $setting == null ) return $this;
		if( is_array($setting) ) $this->_array($setting);
		if( is_int($setting) ) $this->_getId($setting);
	}
	
	function _getId($id_site)
	{
		$result = $this->find($id_site);
		while( ($setting=$result->fetch_array(MYSQLI_ASSOC)) != null )
		{	
			$property = $setting['name'];
			
			if( $setting['serialized'] == false )
			{
				$this->$property = $setting['value'];
			} else {
				$this->$property = unserialize($setting['value']);
			}
		}
		$result->free();
	}
}