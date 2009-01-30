<?php

require_once 'Auth/OAuth/Signer.php';
require_once 'Auth/OAuth/Request.php';
require_once 'Auth/OAuth/Util.php';

/**
 * An OAuth Signer is responsible for signing and verifying signatures of OAuth Requests.
 */
class Auth_OAuth_SignerImpl implements Auth_OAuth_Signer
{


	public static function getSignatureBaseString ( Auth_OAuth_Request $request )
	{
		$base_string = Auth_OAuth_Util::encode($request->getMethod()) 
			. '&' . Auth_OAuth_Util::encode($request->getRequestUrl()) 
			. '&' . Auth_OAuth_Util::encode($request->getNormalizedParameterString());

		return $base_string;
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
	public static function sign ( Auth_OAuth_Request $request,  $user )
	{
	}

	public static function getSignature ( Auth_OAuth_Request $request, $user )
	{
	}

	/**
	 * Build the Authorization header for an OAuth request.
	 * 
	 * @param Auth_OAuth_Request $request OAuth request
	 * @return string
	 */
	public static function getAuthorizationHeader ( Auth_OAuth_Request $request )
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
	 * Verify the request
	 * 
	 * @param string token_type the kind of token needed, defaults to 'access' (false, 'access', 'request')
	 * @exception OAuthException thrown when the request did not verify
	 * @return int user_id associated with token (false when no user associated)
	 */
	public static function verify ( Auth_OAuth_Request $request, $token_type )
	{
	}

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>