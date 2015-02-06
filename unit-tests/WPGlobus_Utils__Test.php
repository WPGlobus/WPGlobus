<?php
/**
 * Unit test for Class WPGlobus_Utils
 * @package WPGlobus
 */
require_once dirname( __FILE__ ) . '/../includes/class-wpglobus-utils.php';

/**
 * Class WPGlobus_Utils__Test
 */
class WPGlobus_Utils__Test extends PHPUnit_Framework_TestCase {

	/**
	 * @see test_is_function_in_backtrace
	 */
	private function _unit_test_for_backtrace() {
		$this->assertTrue( WPGlobus_Utils::is_function_in_backtrace( __FUNCTION__ ) );
	}

	/**
	 * @covers WPGlobus_Utils::is_function_in_backtrace
	 */
	public function test_is_function_in_backtrace() {

		$this->assertTrue( WPGlobus_Utils::is_function_in_backtrace( __FUNCTION__ ) );

		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( __FUNCTION__ . 'trailer' ) );
		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( 'no-such-function' ) );
		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( null ) );
		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( 3.14 ) );
		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( new StdClass ) );
		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( [ 'a', 278, new StdClass ] ) );

		/**
		 * One level deeper
		 */
		$this->_unit_test_for_backtrace();
	}

} // class

# --- EOF
