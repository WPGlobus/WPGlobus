<?php
/**
 * @package WPGlobus\Vendor\ACF.
 *
 * @since   1.9.17
 */

/**
 * Class WPGlobus_Acf_2.
 */
class WPGlobus_Acf_2 {

	protected static $acf_fields = null;

	protected static $post_multilingual_fields = null;

	protected static $post_acf_field_prefix = 'acf-';

	/**
	 * @var object Instance of this class.
	 */
	protected static $instance;

	/**
	 * Constructor.
	 */
	protected function __construct() {
	}

	/**
	 * Get instance of this class.
	 *
	 * @return WPGlobus_Acf_2
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof WPGlobus_Acf_2 ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get multilingual fields.
	 */
	public static function get_post_multilingual_fields() {
		if ( is_null( self::$post_multilingual_fields ) ) {
			return false;
		}

		return self::$post_multilingual_fields;
	}

	/**
	 * Retrieves acf fields which was got @see get_post_meta_fields().
	 *
	 * @since 1.9.25
	 *
	 * @param int|string $post_id Reserved for future version.
	 *
	 * @return array|false An array of acf fields or false if $acf_fields is null.
	 */
	public static function get_acf_fields( $post_id ) {
		if ( ! is_null( self::$acf_fields ) ) {
			return self::$acf_fields;
		}

		return false;
	}

	/**
	 * Get post meta.
	 *
	 * Don't use get_field_objects() to get ACF fields @see advanced-custom-fields\includes\api\api-template.php
	 * to prevent incorrect behavior on post page.
	 * Don't call WPGlobus::Config() inside function to prevent the resetting of `meta` property.
	 *
	 * @param        $post_id
	 * @param string $post_type @since 2.1.3
	 *
	 * @return array
	 */
	public static function get_post_meta_fields( $post_id, $post_type = 'post' ) {

		if ( in_array( $post_type, array( 'acf-field-group', 'acf-field' ) ) ) {
			/**
			 * Prevent working with own post type.
			 */
			return array();
		}

		global $wpdb;

		$_post_meta_fields      = array();
		$_post_meta_fields_temp = array();

		$post_id = (int) $post_id;

		if ( $post_id > 0 ) {

			$info = acf_get_post_id_info( $post_id );

			if ( $info['type'] == 'post' ) {

				/**
				 * @todo Check the case when DB has many records with 'acf-field' post type.
				 */
				$fields = $wpdb->get_results( $wpdb->prepare(
					"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_value LIKE 'field_%'",
					$post_id
				) );

				if ( ! empty( $fields ) ) {

					/**
					 * Filter to enable/disable wysiwyg field.
					 * Returning boolean.
					 *
					 * @since 1.9.17
					 *
					 * @param boolean.
					 */
					$field_wysiwyg_enabled = apply_filters( 'wpglobus/vendor/acf/field/wysiwyg', false );

					self::$post_multilingual_fields = array();

					foreach ( $fields as $key => $field ) :

						$_key = ltrim( $field->meta_key, '_' );

						/**
						 * Because of incorrect behaviour don't use
						 * $_acf_field = acf_maybe_get_field( $field->post_name, $post_id );
						 * and
						 * $_acf_field = acf_get_field($field->post_name);
						 */
						if ( function_exists( '_acf_get_field_by_key' ) ) {
							$_acf_field = _acf_get_field_by_key( $field->meta_value );
						} else {
							$_acf_field = self::_acf_get_field_by_key( $field->meta_value );
						}

						if ( 'wysiwyg' == $_acf_field['type'] ) {
							if ( $field_wysiwyg_enabled ) {
								$_post_meta_fields_temp[ $_key ] = $_key;
							}
						} elseif ( 'repeater' == $_acf_field['type'] ) {
							/**
							 * do nothing.
							 */
						} elseif ( 'flexible_content' == $_acf_field['type'] ) {
							/**
							 * do nothing.
							 */
						} else {

							$status = self::get_3rd_party_field_status($_acf_field);
						
							if ( $status ) {
								$_post_meta_fields_temp[ $_key ] = $_key;
								
								/**
								 * Add field to array to output it with `wpglobus-translatable` class.
								 * @since 2.1.11
								 *
								 * @todo W.I.P maybe to exclude some type of field, e.g. table.
								 */
								self::$post_multilingual_fields[$_key] = self::$post_acf_field_prefix . $_acf_field['key'];
							}

						}
						self::$acf_fields[ $_key ] = $_acf_field;

					endforeach;

				}
			}
		}

		$_post_meta_fields = $_post_meta_fields_temp;

		return $_post_meta_fields;
	}

