<?php
require_once dirname(__FILE__) . '/Auth/OAuth/UtilTest.php';
require_once dirname(__FILE__) . '/Auth/OAuth/RequestTest.php';
require_once dirname(__FILE__) . '/Auth/OAuth/ServerTest.php';
require_once dirname(__FILE__) . '/Auth/OAuth/SignatureTest.php';
require_once dirname(__FILE__) . '/Auth/OAuth/Store/InMemoryTest.php';
require_once dirname(__FILE__) . '/Auth/OAuth/Store/SQLiteTest.php';
 
class AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new AllTests('OAuth');

		$suite->addTestSuite('Auth_OAuth_UtilTest');
		$suite->addTestSuite('Auth_OAuth_RequestTest');
		$suite->addTestSuite('Auth_OAuth_SignatureTest');
		$suite->addTestSuite('Auth_OAuth_Store_InMemoryTest');
		$suite->addTestSuite('Auth_OAuth_Store_SQLiteTest');
		$suite->addTestSuite('Auth_OAuth_ServerTest');

		return $suite;
    }
 
}
?>
