<?php
namespace ideamanagement\library;

trait configurator_v5 {

	
	static function application_include_autoload_register(){
		echo (DEBUG_MODE ==='trace' ? __LINE__.' '.__FILE__.' '.__FUNCTION__.'<br />'.PHP_EOL : '');
		spl_autoload_register(function($class_name){ @include_once INC_DIR.$class_name.'.class'.INC_END;	});
	}
	
	/**
	 * 
	 * @param string $hostname
	 * @param string reference $TLD
	 * @param string reference $SLD
	 * @param string reference $SUB
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
}