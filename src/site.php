<?php

/**
 * Class for gettting site data
 * 
 */

 namespace library\site

Class site extends db_mysql_table
{
	protected $table_name = "site";
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
		
		if( is_string($setting) || func_num_args() == 2 ) $this->_getDomain($setting,func_get_arg(1));
	}
	
	/**
	 * Load Object with input, object data must be first level
	 * @param array $settings_array
	 */
	public function load( Array $settings_array = array() )
	{
		foreach( $settings_array as $property => $value )
		{
			$this->$property = $value;
		}
	}
	
	function _getId($id_site)
	{
// 		$site_data = $this->dbo->read("SELECT *
// 				FROM site 
// 				LEFT JOIN domain ON domain.id_domain = site.default_domain 
// 				WHERE site.id_site='{$id_site}';");
		$site_data = $this->find($id_site);
		if( $site_data == null )
			return false;
					
		$this->load($site_data->fetch_array(MYSQLI_ASSOC));
		$this->_getSettings($this->id_site); //Loads the next set of settings
	}
	
	private function _getSettings($id_site)
	{
		$this->site_settings = new site_settings($id_site);
		
		/*
		$result = $this->read("SELECT * FROM site_setting WHERE id_site='{$id_site}';");
		while( ($setting=$result->fetch_array(MYSQLI_ASSOC)) != null )
		{
			if( $setting['serialized'] == false )
			{
				$this->site_settings[$setting['name']] = $setting['value'];
			} else {
				$this->site_settings[$setting['name']] = unserialize($setting['value']);
			}
		}
		$result->free();
		*/
	}
	/**
	 * Returns Properties as an Array
	 * Add to the $secure array to stop from returning;
	 */
	public function __array()
	{	
		return $this->array;
	}

	private function _array($settings)
	{
		$this->load($settings);
	}
	
	/**
	 * Accepts an integer ID for the site
	 * @param integer $id
	 */
	private function _getDomain($domain,$subdomain=null)
	{	
		$myDomain = new domain($domain,$subdomain);
		
// 		$site_data = $this->dbo->read("SELECT * 
// 						 FROM domain LEFT JOIN site
// 						 	USING (id_site)
// 						 WHERE domain='{$domain}' 
// 						 	   AND subdomain='{$subdomain}'
// 						 	   ;");
// 		if( $site_data == null )
// 			return false;
		$this->load($myDomain->array);
		$this->_getId($myDomain->id_site);
		//$this->_getSettings($this->id_site); //Loads the next set of settings
	}
	
}