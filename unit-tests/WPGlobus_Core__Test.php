<?php

require_once __DIR__ . '/../includes/class-wpglobus.php';
require_once __DIR__ . '/../includes/class-wpglobus-core.php';

/**
 *
 */
class WPGlobus_Core__Test extends PHPUnit_Framework_TestCase {

	/**
	 */
	public function test_text_filter() {

		$this->assertEquals( WPGlobus::RETURN_EMPTY, 'empty', 'WPGlobus::RETURN_EMPTY' );

		$this->assertEmpty( WPGlobus_Core::text_filter( '' ), 'Empty string' );

		$proper = '{:en}EN{:}{:ru}RU{:}';
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $proper ) );
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $proper, 'en' ) );
		$this->assertEquals( 'RU', WPGlobus_Core::text_filter( $proper, 'ru' ) );
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $proper, 'xx' ), 'Non-existing language' );
		$this->assertEmpty( WPGlobus_Core::text_filter( $proper, 'xx', WPGlobus::RETURN_EMPTY ), 'Non-existing language, return empty' );

		$qt_tags = '[:en]EN[:ru]RU';
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $qt_tags ), 'QT tags' );
		$this->assertEquals( 'RU', WPGlobus_Core::text_filter( $qt_tags, 'ru' ), 'QT tags' );

		$no_tags = 'EN';
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags ), 'No tags' );
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags, null, WPGlobus::RETURN_EMPTY ), 'No tags, return empty' );

		$no_default = '{:xx}XX{:}{:ru}RU{:}';
		$this->assertEmpty( WPGlobus_Core::text_filter($no_default)  );

	}


} // class

# --- EOF