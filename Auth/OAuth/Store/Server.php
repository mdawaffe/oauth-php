<?php

interface Auth_OAuth_Store_Server
{

	/**
	 * Get the consumer key that has been issued by this server.
	 *
	 * @return string consumer key
	 */
	public function getKey();


	/**
	 * Get the consumer secret that has been issued by this server.
	 *
	 * @return string consumer secret
	 */
	public function getSecret();


	/**
	 * Get the request token endpoint URI.
	 *
	 * @return string request token endpoint URI
	 */
	public function getRequestTokenURI();


	/**
	 * Get the authorize endpoint URI.
	 *
	 * @return string authorize endpoint URI
	 */
	public function getAuthorizeURI();


	/**
	 * Get the access token endpoint URI.
	 *
	 * @return string access token endpoint URI
	 */
	public function getAccessTokenURI();

}

?>
