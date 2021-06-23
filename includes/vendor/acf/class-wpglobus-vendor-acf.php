<?php
/**
 * File: class-wpglobus-vendor-acf.php
 *
 * @since 2.6.6
 * @package WPGlobus\Vendor\ACF.
 */

if ( ! class_exists( 'WPGlobus_Vendor_Acf' ) ) :

	/**
	 * Class WPGlobus_Vendor_Acf.
	 */
	class WPGlobus_Vendor_Acf {

		/**
		 * Field post type.
		 */
		const FIELD_POST_TYPE = 'acf-field';
	
		/**
		 * Prefix for classes.
		 */	
		const CLASS_PREFIX = self::FIELD_POST_TYPE . '-';

		/**
		 * Field Group's post type.
		 */
		const FIELD_GROUP_POST_TYPE = 'acf-field-group';

		/**
		 * Status of multilingual field.
		 */
		const MULTILINGUAL = 'multilingual';	
		
		/**
		 * Multilingual field name (`wpglobus_multilingual_field`).
		 */
		const MULTILINGUAL_FIELD_NAME = 'wpglobus_' . self::MULTILINGUAL . '_field';

		/**
		 * Status of field that may be or may not be multilingual.
		 * May be changed in filter.
		 */
		const PRETENDER = 'pretender';	

		/**
		 * Pretender field name.
		 */
		const PRETENDER_FIELD_NAME = 'wpglobus_' . self::PRETENDER . '_field';

		/**
		 * Set of field types, that WPGlobus handles.
		 * field_type => field_status
		 */
		protected static $field_types = array();
		
		/**
		 * Array of fields that are multilingual on `post.php` page.
		 */
		protected static $field_ids = array();
		
		/**
		 * Incoming arguments.
		 */
		protected static $args = array();

		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;
		
		/**
		 * Constructor.
		 */
		protected function __construct( $args = array() ) {

			self::$args = wp_parse_args( 
				$args,
				array( 
					'vendor_scripts' => array(
						'ACF' 	 => false,
						'ACFPRO' => false
					)
				)
			);

			// @see wpglobus\includes\wpglobus-controller.php
			self::$field_types = array(
				'text' 		=> self::MULTILINGUAL,
				'textarea'  => self::MULTILINGUAL,
				'wysiwyg' 	=> self::PRETENDER,
				'table'  	=> self::PRETENDER, #with ACF:Table Field - https://wordpress.org/plugins/advanced-custom-fields-table-field/
				#'url',
				#'number',
				#'image',
				#'radio', ?
				#'google_map', ?
			);

			add_action( 'init', array( __CLASS__, 'on__init' ) );
			 
			/**
			 * @see advanced-custom-fields\includes\acf-field-group-functions.php
			 */
			add_filter(
				'acf/get_field_group_style',
				array(
					__CLASS__,
					'filter__get_field_group_style'
				), 10, 2
			);
			
			/**
			 * Add new setting on Edit Field Group page.
			 *
			 * @see advanced-custom-fields\includes\admin\views\field-group-field.php
			 * to get more info @see https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/
			 */
			add_action( 
				'acf/render_field_settings',
				array(
					__CLASS__,
					'on__render_field_settings'
				), 5, 1
			);
	
			/**
			 * @W.I.P @since 2.6.6
			 * @see advanced-custom-fields\includes\acf-field-functions.php
			 */
			/*
			add_action( 
				'acf/render_field',
				array(
					__CLASS__,
					'on__render_field'
				), 5, 1
			);
			// */
	
			/**
			 * Filters the $field array before it is updated.
			 * 
			 * @since 2.7.9
			 * @see advanced-custom-fields\includes\acf-field-functions.php
			 */
			add_filter( 
				'acf/update_field',
				array(
					__CLASS__,
					'on__update_field'
				), 5, 1
			);	
	
			/**
			 * Filter on post, page and CPT except Edit Field Group page.
			 * 
			 * @see advanced-custom-fields\includes\acf-field-functions.php
			 */
			add_filter(
				'acf/prepare_field',
				array(
					__CLASS__,
					'filter__prepare_field'
				), 5, 1
			);

			add_action( 
				'admin_print_scripts',
				array(
					__CLASS__,
					'on__field_group_admin_scripts',
				) 
			);

			/**
			 * Enqueue script for post.php & post-new.php pages except page for CPT Edit Field Group.
			 */			
			add_action( 
				'admin_print_scripts',
				array(
					__CLASS__,
					'on__admin_scripts',
				) 
			);
			
			add_action( 
				'admin_footer',
				array(
					__CLASS__,
					'on__admin_footer',
				) 
			);				
		}
		
		/**
		 * Get instance of this class.
		 *
		 * @return WPGlobus_Vendor_Acf || void
		 */
		public static function get_instance( $args = array() ) {
			
			if ( ! WPGlobus_WP::is_pagenow( array('post.php','post-new.php') ) ) {
				return;
			}

			if ( ! ( self::$instance instanceof WPGlobus_Vendor_Acf  ) ) {
				self::$instance = new self( $args );
			}

			return self::$instance;
		}		
		
		/**
		 * Init action.
		 */
		public static function on__init() {
			
			/**
			 * Filter ACF field types.
			 * Returning array.
			 *
			 * @since 2.6.6
			 *
			 * @param self::$field_types Array of field types.
			 * @param array 			 Field statuses.
			 */
			self::$field_types = apply_filters( 'wpglobus_vendor_acf_field_types', self::$field_types, self::get_field_statuses() );
		}
		
		/**
		 * @W.I.P @since 2.6.6
		 */
		public static function on__render_field( $field ) {
			return;
		}
		
		/**
		 * Add new setting on Edit Field Group page.
		 */
		public static function on__render_field_settings( $field ) {
			
			if ( $field['ID'] == 0 ) {
				return;
			}
			
			$_name = '';
			
			if ( ! self::is_enabled_field_type( $field['type'] ) ) {
				return;
			} 
			
			if ( self::MULTILINGUAL == self::get_field_status( $field ) ) {		
						
				$_name = self::MULTILINGUAL_FIELD_NAME;
				$_value = self::is_multilingual_field( $field );
			
			} elseif ( self::PRETENDER == self::get_field_status( $field ) ) {
			
				$_name = self::PRETENDER_FIELD_NAME;
				$_value = false;

			}
	
			if ( empty( $_name ) ) {
				return;
			}
	
			$_instructions = sprintf( // translators: %s are for A tags.
				esc_html__( 'Use as multilingual with %1$sWPGlobus%2$s', 'wpglobus' ),
				'<a href="#" target="_blank">',
				'</a>'
			);
			
			/**
			 * @see advanced-custom-fields/includes/acf-field-functions.php
			 */
			acf_render_field_setting( $field, array(
				'label'			=> esc_html__( 'Multilingual', 'wpglobus' ),
				'instructions'	=> $_instructions,
				'name'			=> $_name,
				'type'			=> 'true_false',
				'ui'			=> 1,
				'value'			=> $_value
			), true );			
		}

		/**
		 * Check if field is multilingual.
		 *
		 * @param array $field Field array.
		 *
		 * return boolean
		 */		
		public static function is_multilingual_field( $field = array() ) {
			
			if ( empty( $field ) ) {
				return false;
			}			

			if ( ! self::is_enabled_field_type( $field['type'] ) ) {
				/**
				 * A field with a disabled type cannot be multilingual.
				 */				
				return false;
			}

			if ( self::MULTILINGUAL != self::$field_types[ $field['type'] ] ) {
				return false;
			}

			if ( ! isset( $field[ self::MULTILINGUAL_FIELD_NAME ] ) ) {
				/**
				 * Field is multilingual by default.
				 */
				return true;
			}

			return $field[ self::MULTILINGUAL_FIELD_NAME ];			
		}	

	
		/**
		 * Get multilingual field ID.
		 */		
		public static function filter__prepare_field( $field ) {

			global $post;

			if ( self::FIELD_GROUP_POST_TYPE == $post->post_type ) {
				return $field;
			}
			
			if ( ! self::is_multilingual_field( $field ) ) {
				return $field;
			}

			/**
			 * $field['id'] may contains clone field like `acf-field_5cda40e0978f9-acfcloneindex-field_5cda42e66249b`.
			 * to check out @see post with ACF repeater field.
			 */
			self::$field_ids[] = $field['id'];

			return $field;
		}
		
		/**
		 * Filters the generated CSS styles.
		 *
		 * @param	string $style The CSS styles.
		 * @param	array $field_group The field group array.
		 */	
		public static function filter__get_field_group_style( $style, $field_group ) {
			
			if( is_array($field_group['hide_on_screen']) ) {
				
				if ( in_array( 'the_content', $field_group['hide_on_screen'], true ) ) {
					/**
					 * If editor is hidden by ACF, we hide WPGlobus, too.
					 */
					add_filter(
						'wpglobus_postdivrich_style',
						array(
							__CLASS__,
							'filter__postdivrich_style'
						), 10, 2
					);
				}
				
			}

			return $style;
		}
		
		/**
		 * Filters the field array before it is updated.
		 *
		 * @since 2.7.9
		 *
		 * @return array
		 */
		public static function on__update_field( $field ) {
			
			if ( (int) $field['ID'] == 0 && ! isset( $field[ self::MULTILINGUAL_FIELD_NAME ] ) ) {
				// New field.
				if ( self::is_multilingual_field( $field ) ) {
					// We need to add MULTILINGUAL_FIELD_NAME for each new field. 
					$field[ self::MULTILINGUAL_FIELD_NAME ] = 1;
				}
			}
			return $field;
		}
		
		/**
		 * Callback for admin footer.
		 *
		 * @return void
		 */
		public static function on__admin_footer() {
	
			if ( ! WPGlobus_WP::is_pagenow( array('post.php') ) ) {
				return;
			}	
	
			global $post;

			if ( self::FIELD_GROUP_POST_TYPE == $post->post_type ) {
				return;
			}
	
			if ( empty( self::$field_ids ) ) {
				return;
			}
	
			$_ids = implode( ',', self::$field_ids );
			?>
			<script type='text/javascript'>
				/* <![CDATA[ */
				var WPGlobusVendorAcfFields = "<?php echo $_ids; ?>";
				/* ]]> */
			</script>
			<?php
		}
		
		/**
		 * Enqueue script for post.php & post-new.php pages except page for CPT Edit Field Group.
		 *
		 * @since 2.6.6
		 */
		public static function on__admin_scripts() {
			
			global $post;

			if ( self::FIELD_GROUP_POST_TYPE == $post->post_type ) {
				return;
			}
			
			// @see  `Enqueue js for ACF support` in wpglobus\includes\class-wpglobus.php
			
			/**
			 * Filter to disable translation of selected ACF and ACF Pro fields.
			 *
			 * To exclude field in ACF plugin you need to use the field name from Field Group ( usually wp-admin/edit.php?post_type=acf ).
			 * To exclude field in ACF Pro plugin you need to use id, see Wrapper Attributes section on field's edit page.
			 *
			 * @param array   $disabled_fields Default is empty array.
			 * @param boolean $is_acf_pro      Type of ACF plugin.
			 *
			 * @return array
			 */
			$disabled_fields = apply_filters( 'wpglobus_disabled_acf_fields', array(), self::$args['vendor_scripts'] );

			$actions = array(
				'fixTextFields' => false
			);
			if ( defined('ACF_VERSION') ) {
				if ( version_compare( ACF_VERSION, '5.8.0', '>=' ) ) {
					$actions['fixTextFields'] = true;
				}
			}

			$_source = array();
			$_source['class'] = __CLASS__;
			$_source['plugin_basename'] = plugin_basename( __FILE__ );

			wp_register_script(
				'wpglobus-acf',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-vendor-acf' . WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery', 'wpglobus-admin' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-acf' );
			wp_localize_script(
				'wpglobus-acf',
				'WPGlobusAcf',
				array(
					'wpglobus_version'  => WPGLOBUS_VERSION,
					'acf_version'       => defined('ACF_VERSION') ? ACF_VERSION : false,
					'pro'               => self::is_acf_pro() ? 'true' : 'false',
					'actions'      	    => $actions,
					'fields'            => array(),
					'disabledFields'    => $disabled_fields,
					'source'       	    => $_source,
					'builder_id'        => WPGlobus::Config()->builder->get_id(),
					'language'        	=> WPGlobus::Config()->builder->get_language(),
					'translatableClass' => 'wpglobus-translatable',
				)
			);			
		}
		
		/**
		 * Enqueue script for Edit Field Group page.
		 *
		 * @since 2.6.6
		 */
		public static function on__field_group_admin_scripts() {
			
			global $post;

			if ( self::FIELD_GROUP_POST_TYPE != $post->post_type ) {
				return;
			}

			$_fields  = array();
			$_classes = array();
			
			foreach( self::get_field_statuses() as $_key=>$_status ) {
				$_fields[ $_status ] = self::get_field_types( $_status );
				$_classes[ $_key ]['statusClass'] = 'wpglobus-status-'.$_status;
				if ( 'multilingual' == $_status ) {
					$_classes[ $_key ]['translatableClass'] = 'wpglobus-translatable';
				}
			}
			
			$l10n = array();
			$l10n['wysiwyg-pretender-tip'] = sprintf( // translators: %s are for A tags.
				esc_html__( 'To use this field, please activate the %1$sACF Plus%2$s module', 'wpglobus' ),
				'<a href="'.WPGlobus::URL_WPGLOBUS_SHOP.'wpglobus-plus/#acf" target="_blank" style="text-decoration:underline">',
				'</a>'
			);
			
			$data = array(
				'version' 		    => WPGLOBUS_VERSION,
				'acf_version'       => defined('ACF_VERSION') ? ACF_VERSION : false,
				'names'			    => self::get_field_names(),
				'translatableClass' => 'wpglobus-translatable',
				'classes' 			=> $_classes,
				'fields' 			=> $_fields,
				'fieldStatuses'		=> self::get_field_statuses(),
				'l10n'				=> $l10n,
				'pro'               => self::is_acf_pro() ? 'true' : 'false'
			);
			
			wp_register_script(
				'wpglobus-vendor-acf',
				self::url_js( 'wpglobus-vendor-acf' ),
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-vendor-acf' );
			wp_localize_script(
				'wpglobus-vendor-acf',
				'WPGlobusVendorAcf',
				array(
					'data' => $data
				)
			);			
		}
		
		/**
		 * Filter postdivrich style for extra language.
		 *
		 * @param string $style    Current CSS rule
		 * @param string $language Unused
		 * @return string
		 */
		public static function filter__postdivrich_style(
			$style,
			/** @noinspection PhpUnusedParameterInspection */
			$language
		) {
			return $style . ' display:none;';
		}

		/**
		 * Check for Pro version.
		 *
		 * @return bool
		 */
		public static function is_acf_pro() {
			if ( isset( self::$args['vendor_scripts']['ACFPRO'] ) && self::$args['vendor_scripts']['ACFPRO'] ) {
				return true;
			}
			return false;
		}
		
		/**
		 * Checking if field type is in enabled types array.
		 *
		 * @return bool
		 */
		public static function is_enabled_field_type( $field_type = '' ) {
			
			if ( empty( $field_type ) ) {
				return false;
			}
			
			if ( ! in_array( $field_type, self::get_enabled_field_types() ) ) {
				return false;
			}
			
			return true;
		}
		
		/**
		 * Get enabled field types.
		 *
		 * @return array
		 */
		public static function get_enabled_field_types() {
			
			static $_types = null;
			
			if ( is_null( $_types  ) ) {
				
				$_types = self::get_field_types( 
					array( 
						self::MULTILINGUAL, 
						self::PRETENDER
					) 
				);
				
			}

			return $_types;
		}

		/**
		 * Get field status.
		 *
		 * return string || bool
		 */
		public static function get_field_status( $field ) {

			if ( ! isset( self::$field_types[ $field['type'] ] ) ) {
				return false;
			}
			
			return self::$field_types[ $field['type'] ];
		}
		
		/**
		 * Get field statuses.
		 *
		 * @return array
		 */
		public static function get_field_statuses() {
			return array(
				'MULTILINGUAL' => self::MULTILINGUAL,
				'PRETENDER'	   => self::PRETENDER
			);
		}
	
		/**
		 * Get field names.
		 *
		 * @return array
		 */
		public static function get_field_names() {
			return array(
				'MULTILINGUAL' => self::MULTILINGUAL_FIELD_NAME,
				'PRETENDER'	   => self::PRETENDER_FIELD_NAME
			);
		}
	
		/**
		 * Get field types.
		 *
		 * @return array
		 */
		public static function get_field_types( $status = false ) {
			
			$_result = array();
			
			if ( ! $status ) {
				return array_keys( self::$field_types );
			}
			
			if ( is_string( $status ) ) {
				$status = (array) $status;
			}
			
			foreach( self::$field_types as $field_type=>$_status ) {
				if ( in_array( $_status, $status ) ) {
					$_result[] = $field_type;
				}
			}
			
			if ( empty( $_result ) ) {
				return false;
			}
			
			return $_result;
		}

		/**
		 *
		 */
		public static function get_multilingual_field_name() {
			return self::MULTILINGUAL_FIELD_NAME;
		}
		
		/**
		 * Get url's part of vendor folder.
		 */		
		protected static function vendor_dir_url() {
			return WPGlobus::plugin_dir_url() . 'includes/vendor/acf/';
		}

		/**
		 * URL to the JS script.
		 *
		 * @param string $script_name [optional] The script name without extension.
		 *                            If not passed, will return the JS root URL.
		 *
		 * @return string The URL.
		 */
		protected static function url_js( $script_name = '' ) {
			$url = self::vendor_dir_url() . 'assets/js';
			if ( $script_name ) {
				$url .= '/' . $script_name . WPGlobus::SCRIPT_SUFFIX() . '.js';
			}

			return $url;
		}

	} // class WPGlobus_Vendor_Acf.

endif;

# --- EOF
