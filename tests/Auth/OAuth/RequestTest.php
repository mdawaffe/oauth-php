<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Util.php';
require_once 'Auth/OAuth/RequestImpl.php';

/**
 * Tests of Auth_OAuth_Request
 *
 * These tests rely on OAuthTestUtils::build_request to simulate an HTTP
 * request by populating $_SERVER, $_GET & $_POST.
 *
 * @see OAuthTestUtils::build_request
 */
class Auth_OAuth_RequestTest extends Auth_OAuth_TestCase {


	/**
	 * Test that an HTTP POST request is parsed properly.
	 */
	public function testFromRequestPost() {
		self::build_request('POST', 'http://testbed/test', array('foo'=>'bar', 'baz'=>'blargh'));
		$request = Auth_OAuth_RequestImpl::fromRequest();

		$this->assertEquals('POST', $request->getMethod());
		$this->assertEquals('http://testbed/test', $request->getRequestUrl());
		$this->assertEquals(array('foo'=>'bar','baz'=>'blargh'), $request->getParameters());


		self::build_request('POST', 'http://testbed/test', array('a'=>'%25'));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( '%', $request->getParam('a'));
	}


	/**
	 * Test that an HTTP GET request is parsed properly.
	 */
	public function testFromRequestPostGet() {
		self::build_request('GET', 'http://testbed/test', array('foo'=>'bar', 'baz'=>'blargh'));
		$request = Auth_OAuth_RequestImpl::fromRequest();

		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('http://testbed/test', $request->getRequestUrl());
		$this->assertEquals(array('foo'=>'bar','baz'=>'blargh'), $request->getParameters());
	}


	/**
	 * Test that the Authentication HTTP request header is parsed properly.
	 */
	public function testFromRequestHeader() {
		$test_header = 'OAuth realm="",oauth_foo=bar,oauth_baz="blargh"';
		self::build_request('POST', 'http://testbed/test', array(), $test_header);

		$request = Auth_OAuth_RequestImpl::fromRequest();

		$this->assertEquals('POST', $request->getMethod());
		$this->assertEquals('http://testbed/test', $request->getRequestUrl());
		$this->assertEquals(array('oauth_foo'=>'bar','oauth_baz'=>'blargh'), $request->getParameters(), 'Failed to split auth-header correctly');
	}


	/**
	 * Test that parameters are normalized properly.  This includes testing
	 * from proper encoding as well as the ordering of parameters, first by
	 * name, then by value.
	 *
	 * @see http://oauth.pbwiki.com/TestCases
	 * @see http://oauth.net/core/1.0/#anchor14
	 */
	public function testNormalizeParameters() {
		self::build_request('POST', 'http://testbed/test', array('name'=>''));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( 'name=', $request->getNormalizedParameterString());

		self::build_request('POST', 'http://testbed/test', array('a'=>'b'));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( 'a=b', $request->getNormalizedParameterString());

		self::build_request('POST', 'http://testbed/test', array('a'=>'b', 'c'=>'d'));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( 'a=b&c=d', $request->getNormalizedParameterString());

		self::build_request('POST', 'http://testbed/test', array('a'=>array('x!y', 'x y')));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( 'a=x%20y&a=x%21y', $request->getNormalizedParameterString());

		self::build_request('POST', 'http://testbed/test', array('x!y'=>'a', 'x'=>'a'));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( 'x=a&x%21y=a', $request->getNormalizedParameterString());

		self::build_request('POST', 'http://testbed/test',
			array('a'=>1, 'c'=>'hi there', 'f'=>array(25, 50, 'a'), 'z'=>array('p', 't')));
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals( 'a=1&c=hi%20there&f=25&f=50&f=a&z=p&z=t', $request->getNormalizedParameterString());
	}


	/**
	 * Test that the request URL is normalized properly.  This includes testing
	 * that the URL is properly assembled from the various PHP $_SERVER
	 * variables, as well as removing the default port number for URI schemes.
	 */
	public function testNormalizeHttpUrl() {
		self::build_request('POST', 'http://example.com', array());
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals('http://example.com', $request->getRequestUrl());

		self::build_request('POST', 'https://example.com', array());
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals('https://example.com', $request->getRequestUrl());

		// Tests that http on !80 and https on !443 keeps the port
		self::build_request('POST', 'https://example.com:80', array());
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals('https://example.com:80', $request->getRequestUrl());

		self::build_request('POST', 'http://example.com:443', array());
		$request = Auth_OAuth_RequestImpl::fromRequest();
		$this->assertEquals('http://example.com:443', $request->getRequestUrl());
	}

}

?>
