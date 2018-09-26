<?php
/**
 * File: class-wpglobus-core.php
 *
 * @package WPGlobus
 */

/**
 * Class WPGlobus_Core
 */
class WPGlobus_Core {

	/**
	 * The main filter function.
	 * Default behavior: extracts text in one language from multi-lingual strings.
	 *
	 * @param string $text             Multilingual text, with special delimiters between languages
	 * @param string $language         The code of the language to be extracted from the `$text`
	 * @param string $return           What to do if the text in the `$language` was not found
	 * @param string $default_language Pass this if you want to return a non-default language content, when the content in `$language` is not available
	 *
	 * @return string
	 */
	public static function text_filter(
		$text = '',
		$language = '',
		$return = WPGlobus::RETURN_IN_DEFAULT_LANGUAGE,
		$default_language = ''
	) {

		if ( empty( $text ) ) {
			// Nothing to do
			return $text;
		}

		/**
		 * There are cases when numeric terms are passed here. We should not tamper with them.
		 * @since 1.0.8.1 Before, was returning empty string, which was incorrect.
		 */
		if ( ! is_string( $text ) ) {
			return $text;
		}

		/**
		 * `$default_language` not passed
		 */
		if ( ! $default_language ) {
			if ( class_exists( 'WPGlobus_Config' ) ) {
				$default_language = WPGlobus::Config()->default_language;
			} else {
				// When in unit tests
				$default_language = 'en';
			}
		}

		/**
		 * `$language` not passed
		 */
		if ( empty( $language ) ) {
			$language = $default_language;
		}

		/**
		 * Fix for the case
		 * &lt;!--:en--&gt;ENG&lt;!--:--&gt;&lt;!--:ru--&gt;RUS&lt;!--:--&gt;
		 * @todo need careful investigation
		 */
		$text = htmlspecialchars_decode( $text );

		$possible_delimiters =
			array(
				/**
				 * Our delimiters
				 */
				array(
					'start' => sprintf( WPGlobus::LOCALE_TAG_START, $language ),
					'end'   => WPGlobus::LOCALE_TAG_END,
				),
				/**
				 * qTranslate compatibility
				 * qTranslate uses these two types of delimiters
				 * @example
				 * <!--:en-->English<!--:--><!--:ru-->Russian<!--:-->
				 * [:en]English S[:ru]Russian S
				 * The [] delimiter does not have the closing tag, so we will look for the next opening [: or
				 * take the rest until end of end of the string
				 */
				array(
					'start' => "<!--:{$language}-->",
					'end'   => '<!--:-->',
				),
				array(
					'start' => "[:{$language}]",
					'end'   => '[:',
				),
			);

		/**
		 * We'll use this flag after the loop to see if the loop was successful. See the `break` clause in the loop.
		 */
		$is_local_text_found = false;

		/**
		 * We do not know which delimiter was used, so we'll try both, in a loop
		 */
		/* @noinspection LoopWhichDoesNotLoopInspection */
		foreach ( $possible_delimiters as $delimiters ) {

			/**
			 * Try the starting position. If not found, continue the loop to the next set of delimiters.
			 */
			$pos_start = strpos( $text, $delimiters['start'] );
			if ( false === $pos_start ) {
				continue;
			}

			/**
			 * The starting position found..adjust the pointer to the text start
			 * (Do not need mb_strlen here, because we expect delimiters to be Latin only)
			 */
			$pos_start += strlen( $delimiters['start'] );

			/**
			 * Try to find the ending position.
			 * If could not find, will extract the text until end of string.
			 */
			$pos_end = strpos( $text, $delimiters['end'], $pos_start );
			if ( false === $pos_end ) {
				// - Until end of string
				$text = substr( $text, $pos_start );
			} else {
				$text = substr( $text, $pos_start, $pos_end - $pos_start );
			}

			/**
			 * Set the "found" flag and end the loop.
			 */
			$is_local_text_found = true;
			break;

		}

		/**
		 * If we could not find anything in the current language...
		 */
		if ( ! $is_local_text_found ) {
			if ( WPGlobus::RETURN_EMPTY === $return ) {
				// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIF
				if ( $language === $default_language && ! self::has_translations( $text ) ) {
					/**
					 * @todo Check the above condition. What if only one part is true?
					 * If text does not contain language delimiters nothing to do
					 */
				} else {
					/** We are forced to return empty string. */
					$text = '';
				}
			} else {
				/**
				 * Try RETURN_IN_DEFAULT_LANGUAGE
				 */
				if ( $language === $default_language ) {
					if ( self::has_translations( $text ) ) {
						/**
						 * Rare case of text in default language doesn't exist
						 * @todo make option for return warning message or maybe another action
						 */
						$text = '';
					}
				} else {
					/**
					 * Try the default language (recursion)
					 * @qa  covered by the 'one_tag' case
					 * @see WPGlobus_QA::_test_string_parsing()
					 */
					$text = self::text_filter( $text, $default_language );
				}
			}
			/** else - we do not change the input string, and it will be returned as-is */
		}

		return $text;

	}

