<?php
/**
 * Unit test for Class WPGlobus_Utils
 *
 * @package WPGlobus\Unit-Tests
 */

/**
 * Class WPGlobus_Utils__Test
 */

/** @noinspection PhpUndefinedClassInspection */
class WPGlobus_Utils__Test extends \PHPUnit\Framework\TestCase {

	/**
	 * @var string $option_home
	 * Used by mock @see get_option()
	 * Initialized by @see setUP()
	 */
	public static $option_home;

	/**
	 * @var bool $is_404_response
	 * Used by mock @see is_404()
	 * If necessary, can change to true
	 */
	public static $is_404_response = false;
	/** @noinspection PhpLanguageLevelInspection */

	/**
	 * Run before each test.
	 * To run after each test, @see tearDown
	 */
	protected function setUp() : void {
		self::$option_home = 'http://www.example.com';
	}

	/**
	 * @covers WPGlobus_Utils::localize_url
	 */
	public function test_localize_url() {

		/**
		 * Mock object sent as a parameter, because we do now have access to the actual config.
		 *
		 * @var WPGlobus_Config $config
		 */
		$config = $this->getMockBuilder( 'WPGlobus_Config' )->getMock();

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
		 *
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
			array( '', '/ru', 'ru' ),
			array( '/', '/ru/', 'ru' ),
			array( '/pt/', '/ru/', 'ru' ),
			array( '/ru/', '/ru/', 'ru' ),
			array( '/de/', '/ru/de/', 'ru' ),
			array( '/page/', '/ru/page/', 'ru' ),
            array( '/cat/page/', '/pt/cat/page/', 'pt' ),
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
		 *
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
		 *
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
			'http://dots.here.ac.uk/and.here/wordpress',
			'http://www.example.de',
			'http://www.example.pt',
		);

		foreach ( $homes as $home ) {

			self::$option_home = $home;
			$home_url          = get_option( 'home' );

			foreach ( $good as $_ ) {
			    // Make sure that there are no missing array elements.
                self::assertEquals(3, count($_), print_r($_, true));
				list( $url, $localized_url, $language ) = $_;
				self::assertEquals( $home_url . $localized_url,
					WPGlobus_Utils::localize_url( $home_url . $url, $language, $config ),
					"In language={$language}, $url becomes $localized_url"
				);
			}

			foreach ( $bad as $_ ) {
				list( $url, $localized_url, $language ) = $_;
				self::assertNotEquals( $home_url . $localized_url,
					WPGlobus_Utils::localize_url( $home_url . $url, $language, $config ),
					"In language={$language}, $url MUST NOT become $localized_url"
				);
			}
		}

		/**
		 * Checking combinations of `www` - no `www` and http(s)
		 */

