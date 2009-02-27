<?php
require_once dirname(__FILE__) . '/UtilTest.php';
require_once dirname(__FILE__) . '/RequestTest.php';
require_once dirname(__FILE__) . '/SignatureTest.php';
require_once dirname(__FILE__) . '/InMemoryStoreTest.php';
require_once dirname(__FILE__) . '/SQLiteStoreTest.php';
require_once dirname(__FILE__) . '/ServerTest.php';
 
class AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new AllTests('OAuth');

		$suite->addTestSuite('UtilTest');
		$suite->addTestSuite('RequestTest');
		$suite->addTestSuite('SignatureTest');
		$suite->addTestSuite('InMemoryStoreTest');
		$suite->addTestSuite('SQLiteStoreTest');
		$suite->addTestSuite('ServerTest');

		return $suite;
    }
 
}
?>
