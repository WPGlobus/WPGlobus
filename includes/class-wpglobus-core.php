<?php

/**
 *
 */
class WPGlobus_Core {

	/**
	 * @param string $text
	 * @param string $language
	 * @param string $return
	 *
	 * @return string
	 */
	public static function text_filter( $text = '', $language = '', $return = WPGlobus::RETURN_IN_DEFAULT_LANGUAGE ) {

		/**
		 * Fix for case
		 * &lt;!--:en--&gt;ENG&lt;!--:--&gt;&lt;!--:ru--&gt;RUS&lt;!--:--&gt;
		 * @todo need careful investigation
		 */
		$text = htmlspecialchars_decode( $text );

		/** @global string $wpg_default_language */
		//global $wpg_default_language;

		/** @global string $wpg_current_language */
		//global $wpg_current_language;

		global $WPGlobus_Config;

		if ( empty( $text ) ) {
			// Nothing to do
			return $text;
		}

		if ( empty( $language ) ) {
			$language = $WPGlobus_Config->language;
		}

		$possible_delimiters =
			[
				/**
				 * Our delimiters
				 */
				[
					'start' => sprintf( WPGlobus::LOCALE_TAG_START, $language ),
					'end'   => WPGlobus::LOCALE_TAG_END,
				],
				/**
				 * qTranslate compatibility
				 * qTranslate uses these two types of delimiters
				 * @example
				 * <!--:en-->English<!--:--><!--:ru-->Russian<!--:-->
				 * [:en]English S[:ru]Russian S
				 * The [] delimiter does not have the closing tag, so we will look for the next opening [: or
				 * take the rest until end of end of the string
				 */
				[
					'start' => "<!--:{$language}-->",
					'end'   => '<!--:-->',
				],
				[
					'start' => "[:{$language}]",
					'end'   => '[:',
				],
			];

		/**
		 * We'll use this flag after the loop to see if the loop was successful. See the `break` clause in the loop.
		 */
		$is_local_text_found = false;

		/**
		 * We do not know which delimiter was used, so we'll try both, in a loop
		 */
		foreach ( $possible_delimiters as $delimiters ) {

			/**
			 * Try the starting position. If not found, continue the loop to the next set of delimiters
			 */
			$pos_start = mb_strpos( $text, $delimiters['start'] );
			if ( $pos_start === false ) {
				continue;
			}

			/**
			 * The starting position found..adjust the pointer to the text start
			 * (Do not need mb_strlen here, because we expect delimiters to be Latin only)
			 */
			$pos_start = $pos_start + strlen( $delimiters['start'] );

			/**
			 * Try to find the ending position.
			 * If could not find, will extract the text until end of string by passing null to the `substr`
			 */
			$pos_end = mb_strpos( $text, $delimiters['end'], $pos_start );
			if ( $pos_end === false ) {
				// - Until end of string
				$length = null;
			} else {
				$length = $pos_end - $pos_start;
			}

			/**
			 * Extract the text and end the loop
			 */
			$text                = mb_substr( $text, $pos_start, $length );
			$is_local_text_found = true;
			break;

		}

		/**
		 * If we could not find anything in the current language...
		 */
		if ( ! $is_local_text_found ) {
			if ( $return === WPGlobus::RETURN_EMPTY ) {
				if ( $language == $WPGlobus_Config->default_language && ! preg_match( WPGlobus::TAG_REGEXP, $text ) ) {
					/**
					 * If text does not contains language delimiters nothing to do
					 */
				} else {
					/** We are forced to return empty string. */
					$text = '';
				}
			} else {
				/**
				 * Try RETURN_IN_DEFAULT_LANGUAGE
				 */
				if ( $language == $WPGlobus_Config->default_language ) {
					if ( 1 == preg_match( WPGlobus::TAG_REGEXP, $text ) ) {
						/**
						 * Rarely case of text in default language doesn't exists
						 * @todo make option for return warning message or maybe another action
						 */
						$text = __( '(No text in default language)', 'wpglobus' );
					}
				} else {
					/** Try the default language (recursion) */
					$text = __wpg_text_filter( $text, $WPGlobus_Config->default_language );
				}
			}
			/** else - we do not change the input string, and it will be returned as-is */
		}

		return $text;

	}

} // class

# --- EOF