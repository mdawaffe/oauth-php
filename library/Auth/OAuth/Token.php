<?php

interface Auth_OAuth_Token
{

	/**
	 * Get the token type.
	 *
	 * @return string token type
	 */
	public function getType();


	/**
	 * Get the token value.
	 *
	 * @return string token value
	 */
	public function getToken();


	/**
	 * Get the token secret.
	 *
	 * @return string token secret
	 */
	public function getSecret();


	/**
	 * Get the consumer key associated with this token.
	 *
	 * @return string consumer key
	 */
	public function getConsumerKey();


	/**
	 * Get the ID of the user this token has been issued for.  Not all tokens 
	 * will have known users.
	 *
	 * @return int ID of user
	 */
	public function getUser();

	/**
	 * Has the token been authorized by the user.  This only makes sense for 
	 * request tokens.
	 *
	 * @return boolean if the token has been authorized
	 */
	public function isAuthorized();
}

?>
