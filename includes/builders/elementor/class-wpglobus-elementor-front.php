<?php
/**
 * File: class-wpglobus-elementor-front.php
 *
 * @package WPGlobus\Builders\Elementor
 * @author  Alex Gor(alexgff)
 */

// W.I.P
// use Elementor\Core\Files\CSS\Post as Post_CSS;

if ( ! class_exists( 'WPGlobus_Elementor_Front' ) ) :

	/**
	 * Class WPGlobus_Elementor_Front.
	 */
	class WPGlobus_Elementor_Front{

		const ELEMENTOR_DATA_META_KEY = '_elementor_data';

		public static $file_prefix = 'post-';
		
		/**
		 * Init.
		 */
		public static function init() {
			add_filter( 'get_post_metadata', array( __CLASS__, 'filter__post_metadata' ), 5, 4 );
			
			/**
			 * @since 2.1.13
			 */
			add_filter( 'elementor/files/file_name', array( __CLASS__, 'filter__elementor_files_file_name' ), 5, 4 );
		}

		/**
		 * Filters the file name
		 *
		 * @since 2.1.13
		 *
		 * @param string $file_name
		 * @param object $instance  The file instance, which inherits Elementor\Core\Files
		 */
		public static function filter__elementor_files_file_name( $file_name, $instance ) {
			
			static $_file_name = null;
			
			if ( ! is_null( $_file_name ) ) {
				return $_file_name;
			}

			if ( false === strpos( $file_name, self::$file_prefix ) ) {
				return $file_name;
			} else {
				// case when $file_name == 'post-ID.css'
				if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
					$_file_name = $file_name;
				} else {
					if ( false !== strpos( $file_name, '.css' ) ) {
						$_file_name = str_replace( '.css', '-' . WPGlobus::Config()->language . '.css', $file_name );
					}
				}
			}

			return $_file_name;
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
				
			} elseif ( '_elementor_css' === $meta_key ) {
				
				// @todo W.I.P
				/*
				$meta_cache = wp_cache_get( $object_id, 'post_meta' );

				if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

					$_value = '';

					if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {
						$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language );
						//$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
					} else {
						$_value = $meta_cache[ $meta_key ][0];
					}

					return $_value;

				}
				// */
			}

			return $check;

		}

	}

endif;