	/**
	 * Extract text from a string which is either:
	 * - in the requested language (could be multiple blocks)
	 * - or does not have the language marks
	 * @todo  Works with single line of text only. If the text contains line breaks, they will be removed.
	 * @todo  May fail on large texts because regex are used.
	 *
	 * @example
	 * Input:
	 *  '{:en}first_EN{:}{:ru}first_RU{:} blah-blah {:en}second_EN{:}{:ru}second_RU{:}'
	 * Language: en
	 * Output:
	 *  'first_EN blah-blah second_EN'
	 *
	 * @param string $text     Input text.
	 * @param string $language Language to extract. Default is the current language.
	 *
	 * @return string
	 * @since 1.7.9
	 */
	public static function extract_text( $text = '', $language = '' ) {
		if ( ! $text ) {
			return $text;
		}

		/**
		 * `$language` not passed
		 */
		if ( ! $language ) {
			// When in unit tests:
			$language = 'en';
			// Normally:
			if ( class_exists( 'WPGlobus_Config', false ) ) {
				$language = WPGlobus::Config()->language;
			}
		}

		// Pass 1. Remove the language marks surrounding the language we need.
		// Pass 2. Remove the texts surrounded with other language marks, together with the marks.
		return preg_replace(
			array( '/{:' . $language . '}(.+?){:}/m', '/{:.+?}.+?{:}/m' ),
			array( '\\1', '' ),
			$text
		);
	}

	/**
	 * Check if string has language delimiters
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public static function has_translations( $string ) {

		/**
		 * This should detect majority of the strings with our delimiters without calling preg_match
		 * @var int $pos_start
		 */
		$pos_start = strpos( $string, WPGlobus::LOCALE_TAG_OPEN );
		if ( false !== $pos_start ) {
			if ( ctype_lower( $string[ $pos_start + 2 ] ) && ctype_lower( $string[ $pos_start + 3 ] ) ) {
				return true;
			}
		}

		/**
		 * For compatibility, etc. - the universal procedure with regexp
		 */

		return (bool) preg_match( '/(\{:|\[:|<!--:)[a-z]{2}/', $string );
	}

	/**
	 * Keeps only one language in all textual fields of the `$post` object.
	 *
	 * @see \WPGlobus_Core::text_filter for the parameters description
	 *
	 * @param WP_Post|mixed $post The Post object. Object always passed by reference.
	 * @param string        $language
	 * @param string        $return
	 * @param string        $default_language
	 */
	public static function translate_wp_post(
		&$post,
		$language = '',
		$return = WPGlobus::RETURN_IN_DEFAULT_LANGUAGE,
		$default_language = ''
	) {

		/**
		 * `$default_language` not passed
		 */
		if ( ! $default_language ) {
			if ( class_exists( 'WPGlobus_Config' ) ) {
				$default_language = WPGlobus::Config()->default_language;
			} else {
				// When in unit tests
				$default_language = 'en';
			}
		}

		/**
		 * `$language` not passed
		 */
		if ( empty( $language ) ) {
			$language = $default_language;
		}

		$fields = array(
			'post_title',
			'post_content',
			'post_excerpt',
			'title',
			'attr_title',
		);

		foreach ( $fields as $field ) {
			if ( ! empty( $post->$field ) ) {
				$post->$field = self::text_filter( $post->$field, $language, $return, $default_language );
			}
		}

	}

	/**
	 * Translate a term (category, post_tag, etc.)
	 * Term can be an object (default for the @see wp_get_object_terms() filter)
	 * or a string (for example, when wp_get_object_terms is called with the 'fields'=>'names' argument)
	 *
	 * @param string|object $term
	 * @param string        $language
	 */
	public static function translate_term( &$term, $language = '' ) {
		if ( is_object( $term ) ) {
			if ( ! empty( $term->name ) ) {
				$term->name = self::text_filter( $term->name, $language );
			}
			if ( ! empty( $term->description ) ) {
				$term->description = self::text_filter( $term->description, $language );
			}
		} else {
			if ( ! empty( $term ) ) {
				$term = self::text_filter( $term, $language );
			}
		}

	}


} // class

# --- EOF
