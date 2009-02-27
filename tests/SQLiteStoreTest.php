<?php

require_once dirname(__FILE__) . '/StoreTestCase.php';
require_once 'Auth/OAuth/Store/SQLite.php';

/**
 * Tests for the SQLite OAuth store implementation.
 */
class SQLiteStoreTest extends OAuth_StoreTestCase {

	public function setUp() {
		$this->store = new Auth_OAuth_Store_SQLite( tempnam('/tmp', 'oauth_') );
	}

}

?>
