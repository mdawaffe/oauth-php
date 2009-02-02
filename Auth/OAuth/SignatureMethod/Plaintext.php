<?php

require_once 'Auth/OAuth/SignatureMethod.php';
require_once 'Auth/OAuth/Util.php';


class Auth_OAuth_SignatureMethod_PLAINTEXT implements Auth_OAuth_SignatureMethod
{
	public function name ()
	{
		return 'PLAINTEXT';
	}


	/**
	 * Calculate the signature using PLAINTEXT.
	 *
	 * @param string base_string
	 * @param string consumer_secret
	 * @param string token_secret
	 * @return string
	 */
	function signature ( $base_string, $consumer_secret, $token_secret )
	{
		return Auth_OAuth_Util::encode($consumer_secret) . '&' . Auth_OAuth_Util::encode($token_secret);
	}


	/**
	 * Check if the provided signature corresponds to the one calculated for the base_string.
	 *
	 * @param string base_string	data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret
	 * @param string token_secret
	 * @param string signature		(urldecoded) signature
	 * @return string
	 */
	public function verify ( $base_string, $consumer_secret, $token_secret, $signature )
	{
		if (strpos($signature, '&') !== false) {
			list($c_secret, $t_secret) = explode('&', $signature, 2);
		} else {
			$c_secret = $signature;
			$t_secret = false;
		}

		$c_secret = Auth_OAuth_Util::decode($c_secret);
		$t_secret = Auth_OAuth_Util::decode($t_secret);

		return ($c_secret == $consumer_secret && $t_secret == $token_secret);
	}

}

/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>