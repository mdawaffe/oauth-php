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

	/**
	 * OAuth Store.
	 *
	 * @var Auth_OAuth_Store
	 */
	private $store;


	/**
	 * OAuth Signer
	 *
	 * @var Ath_OAuth_Signer
	 */
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
	 * @param Auth_OAuth_Request $request OAuth request, if null current HTTP request will be used
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
	 * Begin the request token authorization process.
	 *
	 * Nota bene: this stores the current token, and callback in the $_SESSION
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 */
	public function authorizeStart ( Auth_OAuth_Request $request = null )
	{
		if ($request == null) {
			$request = new Auth_OAuth_RequestImpl();
		}

		// ensure token is up to par
		// TODO: what if a token is not included in the request? OAuth Spec says to prompt user
		$request_token = $this->store->getConsumerToken($request->getToken());
		if ($request_token->getType() != 'request') {
			error_log('Token is not a request token'); return;
		}

		@session_start();
		$_SESSION['Auth_OAuth_Token'] = array(
			'token' => $request->getToken(),
			'callback' => $request->getCallback(),
		);

	}


	/**
	 * Complete the request token authorization process.  If the user
	 * authorized the token, this will update the token accordingly
	 * and return the user to the OAuth consumer.  If the user did not
	 * authorize the request token, it will be deleted.
	 *
	 * If an OAuth callback URL was specified in the request, the user will be
	 * automatically redirected there.  If a callback URL was not specified,
	 * this function will return a boolean indicating whether the request was
	 * authorized.  The application using this library should then instruct the
	 * user on how to proceed manually (ie. "close this window" or "return to
	 * consumer application").
	 *
	 * @param int $user ID of user this token was associated with
	 * @param boolean $authorized whether or not the user authorized the request token
	 * @param Auth_OAuth_Request $request OAuth request, if null current HTTP request will be used
	 * @return boolean whether or not the request was authorized.  If an OAuth
	 * 		callback URL was included in the request, the user will be redirected
	 * 		and this function will not return.
	 */
	public function authorizeFinish ( $user, $authorized, Auth_OAuth_Request $request = null )
	{
		@session_start();
		if ( empty($_SESSION['Auth_OAuth_Token']) ) {
			error_log('oauth session data lost'); return;
		}

		$token = $this->store->getConsumerToken( $_SESSION['Auth_OAuth_Token']['token'] );
		if ( empty($token) ) {
			error_log('unknown token'); return;
		}

		if ($authorized) {
			$authorized_token = new Auth_OAuth_TokenImpl($token->getToken(), $token->getSecret(), $token->getConsumerKey(), $token->getType(), $user, true);
			$this->store->updateConsumerToken($authorized_token);

			if ( $callback = $_SESSION['Auth_OAuth_Token']['callback'] ) {
				Auth_OAuth_Util::redirect($callback, array('oauth_token' => $token->getToken()));
			}
		} else {
			$this->store->deleteConsumerToken($token->getToken());
		}

		return $authorized;
	}


	/**
	 * Handle request for a new Access Token.
	 *
	 * @param Auth_OAuth_Request $request OAuth request, if null current HTTP request will be used
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

		// send response
		$response = array(
			'oauth_token' => $access_token->getToken(),
			'oauth_token_secret' => $access_token->getSecret(),
		);

		Auth_OAuth_Util::sendResponse($response);
	}

}

?>
