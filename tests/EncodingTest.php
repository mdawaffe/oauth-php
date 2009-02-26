<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Util.php';

/**
 * Test parameter encoding
 *
 * @see http://wiki.oauth.net/TestCases
 */
class EncodingTest extends OAuth_TestCase {	
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
}

?>
