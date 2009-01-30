<?php

require_once 'Auth/OAuth/SignatureMethod.php';
require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/Util.php';


class Auth_OAuth_SignatureMethod_PLAINTEXT implements Auth_OAuth_SignatureMethod
{
	public function name ()
	{
		return 'PLAINTEXT';
	}


	/**
	 * Calculate the signature using PLAINTEXT
	 * 
	 * @param OAuthRequest request
	 * @param string base_string
	 * @param string consumer_secret
	 * @param string token_secret
	 * @return string  
	 */
	function signature ( Auth_OAuth_Request $request, $base_string, $consumer_secret, $token_secret )
	{
		return Auth_OAuth_Util::encode(
			Auth_OAuth_Util::encode($consumer_secret) . '&' . Auth_OAuth_Util::encode($token_secret)
		);
	}


	/**
	 * Check if the request signature corresponds to the one calculated for the request.
	 * 
	 * @param OAuthRequest request
	 * @param string base_string	data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret
	 * @param string token_secret
	 * @param string signature		from the request, still urlencoded
	 * @return string
	 */
	public function verify ( Auth_OAuth_Request $request, $base_string, $consumer_secret, $token_secret, $signature )
	{
		$decoded_signature = Auth_OAuth_Util::decode($signature);

		if (strpos($decoded_signature, '&') !== false) {
			list($c_secret, $t_secret) = explode('&', $decoded_signature, 2);
		} else {
			$c_secret = $decoded_signature;
			$t_secret = false;
		}

		$c_secret = Auth_OAuth_Util::decode($c_secret);
		$t_secret = Auth_OAuth_Util::decode($t_secret);

		return ($c_secret == $consumer_secret && $t_secret == $token_secret);
	}
}

/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>