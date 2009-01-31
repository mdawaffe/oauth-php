<?php

require_once 'Auth/OAuth/Store/Server.php';

class Auth_OAuth_ServerImpl implements Auth_OAuth_Server
{
	private $key;

	private $secret;

	private $request_token_uri;

	private $authorize_uri;

	private $access_token_uri;

	public function __construct ( $key, $secret );
	{

	}

	/**
	 * Get the consumer key that has been issued by this server.
	 *
	 * @return string consumer key
	 */
	public function getKey()
	{
		return $this->key;
	}


	/**
	 * Get the consumer secret that has been issued by this server.
	 *
	 * @return string consumer secret
	 */
	public function getSecret()
	{
		return $this->secret;
	}


	/**
	 * Get the request token endpoint URI.
	 *
	 * @return string request token endpoint URI
	 */
	public function getRequestTokenURI()
	{
		return $this->request_token_uri;
	}


	/**
	 * Get the authorize endpoint URI.
	 *
	 * @return string authorize endpoint URI
	 */
	public function getAuthorizeURI()
	{
		return $this->authorize_uri;
	}


	/**
	 * Get the access token endpoint URI.
	 *
	 * @return string access token endpoint URI
	 */
	public function getAccessTokenURI()
	{
		return $this->access_token_uri;
	}

}

?>
