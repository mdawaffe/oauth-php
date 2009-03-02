<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Util.php';

/**
 * Test Auth_OAuth_Util functions.
 */
class UtilTest extends OAuth_TestCase {	

	/**
	 * Test parameter encoding.
	 *
	 * @see http://wiki.oauth.net/TestCases
	 */
	public function testParameterEncoding() {
		$parameters = array(
			'abcABC123' => 'abcABC123',
			'-._~'      => '-._~',
			'%'         => '%25',
			'+'         => '%2B',
			'&=*'       => '%26%3D%2A',
			"\x0A"      => '%0A',
			"\x20"      => '%20',
			"\x7F"      => '%7F',

			// suggestion for Unicode ?
			//"\u0080"    => '%C2%80',
			//"\u3001"    => '%E3%80%81',
		);

		foreach ($parameters as $param => $encoded) {
			$this->assertEquals($encoded, Auth_OAuth_Util::encode($param));
			$this->assertEquals($param, Auth_OAuth_Util::decode($encoded));
		}
	}

	/**
	 * Test appendQuery().
	 */
	public function testAppendQuery() {
		$this->assertEquals(
			'http://example.com/?oauth_token=6a5c6460', 
			Auth_OAuth_Util::appendQuery('http://example.com/', array('oauth_token' => '6a5c6460'))
		);

		$this->assertEquals(
			'http://example.com/?oauth_token=6a5c6460#complete', 
			Auth_OAuth_Util::appendQuery('http://example.com/#complete', array('oauth_token' => '6a5c6460'))
		);

		$this->assertEquals(
			'http://example.com/?foo=bar&oauth_token=6a5c6460', 
			Auth_OAuth_Util::appendQuery('http://example.com/?foo=bar', array('oauth_token' => '6a5c6460'))
		);

		$this->assertEquals(
			'http://example.com/?foo=bar&oauth_token=6a5c6460#complete', 
			Auth_OAuth_Util::appendQuery('http://example.com/?foo=bar#complete', array('oauth_token' => '6a5c6460'))
		);

	}
}

?>
