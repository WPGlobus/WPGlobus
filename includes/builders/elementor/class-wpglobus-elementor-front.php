<?php
/**
 * File: class-wpglobus-elementor-front.php
 *
 * @package WPGlobus\Builders\Elementor
 * @author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_Elementor_Front' ) ) :

	/**
	 * Class WPGlobus_Elementor_Front.
	 */
	class WPGlobus_Elementor_Front{

		const ELEMENTOR_DATA_META_KEY = '_elementor_data';

		/**
		 * Init.
		 */
		public static function init() {
			add_filter( 'get_post_metadata', array( __CLASS__, 'filter__post_metadata' ), 5, 4 );
		}

		/**
		 * Get meta callback.
		 *
		 * @scope front.
		 * @param $check
		 * @param $object_id
		 * @param $meta_key
		 * @param $single
		 *
		 * @return string
		 */
		public static function filter__post_metadata(
			$check, $object_id, $meta_key, /** @noinspection PhpUnusedParameterInspection */
			$single
		) {

			if ( self::ELEMENTOR_DATA_META_KEY === $meta_key ) {

				$meta_cache = wp_cache_get( $object_id, 'post_meta' );

				if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

					/** @noinspection PhpUnusedLocalVariableInspection */
					$_value = '';

					if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {
						$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language );
					} else {
						$_value = $meta_cache[ $meta_key ][0];
					}

					return $_value;

				}
				
			}

			return $check;

		}

	}

endif;
