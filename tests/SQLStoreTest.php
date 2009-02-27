<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Store/SQLite.php';

class SQLStoreTest extends OAuth_TestCase {

	public function testSQLStore() {
		$store = new Auth_OAuth_Store_SQLite( tempnam('/tmp', 'oauth_') );

		// get a request token
		$request_token = $store->createConsumerRequestToken('6dc71f6');

		$this->assertNotNull($request_token);
		$this->assertNotNull($request_token->getToken());
		$this->assertNotNull($request_token->getSecret());
		$this->assertEquals('6dc71f6', $request_token->getConsumerKey());
		$this->assertEquals('request', $request_token->getType());
		$this->assertNull($request_token->getUser());
		$this->assertFalse($request_token->isAuthorized());


		// authorize the request token
		$auth_token = $store->authorizeConsumerRequestToken($request_token->getToken(), 42);

		$this->assertNotNull($auth_token);
		$this->assertEquals($request_token->getToken(), $auth_token->getToken());
		$this->assertEquals($request_token->getSecret(), $auth_token->getSecret());
		$this->assertEquals('6dc71f6', $auth_token->getConsumerKey());
		$this->assertEquals('request', $auth_token->getType());
		$this->assertEquals(42, $auth_token->getUser());
		$this->assertTrue($auth_token->isAuthorized());


		// exchange for access token
		$access_token = $store->createConsumerAccessToken($auth_token);

		$this->assertNotNull($access_token);
		$this->assertNotNull($access_token->getToken());
		$this->assertNotEquals($request_token->getToken(), $access_token->getToken());
		$this->assertNotNull($access_token->getSecret());
		$this->assertNotEquals($request_token->getSecret(), $access_token->getSecret());
		$this->assertEquals('6dc71f6', $access_token->getConsumerKey());
		$this->assertEquals('access', $access_token->getType());
		$this->assertEquals(42, $access_token->getUser());


		// should have two tokens total
		$tokens = $store->getConsumerTokens();
		$this->assertEquals(2, sizeof($tokens));
		$this->assertTrue(in_array($auth_token, $tokens));
		$this->assertTrue(in_array($access_token, $tokens));


		// delete request token, and ensure only the access token is left
		$store->deleteConsumerToken($request_token->getToken());

		$tokens = $store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($access_token, $tokens[0]);
	}

}

?>
