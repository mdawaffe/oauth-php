<?php

interface Auth_OAuth_Server
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


	/**
	 * Handle request for a new Request Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function requestToken ( Auth_OAuth_Request $request );


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function authorizeStart ( Auth_OAuth_Request $request );


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function authorizeFinish ( Auth_OAuth_Request $request );


	/**
	 * Handle request for a new Access Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function accessToken ( Auth_OAuth_Request $request );

}

?>
