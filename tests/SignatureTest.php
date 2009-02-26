<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/RequestImpl.php';
require_once 'Auth/OAuth/Signer.php';
require_once 'Auth/OAuth/SignatureMethod/PLAINTEXT.php';
require_once 'Auth/OAuth/SignatureMethod/HMAC_SHA1.php';
require_once 'Auth/OAuth/Store/ServerImpl.php';
require_once 'Auth/OAuth/TokenImpl.php';

class SignatureTest extends OAuth_TestCase {

	/**
	 * Test that the signature base string is created properly.
	 */
	public function testSignatureBaseString() {
		$signer = new Auth_OAuth_Signer();

		// test 1
		self::build_request('POST', 'http://testbed/test', array('n'=>'v'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('POST&http%3A%2F%2Ftestbed%2Ftest&n%3Dv', $signer->getSignatureBaseString($request));

		// test 2
		self::build_request('GET', 'http://example.com', array('n'=>'v'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('GET&http%3A%2F%2Fexample.com&n%3Dv', $signer->getSignatureBaseString($request));


		// test 3
		$params = array('oauth_version'=>'1.0', 'oauth_consumer_key'=>'dpf43f3p2l4k3l03',
					'oauth_timestamp'=>'1191242090', 'oauth_nonce'=>'hsu94j3884jdopsl',
					'oauth_signature_method'=>'PLAINTEXT', 'oauth_signature'=>'ignored');
		self::build_request('POST', 'https://photos.example.net/request_token', $params);
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('POST&https%3A%2F%2Fphotos.example.net%2Frequest_token&oauth_'
							.'consumer_key%3Ddpf43f3p2l4k3l03%26oauth_nonce%3Dhsu94j3884j'
							.'dopsl%26oauth_signature_method%3DPLAINTEXT%26oauth_timestam'
							.'p%3D1191242090%26oauth_version%3D1.0',
							$signer->getSignatureBaseString($request));

		// test 4
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0',
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh',
					'oauth_signature'=>'ignored', 'oauth_signature_method'=>'HMAC-SHA1');
		self::build_request('GET', 'http://photos.example.net/photos', $params);
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('GET&http%3A%2F%2Fphotos.example.net%2Fphotos&file%3Dvacation'
							.'.jpg%26oauth_consumer_key%3Ddpf43f3p2l4k3l03%26oauth_nonce%'
							.'3Dkllo9940pd9333jh%26oauth_signature_method%3DHMAC-SHA1%26o'
							.'auth_timestamp%3D1191242096%26oauth_token%3Dnnch734d00sl2jd'
							.'k%26oauth_version%3D1.0%26size%3Doriginal',
							$signer->getSignatureBaseString($request));
	}


	/**
	 * Test the PLAINTEXT signature method.
	 */
	public function testPlaintext() {
		self::build_request('POST', 'http://testbed/test', array());
		$request = new Auth_OAuth_RequestImpl();

		// test 1
		$consumer_secret = 'kd94hf93k423kf44';
		$token_secret = 'pfkkdhi9sl3r4s00';
		$signature = Auth_OAuth_SignatureMethod_PLAINTEXT::signature(null, $consumer_secret, $token_secret);
		$this->assertEquals('kd94hf93k423kf44&pfkkdhi9sl3r4s00', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_PLAINTEXT::verify(null, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_PLAINTEXT::verify(null, $consumer_secret, $token_secret, 'foo'));

		// test 2
		$consumer_secret = 'kd94h+93k%23kf44';
		$token_secret = 'pfkkdh/9sl3r4&00';
		$signature = Auth_OAuth_SignatureMethod_PLAINTEXT::signature(null, $consumer_secret, $token_secret);
		$this->assertEquals('kd94h%2B93k%2523kf44&pfkkdh%2F9sl3r4%2600', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_PLAINTEXT::verify(null, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_PLAINTEXT::verify(null, $consumer_secret, $token_secret, 'foo'));
	}


	/**
	 * Test the HMAC-SHA1 signature method.
	 */
	public function testHMAC() {
		$base_string = 'GET&http%3A%2F%2Fphotos.example.net%2Fphotos&file%3Dvacation.jpg%26oauth_consumer_key%3Ddpf'
			. '43f3p2l4k3l03%26oauth_nonce%3Dkllo9940pd9333jh%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestam'
			. 'p%3D1191242096%26oauth_token%3Dnnch734d00sl2jdk%26oauth_version%3D1.0%26size%3Doriginal';

		// test 1
		$consumer_secret = 'kd94hf93k423kf44';
		$token_secret = 'pfkkdhi9sl3r4s00';
		$signature = Auth_OAuth_SignatureMethod_HMAC_SHA1::signature($base_string, $consumer_secret, $token_secret);
		$this->assertEquals('tR3+Ty81lMeYAr/Fid0kMTYa/WM=', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($base_string, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($base_string, $consumer_secret, $token_secret, 'foo'));

		// test 2
		$consumer_secret = 'kd94h+93k%23kf44';
		$token_secret = 'pfkkdh/9sl3r4&00';
		$signature = Auth_OAuth_SignatureMethod_HMAC_SHA1::signature($base_string, $consumer_secret, $token_secret);
		$this->assertEquals('UCASL/lr96TeEnnnkTSTH2dJN40=', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($base_string, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($base_string, $consumer_secret, $token_secret, 'foo'));
	}


	/**
	 * Test the manually computed HMAC-SHA1 signature method.
	 */
	public function testManualHMAC() {
		$base_string = 'GET&http%3A%2F%2Fphotos.example.net%2Fphotos&file%3Dvacation.jpg%26oauth_consumer_key%3Ddpf'
			. '43f3p2l4k3l03%26oauth_nonce%3Dkllo9940pd9333jh%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestam'
			. 'p%3D1191242096%26oauth_token%3Dnnch734d00sl2jdk%26oauth_version%3D1.0%26size%3Doriginal';
		$key = 'kd94hf93k423kf44&pfkkdhi9sl3r4s00';
		$hmac = HMAC_Test::test_manual_hmac('sha1', $base_string, $key);

		$this->assertEquals('tR3+Ty81lMeYAr/Fid0kMTYa/WM=', base64_encode($hmac));
	}


	/**
	 * Test the full signing of a request. This is just to test that the 
	 * correct signature method is chosen. Generation of the signature base 
	 * string and signature itself is tested elsewhere more extensively.
	 */
	public function testRequestSigning() {
		$signer = new Auth_OAuth_Signer();

		// test 1
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0',
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh');
		self::build_request('GET', 'http://photos.example.net/photos', $params);
		$request = new Auth_OAuth_RequestImpl();
		$server = new Auth_OAuth_Store_ServerImpl('key', 'kd94hf93k423kf44');
		$server->setSignatureMethods( array('PLAINTEXT', 'HMAC-SHA1') );
		$token = new Auth_OAuth_TokenImpl('token', 'pfkkdhi9sl3r4s00');

		$signer->sign($request, $server, $token);
		$this->assertEquals('PLAINTEXT', $request->getSignatureMethod());
		$this->assertEquals('kd94hf93k423kf44&pfkkdhi9sl3r4s00', $request->getSignature());


		// test 2
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0',
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh');
		self::build_request('GET', 'http://photos.example.net/photos', $params);
		$request = new Auth_OAuth_RequestImpl();
		$server = new Auth_OAuth_Store_ServerImpl('key', 'kd94hf93k423kf44');
		$server->setSignatureMethods( array( 'INVALID-METHOD', 'HMAC-SHA1', 'PLAINTEXT') );
		$token = new Auth_OAuth_TokenImpl('token', 'pfkkdhi9sl3r4s00');

		$signer->sign($request, $server, $token);
		$this->assertEquals('HMAC-SHA1', $request->getSignatureMethod());
		$this->assertEquals('tR3+Ty81lMeYAr/Fid0kMTYa/WM=', $signer->getSignature($request, $server, $token));
	}


	public function testAuthorizationHeader() {
		$signer = new Auth_OAuth_Signer();

		// test 1
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0',
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh');
		self::build_request('GET', 'http://photos.example.net/photos', $params);
		$request = new Auth_OAuth_RequestImpl();
		$server = new Auth_OAuth_Store_ServerImpl('key', 'kd94hf93k423kf44');
		$server->setSignatureMethods( array('PLAINTEXT', 'HMAC-SHA1') );
		$token = new Auth_OAuth_TokenImpl('token', 'pfkkdhi9sl3r4s00');

		$signer->sign($request, $server, $token);

		$this->assertEquals('OAuth oauth_version="1.0", oauth_consumer_key="dpf43f3p2l4k3l03", '
			. 'oauth_token="nnch734d00sl2jdk", oauth_timestamp="1191242096", oauth_nonce="kllo9'
			. '940pd9333jh", oauth_signature_method="PLAINTEXT", oauth_signature="kd94hf93k423k'
			. 'f44%26pfkkdhi9sl3r4s00"', $signer->getAuthorizationHeader($request));
	}
}

class HMAC_Test extends Auth_OAuth_SignatureMethod_HMAC_SHA1 {
	public function test_manual_hmac ( $algorithm, $base_string, $key ) {
		return self::manual_hmac($algorithm, $base_string, $key);
	}
}

?>
