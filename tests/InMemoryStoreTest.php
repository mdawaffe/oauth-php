<?php

require_once dirname(__FILE__) . '/StoreTestCase.php';
require_once 'Auth/OAuth/Store/InMemory.php';

/**
 * Test case for InMemory OAuth store implementation.
 */
class InMemoryStoreTest extends OAuth_StoreTestCase {

	public function setUp() {
		$this->store = new Auth_OAuth_Store_InMemory();
	}

}

?>
