<?php

require_once 'Auth/OAuth/Token.php';

interface Auth_OAuth_Consumer
{

	/**
	 * Get the consumer key for this consumer.
	 */
	public function getKey();


	/**
	 * Get the consumer secret for this consumer.
	 */
	public function getSecret();


	/**
	 * Get a new request token.
	 */
	public static function requestToken ( $consumer_key, $user );


	/**
	 * Exchange request token for a new access token.
	 *
	 * @param Auth_OAuth_Token request token to be exchanged for access token
	 */
	public static function accessToken ( Auth_OAuth_Token $request_token );

}

?>
