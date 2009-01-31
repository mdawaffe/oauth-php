<?php

interface Auth_OAuth_Store_Consumer
{

	/**
	 * Get the consumer key for this consumer.
	 */
	public function getKey();


	/**
	 * Get the consumer secret for this consumer.
	 */
	public function getSecret();

}

?>
