<?php

require_once dirname(__FILE__) . '/TestCase.php';
require_once 'Auth/OAuth/Store/InMemory.php';

/**
 * Test case for InMemory OAuth store implementation.
 */
class Auth_OAuth_Store_InMemoryTest extends Auth_OAuth_Store_TestCase {

	public function setUp() {
		$this->store = new Auth_OAuth_Store_InMemory();
	}

}

?>