		self::$option_home = 'http://www.example.com';
		self::assertEquals( 'http://www.example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://www.example.com/page/', 'ru', $config ) );
		self::assertEquals( 'http://example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://example.com/page/', 'ru', $config ) );

		self::$option_home = 'http://example.com';
		self::assertEquals( 'http://www.example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://www.example.com/page/', 'ru', $config ) );
		self::assertEquals( 'http://example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://example.com/page/', 'ru', $config ) );

		self::$option_home = 'https://example.com';
		self::assertEquals( 'http://www.example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://www.example.com/page/', 'ru', $config ) );
		self::assertEquals( 'http://example.com/ru/page/',
			WPGlobus_Utils::localize_url( 'http://example.com/page/', 'ru', $config ) );

		// A specific case from support forum
		/** @noinspection SpellCheckingInspection */
		self::$option_home         = 'http://www.fiskfelagid.is';
		$config->default_language  = 'en';
		$config->enabled_languages = array( 'en', 'is' );
		/** @noinspection SpellCheckingInspection */
		self::assertEquals( 'http://www.fiskfelagid.is/is',
			WPGlobus_Utils::localize_url( 'http://www.fiskfelagid.is/is', 'is', $config ) );

	}

	/**
	 * @covers WPGlobus_Utils::extract_language_from_url
	 */
	public function test_extract_language_from_url() {

		/**
		 * Mock object sent as a parameter, because we do now have access to the actual config.
		 *
		 * @var WPGlobus_Config $config
		 */
		$config = $this->getMockBuilder( 'WPGlobus_Config' )->getMock();

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

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'http://example.com/ru/page/', $config ) );

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'https://example.com/ru/page/', $config ) );

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'https://develop.example.com/ru/page/', $config ) );

		self::assertEquals( 'pt',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/pt/page/', $config ) );

		// Unknown language
		self::assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/ar/page/', $config ) );

		// Default language or no language
		self::assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/page/', $config ) );

		// Default language, but specified in the URL for some reason - returns it
		self::assertEquals( 'en',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/en/page/', $config ) );

		// Wrong position
		self::assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/page/ru/something', $config ) );

		// TODO Not sure about this. PHP manual says it should not work.
		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru/something', $config ) );

		self::assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( 3.14, $config ) );

		self::assertEquals( '',
			WPGlobus_Utils::extract_language_from_url( array( 1, 'pi' ), $config ) );

		// No trailing slash
		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru', $config ) );

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru?a=b', $config ) );

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru/?a=b', $config ) );

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru/#hash', $config ) );

		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( '/ru#hash', $config ) );

		// Site in subfolder
		self::$option_home = 'http://www.example.com/subfolder';
		self::assertEquals( 'ru',
			WPGlobus_Utils::extract_language_from_url( 'http://www.example.com/subfolder/ru/something', $config ) );

	}

	/**
	 * @covers WPGlobus_Utils::domain_tld
	 */
	public static function test_domain_tld() {
		$schemes = array(
			'http://',
			'https://',
			'//',
			'',
		);

		$domains = array(
			'example',
			'with-dashes',
			'localhost',
		);

		$prefixes = array(
			'',
			'www.',
			'develop.',
			'127.',
			'multiple.prefixes.',
		);

		$tlds = array(
			'.com',
			'.co.uk',
			'.com.au',
			'.a.bg',
			'.edu.hk',
			'.bg.it',
		);

		foreach ( $schemes as $scheme ) {

			foreach ( $prefixes as $prefix ) {
				foreach ( $domains as $domain ) {
					foreach ( $tlds as $tld ) {
						self::assertEquals( $domain . $tld,
							WPGlobus_Utils::domain_tld( $scheme . $prefix . $domain . $tld ) );

					}
				}
			}

		}

		// Example of parse_url failure (we return the input as-is)
		self::assertEquals( 'http://', WPGlobus_Utils::domain_tld( 'http://' ) );

		// Special cases
		self::assertEquals( 'localhost', WPGlobus_Utils::domain_tld( 'http://localhost' ) );
		self::assertEquals( '127.0.0.1', WPGlobus_Utils::domain_tld( 'http://127.0.0.1' ) );
		self::assertEquals( 'example.special-public-suffix.it',
			WPGlobus_Utils::domain_tld( 'http://www.example.special-public-suffix.it' ) );

	}

	/**
	 * @covers WPGlobus_Utils::build_multilingual_string
	 */
	public static function test_build_multilingual_string() {
		$translations = array(
			'en' => 'EN',
			'ru' => 'RU',
			'de' => 'DE',
			'fr' => 'FR',
		);

		self::assertEquals(
			'{:en}EN{:}{:ru}RU{:}{:de}DE{:}{:fr}FR{:}',
			WPGlobus_Utils::build_multilingual_string( $translations )
		);
	}

	/**
	 * "Stub" test for coverage.
	 *
	 * @covers WPGlobus_Utils::current_url
	 */
	public static function test_current_url() {
		$_SERVER['HTTP_HOST']   = 'www.example.com';
		$_SERVER['REQUEST_URI'] = '/folder/file?var=value';
		self::assertEquals( 'http://www.example.com/folder/file?var=value', WPGlobus_Utils::current_url() );
	}

	/**
	 * @covers \WPGlobus_Utils::hreflangs
	 */
	public function test_hreflangs() {

		/**
		 * Mock object sent as a parameter, because we do now have access to the actual config.
		 *
		 * @var WPGlobus_Config $config
		 */
		$config = $this->getMockBuilder( 'WPGlobus_Config' )->getMock();

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
		$config->default_language = 'ru';

		/**
		 * This says "Do not use language code in the default URL"
		 * So, no /en/page/, just /page/
		 */
		$config->hide_default_language = true;

		$config->locale['en'] = "en_US";
		$config->locale['ru'] = "ru_RU";
		$config->locale['pt'] = "pt_PT";


		/**
		 * Mock web request
		 */
		$_SERVER['HTTP_HOST']   = 'www.example.com';
		$_SERVER['REQUEST_URI'] = '/folder/file?var=value';


		$hreflangs = WPGlobus_Utils::hreflangs( $config );

		self::assertEquals( '<link rel="alternate" hreflang="ru-RU" href="http://www.example.com/folder/file?var=value"/>', $hreflangs['ru'] );

		self::assertEquals( '<link rel="alternate" hreflang="pt-PT" href="http://www.example.com/pt/folder/file?var=value"/>', $hreflangs['pt'] );

	}

} // class

# --- EOF
