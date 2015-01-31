<?php
/**
 * Unit test for Class WPGlobus_WP
 * @package WPGlobus
 */
require_once __DIR__ . '/../includes/class-wpglobus-wp.php';

/**
 * Class WPGlobus_WP__Test
 */
class WPGlobus_WP__Test extends PHPUnit_Framework_TestCase {

	/**
	 * @covers WPGlobus_WP::is_doing_ajax
	 */
	public function test_is_doing_ajax() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
		if ( DOING_AJAX ) {
			$this->assertTrue( WPGlobus_WP::is_doing_ajax() );
		} else {
			$this->assertFalse( WPGlobus_WP::is_doing_ajax() );
		}
	}

	/**
	 * @covers WPGlobus_WP::is_pagenow
	 */
	public function test_is_pagenow() {
		global $pagenow;
		$pagenow = 'unit-test-page';
		$this->assertTrue( WPGlobus_WP::is_pagenow( 'unit-test-page' ) );
		$this->assertTrue( WPGlobus_WP::is_pagenow( array( 'unit-test-page', 'another-page' ) ) );
		$this->assertTrue( WPGlobus_WP::is_pagenow( array( new StdClass, 'unit-test-page' ) ) );
		$this->assertFalse( WPGlobus_WP::is_pagenow( 'not-unit-test-page' ) );
		$this->assertFalse( WPGlobus_WP::is_pagenow( array( 'not-unit-test-page', 'another-page' ) ) );
		$this->assertFalse( WPGlobus_WP::is_pagenow( 3.14 ) );
		$this->assertFalse( WPGlobus_WP::is_pagenow( new StdClass ) );
	}

	/**
	 * @covers WPGlobus_WP::is_http_post_action
	 */
	public function test_is_http_post_action() {
		$_POST['action'] = 'unit-test-action';
		$this->assertTrue( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );
		$this->assertFalse( WPGlobus_WP::is_http_post_action( '' ) );
		$this->assertFalse( WPGlobus_WP::is_http_post_action( null ) );
		$this->assertFalse( WPGlobus_WP::is_http_post_action( 3.14 ) );
		$bad_boy = new StdClass;
		$this->assertFalse( WPGlobus_WP::is_http_post_action( $bad_boy ) );

		$_POST['action'] = 'not-unit-test-action';
		$this->assertFalse( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );
		unset( $_POST['action'] );
		$this->assertFalse( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );
		$_POST['action'] = 'unit-test-action';
		$this->assertTrue( WPGlobus_WP::is_http_post_action( array( 'unit-test-action', 'not-unit-test-action' ) ) );

		$_POST['action'] = array( 'this-should-not-be-an-array' );
		$this->assertFalse( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );

	}

} // class

# --- EOF