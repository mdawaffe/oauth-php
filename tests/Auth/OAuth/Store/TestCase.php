<?php

require_once dirname(dirname(__FILE__)) . '/TestCase.php';
require_once 'Auth/OAuth/TokenImpl.php';
require_once 'Auth/OAuth/Store/ConsumerImpl.php';
require_once 'Auth/OAuth/Store/ServerImpl.php';

/**
 * Common base test case for OAuth store implementations.  Sub-classes of this 
 * test case need only implement the setUp() function to populate the $store 
 * class variable to be an instance of the OAuth Store implementation being 
 * tested.
 */
abstract class Auth_OAuth_Store_TestCase extends Auth_OAuth_TestCase {

	/**
	 * OAuth store implementation being tested.
	 *
	 * @var Auth_OAuth_Store
	 */
	protected $store;


	/**
	 * Common test case for OAuth Store implementations to test CRUD operations
	 * of consumer tokens.
	 */
	public function testConsumerTokens() {

		// add a token
		$request_token = new Auth_OAuth_TokenImpl('0f75d402', '6548e524', '871e8e03', 'request');
		$this->store->updateConsumerToken($request_token);

		$fetched_token = $this->store->getConsumerToken($request_token->getToken());
		$this->assertEquals($request_token, $fetched_token);

		$tokens = $this->store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($request_token, $tokens[0]);

		// update the token
		$request_token = new Auth_OAuth_TokenImpl('0f75d402', '6548e524', '871e8e03', 'request', 42, true);
		$this->store->updateConsumerToken($request_token);

		$tokens = $this->store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($request_token, $tokens[0]);

		// add another token or two, and fetch tokens for specific user
		$access_token_1 = new Auth_OAuth_TokenImpl('8e0e6cd5', '6548e524', '871e8e03', 'access', 99);
		$this->store->updateConsumerToken($access_token_1);
		$access_token_2 = new Auth_OAuth_TokenImpl('6a5c6460', '6548e524', '871e8e03', 'access', 99);
		$this->store->updateConsumerToken($access_token_2);

		$user_tokens = $this->store->getConsumerTokens(99);
		$this->assertEquals(2, sizeof($user_tokens));
		$this->assertTrue(in_array($access_token_1, $user_tokens));
		$this->assertTrue(in_array($access_token_2, $user_tokens));

		// delete a token
		$this->store->deleteConsumerToken($access_token_2->getToken());

		$tokens = $this->store->getConsumerTokens();
		$this->assertEquals(2, sizeof($tokens));
		$this->assertTrue(in_array($request_token, $tokens));
		$this->assertTrue(in_array($access_token_1, $tokens));
	}


	/**
	 * Common test case for OAuth Store implementations to test CRUD operations
	 * of server tokens.
	 */
	public function testServerTokens() {

		// add a token
		$request_token = new Auth_OAuth_TokenImpl('0f75d402', '6548e524', '871e8e03', 'request');
		$this->store->updateServerToken($request_token);

		$fetched_token = $this->store->getServerToken($request_token->getToken());
		$this->assertEquals($request_token, $fetched_token);

		$tokens = $this->store->getServerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($request_token, $tokens[0]);

		// add another token or two, and fetch tokens for specific user
		$access_token_1 = new Auth_OAuth_TokenImpl('8e0e6cd5', '6548e524', '871e8e03', 'access', 99);
		$this->store->updateServerToken($access_token_1);
		$access_token_2 = new Auth_OAuth_TokenImpl('6a5c6460', '6548e524', '871e8e03', 'access', 99);
		$this->store->updateServerToken($access_token_2);

		$user_tokens = $this->store->getServerTokens(99);
		$this->assertEquals(2, sizeof($user_tokens));
		$this->assertTrue(in_array($access_token_1, $user_tokens));
		$this->assertTrue(in_array($access_token_2, $user_tokens));

		// delete a token
		$this->store->deleteServerToken($access_token_2->getToken());

		$tokens = $this->store->getServerTokens();
		$this->assertEquals(2, sizeof($tokens));
		$this->assertTrue(in_array($request_token, $tokens));
		$this->assertTrue(in_array($access_token_1, $tokens));
	}
}

?>
