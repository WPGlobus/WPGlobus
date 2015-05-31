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
		$config = $this->getMock( 'WPGlobus_Config' );

		/**
		 * These languages are enabled
		 */
		$config->enabled_languages = array( 'en', 'ru', 'pt' );

		/**
		 * This is the current language
		 */
		$config->language = 'pt';

		/**
		 * This is the default language
		 */
		$config->default_language = 'en';

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
			//
			// Default language - no prefix
			//
			array( '/something/', '/something/', 'en' ),
			array( '?', '?', 'en' ),
			//
			array( '', '/pt', '' ),
			array( '/cat/page/', '/pt/cat/page/' ),
			array( '', '/ru', 'ru' ),
			array( '/', '/ru/', 'ru' ),
			array( '/pt/', '/ru/', 'ru' ),
			array( '/ru/', '/ru/', 'ru' ),
			array( '/de/', '/ru/de/', 'ru' ),
			array( '/page/', '/ru/page/', 'ru' ),
			array( '/cat/page/', '/ru/cat/page/', 'ru' ),
			array( '/cat/page/pt/aaa/', '/ru/cat/page/pt/aaa/', 'ru' ),
			//
			// Queries
			//
			array( '?', '/ru?', 'ru' ),
			array( '/?', '/ru/?', 'ru' ),
			array( '/pt?', '/ru?', 'ru' ),
			array( '/ru/?', '/pt/?', 'pt' ),
			array( '?a=b', '/ru?a=b', 'ru' ),
			array( '/?a=b', '/ru/?a=b', 'ru' ),
			array( '/ru/?a=b', '/ru/?a=b', 'ru' ),
			array( '/ru/?a=b', '/pt/?a=b', 'pt' ),
			array( '/de/?a=b', '/pt/de/?a=b', 'pt' ),
			//
			// Hashes
			//
			array( '/#hash', '/ru/#hash', 'ru' ),
			array( '/ru/#hash', '/pt/#hash', 'pt' ),
			array( '#hash', '/ru#hash', 'ru' ),
			array( '/#', '/ru/#', 'ru' ),
			array( '#', '/ru#', 'ru' ),
			array( '/pt#', '/ru#', 'ru' ),
			array( '/de#', '/ru/de#', 'ru' ),
			//
			// All in
			//
			array( '/cat/page/pt/aaa/?a=b&c=d#hash', '/ru/cat/page/pt/aaa/?a=b&c=d#hash', 'ru' ),
			//
			// Must not see `/rush` as `/ru` (language) and `sh`
			//
			array( '/rush/', '/pt/rush/', 'pt' ),
			array( '/rush', '/pt/rush', 'pt' ),
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
			'http://www.example.com/blog',
			'http://localhost',
			'http://localhost/my-site',
			'http://just-name',
			'http://just-name/my-site',
			'http://127.0.0.1',
			'http://127.0.0.1/my-site',
			'http://www.example.com',
			'http://develop.example.com',
			'http://many.dots.in.domain.example.com',
			'http://example.com',
			'https://www.example.com',
			'http://www.example.com/my-site/blog',
		);

		foreach ( $homes as $home ) {

			self::$option_home = $home;
			$home_url          = get_option( 'home' );

			foreach ( $good as $_ ) {
				list( $url, $localized_url, $language ) = $_;
				$this->assertEquals( $home_url . $localized_url,
					WPGlobus_Utils::localize_url( $home_url . $url, $language, $config ),
					"In language={$language}, $url becomes $localized_url"
				);
			}

			foreach ( $bad as $_ ) {
				list( $url, $localized_url, $language ) = $_;
				$this->assertNotEquals( $home_url . $localized_url,
					WPGlobus_Utils::localize_url( $home_url . $url, $language, $config ),
					"In language={$language}, $url MUST NOT become $localized_url"
				);
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

	/**
	 * @covers WPGlobus_Utils::extract_language_from_url
	 */
	function test_extract_language_from_url() {

		/**
		 * Mock object sent as a parameter, because we do now have access to the actual config.
		 * @var WPGlobus_Config $config
		 */
		$config = $this->getMock( 'WPGlobus_Config' );

		/**
		 * These languages are enabled
		 */
		$config->enabled_languages = array( 'en', 'ru', 'pt' );

		/**
		 * This is the current language
		 */
		$config->language = 'pt';

		/**
		 * This is the default language
		 */
		$config->default_language = 'en';

		/**
		 * This says "Do not use language code in the default URL"
		 * So, no /en/page/, just /page/
		 */
		$config->hide_default_language = true;

		$this->assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'http://example.com/ru/page/', $config ) );

		$this->assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'https://example.com/ru/page/', $config ) );

		$this->assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'https://develop.example.com/ru/page/', $config ) );

		$this->assertEquals( 'pt',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/pt/page/', $config ) );

		// Unknown language
		$this->assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/ar/page/', $config ) );

		// Default language or no language
		$this->assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/page/', $config ) );

		// Default language, but specified in the URL for some reason - returns it
		$this->assertEquals( 'en',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/en/page/', $config ) );

		// Wrong position
		$this->assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/page/ru/something', $config ) );

		// TODO Not sure about this. PHP manual says it should not work.
		$this->assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru/something', $config ) );

		$this->assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 3.14, $config ) );

		$this->assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( array( 1, 'pi' ), $config ) );

	}

	/**
	 * @covers WPGlobus_Utils::domain_tld
	 */
	function test_domain_tld() {

		$data = array(
			'http://www.example.com'               => 'example.com',
			'http://example.com'                   => 'example.com',
			'http://www.example.co.uk'             => 'co.uk',
			'http://localhost'                     => 'localhost',
			'http://something.example.com'         => 'example.com',
			'http://multiple.prefixes.example.com' => 'example.com',
			'example.com'                          => 'example.com',
			'http://127.0.0.1'                     => '127.0.0.1',
		);

		foreach ( $data as $url => $domain_tld ) {
			$this->assertEquals( $domain_tld, WPGlobus_Utils::domain_tld( $url ) );
		}

	}

	/**
	 * @covers WPGlobus_Utils::build_multilingual_string
	 */
	function test_build_multilingual_string() {
		$translations = array(
			'en' => 'EN',
			'ru' => 'RU',
			'de' => 'DE',
			'fr' => 'FR',
		);

		$this->assertEquals(
			'{:en}EN{:}{:ru}RU{:}{:de}DE{:}{:fr}FR{:}',
			WPGlobus_Utils::build_multilingual_string( $translations )
		);
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
