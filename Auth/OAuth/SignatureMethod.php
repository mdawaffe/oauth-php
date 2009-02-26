<?php

interface Auth_OAuth_SignatureMethod
{
	/**
	 * Return the name of this signature method
	 *
	 * @return string
	 */
	public function name();

	/**
	 * Return the signature for the given base_string.
	 *
	 * @param string base_string data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret consumer secret used to build signature
	 * @param string token_secret token secret used to build signature
	 * @return string calculated signature
	 */
	public function signature ( $base_string, $consumer_secret, $token_secret );

	/**
	 * Check if the provided signature is valid for the base_string and consumer and token secrets.
	 *
	 * @param string base_string	data to be signed, usually the base string, can be a request body
	 * @param string consumer_secret consumer secret used to build signature
	 * @param string token_secret token secret used to build signature
	 * @param string signature		(urldecoded) signature to verify
	 * @return boolean true if signature is valid, false otherwise
	 */
	public function verify ( $base_string, $consumer_secret, $token_secret, $signature );
}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>