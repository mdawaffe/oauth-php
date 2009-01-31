<?php

require_once dirname(__FILE__) . '/common.php';
require_once 'Auth/OAuth/RequestImpl.php';
require_once 'Auth/OAuth/SignerImpl.php';
require_once 'Auth/OAuth/SignatureMethod/PLAINTEXT.php';
require_once 'Auth/OAuth/SignatureMethod/HMAC_SHA1.php';

class SignatureTest extends PHPUnit_Framework_TestCase {	

	public function testGetBaseString() {
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('n'=>'v'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('POST&http%3A%2F%2Ftestbed%2Ftest&n%3Dv', Auth_OAuth_SignerImpl::getSignatureBaseString($request));
		
		OAuthTestUtils::build_request('GET', 'http://example.com', array('n'=>'v'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('GET&http%3A%2F%2Fexample.com&n%3Dv', Auth_OAuth_SignerImpl::getSignatureBaseString($request));
		
		
		$params = array('oauth_version'=>'1.0', 'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 
					'oauth_timestamp'=>'1191242090', 'oauth_nonce'=>'hsu94j3884jdopsl',
					'oauth_signature_method'=>'PLAINTEXT', 'oauth_signature'=>'ignored');
		OAuthTestUtils::build_request('POST', 'https://photos.example.net/request_token', $params);			
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('POST&https%3A%2F%2Fphotos.example.net%2Frequest_token&oauth_'
							.'consumer_key%3Ddpf43f3p2l4k3l03%26oauth_nonce%3Dhsu94j3884j'
							.'dopsl%26oauth_signature_method%3DPLAINTEXT%26oauth_timestam'
							.'p%3D1191242090%26oauth_version%3D1.0', 
							Auth_OAuth_SignerImpl::getSignatureBaseString($request));
							
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0', 
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh',
					'oauth_signature'=>'ignored', 'oauth_signature_method'=>'HMAC-SHA1');
		OAuthTestUtils::build_request('GET', 'http://photos.example.net/photos', $params);			
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('GET&http%3A%2F%2Fphotos.example.net%2Fphotos&file%3Dvacation'
							.'.jpg%26oauth_consumer_key%3Ddpf43f3p2l4k3l03%26oauth_nonce%'
							.'3Dkllo9940pd9333jh%26oauth_signature_method%3DHMAC-SHA1%26o'
							.'auth_timestamp%3D1191242096%26oauth_token%3Dnnch734d00sl2jd'
							.'k%26oauth_version%3D1.0%26size%3Doriginal', 
							Auth_OAuth_SignerImpl::getSignatureBaseString($request));
							
	}
	/*
	// We only test two entries here. This is just to test that the correct 
	// signature method is chosen. Generation of the signatures is tested 
	// elsewhere, and so is the base-string the signature build upon.
	public function testBuildSignature() {
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0', 
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh',
					'oauth_signature'=>'ignored', 'oauth_signature_method'=>'HMAC-SHA1');
		OAuthTestUtils::build_request('GET', 'http://photos.example.net/photos', $params);			
		$request = new Auth_OAuth_RequestImpl();
		
		$cons = new OAuthConsumer('key', 'kd94hf93k423kf44');
		$token = new OAuthToken('token', 'pfkkdhi9sl3r4s00');
		$hmac = new OAuthSignatureMethod_HMAC_SHA1();
		$plaintext = new OAuthSignatureMethod_PLAINTEXT();
		
		$this->assertEquals('tR3+Ty81lMeYAr/Fid0kMTYa/WM=', $r->build_signature($hmac, $cons, $token));
		$this->assertEquals('kd94hf93k423kf44%26pfkkdhi9sl3r4s00', $r->build_signature($plaintext, $cons, $token));
	}
	*/


	public function testPlaintext() {
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array());
		$request = new Auth_OAuth_RequestImpl();

		// test 1
		$consumer_secret = 'kd94hf93k423kf44';
		$token_secret = 'pfkkdhi9sl3r4s00';
		$signature = Auth_OAuth_SignatureMethod_PLAINTEXT::signature($request, null, $consumer_secret, $token_secret);
		$this->assertEquals('kd94hf93k423kf44%26pfkkdhi9sl3r4s00', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_PLAINTEXT::verify($request, null, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_PLAINTEXT::verify($request, null, $consumer_secret, $token_secret, 'foo'));

		// test 2
		$consumer_secret = 'kd94h+93k%23kf44';
		$token_secret = 'pfkkdh/9sl3r4&00';
		$signature = Auth_OAuth_SignatureMethod_PLAINTEXT::signature($request, null, $consumer_secret, $token_secret);
		$this->assertEquals('kd94h%252B93k%252523kf44%26pfkkdh%252F9sl3r4%252600', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_PLAINTEXT::verify($request, null, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_PLAINTEXT::verify($request, null, $consumer_secret, $token_secret, 'foo'));
	}


	public function testHMAC() {
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0', 
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh',
					'oauth_signature'=>'ignored', 'oauth_signature_method'=>'HMAC-SHA1');
		OAuthTestUtils::build_request('GET', 'http://photos.example.net/photos', $params);			

		$request = new Auth_OAuth_RequestImpl();
		$base_string = Auth_OAuth_SignerImpl::getSignatureBaseString($request);
		
		// test 1
		$consumer_secret = 'kd94hf93k423kf44';
		$token_secret = 'pfkkdhi9sl3r4s00';
		$signature = Auth_OAuth_SignatureMethod_HMAC_SHA1::signature($request, $base_string, $consumer_secret, $token_secret);
		$this->assertEquals('tR3%2BTy81lMeYAr%2FFid0kMTYa%2FWM%3D', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($request, $base_string, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($request, $base_string, $consumer_secret, $token_secret, 'foo'));

		// test 2
		$consumer_secret = 'kd94h+93k%23kf44';
		$token_secret = 'pfkkdh/9sl3r4&00';
		$signature = Auth_OAuth_SignatureMethod_HMAC_SHA1::signature($request, $base_string, $consumer_secret, $token_secret);
		$this->assertEquals('UCASL%2Flr96TeEnnnkTSTH2dJN40%3D', $signature);
		$this->assertTrue(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($request, $base_string, $consumer_secret, $token_secret, $signature));
		$this->assertFalse(Auth_OAuth_SignatureMethod_HMAC_SHA1::verify($request, $base_string, $consumer_secret, $token_secret, 'foo'));
	}
	
	/*
	public function testSign() {
		$params = array('file'=>'vacation.jpg', 'size'=>'original', 'oauth_version'=>'1.0', 
					'oauth_consumer_key'=>'dpf43f3p2l4k3l03', 'oauth_token'=>'nnch734d00sl2jdk',
					'oauth_timestamp'=>'1191242096', 'oauth_nonce'=>'kllo9940pd9333jh',
					'oauth_signature'=>'ignored', 'oauth_signature_method'=>'HMAC-SHA1');
		OAuthTestUtils::build_request('GET', 'http://photos.example.net/photos', $params);			
		$r = OAuthRequest::from_request();
		
		$cons = new OAuthConsumer('key', 'kd94hf93k423kf44');
		$token = new OAuthToken('token', 'pfkkdhi9sl3r4s00');
		$hmac = new OAuthSignatureMethod_HMAC_SHA1();
		$plaintext = new OAuthSignatureMethod_PLAINTEXT();
		
		$r->sign_request($hmac, $cons, $token);
		
		$params = $r->get_parameters();
		$this->assertEquals('HMAC-SHA1', $params['oauth_signature_method']);
		$this->assertEquals('tR3+Ty81lMeYAr/Fid0kMTYa/WM=', $params['oauth_signature']);
		
		$r->sign_request($plaintext, $cons, $token);
		
		$params = $r->get_parameters();
		$this->assertEquals('PLAINTEXT', $params['oauth_signature_method']);
		$this->assertEquals('kd94hf93k423kf44%26pfkkdhi9sl3r4s00', $params['oauth_signature']);
	}
	 */
}

?>
