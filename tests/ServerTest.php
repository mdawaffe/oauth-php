<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/RequestImpl.php';
require_once 'Auth/OAuth/Server.php';
require_once 'Auth/OAuth/Store/InMemory.php';
require_once 'Auth/OAuth/Store/ConsumerImpl.php';

define('Auth_OAuth_TESTING', true);

/**
 * Tests of Auth_OAuth_Server
 */
class ServerTest extends PHPUnit_Extensions_OutputTestCase {


	/**
	 * Test that an HTTP POST request is parsed properly.
	 */
	public function testRequestToken() {
		$params = array('oauth_version'=>'1.0', 'oauth_consumer_key'=>'dpf43f3p2l4k3l03',
					'oauth_timestamp'=>'1191242090', 'oauth_nonce'=>'hsu94j3884jdopsl',
					'oauth_signature_method'=>'PLAINTEXT', 'oauth_signature'=>'kd94hf93k423kf44%26');
		OAuth_TestCase::build_request('POST', 'https://photos.example.net/request_token', $params);

		$store = new Auth_OAuth_Store_InMemory();

		$consumer = new Auth_OAuth_Store_ConsumerImpl('dpf43f3p2l4k3l03', 'kd94hf93k423kf44');
		$store->updateConsumer($consumer);

		$server = new Auth_OAuth_Server($store);

		$this->expectOutputRegex('/oauth_token=(.+)&oauth_token_secret=(.+)/');
		$server->requestToken();
	}

}

?>
