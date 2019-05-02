<?php

/*
 * To be v4 compliant We need to seperate out all tables into classes
 * 
 * 
 */
//current columns
//id_domain 	id_site 	domain 	subdomain

class domain extends db_mysql_table
{
	protected $table_name = "domain";
	protected $primary_key = "id_domain";
	
	function __construct()
	{
		$this->dbo = DATASOURCE::DB()->_default();
		parent::__construct($this->dbo, $this->table_name);
		
		//func_get_arg(1)
		switch( func_num_args() )
		{
			case 1:
				if( is_int(func_get_arg(0)) )	  $this->getByID(func_get_arg(0));
				if( is_string(func_get_arg(0)) )  $this->getByDomain(func_get_arg(0));
				break;
				
			case 2:
				if( is_string(func_get_arg(0)) )  $this->getBy(func_get_arg(0),func_get_arg(1));
				break;
		}
	
	}
	
	
	function getByID( $id_domain)
	{
		$id_domain = (int) $id_domain;
		/*$query = "SELECT domain.*,site.name AS site_name
		 FROM {$this->domain_table}
		LEFT JOIN site USING (id_site)
		WHERE domain.id_domain={$id_domain}";
		*/
		$this->_select = "SELECT domain.*,site.name AS site_name ";
		$this->_join = " site USING (id_site) ";
		$result = $this->read("domain.id_domain={$id_domain}",'','',1)->fetch_array();
		
		if($result == null) return false;
		
		foreach( $result as $property => $value )
		{
			$this->$property = $value;
		}
		
		//$this->load($result);
	}
	
	function getByDomain($domain)
	{
		$domain_parts = explode('.',$domain);
		$domain_tld = array_pop($domain_parts);
		$domain_sld = array_pop($domain_parts);
		$domain_sub = "";
		
		if( count($domain_parts) > 0 )
			foreach($domain_parts as $sub)	$domain_sub .= ( $domain_sub != "" ? '.' : '').$sub;
		
		$result = $this->read("domain='{$domain_sld}.{$domain_tld}' AND subdomain='{$domain_sub}'",'','',1)->fetch_array( );
		
		if( defined("DEBUG_MODE") && DEBUG_MODE == "backtrace") {
			trigger_error(__METHOD__." in ".__FILE__." <pre>".print_r($result,true).'</pre>',E_USER_NOTICE);
		}
		
		if($result == null) return false;
		
		$this->load($result);
	}
	
	function getBy($id,$value=null)
	{
		return $this->getByDomain($id);	
	}
	
	

	public function load($settings_array)
	{
		foreach( $settings_array as $property => $value )
		{
			$this->$property = $value;
		}
	}
 
}
