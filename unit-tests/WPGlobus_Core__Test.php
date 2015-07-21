<?php

require_once dirname( __FILE__ ) . '/../includes/class-wpglobus.php';
require_once dirname( __FILE__ ) . '/../includes/class-wpglobus-core.php';

/**
 *
 */
class WPGlobus_Core__Test extends PHPUnit_Framework_TestCase {

	/**
	 * @covers WPGlobus_Core::text_filter
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

		$qt_comments = '<!--:en-->EN<!--:--><!--:ru-->RU<!--:-->';
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $qt_comments ), 'QT comments' );
		$this->assertEquals( 'RU', WPGlobus_Core::text_filter( $qt_comments, 'ru' ), 'QT comments' );

		$no_tags = 'EN';
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags ), 'No tags' );
		$this->assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags, null, WPGlobus::RETURN_EMPTY ), 'No tags, return empty' );

		$no_default = '{:xx}XX{:}{:ru}RU{:}';
		$this->assertEmpty( WPGlobus_Core::text_filter( $no_default ) );

		$not_a_string = 3.14;
		$this->assertEquals( 3.14, WPGlobus_Core::text_filter( $not_a_string ), 'Not a string' );

	}

	/**
	 * @covers WPGlobus_Core::has_translations
	 */
	public function test_has_translations() {

		/** @var string[] $positives */
		$positives = array(
			'{:en}EN{:}{:ru}RU{:}',
			'[:en]EN[:ru]RU',
			'<!--:en-->EN<!--:--><!--:ru-->RU<!--:-->',
			"Multi-line\n\n {:en}E\nN{:}\n\n{:ru}RU{:}",
			'{:xx',
			'Lead {:xx',
			'Lead {:xx trail',
		);

		foreach ( $positives as $_ ) {
			$this->assertTrue( WPGlobus_Core::has_translations( $_ ), 'Has translation: ' . $_ );
		}

		/** @var string[] $negatives */
		$negatives = array(
			'',
			'No delimiters',
			'Wrong delimiter {xx:}',
			'One-character locale {:e}',
			'Non-alpha locale {:e1}EN{:}{:r2}RU{:}',
			'Non-latin locale {:ан}EN{:}{:ру}RU{:}',
			'Uppercase locale {:EN}EN{:}{:RU}RU{:}',
		);

		foreach ( $negatives as $_ ) {
			$this->assertFalse( WPGlobus_Core::has_translations( $_ ), 'Has no translation: ' . $_ );
		}

	}

	/**
	 * @covers WPGlobus_Core::translate_wp_post
	 */
	public function test_translate_wp_post() {

		/**
		 * We are using a mock, so need this to please the lint
		 * @var WP_Post $post
		 */

		/**
		 * Default behavior (no parameters)
		 */

		$post = $this->getMock( 'WP_Post' );

		$post->post_title   = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		$post->post_content = '{:en}post_content EN{:}{:ru}post_content RU{:}';
		$post->post_excerpt = '{:en}post_excerpt EN{:}{:ru}post_excerpt RU{:}';

		/**
		 * nav-menu's additional fields do not exist in the WP_Post class
		 */
		/** @noinspection PhpUndefinedFieldInspection */
		$post->title = '{:en}title EN{:}{:ru}title RU{:}';
		/** @noinspection PhpUndefinedFieldInspection */
		$post->attr_title = '{:en}attr_title EN{:}{:ru}attr_title RU{:}';

		WPGlobus_Core::translate_wp_post( $post );

		$this->assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		$this->assertEquals( 'post_content EN', $post->post_content, 'post_content' );
		$this->assertEquals( 'post_excerpt EN', $post->post_excerpt, 'post_excerpt' );
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertEquals( 'title EN', $post->title, 'title' );
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertEquals( 'attr_title EN', $post->attr_title, 'attr_title' );

		unset( $post );


		/**
		 * Translate to a language other than the current one
		 */
		$post             = $this->getMock( 'WP_Post' );
		$post->post_title = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		WPGlobus_Core::translate_wp_post( $post, 'ru' );
		$this->assertEquals( 'post_title RU', $post->post_title, 'post_title' );
		unset( $post );

		/**
		 * Translate to a non-existing language - return in default language
		 */
		$post               = $this->getMock( 'WP_Post' );
		$post->post_title   = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		$post->post_content = '{:en}post_content EN{:}{:xx}post_content XX{:}';
		WPGlobus_Core::translate_wp_post( $post, 'xx' );
		$this->assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		$this->assertEquals( 'post_content XX', $post->post_content, 'post_content' );
		unset( $post );

		/**
		 * Repeated attempt to translate has no effect, when called with no parameters,
		 * because we pass the post object by reference
		 */
		$post             = $this->getMock( 'WP_Post' );
		$post->post_title = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		WPGlobus_Core::translate_wp_post( $post, 'en' );
		$this->assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		WPGlobus_Core::translate_wp_post( $post, 'ru' );
		$this->assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		unset( $post );

	}

	/**
	 * @covers WPGlobus_Core::translate_term
	 */
	public function test_translate_term() {

		/**
		 * Term as a string
		 */
		$term = '{:en}term EN{:}{:ru}term RU{:}';
		WPGlobus_Core::translate_term( $term, 'en' );
		$this->assertEquals( 'term EN', $term, 'term' );

		$term = '{:en}term EN{:}{:ru}term RU{:}';
		WPGlobus_Core::translate_term( $term, 'ru' );
		$this->assertEquals( 'term RU', $term, 'term' );

		/**
		 * Term as an object
		 */
		$term_object = new stdClass;

		$term_object->name        = '{:en}term name EN{:}{:ru}term name RU{:}';
		$term_object->description = '{:en}term description EN{:}{:ru}term description RU{:}';
		WPGlobus_Core::translate_term( $term_object, 'en' );
		$this->assertEquals( 'term name EN', $term_object->name, '$term_object->name' );
		$this->assertEquals( 'term description EN', $term_object->description, '$term_object->description' );

		$term_object->name        = '{:en}term name EN{:}{:ru}term name RU{:}';
		$term_object->description = '{:en}term description EN{:}{:ru}term description RU{:}';
		WPGlobus_Core::translate_term( $term_object, 'ru' );
		$this->assertEquals( 'term name RU', $term_object->name, '$term_object->name' );
		$this->assertEquals( 'term description RU', $term_object->description, '$term_object->description' );

	}

} // class

# --- EOF
