<?php

require_once 'Auth/OAuth/SignatureMethod.php';
require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/Util.php';

class Auth_OAuth_SignatureMethod_HMAC_SHA1 implements Auth_OAuth_SignatureMethod
{
	public function name ()
	{
		return 'HMAC-SHA1';
	}


	/**
	 * Calculate the signature using HMAC-SHA1
	 * This function is copyright Andy Smith, 2007.
	 * 
	 * @param Auth_OAuth_Request request
	 * @param string base_string
	 * @param string consumer_secret
	 * @param string token_secret
	 * @return string  
	 */
	public function signature ( Auth_OAuth_Request $request, $base_string, $consumer_secret, $token_secret )
	{
		$hmac = self::buildHMAC($base_string, $consumer_secret, $token_secret);
		return base64_encode($hmac);
	}

	private function buildHMAC ( $base_string, $consumer_secret, $token_secret )
	{
		$key = Auth_OAuth_Util::encode($consumer_secret) . '&' . Auth_OAuth_Util::encode($token_secret);

		if (function_exists('hash_hmac'))
		{
			$hmac = hash_hmac("sha1", $base_string, $key, true);
		}
		else
		{
		    $blocksize	= 64;
		    $hashfunc	= 'sha1';
		    if (strlen($key) > $blocksize)
		    {
		        $key = pack('H*', $hashfunc($key));
		    }
		    $key	= str_pad($key,$blocksize,chr(0x00));
		    $ipad	= str_repeat(chr(0x36),$blocksize);
		    $opad	= str_repeat(chr(0x5c),$blocksize);
		    $hmac 	= pack(
		                'H*',$hashfunc(
		                    ($key^$opad).pack(
		                        'H*',$hashfunc(
		                            ($key^$ipad).$base_string
		                        )
		                    )
		                )
		            );
		}

		return $hmac;
	}


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
	public function verify ( Auth_OAuth_Request $request, $base_string, $consumer_secret, $token_secret, $signature )
	{
		$decoded_signature = base64_decode(Auth_OAuth_Util::decode($signature));
		$valid_signature = self::buildHMAC($base_string, $consumer_secret, $token_secret);

		return ($valid_signature == $decoded_signature);
	}
}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>