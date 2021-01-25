<?php
namespace ideamanagement\library;

use ideamanagement\library\site;

/**
 * Configurator Object
 * 
 * This is a loose defined object for now
 * Some Methods of the Global-Config should be standardized
 * Such AS:
 * 1) //Extract Domain Settings
 * 2)//Load Site settings - from Db or Session Cache
 * 
 * ++ Things we've loaded the general.inc.php
 *  2 methods used by controllers
 *  
 *  Decision is do we call these statically
 *  or 
 *  make this an appropriate object
 */

trait configurator_v4 {
	
	/**
	 * Splits the hostname (subdomain.rex-pro.com) into its parts returned as an array
	 * 
	 * @param string $hostname
	 * @return multitype:array
	 */
	static function domain_parts($hostname)
	{

		$hostname = str_ireplace("www.","",$hostname);
		$domain_parts = explode('.',$hostname);

		$domain_tld = array_pop($domain_parts);
		$domain_sld = array_pop($domain_parts);
		$domain_sub = "";
		if( count($domain_parts) > 0 )
		foreach($domain_parts as $sub)	$domain_sub .= ( $domain_sub != "" ? '.' : '').$sub;

		return array($domain_tld,$domain_sld,$domain_sub);
	}
	
	static function uri_parts($uri='')
	{
		$uri_array = array('subAction'=>array());
		
		if( $uri == '' )
		{
			$uri = $_SERVER['SCRIPT_URL'];
		}
		
		if( $uri[0] == '/' )
		{	$uri = substr($uri,1); }
		
		$exploded_uri = explode( '/',(isset($uri) && $uri != '' ? $uri : "index/index") );
		
		$uri_array['controller'] =  str_ireplace(".","",(!empty($exploded_uri[0]) ? array_shift($exploded_uri) : ''));
		$uri_array['page'] =  str_ireplace(".","",(!empty($exploded_uri[0]) ? array_shift($exploded_uri) : ''));
		$uri_array['action'] =  str_ireplace(".","",(!empty($exploded_uri[0]) ? array_shift($exploded_uri) : NULL));
		
		//Allow for default controllers if controller does not exit
		if( (!isset($uri_array['page']) || $uri_array['page'] == '') 
			&& !file_exists(CONTROLLER_DIR.$uri_array['controller'].'/') 
			&& file_exists(CONTROLLER_DIR.CONTROLLER_DEFAULT.'/'.$uri_array['controller'].TMPL_END) )
		{
			$uri_array['page'] = $uri_array['controller'];
			$uri_array['controller'] = CONTROLLER_DEFAULT;
		}			
		
		if( count( $exploded_uri ) > 0 )
			foreach( $exploded_uri as $subAction)
				$uri_array['subAction'][] = str_ireplace(".","",$subAction);
				
		return $uri_array;
	}
	
	static function SiteSettings($domain,$subdomain="")
	{
		unset($_SESSION['site_settings']);
		if( isset($_SESSION['site_settings']) && ($_SESSION['site_settings']['domain'] == $domain && $_SESSION['site_settings']['subdomain'] == $subdomain) )
		{
			return $_SESSION['site_settings'];
		} else {
			$site_settings = new site($domain,$subdomain);
			if(  $site_settings->id_site == null ||  $site_settings->id_client == null )
				return null;
			$_SESSION['site_settings'] = $site_settings->array;
		}
		
		return $_SESSION['site_settings'];
	}
}