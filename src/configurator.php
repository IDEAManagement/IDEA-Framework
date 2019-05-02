<?php

/**
 *  Introduction of Traits - update back to V3 to allow for skipping of V4 which is going to be rendered obsolete by V5
 *  V5 is a large leap from V3 so V3 will not be obsoleted.
 */
namespace ideamanagement\library\;
class configurator {

	static function application_include_autoload_register(){
		echo (DEBUG_MODE ==='trace' ? __LINE__.' '.__FILE__.' '.__FUNCTION__.'<br />'.PHP_EOL : '');
		spl_autoload_register(function($class_name){ @include_once INC_DIR.$class_name.'.class'.INC_END;	});
	}

	/**
	 * Splits the hostname (subdomain.rex-pro.com) into its parts returned as an array
	 * 
	 * @param string $hostname
	 * @param string reference $TLD
	 * @param string reference $SLD
	 * @param string reference $SUB
	 * @return multitype:array
	 */
	static function domain_parts($hostname, &...$params)
	{
	
		$hostname = str_ireplace("www.","",$hostname);
		$domain_parts = explode('.',$hostname);
	
		$domain_tld =array_pop($domain_parts);
		$domain_sld = array_pop($domain_parts);
		$domain_sub = "";
		if( count($domain_parts) > 0 )
		foreach($domain_parts as $sub)	$domain_sub .= ( $domain_sub != "" ? '.' : '').$sub;
	
		if( count($params) == 0 )
			return array($domain_tld,$domain_sld,$domain_sub);
		else {
			$params[0] = $domain_tld;
			$params[1] = $domain_sld;
			$params[2] = $domain_sub;
		}
			
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