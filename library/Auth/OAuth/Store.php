<?php


/**
 * An OAuth store is responsible for handling the persistent storage and retrieval 
 * of OAuth Consumer and Server definitions as well as OAuth tokens.
 */
interface Auth_OAuth_Store
{

	/**
	 * Get an OAuth Consumer.
	 *
	 * @param string $consumer_key consumer key to get
	 * @return Auth_OAuth_Consumer
	 */
	public function getConsumer ( $consumer_key );


	/**
	 * Update an OAuth Consumer.  If a consumer does not already exist with the 
	 * consumer_key, a new one will be added.
	 *
	 * @param Auth_OAuth_Consumer $consumer consumer to update
	 */
	public function updateConsumer ( Auth_OAuth_Consumer $consumer );


	/**
	 * Delete an OAuth Consumer.
	 *
	 * @param string $consumer_key consumer key to delete
	 */
	public function deleteConsumer ( $consumer_key );


	/**
	 * Get an OAuth Consumer Token.
	 *
	 * @param string $token_key token to get
	 * @return Auth_OAuth_Token
	 */
	public function getConsumerToken ( $token_key );


	/**
	 * Get all OAuth Consumer Tokens issued to the specified user.
	 *
	 * @param int $user ID of user to get tokens for.  If null, tokens for all users will be retrieved.
	 * @return array of Auth_OAuth_Token objects
	 */
	public function getConsumerTokens ( $user );


	/**
	 * Create a new token for the specified consumer.
	 *
	 * @param string $consumer_key key of OAuth consumer to create token for
	 * @param string $type type of token to create (request, access)
	 * @return Auth_OAuth_Token
	 */
	public function createConsumerToken ( $consumer_key, $token_type );


	/**
	 * Delete a consumer token.
	 *
	 * @param string $token_key token to be deleted
	 */
	public function deleteConsumerToken ( $token_key );


	/**
	 * Authorize a consumer token.
	 *
	 * @param string $token_key token to be authorized
	 * @param int $user ID of user token is authorized for
	 */
	public function authorizeConsumerToken ( $token_key, $user );


	/**
	 * Get an OAuth Server.
	 *
	 * @param string $consumer_key consumer key of server to get
	 * @return Auth_OAuth_Server
	 */
	public function getServer ( $consumer_key );


	/**
	 * Update an OAuth Server.  If a server does not already exist with the 
	 * consumer_key, a new one will be added.
	 *
	 * @param Auth_OAuth_Server $server server to update
	 */
	public function updateServer ( Auth_OAuth_Server $server );


	/**
	 * Delete an OAuth Server.
	 *
	 * @param string $consumer_key consumer key to delete
	 */
	public function deleteServer ( $consumer_key );


	/**
	 * Get an OAuth Server Token.
	 *
	 * @param string $token_key token to get
	 * @return Auth_OAuth_Token
	 */
	public function getServerToken ( $token_key );


	/**
	 * Get all OAuth Server Tokens issued to the specified user.
	 *
	 * @param int $user ID of user to get tokens for.  If null, tokens for all users will be retrieved.
	 * @return array of Auth_OAuth_Token objects
	 */
	public function getServerTokens ( $user );


	/**
	 * Add a new server token.
	 *
	 * @param Auth_OAuth_Token $token server token to add
	 */
	public function addServerToken ( Auth_OAuth_Token $token );


	/**
	 * Delete a server token.
	 *
	 * @param string $token_key token to be deleted
	 */
	public function deleteServerToken ( $token_key );

}


?>
