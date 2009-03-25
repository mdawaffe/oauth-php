<?php
// call session_start first thing so it doesn't interfere with PHPUnit
session_start();

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/RequestImpl.php';
require_once 'Auth/OAuth/TokenImpl.php';
require_once 'Auth/OAuth/Server.php';
require_once 'Auth/OAuth/Store/InMemory.php';
require_once 'Auth/OAuth/Store/ConsumerImpl.php';

define('Auth_OAuth_TESTING', true);

/**
 * Tests of Auth_OAuth_Server
 */
class Auth_OAuth_ServerTest extends PHPUnit_Extensions_OutputTestCase {


	/**
	 * Test request for a request token.
	 */
	public function testRequestToken() {
		$params = array('oauth_version'=>'1.0', 'oauth_consumer_key'=>'dpf43f3p2l4k3l03',
					'oauth_timestamp'=>'1191242090', 'oauth_nonce'=>'hsu94j3884jdopsl',
					'oauth_signature_method'=>'PLAINTEXT', 'oauth_signature'=>'kd94hf93k423kf44%26');
		Auth_OAuth_TestCase::build_request('POST', 'https://photos.example.net/request_token', $params);

		$store = new Auth_OAuth_Store_InMemory();

		$consumer = new Auth_OAuth_Store_ConsumerImpl('dpf43f3p2l4k3l03', 'kd94hf93k423kf44');
		$store->updateConsumer($consumer);

		$server = new Auth_OAuth_Server($store);

		$this->expectOutputRegex('/oauth_token=(.+)&oauth_token_secret=(.+)/');
		$server->requestToken();

		// ensure the store got updated properly
		$tokens = $store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertNotNull($tokens[0]->getToken());
		$this->assertNotNull($tokens[0]->getSecret());
		$this->assertEquals('request', $tokens[0]->getType());
		$this->assertNull($tokens[0]->getUser());
		$this->assertFalse($tokens[0]->isAuthorized());
	}


	/**
	 * Test authorization request for token.
	 */
	public function testAuthorizeToken()
	{
		$store = new Auth_OAuth_Store_InMemory();
		$request_token = new Auth_OAuth_TokenImpl('6a5c6460', '59b13abc', 'dpf43f3p2l4k3l03', 'request');
		$store->updateConsumerToken($request_token);

		$params = array('oauth_token' => $request_token->getToken(), 'oauth_callback' => 'http://example.com/oauth_callback');
		Auth_OAuth_TestCase::build_request('GET', 'https://photos.example.net/authorize_token', $params);

		$server = new Auth_OAuth_Server($store);

		$server->authorizeStart();
		$server->authorizeFinish(42, true);

		// check tokens
		$tokens = $store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertEquals($request_token->getToken(), $tokens[0]->getToken());
		$this->assertEquals($request_token->getSecret(), $tokens[0]->getSecret());
		$this->assertEquals($request_token->getConsumerKey(), $tokens[0]->getConsumerKey());
		$this->assertEquals('request', $tokens[0]->getType());
		$this->assertEquals(42, $tokens[0]->getUser());
		$this->assertTrue($tokens[0]->isAuthorized());
	}


	/**
	 * Test request for an access token.
	 */
	public function testAccessToken() {
		$store = new Auth_OAuth_Store_InMemory();

		$consumer = new Auth_OAuth_Store_ConsumerImpl('dpf43f3p2l4k3l03', 'kd94hf93k423kf44');
		$store->updateConsumer($consumer);

		$request_token = new Auth_OAuth_TokenImpl('6a5c6460', '59b13abc', $consumer->getKey(), 'request', 42, true);
		$store->updateConsumerToken($request_token);

		$params = array(
			'oauth_version'=>'1.0',
			'oauth_consumer_key'=>'dpf43f3p2l4k3l03',
			'oauth_token' => $request_token->getToken(),
			'oauth_timestamp' => '1191242090',
			'oauth_nonce' => 'hsu94j3884jdopsl',
			'oauth_signature_method' => 'PLAINTEXT',
			'oauth_signature' => 'kd94hf93k423kf44%26' . $request_token->getSecret(),
		);
		Auth_OAuth_TestCase::build_request('POST', 'https://photos.example.net/access_token', $params);

		$server = new Auth_OAuth_Server($store);

		$this->expectOutputRegex('/oauth_token=(.+)&oauth_token_secret=(.+)/');
		$server->accessToken();

		// ensure the store got updated properly
		$tokens = $store->getConsumerTokens();
		$this->assertEquals(1, sizeof($tokens));
		$this->assertNotNull($tokens[0]->getToken());
		$this->assertNotEquals($request_token->getToken(), $tokens[0]->getToken());
		$this->assertNotNull($tokens[0]->getSecret());
		$this->assertNotEquals($request_token->getSecret(), $tokens[0]->getSecret());
		$this->assertEquals('access', $tokens[0]->getType());
		$this->assertEquals(42, $tokens[0]->getUser());
	}
}

?>
