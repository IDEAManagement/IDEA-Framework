<?php 
/*
Zendesk PHP Library
zendesk.php
(c) 2011 Brian Hartvigsen
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

	* Redistributions of source code must retain the above copyright
	  notice, this list of conditions and the following disclaimer.
	* Redistributions in binary form must reproduce the above
	  copyright notice, this list of conditions and the following
	  disclaimer in the documentation and/or other materials provided
	  with the distribution.
	* Neither the name of the Zendesk PHP Library nor the names of its
	  contributors may be used to endorse or promote products derived
	  from this software without specific prior written permission.
*/
define('ZENDESK_OUTPUT_JSON', 1);

define('ZENDESK_ENTRIES', 'entries');
define('ZENDESK_FORUMS', 'forums');
define('ZENDESK_GROUPS', 'groups');
define('ZENDESK_MACROS', 'macros');
define('ZENDESK_ORGANIZATIONS', 'organizations');
define('ZENDESK_POSTS', 'posts');
define('ZENDESK_REQUESTS', 'requests');
define('ZENDESK_RULES', 'rules');
define('ZENDESK_SEARCH', 'search');
define('ZENDESK_TAGS', 'tags');
define('ZENDESK_TICKETS', 'tickets');
define('ZENDESK_TICKET_FIELDS', 'ticket_fields');
define('ZENDESK_UPLOADS', 'uploads'); // not currently supported!!!
define('ZENDESK_USERS', 'users');

// Aliases
define('ZENDESK_ATTACHMENTS', ZENDESK_UPLOADS);
define('ZENDESK_VIEWS', ZENDESK_RULES);

class zendesk
{
	public $curl = NULL;
	public $uri = 'https://[account].zendesk.com/api/v2/';
	
	function __construct($account,$username,$passkey)
	{
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($this->curl, CURLOPT_USERPWD,$username."/".$passkey);
		
		$this->uri = str_replace('[account]',$account,$this->uri);
	}
	
	private function _request($page, $args,$method="GET")
	{
		$url = $this->uri;
		$url .= $page.'.json';
		
		$query = "";
		if( isset( $args['query'] ) )
			foreach( $args['query'] as $key => $value)
				$query .= ($query == '' ? '?' : '&' )."{$key}={$value}";
		$url .= $query;				
		
		if( isset($args['json']) )
			$json = json_encode($args['json']);
		
		switch(strtoupper($method)){
			case "POST":
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
				break;
			case "GET":
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "GET");
				break;
			case "PUT":
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
			break;
				case "DELETE":
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
			default:
				break;
		}
		$headers = array('Content-type: application/json');
		if (isset($args['on-behalf-of']))
        	$headers[] = 'X-On-Behalf-Of: ' . $args['on-behalf-of'];

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 10);
		$data = json_decode( curl_exec($this->curl) );
		$info = curl_getinfo($this->curl);
		
		return $this->_response( $info['http_code'], $method, $data );
	}
	
	private function _response($http_code,$method, $data = '')
	{
		$this->last_code = $http_code;
		
		switch($http_code)
		{
			case 200:
			case 201:
				if( $method == 'GET')
					$this->response = $data; // Caching Response
				return $data;
				break;
			case 429: //Too Many Requests - Gets are cached though
				if( $method == "GET")
					return isset($this->response) ? $this->response : false;
				else 
					return false;
				break;
		}
	}
	
	function get($page,$args=array())
	{
		return $this->_request($page,$args,$method="GET");
	}
	
	function post($page,$args=array())
	{
		return $this->_request($page, $args, $method="POST");
	}
}