	/**
	 * Get post meta.
	 *
	 * Don't use get_field_objects() to get ACF fields @see advanced-custom-fields\includes\api\api-template.php
	 * to prevent incorrect behavior on post page.
	 * Don't call WPGlobus::Config() inside function to prevent the resetting of `meta` property.
	 *
	 * @param        $post_id
	 * @param string $post_type @since 2.1.3
	 *
	 * @return array
	 */
	public static function get_post_meta_fields_1( $post_id, $post_type = 'post' ) {

		if ( in_array( $post_type, array( 'acf-field-group', 'acf-field' ) ) ) {
			/**
			 * Prevent working with own post type.
			 */
			return array();
		}

		global $wpdb;

		$_post_meta_fields      = array();
		$_post_meta_fields_temp = array();

		$post_id = (int) $post_id;

		if ( $post_id > 0 ) {

			$info = acf_get_post_id_info( $post_id );

			if ( $info['type'] == 'post' ) {

				/**
				 * @todo Check the case when DB has many records with 'acf-field' post type.
				 */
				$fields = $wpdb->get_results( $wpdb->prepare(
					"SELECT ID, post_excerpt, post_name, post_parent FROM $wpdb->posts WHERE post_type = '%s'",
					'acf-field'
				) );

				if ( ! empty( $fields ) ) {

					/**
					 * Filter to enable/disable wysiwyg field.
					 * Returning boolean.
					 *
					 * @since 1.9.17
					 *
					 * @param boolean.
					 */
					$field_wysiwyg_enabled = apply_filters( 'wpglobus/vendor/acf/field/wysiwyg', false );

					self::$post_multilingual_fields = array();

					$repeaters = array();

					foreach ( $fields as $key => $field ) :

						/**
						 * Because incorrect behaviour don't use
						 * $_acf_field = acf_maybe_get_field( $field->post_name, $post_id );
						 * and
						 * $_acf_field = acf_get_field($field->post_name);
						 */
						$_acf_field = _acf_get_field_by_key( $field->post_name );

						if ( empty( $_acf_field['type'] ) ) {
							continue;
						}

						if ( 'wysiwyg' == $_acf_field['type'] && ! $field_wysiwyg_enabled ) {
							/**
							 * do nothing.
							 */
						} elseif ( 'repeater' == $_acf_field['type'] ) {
							/**
							 * Get repeater to process it later.
							 */
							$repeaters[ $_acf_field['name'] ] = $_acf_field;
						} else {

							$_post_meta_fields_temp[ $field->post_excerpt ] = $field->post_excerpt;
							self::$post_multilingual_fields[]               = self::$post_acf_field_prefix . $field->post_name;

						}
						self::$acf_fields[ $field->post_excerpt ] = $_acf_field;

					endforeach;

					if ( empty( $repeaters ) ) {

						$_post_meta_fields = $_post_meta_fields_temp;

					} else {

						$meta_data = get_metadata( 'post', $post_id );

						foreach ( $repeaters as $key => $repeater ) :

							/**
							 * Get fields that the repeater contains.
							 *
							 * @see advanced-custom-fields\includes\api\api-field.php
							 * @see advanced-custom-fields-pro\includes\api\api-field.php
							 */
							$repeater_fields = acf_get_fields_by_id( $repeater['ID'] );

							if ( ! empty( $repeater_fields ) ) {

								foreach ( $repeater_fields as $_key => $_field ) {

									/**
									 * Unset unneeded field.
									 */
									unset( $_post_meta_fields_temp[ $_field['name'] ] );

									foreach ( $meta_data as $meta => $data ) {

										if ( 0 == strpos( $meta, $repeater['name'] ) && false !== strpos( $meta, $_field['name'] ) ) {
											$_post_meta_fields_temp[ $meta ] = $meta;

											/**
											 * @todo W.I.P.
											 */
											//self::$acf_fields[ $_field['name'] ]['wpglobus'] = array();

											$_key                             = str_replace( array(
												$repeater['name'] . '_',
												'_' . $_field['name'],
											), '', $meta );
											self::$post_multilingual_fields[] = self::$post_acf_field_prefix . $repeater['key'] . '-' . $_key . '-' . $_field['key'];
										}
									}
								}
							}

						endforeach;

						$_post_meta_fields = $_post_meta_fields_temp;

					}
				}
			}
		}

		return $_post_meta_fields;

	}

	/**
	 * Get post meta.
	 *
	 * Version 0.
	 * Don't use get_field_objects() to get ACF fields @see advanced-custom-fields\includes\api\api-template.php
	 * to prevent incorrect behavior on post page.
	 */
	public static function get_post_meta_fields_0( $post_id ) {

		global $wpdb;

		$_post_meta_fields = array();

		$post_id = (int) $post_id;

		if ( $post_id > 0 ) {

			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_value LIKE 'field_%'",
				$post_id
			), ARRAY_A );

