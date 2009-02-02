<?php

require_once 'Auth/OAuth/Signer.php';
require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/Util.php';

/**
 * An OAuth Signer is responsible for signing and verifying signatures of OAuth Requests.
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
	 * Add a signature method to be used by this signer.
	 *
	 * @param string $class name of a class that implements Auth_OAuth_SignatureMethod
	 */
	public function addSignatureMethod( $class )
	{
		$method_name = call_user_func( array($class, 'name') );
		$this->signature_methods[$method_name] = $class;
	}


	/**
	 * Sign our message in the way the server understands.
	 * Set the needed oauth_xxxx parameters.
	 *
	 * @param Auth_OAuth_Request $request OAuth request
	 * @param int user			(optional) user that wants to sign this request
	 * @exception OAuthException when there is no oauth relation with the server
	 * @exception OAuthException when we don't support the signing methods of the server
	 */
	public function sign ( Auth_OAuth_Request $request,  Auth_OAuth_Store_Consumer $consumer, Auth_OAuth_Token $token )
	{
	}


	public function getSignature ( Auth_OAuth_Request $request,  Auth_OAuth_Store_Consumer $consumer, Auth_OAuth_Token $token )
	{
		$signature_class = $this->getSignatureMethod($request->getSignatureMethod());
		if ($signature_class) {
			$signature = call_user_func(
				array($signature_class, 'signature'),
				$this->getSignatureBaseString($request),
				$consumer->getSecret(),
				$token->getSecret()
			);

			return $signature;
		}

	}


	/**
	 * Get the name of the correct Auth_OAuth_SignatureMethod implementation based on the provided string.
	 *
	 */
	private function getSignatureMethod ( $method )
	{
		if (array_key_exists($method, $this->signature_methods)) {
			return $this->signature_methods[$method];
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
	public function requestIsSigned ()
	{
	}


	/**
	 * Verify the request
	 *
	 * @param string token_type the kind of token needed, defaults to 'access' (false, 'access', 'request')
	 * @exception OAuthException thrown when the request did not verify
	 * @return int user_id associated with token (false when no user associated)
	 */
	public function verify ( Auth_OAuth_Request $request, $token_type )
	{
	}

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>