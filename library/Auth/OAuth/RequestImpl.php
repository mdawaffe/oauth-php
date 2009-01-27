<?php

/**
 * Object to parse an incoming OAuth request or prepare an outgoing OAuth request
 */
class Auth_OAuth_RequestImpl implements Auth_OAuth_Request
{

	private $parameters;


	public function __construct()
	{
		$this->parameters = array();
	}

	/**
	 * Return the request method
	 * 
	 * @return string
	 */
	public function getMethod ()
	{

	}


	/**
	 * Return the normalised url for signature checks
	 *
	 * @return string
	 */
	public function getRequestUrl ()
	{

	}


	/**
	 * Return the complete parameter string for the signature check.
	 * All parameters are correctly urlencoded and sorted on name and value
	 * 
	 * @return array associative array of parameters
	 */
	public function getParams ()
	{
		return $this->parameters;
	}


	/**
	 * Get a parameter, value is always urlencoded.
	 * 
	 * @param string	parameter name
	 * @param boolean	urldecode	set to true to decode the value upon return
	 * @return string value		false when not found
	 */
	public function getParam ( $name, $urldecode )
	{
		if (array_key_exists($name, $this->parameters)) {
			$value = $this->parameters[$name];

			if ($urldecode) {
				// TODO
			}

			return $value;
		}

	}


	/**
	 * Set a parameter.
	 * 
	 * @param string	parameter name
	 * @param string	parameter value
	 * @param boolean	encoded	set to true when the values are already encoded
	 */
	public function setParam ( $name, $value, $encoded )
	{
		if ($encoded) {
			// TODO
		}

		$this->parameters[$name] = $value;
	}


	/**
	 * Return the body of the OAuth request.
	 * 
	 * @return string	null when no body
	 */
	public function getBody ()
	{

	}


	/**
	 * Set the body of the OAuth request.
	 * 
	 * @param string	null when no body
	 */
	public function setBody ( $body )
	{
	}

	public function getVersion()
	{
		return $this->getParam('oauth_version', true);
	}

	public function getConsumerKey ()
	{
		return $this->getParam('oauth_consumer_key', true);
	}

	public function getSignatureMethod()
	{
		return $this->getParam('oauth_signature_method', true);
	}

	public function getSignature()
	{
		return $this->getParam('oauth_signature', true);
	}

	public function getTimestamp()
	{
		return $this->getParam('oauth_timestamp', true);
	}

	public function getNonce()
	{
		return $this->getParam('oauth_nonce', true);
	}

	public function getToken()
	{
		return $this->getParam('oauth_token', true);
	}

	public function getCallback()
	{
		return $this->getParam('oauth_callback', true);
	}

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>