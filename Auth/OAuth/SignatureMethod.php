<?php

require_once 'Auth/OAuth/Request.php';

interface Auth_OAuth_SignatureMethod
{
	/**
	 * Return the name of this signature
	 * 
	 * @return string
	 */
	public function name();
	
	/**
	 * Return the signature for the given request
	 * 
	 * @param Auth_OAuth_Request request
	 * @param string base_string
	 * @param string consumer_secret
	 * @param string token_secret
	 * @return string  
	 */
	public function signature ( Auth_OAuth_Request $request, $base_string, $consumer_secret, $token_secret );

	/**
	 * Check if the request signature corresponds to the one calculated for the request.
	 * 
	 * @param Auth_OAuth_Request request
	 * @param string base_string	data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret
	 * @param string token_secret
	 * @param string signature		from the request, still urlencoded
	 * @return string
	 */
	public function verify ( Auth_OAuth_Request $request, $base_string, $consumer_secret, $token_secret, $signature );
}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>