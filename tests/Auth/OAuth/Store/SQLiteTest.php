<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Store/SQLite.php';

/**
 * Tests for the SQLite OAuth store implementation.
 */
class Auth_OAuth_Store_SQLiteTest extends Auth_OAuth_Store_TestCase {

	public function setUp() {
		$this->store = new Auth_OAuth_Store_SQLite( tempnam('/tmp', 'oauth_') );
		$this->store->build_tables();
	}

}

?>
