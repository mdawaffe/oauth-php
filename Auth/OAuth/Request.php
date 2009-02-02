<?php

/**
 * Object to parse an incoming OAuth request or prepare an outgoing OAuth request
 */
interface Auth_OAuth_Request
{

	/**
	 * Return the request method
	 *
	 * @return string
	 */
	public function getMethod ();


	/**
	 * Return the normalised url for signature checks.
	 *
	 * @return string
	 */
	public function getRequestUrl ();


	/**
	 * Get all request parameters.  Values are NOT url encoded.
	 *
	 * @return array associative array of parameters
	 */
	public function getParameters ();


	/**
	 * Return the complete parameter string for the signature check.
	 * All parameters are correctly urlencoded and sorted on name and value.
	 *
	 * @return array associative array of parameters
	 */
	public function getNormalizedParameterString ();


	/**
	 * Get a parameter. Return value is NOT url encoded.
	 *
	 * @param string $name parameter name
	 * @return string|boolean parameter value, or false when not found
	 */
	public function getParam ( $name );


	/**
	 * Set a parameter.  Value should NOT be url encoded.
	 *
	 * @param string	parameter name
	 * @param string	parameter value
	 */
	public function setParam ( $name, $value );


	/**
	 * Return the body of the OAuth request.
	 *
	 * @return string	null when no body
	 */
	public function getBody ();


	/**
	 * Set the body of the OAuth request.
	 *
	 * @param string	null when no body
	 */
	public function setBody ( $body );


	/**
	 * Get the OAuth version of the request.
	 *
	 * @param string OAuth version
	 */
	public function getVersion();


	/**
	 * Get the OAuth consumer key of the request.
	 *
	 * @param string OAuth consumer key
	 */
	public function getConsumerKey ();


	/**
	 * Get the OAuth signature method of the request.
	 *
	 * @param string OAuth signature method
	 */
	public function getSignatureMethod();


	/**
	 * Get the OAuth signature of the request.
	 *
	 * @param string OAuth signature
	 */
	public function getSignature();


	/**
	 * Get the OAuth timestamp of the request.
	 *
	 * @param string OAuth timestamp
	 */
	public function getTimestamp();


	/**
	 * Get the OAuth nonce of the request.
	 *
	 * @param string OAuth nonce
	 */
	public function getNonce();


	/**
	 * Get the OAuth token of the request.
	 *
	 * @param string OAuth token
	 */
	public function getToken();


	/**
	 * Get the OAuth callback URL of the request.
	 *
	 * @param string OAuth callback URL
	 */
	public function getCallback();

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>