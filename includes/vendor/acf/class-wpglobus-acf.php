<?php
/**
 * File: class-wpglobus-acf.php
 *
 * @package WPGlobus\Vendor\ACF.
 *
 * @since 1.9.17
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
	protected function __construct() {}

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

						/**
						 * @since 2.7.8
						 */
						if ( empty( $_acf_field ) || ! is_array( $_acf_field ) ) {
							/**
							 * In some rarily cases self::_acf_get_field_by_key returns empty field.
							 * @todo need more investigation
							 */
							continue;
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

		/**
		 * @since 2.6.6
		 */
		$_post_meta_fields = self::handle_acf_fields( $_post_meta_fields_temp );

		return $_post_meta_fields;
	}

	/**
	 * @since 2.6.6
	 *
	 * @return array
	 */
	protected static function handle_acf_fields( $post_meta_fields ) {
		
		if ( ! empty( self::$acf_fields ) ) :
		
			if ( ! class_exists( 'WPGlobus_Vendor_Acf' ) ) {
				require_once( 'class-wpglobus-vendor-acf.php' );
			}

			$_vendors = array(
				'ACF'	 => false,
				'ACFPRO' => false
			);
			
			if ( defined('ACF_PRO') ) {
				$_vendors['ACFPRO'] = true;
			} else if( defined('ACF') ) {
				$_vendors['ACF'] = true;
			}
			
			$WPGlobus_Vendor_Acf = WPGlobus_Vendor_Acf::get_instance(
				array( 'vendor_scripts' => $_vendors )
			);
			
			/**
			 * @since 2.10.10 Check $WPGlobus_Vendor_Acf for null.
			 */
			if ( is_null($WPGlobus_Vendor_Acf) ) {
				return $post_meta_fields;
			}	
			
			$multilingual_field_name = $WPGlobus_Vendor_Acf::get_multilingual_field_name();
		
			$acf_fields = self::$acf_fields;

			foreach( $acf_fields as $name=>$attrs ) {
				
				if ( empty( $name ) ) {
					continue;
				}
				
				if ( ! $WPGlobus_Vendor_Acf::is_enabled_field_type( $attrs['type'] ) ) {
					/**
					 * Remove field that has disabled type.
					 */
					unset( self::$acf_fields[$name] );
					unset( self::$post_multilingual_fields[$name] );
					unset( $post_meta_fields[$name] );
					continue;	
				}
				
				if ( isset( $attrs[ $multilingual_field_name ] ) && false == $attrs[ $multilingual_field_name ] ) {
					/**
					 * Remove field that had been checked as non-multilingual.
					 */
					unset( self::$acf_fields[$name] );
					unset( self::$post_multilingual_fields[$name] );
					unset( $post_meta_fields[$name] );
				}			
			}
		
		endif;

		return $post_meta_fields;
	}

	/**
	 * @since 2.1.11
	 * @since 2.6.6 obsolete @see `self::$field_types` in Constructor (wpglobus\includes\vendor\acf\class-wpglobus-vendor-acf.php).
	 *
	 * @param array $field
	 *
	 * @return bool
	 */
	protected static function get_3rd_party_field_status( $field ) {
		
		return true;
		
		/**
		 * @todo Remove after testing.
		 */
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

# --- EOF