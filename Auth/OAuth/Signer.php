<?php

require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/Store/Consumer.php';
require_once 'Auth/OAuth/Store/Server.php';

/**
 * An OAuth Signer is responsible for signing and verifying signatures of OAuth
 * Requests.
 */
class Auth_OAuth_Signer
{

	private $signature_methods;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		require_once 'Auth/OAuth/SignatureMethod/Plaintext.php';
		$this->addSignatureMethod('Auth_OAuth_SignatureMethod_PLAINTEXT');

		require_once 'Auth/OAuth/SignatureMethod/HMAC_SHA1.php';
		$this->addSignatureMethod('Auth_OAuth_SignatureMethod_HMAC_SHA1');
	}


	/**
	 * Get the name of the correct Auth_OAuth_SignatureMethod implementation
	 * based on the provided string.
	 *
	 */
	private function getSignatureMethodClass ( $method )
	{
		if (array_key_exists($method, $this->signature_methods)) {
			return $this->signature_methods[$method];
		}
	}


	/**
	 * Add a signature method to be used by this signer.
	 *
	 * @param string $class name of a class that implements Auth_OAuth_SignatureMethod
	 */
	public function addSignatureMethod ( $class )
	{
		$method_name = call_user_func( array($class, 'name') );
		$this->signature_methods[$method_name] = $class;
	}


	/**
	 * Get signature base string for a request.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 * @return string signature base string
	 */
	public function getSignatureBaseString ( Auth_OAuth_Request $request )
	{
		$base_string = Auth_OAuth_Util::encode($request->getMethod())
			. '&' . Auth_OAuth_Util::encode($request->getRequestUrl())
			. '&' . Auth_OAuth_Util::encode($request->getNormalizedParameterString());

		return $base_string;
	}


	/**
	 * Sign our message in the way the server understands.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 * @param int user			(optional) user that wants to sign this request
	 * @exception OAuthException when there is no oauth relation with the server
	 * @exception OAuthException when we don't support the signing methods of the server
	 */
	public function sign ( Auth_OAuth_Request $request,  Auth_OAuth_Store_Server $server, Auth_OAuth_Token $token )
	{
		foreach ($server->getSignatureMethods as $method) {
			if ($this->getSignatureMethodClass($method)) {
				$signature_method = $method;
			}
		}
		if ($signature_method) {
			$request->setParam('oauth_signature_method', $signature_method);
			$signature = $this->getSignature($request, $consumer, $token);
			$request->setParam('oauth_signature', $signature);
		}
	}


	/**
	 * Build the OAuth Signature for a request.
	 *
	 * @return string OAuth signature
	 */
	public function getSignature ( Auth_OAuth_Request $request,  Auth_OAuth_Store_Server $server, Auth_OAuth_Token $token )
	{
		$signature_method = $request->getSignatureMethod();
		$signature_class = $this->getSignatureMethodClass($signature_method);

		if ($signature_class) {
			$signature = call_user_func(
				array($signature_class, 'signature'),
				$this->getSignatureBaseString($request),
				$server->getSecret(),
				$token->getSecret()
			);

			return $signature;
		}

	}


	/**
	 * Build the Authorization header for an OAuth request.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 * @return string
	 */
	public function getAuthorizationHeader ( Auth_OAuth_Request $request )
	{
	}


	/**
	 * See if the current HTTP request is signed with OAuth
	 *
	 * @return boolean
	 */
	public static function requestIsSigned ()
	{
	}


	/**
	 * Verify the request signature.
	 *
	 * @param string token_type the kind of token needed, defaults to 'access' (false, 'access', 'request')
	 * @exception OAuthException thrown when the request did not verify
	 * @return int user_id associated with token (false when no user associated)
	 */
	public function verify ( Auth_OAuth_Request $request, Auth_OAuth_Store_Consumer $consumer, Auth_OAuth_Token $token )
	{
		$valid = false;

		$signature_method = $request->getSignatureMethod();
		$signature_class = $this->getSignatureMethodClass($signature_method);

		if ($signature_class) {
			$valid = call_user_func(
				array($signature_class, 'verify'),
				$this->getSignatureBaseString($request),
				$consumer->getSecret(),
				$token->getSecret(),
				$request->getSignature()
			);
		}

		return $valid;
	}

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>