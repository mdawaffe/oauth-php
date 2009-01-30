<?php

require_once dirname(__FILE__) . '/common.php';
require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/RequestImpl.php';

/**
 * Tests of Auth_OAuth_Request
 *
 * The tests works by using OAuthTestUtils::build_request
 * to populare $_SERVER, $_GET & $_POST.
 *
 * Most of the base string and signature tests
 * are either very simple or based upon
 * http://wiki.oauth.net/TestCases
 *
 * @see http://wiki.oauth.net/TestCases
 */
class RequestTest extends PHPUnit_Framework_TestCase {	

	public function testFromRequestPost() {
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('foo'=>'bar', 'baz'=>'blargh'));
		$request = new Auth_OAuth_RequestImpl();
		
		$this->assertEquals('POST', $request->getMethod());
		$this->assertEquals('http://testbed/test', $request->getRequestUrl());
		$this->assertEquals(array('foo'=>'bar','baz'=>'blargh'), $request->getParameters());
	}
	
	public function testFromRequestPostGet() {
		OAuthTestUtils::build_request('GET', 'http://testbed/test', array('foo'=>'bar', 'baz'=>'blargh'));		
		$request = new Auth_OAuth_RequestImpl();
		
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('http://testbed/test', $request->getRequestUrl());
		$this->assertEquals(array('foo'=>'bar','baz'=>'blargh'), $request->getParameters());
	}
	
	public function testFromRequestHeader() {
		$test_header = 'OAuth realm="",oauth_foo=bar,oauth_baz="blargh"';
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('oauth_foo'=>'foo', 'oauth_baz'=>'baz'), $test_header);
		
		$request = new Auth_OAuth_RequestImpl();
		
		$this->assertEquals('POST', $request->getMethod());
		$this->assertEquals('http://testbed/test', $request->getRequestUrl());
		$this->assertEquals(array('oauth_foo'=>'bar','oauth_baz'=>'blargh'), $request->getParameters(), 'Failed to split auth-header correctly');
	}

	public function testNormalizeParameters() {
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('name'=>''));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals( 'name=', $request->getNormalizedParameterString());

		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('a'=>'b'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals( 'a=b', $request->getNormalizedParameterString());
		
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('a'=>'b', 'c'=>'d'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals( 'a=b&c=d', $request->getNormalizedParameterString());
		
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('a'=>array('x!y', 'x y')));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals( 'a=x%20y&a=x%21y', $request->getNormalizedParameterString());
		
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('x!y'=>'a', 'x'=>'a'));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals( 'x=a&x%21y=a', $request->getNormalizedParameterString());
		
		OAuthTestUtils::build_request('POST', 'http://testbed/test', array('a'=>1, 'c'=>'hi there', 'f'=>array(25, 50, 'a'), 'z'=>array('p', 't')));
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals( 'a=1&c=hi%20there&f=25&f=50&f=a&z=p&z=t', $request->getNormalizedParameterString());
	}
	
	public function testNormalizeHttpUrl() {
		OAuthTestUtils::build_request('POST', 'http://example.com', array());
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('http://example.com', $request->getRequestUrl());
		
		OAuthTestUtils::build_request('POST', 'https://example.com', array());
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('https://example.com', $request->getRequestUrl());
		
		// Tests that http on !80 and https on !443 keeps the port
		OAuthTestUtils::build_request('POST', 'https://example.com:80', array());
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('https://example.com:80', $request->getRequestUrl());
		
		OAuthTestUtils::build_request('POST', 'http://example.com:443', array());
		$request = new Auth_OAuth_RequestImpl();
		$this->assertEquals('http://example.com:443', $request->getRequestUrl());
	}
}

?>
