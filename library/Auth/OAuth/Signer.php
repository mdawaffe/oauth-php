<?php

/**
 * An OAuth Signer is responsible for signing and verifying signatures of OAuth Requests.
 */
interface Auth_OAuth_Signer
{

	/**
	 * Sign our message in the way the server understands.
	 * Set the needed oauth_xxxx parameters.
	 * 
	 * @param Auth_OAuth_Request $request OAuth request
	 * @param int user			(optional) user that wants to sign this request
	 * @exception OAuthException when there is no oauth relation with the server
	 * @exception OAuthException when we don't support the signing methods of the server
	 */	
	public static function sign ( Auth_OAuth_Request $request,  $user = 0 );


	/**
	 * Build the Authorization header for an OAuth request.
	 * 
	 * @param Auth_OAuth_Request $request OAuth request
	 * @return string
	 */
	public static function getAuthorizationHeader ( Auth_OAuth_Request $request );


	/**
	 * See if the current HTTP request is signed with OAuth
	 * 
	 * @return boolean
	 */
	public static function requestIsSigned ();

	
	/**
	 * Verify the request
	 * 
	 * @param string token_type the kind of token needed, defaults to 'access' (false, 'access', 'request')
	 * @exception OAuthException thrown when the request did not verify
	 * @return int user_id associated with token (false when no user associated)
	 */
	public static function verify ( Auth_OAuth_Request $request, $token_type = 'access' );

}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */

?>