<?php

require_once 'Auth/OAuth/Server.php';
require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/RequestImpl.php';
require_once 'Auth/OAuth/Signer.php';
require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/Store.php';
require_once 'Auth/OAuth/TokenImpl.php';

/**
 * Convenience methods used for handling requests from an OAuth Consumer.
 */
class Auth_OAuth_Server
{

	private $store;
	private $signer;


	/**
	 * Constructor for OAuth Server.
	 *
	 * @param Auth_OAuth_Store $store OAuth store implementation
	 */
	public function __construct($store) {
		$this->store = $store;
		$this->signer = new Auth_OAuth_Signer();
	}


	/**
	 * Handle request for a new Request Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public function requestToken ( Auth_OAuth_Request $request = null )
	{
		if ($request == null) {
			$request = new Auth_OAuth_RequestImpl();
		}

		$consumer = $this->store->getConsumer($request->getConsumerKey());
		if ( empty($consumer) ) {
			error_log('unknown consumer'); return;
		}

		$valid = $this->signer->verify($request, $consumer);
		if ( !$valid ) {
			error_log('invalid signature'); return;
		}

		// create a request token
		$token_key = Auth_OAuth_Util::generateKey();
		$token_secret = Auth_OAuth_Util::generateKey();
		$token = new Auth_OAuth_TokenImpl($token_key, $token_secret, $consumer->getKey(), 'request');
		$this->store->updateConsumerToken($token);

		$response = array(
			'oauth_token' => $token->getToken(),
			'oauth_token_secret' => $token->getSecret(),
		);

		Auth_OAuth_Util::sendResponse($response);
	}


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public function authorizeStart ( Auth_OAuth_Request $request = null ) { }


	/**
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public function authorizeFinish ( Auth_OAuth_Request $request = null ) { }


	/**
	 * Handle request for a new Access Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public function accessToken ( Auth_OAuth_Request $request = null )
	{
		if ($request == null) {
			$request = new Auth_OAuth_RequestImpl();
		}

		$consumer = $this->store->getConsumer($request->getConsumerKey());
		if ( empty($consumer) ) {
			error_log('unknown consumer'); return;
		}

		// ensure token is up to par
		$request_token = $this->store->getConsumerToken($request->getToken());
		if ($request_token->getType() != 'request') {
			error_log('Token is not a request token'); return;
		}
		if (!$request_token->isAuthorized()) {
			error_log('Token has not been authorized by the user'); return;
		}

		if ($consumer->getKey() != $request_token->getConsumerKey()) {
			error_log('token is bad (consumer key does not match)'); return;
		}

		$valid = $this->signer->verify($request, $consumer, $request_token);
		if ( !$valid ) {
			error_log('invalid signature'); return;
		}

		// create an access token
		$token_key = Auth_OAuth_Util::generateKey();
		$token_secret = Auth_OAuth_Util::generateKey();
		$access_token = new Auth_OAuth_TokenImpl($token_key, $token_secret, $consumer->getKey(), 'access', $request_token->getUser());
		$this->store->updateConsumerToken($access_token);

		// delete old request token
		$this->store->deleteConsumerToken($request_token->getToken());

		$response = array(
			'oauth_token' => $access_token->getToken(),
			'oauth_token_secret' => $access_token->getSecret(),
		);

		Auth_OAuth_Util::sendResponse($response);
	}

}

?>
