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
	 * Return the normalised url for signature checks
	 *
	 * @return string
	 */
	public function getRequestUrl ();


	/**
	 * Return the complete parameter string for the signature check.
	 * All parameters are correctly urlencoded and sorted on name and value
	 * 
	 * @return array associative array of parameters
	 */
	public function getParams ();


	/**
	 * Get a parameter, value is always urlencoded.
	 * 
	 * @param string	parameter name
	 * @param boolean	urldecode	set to true to decode the value upon return
	 * @return string value		false when not found
	 */
	public function getParam ( $name, $urldecode );


	/**
	 * Set a parameter.
	 * 
	 * @param string	parameter name
	 * @param string	parameter value
	 * @param boolean	encoded	set to true when the values are already encoded
	 */
	public function setParam ( $name, $value, $encoded );


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

	public function getVersion();

	public function getConsumerKey ();

	public function getSignatureMethod();

	public function getSignature();

	public function getTimestamp();

	public function getNonce();

	public function getToken();

	public function getCallback();

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>