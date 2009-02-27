<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Store/SQLite.php';
require_once 'Auth/OAuth/TokenImpl.php';

class SQLiteStoreTest extends OAuth_TestCase {

	private $store;

	public function setUp() {
		$this->store = new Auth_OAuth_Store_SQLite( tempnam('/tmp', 'oauth_') );
	}

	public function testConsumerTokens() {

		// get a request token
		$request_token = $this->store->createConsumerRequestToken('6dc71f6');

		$this->assertNotNull($request_token);
		$this->assertNotNull($request_token->getToken());
		$this->assertNotNull($request_token->getSecret());
		$this->assertEquals('6dc71f6', $request_token->getConsumerKey());
		$this->assertEquals('request', $request_token->getType());
		$this->assertNull($request_token->getUser());
		$this->assertFalse($request_token->isAuthorized());


		// authorize the request token
		$auth_token = $this->store->authorizeConsumerRequestToken($request_token->getToken(), 42);

		$this->assertNotNull($auth_token);
		$this->assertEquals($request_token->getToken(), $auth_token->getToken());
		$this->assertEquals($request_token->getSecret(), $auth_token->getSecret());
		$this->assertEquals('6dc71f6', $auth_token->getConsumerKey());
		$this->assertEquals('request', $auth_token->getType());
		$this->assertEquals(42, $auth_token->getUser());
		$this->assertTrue($auth_token->isAuthorized());


		// exchange for access token
		$access_token = $this->store->createConsumerAccessToken($auth_token);

		$this->assertNotNull($access_token);
		$this->assertNotNull($access_token->getToken());
		$this->assertNotEquals($request_token->getToken(), $access_token->getToken());
		$this->assertNotNull($access_token->getSecret());
		$this->assertNotEquals($request_token->getSecret(), $access_token->getSecret());
		$this->assertEquals('6dc71f6', $access_token->getConsumerKey());
		$this->assertEquals('access', $access_token->getType());
		$this->assertEquals(42, $access_token->getUser());


		// should have two tokens total
		$tokens = $this->store->getConsumerTokens();
		$this->assertEquals(2, sizeof($tokens));
		$this->assertTrue(in_array($auth_token, $tokens));
		$this->assertTrue(in_array($access_token, $tokens));


		// delete request token, and ensure only the access token is left
		$this->store->deleteConsumerToken($request_token->getToken());

		$tokens = $this->store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($access_token, $tokens[0]);
	}

	public function testServerTokens() {
		$request_token = new Auth_OAuth_TokenImpl('937d73', '1a6501', '6dc71f6', 'request', 42);
		$access_token = new Auth_OAuth_TokenImpl('bcf425', 'aed227', '6dc71f6', 'request', 99);

		$this->store->addServerToken($request_token);
		$this->store->addServerToken($access_token);

		$tokens = $this->store->getServerTokens();
		$this->assertEquals(2, sizeof($tokens));
		$this->assertTrue(in_array($request_token, $tokens));
		$this->assertTrue(in_array($access_token, $tokens));

		$user_tokens = $this->store->getServerTokens(42);
		$this->assertEquals(1, sizeof($user_tokens));

		// delete token
		$this->store->deleteServerToken($request_token->getToken());
		$tokens = $this->store->getServerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($access_token, $tokens[0]);
	}
}

?>