			if ( ! empty( $rows ) ) {

				/**
				 * Filter to enable/disable wysiwyg field.
				 * Returning boolean.
				 *
				 * @since 1.9.17
				 *
				 * @param boolean.
				 */
				$field_wysiwyg_enabled = apply_filters( 'wpglobus/vendor/acf/field/wysiwyg', false );

				self::$post_multilingual_fields = array();
				foreach ( $rows as $key => $field ) {
					if ( '_' == $field['meta_key'][0] ) {

						$_acf_field = acf_maybe_get_field( $field['meta_value'] );

						if ( $_acf_field['type'] == 'wysiwyg' && ! $field_wysiwyg_enabled ) {
							// do nothing.
						} else {
							$_post_meta_fields[]              = substr_replace( $field['meta_key'], '', 0, 1 );
							self::$post_multilingual_fields[] = self::$post_acf_field_prefix . $field['meta_value'];
						}
					}
				}
			}
		}

		return $_post_meta_fields;

	}
	
	/**
	 * @since 2.1.11
	 *
	 * @param array $field
	 *
	 * @return bool
	 */
	protected static function get_3rd_party_field_status( $field ) {
		
		if ( function_exists('acf_table_load_plugin_textdomain') ) {
			
			/**
			 * Filter to enable/disable `table` field.
			 * Returning boolean.
			 *
			 * @since 2.1.11
			 *
			 * @param boolean.
			 */
			$field_table_enabled = apply_filters( 'wpglobus/vendor/acf/field/table', false );
			
			/**
			 * Advanced Custom Fields: Table Field.
			 * https://wordpress.org/plugins/advanced-custom-fields-table-field/
			 * 
			 * Table Field has incorrect filter 'update_post_metadata' @see advanced-custom-fields-table-field\acf-table-v5.php
			 * starting from v.1.3.5 an issue with the update_post_metadata filter was fixed.
			 */
			if ( 'table' == $field['type'] && ! $field_table_enabled ) { 
				return false;
			}

		}
		
		return true;
	}
	
	/**
	 * _acf_get_field_by_key
	 *
	 * This function will get a field via its key
	 *
	 * @see advanced-custom-fields\includes\api\api-field.php Version: 5.7.10
	 *
	 * @param	$key (string)
	 * @return	$field (array)
	 */	
	protected static function _acf_get_field_by_key( $key = '', $db_only = false ) {
	
		// try JSON before DB to save query time
		/**
		 * @todo check
		 * acf_is_local_field() & acf_get_local_field()
		 */
		if( !$db_only && acf_is_local_field( $key ) ) {
			
			return acf_get_local_field( $key );
			
		}
		
		// vars
		$post_id = self::acf_get_field_id( $key );
		
		// bail early if no post_id
		if( !$post_id ) return false;
		
		// return
		return self::_acf_get_field_by_id( $post_id, $db_only );
		
	}
	
	/**
	 * _acf_get_field_by_id
	 *
	 * This function will get a field via its ID
	 *
	 * @see advanced-custom-fields\includes\api\api-field.php Version: 5.7.10 
	 *
	 * @param	$post_id (int)
	 * @return	$field (array)
	 */
	protected static function _acf_get_field_by_id( $post_id = 0, $db_only = false ) {
		
		// get post
		$post = get_post( $post_id );

		// bail early if no post, or is not a field
		if( empty($post) || $post->post_type != 'acf-field' ) return false;
		
		
		// unserialize
		$field = maybe_unserialize( $post->post_content );
		
		
		// update attributes
		$field['ID'] = $post->ID;
		$field['key'] = $post->post_name;
		$field['label'] = $post->post_title;
		$field['name'] = $post->post_excerpt;
		$field['menu_order'] = $post->menu_order;
		$field['parent'] = $post->post_parent;

		// override with JSON
		if( !$db_only && acf_is_local_field($field['key']) ) {
			
			// load JSON field
			$local = acf_get_local_field( $field['key'] );
			
			
			// override IDs
			$local['ID'] = $field['ID'];
			$local['parent'] = $field['parent'];
			
			
			// return
			return $local;
			
		}
		
		
		// return
		return $field;
		
	}
	
	/**
	 * acf_get_field_id
	 *
	 * This function will lookup a field's ID from the DB
	 * Useful for local fields to find DB sibling
	 *
	 * @see advanced-custom-fields\includes\api\api-field.php Version: 5.7.10
	 *
	 * @param	$key (string)
	 * @return	$post_id (int)
	 */
	protected static function acf_get_field_id( $key = '' ) {
	
		// vars
		$args = array(
			'posts_per_page'	=> 1,
			'post_type'			=> 'acf-field',
			'orderby' 			=> 'menu_order title',
			'order'				=> 'ASC',
			'suppress_filters'	=> false,
			'acf_field_key'		=> $key,
			'update_post_meta_cache'	=> false,
			'update_post_term_cache'	=> false
		);
		
		
		// load posts
		$posts = get_posts( $args );
		
		
		// validate
		if( empty($posts) ) return 0;
		
		
		// return
		return $posts[0]->ID;
		
	}
	
}
