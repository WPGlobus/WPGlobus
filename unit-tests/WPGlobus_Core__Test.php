<?php
/**
 * File: WPGlobus_Core__Test.php
 *
 * @package WPGlobus\Unit-Tests
 */

/**
 *
 */
class WPGlobus_Core__Test extends \PHPUnit\Framework\TestCase {

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

		$qtx_tags = '[:en]EN[:ru]RU[:de]DE[:]';
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $qtx_tags ), __LINE__ );
		self::assertEquals( 'RU', WPGlobus_Core::text_filter( $qtx_tags, 'ru' ), __LINE__ );
		self::assertEquals( 'DE', WPGlobus_Core::text_filter( $qtx_tags, 'de' ), __LINE__ );

		$no_tags = 'EN';
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags ), 'No tags' );
		self::assertEquals( 'EN', WPGlobus_Core::text_filter( $no_tags, null, WPGlobus::RETURN_EMPTY ), 'No tags, return empty' );

		$no_default = '{:xx}XX{:}{:ru}RU{:}';
		self::assertEmpty( WPGlobus_Core::text_filter( $no_default ) );

		$not_a_string = 3.14;
		self::assertEquals( 3.14, WPGlobus_Core::text_filter( $not_a_string ), 'Not a string' );

		$multiple = '{:en}first_EN{:}{:ru}first_RU{:} &ndash; {:en}second_EN{:}{:ru}second_RU{:}';
		self::assertEquals( 'first_EN', WPGlobus_Core::text_filter( $multiple ), __LINE__ );
		self::assertEquals( 'first_RU', WPGlobus_Core::text_filter( $multiple, 'ru' ), __LINE__ );
		self::assertEquals( 'first_EN', WPGlobus_Core::text_filter( $multiple, 'de' ), __LINE__ );

	}

	/**
	 * @covers WPGlobus_Core::extract_text
	 */
	public function test_extract_text() {
		$multiple = '{:en}first_EN{:}{:ru}first_RU{:} &ndash; {:en}second_EN{:}{:ru}second_RU{:}';
		self::assertEquals( 'first_EN &ndash; second_EN', WPGlobus_Core::extract_text( $multiple ), __LINE__ );
		self::assertEquals( 'first_RU &ndash; second_RU', WPGlobus_Core::extract_text( $multiple, 'ru' ), __LINE__ );


		// Content with line breaks.
		$_content = ' <br /> <h2 style="color:#fff;" class="message-title">{:en}English Title{:}{:ru}Русский заголовок{:}</h2> <div class="message-content"> <p style="color:#fff;">{:en}English Content{:}{:ru}Русский контент.
Русский контент.{:}</p> </div>';

		$expected_en = ' <br /> <h2 style="color:#fff;" class="message-title">English Title</h2> <div class="message-content"> <p style="color:#fff;">English Content</p> </div>';
		self::assertEquals( $expected_en, WPGlobus_Core::extract_text( $_content, 'en' ) );

		$expected_ru = ' <br /> <h2 style="color:#fff;" class="message-title">Русский заголовок</h2> <div class="message-content"> <p style="color:#fff;">Русский контент.
Русский контент.</p> </div>';
		self::assertEquals( $expected_ru, WPGlobus_Core::extract_text( $_content, 'ru' ) );

	}

	/**
	 * @covers WPGlobus_Core::has_translations
	 */
	public function test_has_translations() {

		/** @var string[] $positives */
		$positives = array(
			'{:en}EN{:}{:ru}RU{:}',
			"Multi-line\n\n {:en}E\nN{:}\n\n{:ru}RU{:}",
			'{:xx',
			'Lead {:xx',
			'Lead {:xx trail',
			// qTranslate
			'[:en]EN[:ru]RU',
			'<!--:en-->EN<!--:--><!--:ru-->RU<!--:-->',
			// qTranslate-X
			'[:en]EN[:ru]RU[:]',
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
	 * Test `has_translation`.
	 *
	 * @since  2.5.6
	 * @covers WPGlobus_Core::has_translation
	 */
	public function test_has_translation() {

		/**
		 * These must pass.
		 *
		 * @var string[] $positives
		 */
		$positives = array(
			'{:en}EN{:}{:ru}RU{:}'                                  => 'en',
			"Multi-line\n\n {:en}E\nN{:}\n\n{:ru}RU{:}"             => 'en',
			'{:xx}'                                                 => 'xx',
			'Lead {:xx}'                                            => 'xx',
			'Lead {:xx} trail'                                      => 'xx',
			'No delimiters, en'                                     => 'en',
			'English exists w/o delimiters plus some garbage {xx:}' => 'en',
		);

		foreach ( $positives as $string => $language ) {
			self::assertTrue( WPGlobus_Core::has_translation( $string, $language ), 'Has translation: ' . $string . ', ' . $language );
		}

		/**
		 * These must not pass.
		 *
		 * @var string[] $negatives
		 */
		$negatives = array(
			// Non-latin locale
			'{:ан}EN{:}{:ру}RU{:}'                          => 'ан',
			''                                              => 'en',
			'No delimiters, NOT en'                         => 'xx',
			'{:xx}No English{:}{:yy}Blah{:}'                => 'en',
			'Russian but in uppercase {:EN}EN{:}{:RU}RU{:}' => 'ru',
			// 'Non-alpha locale
			'{:e1}EN{:}{:r2}RU{:}'                          => 'e1',
			// 'Uppercase default locale
			'{:EN}EN{:}{:RU}RU{:}'                          => 'EN',
			// 'One-character locale
			'{:e}'                                          => 'e',
		);

		foreach ( $negatives as $string => $language ) {
			self::assertFalse( WPGlobus_Core::has_translation( $string, $language ), 'Has no translation: ' . $string . ', ' . $language );
		}
	}

	/**
	 * Mock WP_Post.
	 *
	 * @return WP_Post|PHPUnit_Framework_MockObject_MockObject
	 */
	protected function createMockWPPost() {
		/** @noinspection PhpParamsInspection */
		return $this->getMockBuilder( 'WP_Post' )->getMock();
	}

	/**
	 * @covers WPGlobus_Core::translate_wp_post
	 */
	public function test_translate_wp_post() {

		/**
		 * Default behavior (no parameters)
		 */

		$post               = $this->createMockWPPost();
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
		$post             = $this->createMockWPPost();
		$post->post_title = '{:en}post_title EN{:}{:ru}post_title RU{:}';
		WPGlobus_Core::translate_wp_post( $post, 'ru' );
		self::assertEquals( 'post_title RU', $post->post_title, 'post_title' );
		unset( $post );

		/**
		 * Translate to a non-existing language - return in default language
		 */
		$post               = $this->createMockWPPost();
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
		$post             = $this->createMockWPPost();
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
