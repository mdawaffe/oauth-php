<?php

require_once 'Auth/OAuth/Store/Consumer.php';

class Auth_OAuth_Store_ConsumerImpl implements Auth_OAuth_Store_Consumer
{
	private $key;

	private $secret;

	public function __construct ( $key, $secret )
	{
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * Get the consumer key for this consumer.
	 */
	public function getKey()
	{
		return $this->key;
	}


	/**
	 * Get the consumer secret for this consumer.
	 */
	public function getSecret()
	{
		return $this->secret;
	}

}

?>
