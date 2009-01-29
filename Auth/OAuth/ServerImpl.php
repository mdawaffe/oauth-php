<?php

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


	/**
	 * Handle request for a new Request Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function requestToken ( Auth_OAuth_Request $request )
	{
		Auth_OAuth_Signer::verify($request, false);

		// create a request token
		$store = Auth_OAuth_Store::instance();
		$token = $store->createConsumerRequestToken($request->getConsumerKey());

		$response = array(
			'oauth_token' => $token->getToken(),
			'oauth_token_secret' => $token->getSecret(),
		);

		Auth_OAuth_Util::sendResonse($response);
	}


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function authorizeStart ( Auth_OAuth_Request $request )
	{

	}


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function authorizeFinish ( Auth_OAuth_Request $request )
	{

	}


	/**
	 * Handle request for a new Access Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public static function accessToken ( Auth_OAuth_Request $request )
	{
		Auth_OAuth_Signer::verify($request, 'request');

		$store = Auth_OAuth_Store::instance();
		$request_token = $store->getConsumerToken($request->getToken());

		if ($requst->getConsumerKey() != $request_token->getConsumerKey()) {
			// throw exception or send 401
		}

		if (!$request_token->isAuthorized()) {
			// throw exception or send 401
		}

		$store->deleteConsumerToken($request->getToken());

		// exchange for an access token
		$access_token = $store->createConsumerAccessToken($request_token);

		$response = array(
			'oauth_token' => $access_token->getToken(),
			'oauth_token_secret' => $access_token->getSecret(),
		);

		Auth_OAuth_Util::sendResonse($response);
	}

}

?>
