<?php
/**
 * File: class-wpglobus-yoast_seo.php
 *
 * @package WPGlobus\Builders\Yoast_SEO
 * @author  Alex Gor(alexgff)
 */


if ( ! class_exists( 'WPGlobus_Yoast_SEO' ) ) :

	/**
	 * Class WPGlobus_Yoast_SEO.
	 */
	class WPGlobus_Yoast_SEO extends WPGlobus_Builder {

		/**
		 * Current taxonomy.
		 */
		protected $taxonomy = '';
		
		protected $wpseo_taxonomy_option_name = '';
	
		/**
		 * Constructor.
		 */
		public function __construct() {
			
			/**
			 * @see 'WPSEO_PREMIUM_FILE' for premium add-on.
			 */
			 
			parent::__construct( 'yoast_seo' );
			
			$this->taxonomy = WPGlobus::Config()->builder->get('taxonomy'); 
			
			if ( $this->taxonomy ) {
				
				add_action( "{$this->taxonomy}_pre_edit_form", array( $this, 'on__pre_edit_form' ), 10, 2 );
				
				add_action( "{$this->taxonomy}_term_edit_form_top", array( $this, 'on__edit_form' ), 10, 2 );
			
				if ( class_exists('WPSEO_Taxonomy_Meta') ) {
					$wpseo_taxonomy_meta = WPSEO_Taxonomy_Meta::get_instance();
					$this->wpseo_taxonomy_option_name = $wpseo_taxonomy_meta::$name;
				} else {
					$this->wpseo_taxonomy_option_name = 'wpseo_taxonomy_meta';
				}

				add_filter( "option_{$this->wpseo_taxonomy_option_name}", array( $this, 'filter__wpseo_taxonomy_meta' ), 5, 2 );
				
				/**
				 * @see wordpress-seo\inc\class-wpseo-replace-vars.php
				 */
				add_filter( 'wpseo_replacements', array( $this, 'filter__wpseo_replacements' ), 5, 2 );
				
			}

		}

		/**
		 * Customization of the replacements before they are applied.
		 *
		 * @api     array   $replacements The replacements.
		 *
		 * @param   array   $args The object some of the replacement values might come from,
		 *                       could be a post, taxonomy or term.
		 */		
		public function filter__wpseo_replacements( $replacements, $args  ) {
			
			if ( ! is_array($replacements) || empty($replacements) ) {
				return $replacements;
			}
			
			$key = key($replacements);
			
			if ( WPGlobus_Core::has_translations( $replacements[$key] ) ) {
				$replacements[$key] = WPGlobus_Core::text_filter( $replacements[$key], WPGlobus::Config()->builder->get_language(), WPGlobus::RETURN_EMPTY );
			}
			
			return $replacements;
		}
		
		/**
		 * Filters the value of a 'wpseo_taxonomy_meta' option.
		 *
		 * @param mixed  $option_value  Value of the option. If stored serialized, it will be
		 *                       		unserialized prior to being returned.
		 * @param string $option_name   Option name.
		 */	
		public function filter__wpseo_taxonomy_meta( $option_value, $option_name ) {
			
			if ( empty( $option_value[$this->taxonomy] ) ) {
				return $option_value;
			}
			
			$_enabled_keys = array('wpseo_title', 'wpseo_desc', 'wpseo_focuskw');
			
			$language = WPGlobus::Config()->builder->get_language();
			
			foreach( $option_value[$this->taxonomy] as $tag_ID=>$values ) {
				foreach($values as $_key=>$_value) {
					if ( in_array($_key, $_enabled_keys) ) {
						if ( WPGlobus_Core::has_translations($_value) ) {
							$option_value[$this->taxonomy][$tag_ID][$_key] = WPGlobus_Core::text_filter($_value, $language, WPGlobus::RETURN_EMPTY);
						} else {
							if ( $language != WPGlobus::Config()->default_language ) {
								$option_value[$this->taxonomy][$tag_ID][$_key] = '';
							}
						}
					}
				}
			}

			return $option_value;
		}
		
		/**
		 * Fires before the Edit Term form for all taxonomies.
		 *
		 * @param object $tag      Current taxonomy term object.
		 * @param string $taxonomy Current $taxonomy slug.
		 */
		public function on__pre_edit_form( $tag, $taxonomy ) {
			
			$language = WPGlobus::Config()->builder->get_language();
			$tag->description	= WPGlobus_Core::text_filter( $tag->description, $language, WPGlobus::RETURN_EMPTY );
			$tag->name 			= WPGlobus_Core::text_filter( $tag->name, $language, WPGlobus::RETURN_EMPTY );
			
		}

		/**
		 * Fires at the beginning of the Edit Term form. Add language field to taxonomy edit form.
		 *
		 * @param object $tag      Current taxonomy term object.
		 * @param string $taxonomy Current $taxonomy slug.
		 */
		public function on__edit_form( $tag, $taxonomy ) {
			echo '<input type="hidden" name="'.WPGlobus::get_language_meta_key().'" value="'.WPGlobus::Config()->builder->get_language().'">';
			
		}

	}

endif;
