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
		$this->assertFalse( WPGlobus_Utils::is_function_in_backtrace( array( 'a', 278, new StdClass ) ) );

		/**
		 * One level deeper
		 */
		$this->_unit_test_for_backtrace();
	}

	public static $option_home = 'http://www.example.com';

	/**
	 * @covers WPGlobus_Utils::localize_url
	 */
	public function test_localize_url() {

		/**
		 * Mock object sent as a parameter, because we do now have access to the actual config.
		 * @var WPGlobus_Config $config
		 */
		$config                        = $this->getMock( 'WPGlobus_Config' );

		/**
		 * These languages are enabled
		 */
		$config->enabled_languages     = array( 'en', 'ru', 'pt' );

		/**
		 * This is the current language
		 */
		$config->language              = 'pt';

		/**
		 * This is the default language
		 */
		$config->default_language      = 'en';

		/**
		 * This says "Do not use language code in the default URL"
		 * So, no /en/page/, just /page/
		 */
		$config->hide_default_language = true;

		/**
		 * Good test cases
		 * @var string[][]
		 * list($url, $localized_url, $language)
		 */
		$good = array(
			array( '', '/pt', '' ),
			array( '/something/', '/something/', 'en' ), // Default language - no prefix
			array( '/cat/page/', '/pt/cat/page/' ),
			array( '', '/ru', 'ru' ),
			array( '/', '/ru/', 'ru' ),
			array( '/pt/', '/ru/', 'ru' ),
			array( '?a=b', '/ru?a=b', 'ru' ),
			array( '/?a=b', '/ru/?a=b', 'ru' ),
			array( '/page/', '/ru/page/', 'ru' ),
			array( '/cat/page/', '/ru/cat/page/', 'ru' ),
			array( '/cat/page/pt/aaa/', '/ru/cat/page/pt/aaa/', 'ru' ),
			array( '/#hash', '/ru/#hash', 'ru' ),
		);

		/**
		 * Bad test cases
		 * @var string[][]
		 */
		$bad = array(
			/**
			 * 'de' is not a supported language..ignore
			 */
			array( '/de/', '/ru/', 'ru' ),
			/**
			 * 'pt' is not at the beginning of the path..ignore
			 */
			array( '/cat/page/pt/aaa/', '/cat/page/ru/aaa/', 'ru' ),
		);

		/**
		 * Various examples of home_url
		 * @var string[];
		 */
		$homes = array(
			'http://www.example.com',
			'http://develop.example.com',
			'http://example.com',
			'https://www.example.com',
			'http://www.example.com/blog',
		);

		foreach ( $homes as $home ) {

			self::$option_home = $home;
			$home_url          = get_option( 'home' );

			foreach ( $good as $_ ) {
				list( $url, $localized_url, $language ) = $_;
				$this->assertEquals( $home_url . $localized_url,
					WPGlobus_Utils::localize_url( $home_url . $url, $language, $config ) );
			}

			foreach ( $bad as $_ ) {
				list( $url, $localized_url, $language ) = $_;
				$this->assertNotEquals( $home_url . $localized_url,
					WPGlobus_Utils::localize_url( $home_url . $url, $language, $config ) );
			}
		}

		/**
		 * Checking combinations of `www` - no `www` and http(s)
		 */

		self::$option_home = 'http://www.example.com';
		$this->assertEquals( 'http://www.example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://www.example.com/page/', 'ru', $config ) );
		$this->assertEquals( 'http://example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://example.com/page/', 'ru', $config ) );

		self::$option_home = 'http://example.com';
		$this->assertEquals( 'http://www.example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://www.example.com/page/', 'ru', $config ) );
		$this->assertEquals( 'http://example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://example.com/page/', 'ru', $config ) );

		self::$option_home = 'https://example.com';
		$this->assertEquals( 'http://www.example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://www.example.com/page/', 'ru', $config ) );
		$this->assertEquals( 'http://example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://example.com/page/', 'ru', $config ) );
	}

} // class

/**
 * WordPress utilities mocks
 */


/**
 * @param string $option_name
 *
 * @return string
 */
function get_option( $option_name ) {
	$option = '';
	if ( 'home' === $option_name ) {
		$option = WPGlobus_Utils__Test::$option_home;
	}

	return $option;
}

/**
 * @param string $string
 *
 * @return string
 */
function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * @param string $string
 *
 * @return string
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}

# --- EOF
