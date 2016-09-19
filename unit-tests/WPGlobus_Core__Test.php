<?php
/**
 * File: WPGlobus_Core__Test.php
 *
 * @package WPGlobus\Unit-Tests
 */


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

		self::assertEquals( WPGlobus::RETURN_EMPTY, 'empty', 'WPGlobus::RETURN_EMPTY' );

		self::assertEmpty( WPGlobus_Core::text_filter( '' ), 'Empty string' );

		$proper = '{:en}EN{:}{:ru}RU{:}';
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $proper ) );
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $proper, 'en' ) );
		self::assertEquals( 'RU', WPGlobus_Core::text_filter( $proper, 'ru' ) );
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $proper, 'xx' ), 'Non-existing language' );
		self::assertEmpty( WPGlobus_Core::text_filter( $proper, 'xx', WPGlobus::RETURN_EMPTY ), 'Non-existing language, return empty' );

		$with_accents = '{:en}an ÅccENt{:}{:ru}anÖther ÄccENt{:}';
		self::assertEquals( 'an ÅccENt', WPGlobus_Core::text_filter( $with_accents, 'en' ), __LINE__ );
		self::assertEquals( 'anÖther ÄccENt', WPGlobus_Core::text_filter( $with_accents, 'ru' ), __LINE__ );

		$qt_tags = '[:en]EN[:ru]Рус';
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $qt_tags ), 'QT tags' );
		self::assertEquals( 'Рус', WPGlobus_Core::text_filter( $qt_tags, 'ru' ), 'QT tags' );

		$qt_comments = '<!--:en-->EN<!--:--><!--:ru-->RU<!--:-->';
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $qt_comments ), 'QT comments' );
		self::assertEquals( 'RU', WPGlobus_Core::text_filter( $qt_comments, 'ru' ), 'QT comments' );

		$no_tags = 'EN';
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags ), 'No tags' );
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags, null, WPGlobus::RETURN_EMPTY ), 'No tags, return empty' );

		$no_default = '{:xx}XX{:}{:ru}RU{:}';
		self::assertEmpty( WPGlobus_Core::text_filter( $no_default ) );

		$not_a_string = 3.14;
		self::assertEquals( 3.14, WPGlobus_Core::text_filter( $not_a_string ), 'Not a string' );

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
			self::assertTrue( WPGlobus_Core::has_translations( $_ ), 'Has translation: ' . $_ );
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
			self::assertFalse( WPGlobus_Core::has_translations( $_ ), 'Has no translation: ' . $_ );
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

		self::assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		self::assertEquals( 'post_content EN', $post->post_content, 'post_content' );
		self::assertEquals( 'post_excerpt EN', $post->post_excerpt, 'post_excerpt' );
		/** @noinspection PhpUndefinedFieldInspection */
		self::assertEquals( 'title EN', $post->title, 'title' );
		/** @noinspection PhpUndefinedFieldInspection */
		self::assertEquals( 'attr_title EN', $post->attr_title, 'attr_title' );

		unset( $post );

		/**
		 * Translate to a language other than the current one
		 */
		$post             = $this->getMock( 'WP_Post' );
		$post->post_title = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		WPGlobus_Core::translate_wp_post( $post, 'ru' );
		self::assertEquals( 'post_title RU', $post->post_title, 'post_title' );
		unset( $post );

		/**
		 * Translate to a non-existing language - return in default language
		 */
		$post               = $this->getMock( 'WP_Post' );
		$post->post_title   = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		$post->post_content = '{:en}post_content EN{:}{:xx}post_content XX{:}';
		WPGlobus_Core::translate_wp_post( $post, 'xx' );
		self::assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		self::assertEquals( 'post_content XX', $post->post_content, 'post_content' );
		unset( $post );

		/**
		 * Repeated attempt to translate has no effect, when called with no parameters,
		 * because we pass the post object by reference
		 */
		$post             = $this->getMock( 'WP_Post' );
		$post->post_title = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		WPGlobus_Core::translate_wp_post( $post, 'en' );
		self::assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		WPGlobus_Core::translate_wp_post( $post, 'ru' );
		self::assertEquals( 'post_title EN', $post->post_title, 'post_title' );
		unset( $post );

		/**
		 * Not a WP_Post must not get fatal
		 */
		$not_a_post = 'a string';
		WPGlobus_Core::translate_wp_post( $not_a_post, 'en' );
		self::assertEquals( 'a string', $not_a_post, __LINE__ );
		$not_a_post = 3.14;
		WPGlobus_Core::translate_wp_post( $not_a_post, 'en' );
		self::assertEquals( 3.14, $not_a_post, __LINE__ );
		unset( $not_a_post );

		/**
		 * Any object with "post-like" properties should work fine
		 */
		$post             = new stdClass;
		$post->post_title = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		WPGlobus_Core::translate_wp_post( $post, 'en' );
		self::assertEquals( 'post_title EN', $post->post_title, __LINE__ );
		$post->post_title   = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		$post->post_content = '{:ru}post_content RU{:}{:en}post_content EN{:}';
		WPGlobus_Core::translate_wp_post( $post, 'ru' );
		self::assertEquals( 'post_title RU', $post->post_title, __LINE__ );
		self::assertEquals( 'post_content RU', $post->post_content, __LINE__ );
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
		self::assertEquals( 'term EN', $term, 'term' );

		$term = '{:en}term EN{:}{:ru}term RU{:}';
		WPGlobus_Core::translate_term( $term, 'ru' );
		self::assertEquals( 'term RU', $term, 'term' );

		/**
		 * Term as an object
		 */
		$term_object = new stdClass;

		$term_object->name        = '{:en}term name EN{:}{:ru}term name RU{:}';
		$term_object->description = '{:en}term description EN{:}{:ru}term description RU{:}';
		WPGlobus_Core::translate_term( $term_object, 'en' );
		self::assertEquals( 'term name EN', $term_object->name, '$term_object->name' );
		self::assertEquals( 'term description EN', $term_object->description, '$term_object->description' );

		$term_object->name        = '{:en}term name EN{:}{:ru}term name RU{:}';
		$term_object->description = '{:en}term description EN{:}{:ru}term description RU{:}';
		WPGlobus_Core::translate_term( $term_object, 'ru' );
		self::assertEquals( 'term name RU', $term_object->name, '$term_object->name' );
		self::assertEquals( 'term description RU', $term_object->description, '$term_object->description' );

	}
} // class

# --- EOF
