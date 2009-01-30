<?php
require_once dirname(__FILE__) . '/EncodingTest.php';
require_once dirname(__FILE__) . '/RequestTest.php';
require_once dirname(__FILE__) . '/SignatureTest.php';
 
class AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new AllTests('OAuth');

		$suite->addTestSuite('EncodingTest');
		$suite->addTestSuite('RequestTest');
		$suite->addTestSuite('SignatureTest');

		return $suite;
    }
 
}
?>
