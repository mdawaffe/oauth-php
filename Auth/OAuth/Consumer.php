<?php

require_once 'Auth/OAuth/Store.php';
require_once 'Auth/OAuth/Token.php';

class Auth_OAuth_Consumer
{

	/**
	 * Get a new request token.
	 */
	public static function requestToken ( $consumer_key, $user ) { }


	/**
	 * Exchange request token for a new access token.
	 *
	 * @param Auth_OAuth_Token request token to be exchanged for access token
	 */
	public static function accessToken ( Auth_OAuth_Token $request_token ) { }

}

?>
