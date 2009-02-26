<?php

require_once 'Auth/OAuth/SignatureMethod.php';
require_once 'Auth/OAuth/Util.php';

class Auth_OAuth_SignatureMethod_HMAC_SHA1 implements Auth_OAuth_SignatureMethod
{

	/**
	 * Return the name of this signature method
	 *
	 * @return string
	 */
	public function name ()
	{
		return 'HMAC-SHA1';
	}


	/**
	 * Calculate the signature using HMAC-SHA1
	 *
	 * @param string base_string data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret consumer secret used to build signature
	 * @param string token_secret token secret used to build signature
	 * @return string calculated signature
	 */
	public function signature ( $base_string, $consumer_secret, $token_secret )
	{
		$hmac = self::buildHMAC($base_string, $consumer_secret, $token_secret);
		return base64_encode($hmac);
	}


	/**
	 * Build the HMAC, using the PHP hash functions if available.  Otherwise, build it manually.
	 *
	 * @param string $base_string string to be hashed
	 * @param string $consumer_secret consumer secret
	 * @param string $token_secret token secret
	 * @return string raw binary HMAC
	 */
	private function buildHMAC ( $base_string, $consumer_secret, $token_secret )
	{
		$key = Auth_OAuth_Util::encode($consumer_secret) . '&' . Auth_OAuth_Util::encode($token_secret);

		if (function_exists('hash_hmac'))
		{
			$hmac = hash_hmac("sha1", $base_string, $key, true);
		}
		else
		{
			$hmac = self::manual_hmac('sha1', $base_string, $key);
		}

		return $hmac;
	}


	/**
	 * Build the HMAC manually, when the PHP hash functions are not available.
	 * This function is copyright Andy Smith, 2007.
	 *
	 * @param string $algorithm name of selected hashing algorithm (i.e. "md5", "sha1", etc)
	 * @param string $base_string string to be hashed
	 * @param string $key secret key used for generating the HMAC
	 * @return string raw binary HMAC
	 */
	protected function manual_hmac ( $algorithm, $base_string, $key )
	{
		$blocksize	= 64;
		if (strlen($key) > $blocksize)
		{
			$key = pack('H*', $algorithm($key));
		}
		$key	= str_pad($key,$blocksize,chr(0x00));
		$ipad	= str_repeat(chr(0x36),$blocksize);
		$opad	= str_repeat(chr(0x5c),$blocksize);
		$hmac 	= pack(
					'H*',$algorithm(
						($key^$opad).pack(
							'H*',$algorithm(
								($key^$ipad).$base_string
							)
						)
					)
				);

		return $hmac;
	}


	/**
	 * Check if the provided signature is valid for the base_string and consumer and token secrets.
	 *
	 * @param string base_string	data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret consumer secret used to build signature
	 * @param string token_secret token secret used to build signature
	 * @param string signature		(urldecoded) signature to verify
	 * @return boolean true if signature is valid, false otherwise
	 */
	public function verify ( $base_string, $consumer_secret, $token_secret, $signature )
	{
		$decoded_signature = base64_decode($signature);
		$valid_signature = self::buildHMAC($base_string, $consumer_secret, $token_secret);

		return ($valid_signature == $decoded_signature);
	}
}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>