<?php

require_once 'Auth/OAuth/Token.php';

class Auth_OAuth_TokenImpl implements Auth_OAuth_Token
{

	private $type;

	private $token;

	private $secret;

	private $consumer_key;

	private $user;

	private $authorized;


	public function __construct( $token, $secret )
	{
		$this->token = $token;
		$this->secret = $secret;
	}

	/**
	 * Get the token type.
	 *
	 * @return string token type
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Get the token value.
	 *
	 * @return string token value
	 */
	public function getToken()
	{
		return $this->token;
	}


	/**
	 * Get the token secret.
	 *
	 * @return string token secret
	 */
	public function getSecret()
	{
		return $this->secret;
	}


	/**
	 * Get the consumer key associated with this token.
	 *
	 * @return string consumer key
	 */
	public function getConsumerKey()
	{
		return $this->consumer_key;
	}


	/**
	 * Get the ID of the user this token has been issued for.  Not all tokens
	 * will have known users.
	 *
	 * @return int ID of user
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Has the token been authorized by the user.  This only makes sense for
	 * request tokens.
	 *
	 * @return boolean if the token has been authorized
	 */
	public function isAuthorized()
	{
		return $this->authorized;
	}

}

?>
