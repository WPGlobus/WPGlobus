<?php
/**
 * File: class-wpglobus-yoastseo140.php
 * 
 * Support of Yoast SEO 14.0
 *
 * @package WPGlobus\Vendor\YoastSEO
 * @since 2.4
 * @since 2.5.19 Added support multilingual fields for social tab.
 */

/**
 * Class WPGlobus_YoastSEO.
 */
class WPGlobus_YoastSEO {

	/**
	 * Yoast SEO separator.
	 *
	 * @var string
	 */
	public static $yoastseo_separator = '';

	/**
	 * Name of the script.
	 *
	 * @var string
	 */
	public static $handle_script = 'wpglobus-yoastseo';
	
	/**
	 * Name of the dashboard script.
	 *
	 * @since 2.4.5
	 * @var string
	 */
	public static $handle_script_dashboard = 'wpglobus-yoastseo-dashboard';

	/**
	 * Name of the premium script.
	 *
	 * @since 1.7.2
	 * @var string
	 */
	public static $handle_script_premium = 'wpglobus-yoastseo-premium';

	/**
	 * Current version yoast seo.
	 *
	 * @since 1.8
	 * @var string	 
	 */
	protected static $version = '';

	/**
	 * Contains wpseo post meta.
	 *
	 * @since 2.2.16
	 * @var null|array 
	 */	
	protected static $wpseo_meta = null;

	/**
	 * Contains wpseo taxonomy meta.
	 *
	 * @since 2.5.1
	 * @var null|array 
	 */	
	protected static $wpseo_taxonomy_meta = null;

	/**
	 * Contains document title.
	 *
	 * @since 2.4.7
	 * @var null|string
	 */		
	protected static $title = null;
	
	/**
	 * Plus access.
	 *
	 * @since 2.2.20
	 * @var boolean|string
	 */		
	protected static $plus_module = false;

	/**
	 * Canonical URLs.
	 *
	 * @since 2.5.11
	 * @var array
	 */			
	protected static $canonical_url = array();
	
	/**
	 * Description meta value.
	 *
	 * @scope front
	 * @since 2.6.3
	 * @var string
	 */	
	protected static $description = '';

	/**
	 * Static "controller"
	 */
	public static function controller($version, $plus_module = false) {
			
		self::$version = $version;
		self::$plus_module = $plus_module;

		if ( is_admin() ) {
			
			/**
			 * @since 2.2.20
			 */
			add_action( 'admin_print_scripts', array(
				__CLASS__,
				'action__admin_print_scripts'
			) );
				
			if ( WPGlobus_WP::is_pagenow( 'edit.php' ) ) {
				
				/**
				 * To translate Yoast columns on `edit.php` page.
				 * @since 2.2.16
				 */
				add_filter( 'wpseo_title', array(
					__CLASS__,
					'filter__wpseo_title'
				), 5 );
				
				
				add_filter( 'wpseo_metadesc', array(
					__CLASS__,
					'filter__wpseo_metadesc'
				), 5 );
			}

		} else {
			
			/**
			 * Frontend.
			 */
			 
			/**
			 * Filter SEO title and meta description on front only, when the page header HTML tags are generated.
			 * AJAX is probably not required (waiting for a case).
			 */
			// add_filter( 'wpseo_title', array( __CLASS__, 'filter__title' ), PHP_INT_MAX );
			/**
			 * Filter title description.
			 * @since 2.5.22
			 */			
			add_filter( 'wpseo_title', array( __CLASS__, 'filter_front__title' ), PHP_INT_MAX, 2 );
	
			/**
			 * Filter meta description.
			 * @since 2.4
			 */
			add_filter( 'wpseo_metadesc', array( __CLASS__, 'filter_front__description' ), 5, 2 );	
			
			/**
			 * Open Graph.
			 * @since 2.4 Open Graph title.
			 * @since 2.4 Open Graph description.
			 * @since 2.4 Open Graph URL.
			 * @since 2.5.19 Open Graph image. 
			 */			
			add_filter( 'wpseo_opengraph_title', array( __CLASS__, 'filter_front__opengraph_title' ), 5, 2 );	
			add_filter( 'wpseo_opengraph_desc', array( __CLASS__, 'filter_front__opengraph_description' ), 5, 2 );	
			add_filter( 'wpseo_opengraph_image', array( __CLASS__, 'filter_front__opengraph_image' ), 5, 2 );
			add_filter( 'wpseo_opengraph_url', array( __CLASS__, 'filter_front__localize_url' ), 5, 2 );
			
			/**
			 * Twitter.
			 * @since 2.5.19
			 */
			add_filter( 'wpseo_twitter_title', array( __CLASS__, 'filter_front__twitter_title' ), 5, 2 );
			add_filter( 'wpseo_twitter_description', array( __CLASS__, 'filter_front__twitter_description' ), 5, 2 );
			add_filter( 'wpseo_twitter_image', array( __CLASS__, 'filter_front__twitter_image' ), 5, 2 );
			
			/**
			 * Filter canonical URL and open graph URL.
			 * @since 2.4
			 */
			add_filter( 'wpseo_canonical', array( __CLASS__, 'filter_front__localize_url' ), 5, 2 );	
	
			/**
			 * Filter of the rel prev and next URL put out by Yoast SEO.
			 * @since 2.5.11
			 */
			add_filter( 'wpseo_adjacent_rel_url', array( __CLASS__, 'filter_front__wpseo_adjacent_rel_url' ), 5, 3 );	
			
			/**
			 * Filter the HTML output of the Yoast SEO breadcrumbs class.
			 * @since 2.4.2
			 */		
			add_filter( 'wpseo_breadcrumb_output', array( __CLASS__, 'filter__breadcrumb_output' ), 5, 2 );	

			/**
			 * @todo check for '_yoast_wpseo_title' meta
			 * @see <title> view-source:http://test/test-post-seo/
			 * @see <title> view-source:http://test/ru/test-post-seo/
			 */
			add_filter( 'get_post_metadata', array( __CLASS__, 'filter__get_post_metadata' ), 6, 4 );

			/**
			 * Filter metadata during sitemap generation.
			 * @since 2.8.7
			 */			
			add_filter( 'get_post_metadata', array( __CLASS__, 'filter__sitemap_get_post_metadata' ), 5, 4 );
			
			/**
			 * Filter meta keywords.
			 * @since 1.8.8
			 */
			add_filter( 'wpseo_metakeywords', array( __CLASS__, 'filter__metakeywords' ), 0 );

			/**
			 * Filter `wpseo_schema_breadcrumb` generator.
			 * @since 2.4.7
			 */			
			add_filter( 'wpseo_schema_breadcrumb', array( __CLASS__, 'filter__wpseo_schema_breadcrumb' ), 5, 2 );

			/**
			 * Filter `wpseo_schema_webpage` generator.
			 * @since 2.4.14
			 */	
			add_filter( 'wpseo_schema_webpage', array( __CLASS__, 'filter__wpseo_schema_webpage' ), 5, 2 );

			/**
			 * Filter "application/ld+json".
			 * @since 2.7.4
			 * 
			 * @see wordpress-seo\src\presenters\schema-presenter.php
			 */			
			add_action( 'wpseo_json_ld', array( __CLASS__, 'on__wpseo_json_ld' ), 5 );
			
			/**	
			 * @since 2.7.7
			 */			
			add_filter( 'option_wpseo_titles', array( __CLASS__, 'filter__wpseo_titles' ), 5, 2 );

			/**
			 * @since 2.7.14
			 */
			add_filter( 'wpseo_replacements', array( __CLASS__, 'filter__wpseo_replacements' ), 0, 2 );
			
			/**
			 * @since 2.7.14
			 */			
			add_filter( 'wpseo_enhanced_slack_data', array( __CLASS__, 'filter__wpseo_enhanced_slack_data' ), 5, 2 );

			/**
			 * @since 2.7.14
			 */				
			add_filter( 'wpseo_schema_person', array( __CLASS__, 'filter__wpseo_schema_person' ), 5, 4 );

			/**
			 * @since 2.7.14
			 * @todo Add filters like "get_the_author_{$field}" to controller, @see wp-includes\author-template.php
			 */				
			add_filter( 'get_the_author_display_name', array( 'WPGlobus_Filters', 'filter__text' ), 5 );
			add_filter( 'get_the_author_description', array( 'WPGlobus_Filters', 'filter__text' ), 5 );			
		}
	}

	/**
	 * Filter: 'wpseo_schema_<type>' - Allows changing graph piece output by @type.
	 * @see wordpress-seo\src\generators\schema-generator.php
	 *
	 * @since 2.7.14
	 *
	 * @param array $graph_piece 							  The graph piece to filter.
	 * @param Meta_Tags_Context $context 				  	  A value object with context variables.
	 * @param Abstract_Schema_Piece $graph_piece_generator    A value object with context variables.
	 * @param Abstract_Schema_Piece[] $graph_piece_generators A value object with context variables.
	 */
	public static function filter__wpseo_schema_person( $graph_piece, $context, $graph_piece_generator, $graph_piece_generators ) {

		$keys = array( 'name', 'description' );
		foreach( $keys as $_key ) {
			if ( ! empty( $graph_piece[$_key] ) && WPGlobus_Core::has_translations( $graph_piece[$_key] ) ) {
				$graph_piece[$_key] = WPGlobus_Core::extract_text( $graph_piece[$_key], WPGlobus::Config()->language );
			}		
		}
		
		$image_keys = array( 'caption' );
		foreach( $image_keys as $_key ) {
			if ( WPGlobus_Core::has_translations( $graph_piece['image'][$_key] ) ) {
				$graph_piece['image'][$_key] = WPGlobus_Core::extract_text( $graph_piece['image'][$_key], WPGlobus::Config()->language );
			}		
		}		
		return $graph_piece;
	}
	
	/**
	 * Filter: 'wpseo_enhanced_slack_data' - Allows filtering of the enhanced data for sharing on Slack.
	 * @see wordpress-seo\src\presenters\slack\enhanced-data-presenter.php
	 *
	 * @since 2.7.14
	 *
	 * @param array $data 						   The enhanced Slack sharing data.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 */
	public static function filter__wpseo_enhanced_slack_data( $data, $presentation ) {
		foreach( $data as $_key=>$_value ) {
			if ( WPGlobus_Core::has_translations($_value) ) {
				$data[$_key] = WPGlobus_Core::extract_text( $_value, WPGlobus::Config()->language );
			}
		}
		return $data;
	}
	
	/**
	 * Customization of the replacements before they are applied.
	 *
	 * @since 2.7.14
	 *
	 * @param array $replacements The replacements.
	 * @param array $args         The object some of the replacement values might come from,
	 *                            could be a post, taxonomy or term.
	 */
	public static function filter__wpseo_replacements( $replacements, $args ) {

		if ( empty( $replacements ) ) {
			return $replacements;
		}

		$replacements = (array) $replacements;

		foreach ( $replacements as &$replacement ) {
			$replacement = WPGlobus_Core::extract_text( $replacement );
		}

		return $replacements;
	}


	/**
	 * Filter "application/ld+json".
	 * @since 2.7.4
	 */	
	public static function on__wpseo_json_ld() {
		add_filter( 'wp_get_attachment_caption', array( __CLASS__, 'filter__wp_get_attachment_caption' ), 5, 2 );
	}

	/**	
	 * Filter `wpseo_titles` option.
	 * @since 2.7.7
	 */	
	public static function filter__wpseo_titles( $value, $option ) {
		$_keys[] = 'breadcrumbs-home';
		foreach ( $_keys as $_key ) {
			if ( WPGlobus_Core::has_translations($value[$_key]) ) {
				$value[$_key] = WPGlobus_Core::extract_text( $value[$_key], WPGlobus::Config()->language );
			}
		}			
		return $value;
	}
	
	/**
     * Filters the attachment caption in "application/ld+json".
	 * @since 2.7.4
	 */
	public static function filter__wp_get_attachment_caption( $caption, $post_id ) {
		
		if ( WPGlobus_Core::has_translations($caption) ) {
			return WPGlobus_Core::extract_text( $caption, WPGlobus::Config()->language );
		}

		return $caption;
	}
	
	/**
	 * obsolete @since 2.5.22
	 * Filter Yoast post meta title.
	 *
	 * @scope front
	 * @since 1.9.18
	 * @since 2.4.7	  Handle multilingual title from `postmeta` table.
	 *
	 * @param string $title Post title.
	 *
	 * @return string.
	 */	
	public static function filter__title( $title ) {

		/**
		 * In some cases we can get $title like {:en}En title{:}{:ru}Ru title{:}{:fr}Fr title{:} - SiteTitle
		 * so, let's filter.
		 */
		if ( WPGlobus_Core::has_translations($title) ) {
		
			if ( is_null( self::$title ) ) {
				self::$title = $title;
			}			
			return WPGlobus_Core::extract_text( self::$title, WPGlobus::Config()->language );
		}

		/**
		 * We can get title in last saved language (has no multilingual) from @see `wp_yoast_indexable` table.  
		 * So, we need get multilingual title from `postmeta` table.
		 * @since 2.4.7
		 */
		if ( ! is_null( self::$title ) ) {
			return WPGlobus_Core::extract_text( self::$title, WPGlobus::Config()->language );
		}
		
		/** @global wpdb $wpdb */		
		global $wpdb;
		
		/** @global WP_Post $post */
		global $post;
	
		/**
		 * @since 2.4.14 Fixed PHP Notice: Trying to get property 'ID' of non-object.
		 */
		if ( $post instanceof WP_Post && (int) $post->ID > 0 ) {
			$query = $wpdb->prepare( 
				"SELECT meta_value FROM {$wpdb->prefix}postmeta AS m WHERE m.post_id = %s AND m.meta_key = %s",
				$post->ID,
				'_yoast_wpseo_title'
			);
			
			$meta = $wpdb->get_var($query);
			if ( ! empty($meta)	&& false != mb_strpos($meta, $title) && WPGlobus_Core::has_translations($meta) ) {
				self::$title = $meta;
				return WPGlobus_Core::extract_text( self::$title, WPGlobus::Config()->language );
			}
		}		
	
		return $title;
	}
	
	/**
	 * Filter for changing the Yoast SEO generated Open Graph description.
	 *
	 * @see wordpress-seo\src\presenters\open-graph\description-presenter.php
	 *
	 * @since 2.5.19
	 *
	 * @scope front
	 * @param string 				 $description  The description.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__opengraph_description( $description, $presentation ) {
		
		if ( empty( $description ) ) {
			return $description;
		}
		
		if ( 'post' == $presentation->model->object_type ) {
			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_opengraph-description'][0] ) ) {
				
				$description = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_opengraph-description'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {
					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */					
					$description = wpseo_replace_vars( $description, $presentation->source );
				}
			} else {
				/**
				 * Opengraph description is empty. We get it from `description` meta.
				 * @since 2.6.3
				 */
				$description = self::$description;
			}
			
		} elseif ( 'term' == $presentation->model->object_type ) {
			
			/**
			 * Taxonomy.
			 */
			$__desc = self::get_taxonomy_meta( $presentation->model->object_sub_type, $presentation->model->object_id, 'wpseo_desc' );

			if ( empty( $__desc ) ) {
				
				$__template = self::get_option( 'wpseo_titles', 'metadesc-tax-' . $presentation->model->object_sub_type );

				if ( ! empty( $__template ) ) {
					$__desc = $__template;
				} else {
					// @W.I.P if empty `Meta description` from Yoast, then we need output `Description` from Edit taxonomy page.
					$__desc = $presentation->source->description;
				}

			}
		
			$description = wpseo_replace_vars( $__desc, $presentation->source );	
		
		} elseif ( 'home-page' == $presentation->model->object_type ) {
			
			/**
			 * When homepage displays latest post.
			 * @since 2.7.3
			 */
			if ( WPGlobus_Core::has_translations($description) ) {
				$description = WPGlobus_Core::text_filter( $description, WPGlobus::Config()->language );
			}
			
		} elseif ( 'user' == $presentation->model->object_type ) {
			
			/** 
			 * @since 2.10.1
			 */			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'user_meta' );
			
			if ( ! empty( $meta_cache['wpseo_metadesc'][0] ) ) {
				
				$description = WPGlobus_Core::text_filter( $meta_cache['wpseo_metadesc'][0], WPGlobus::Config()->language );
				$description = wpseo_replace_vars( $description, $presentation->source );
			
			} else {

				if ( empty($description) ) {
					$description = self::get_option( 'wpseo_titles', 'metadesc-author-wpseo' );
				}

				if (  ! empty($description) ) {
						
					if ( WPGlobus_Core::has_translations($description) ) {
						$description = WPGlobus_Core::text_filter( $description, WPGlobus::Config()->language );
					}
					
					$description = wpseo_replace_vars( $description, $presentation->source );
				}			
				
			}
		}

		return $description;
	}
	
	/**
	 * Filter for changing the Open Graph image.
	 *
	 * @see wordpress-seo\src\presenters\open-graph\image-presenter.php
	 *
	 * @since 2.5.19
	 *
	 * @scope front
	 * @param string 				 $image_url		The URL of the Open Graph image.
	 * @param Indexable_Presentation $presentation  The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__opengraph_image( $image_url, $presentation ) {

		if ( empty( $image_url) ) {
			return $image_url;
		}
		
		if ( 'post' == $presentation->model->object_type ) {
			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_opengraph-image'][0] ) ) {
				
				$image_url = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_opengraph-image'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {
					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */
					$image_url = wpseo_replace_vars( $image_url, $presentation->source );
				}
			}
		}

		return $image_url;
	}	
	
	/**
	 * Filter for changing the Twitter title.
	 *
	 * @see wordpress-seo\src\presenters\twitter\title-presenter.php
	 *
	 * @since 2.5.19
	 *
	 * @scope front
	 * @param string 				 $title 		The Twitter title.
	 * @param Indexable_Presentation $presentation  The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__twitter_title( $title, $presentation ) {

		if ( empty( $title ) ) {
			return $title;
		}
		
		if ( 'post' == $presentation->model->object_type ) {
			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_twitter-title'][0] ) ) {
				
				$title = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_twitter-title'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {
					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */
					$title = wpseo_replace_vars( $title, $presentation->source );
				}
			}
		}

		return $title;
	}

	/**
	 * Filter for changing the Twitter description as output in the Twitter card by Yoast SEO.
	 *
	 * @see wordpress-seo\src\presenters\twitter\description-presenter.php
	 *
	 * @since 2.5.19
	 *
	 * @scope front
	 * @param string 				 $description  The description string.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */	
	public static function filter_front__twitter_description( $description, $presentation ) {

		if ( empty( $description ) ) {
			return $description;
		}
		
		if ( 'post' == $presentation->model->object_type ) {
			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_twitter-description'][0] ) ) {
				
				$description = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_twitter-description'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {
					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */					
					$description = wpseo_replace_vars( $description, $presentation->source );
				}
			}
		}

		return $description;		
	}
	
	/**
	 * Filter for changing the Twitter Card image.
	 *
	 * @see wordpress-seo\src\presenters\twitter\image-presenter.php
	 *
	 * @since 2.5.19
	 *
	 * @scope front
	 * @param string 				 $image        Image URL string.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */		
	public static function filter_front__twitter_image( $image, $presentation ) {

		if ( empty( $image ) ) {
			return $image;
		}
		
		if ( 'post' == $presentation->model->object_type ) {
			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_twitter-image'][0] ) ) {
				
				$image = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_twitter-image'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {
					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */					
					$image = wpseo_replace_vars( $image, $presentation->source );
				}
			}
		}
		
		return $image;
	}
	
	/**
	 * Filter for changing the Yoast SEO generated title.
	 *
	 * @see wordpress-seo\src\presenters\open-graph\title-presenter.php
	 *
	 * @since 2.5.19
	 *
	 * @scope front
	 * @param string 				 $title 	   The title.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__opengraph_title( $title, $presentation ) {
		
		if ( empty( $title ) ) {
			return $title;
		}

		if ( 'post' == $presentation->model->object_type ) {
			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_opengraph-title'][0] ) ) {
				
				$title = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_opengraph-title'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {
					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */					
					$title = wpseo_replace_vars( $title, $presentation->source );
				}
				
			} else {

				/** 
				 * @since 2.5.23
				 */
				if ( WPGlobus_Core::has_translations( $title ) ) {
					$title = WPGlobus_Core::extract_text( $title, WPGlobus::Config()->language );
				} else {
					/** 
					 * The Opengraph title is received in the last saved language. Get it from meta.			
					 * @since 2.8.1
					 */
					if ( empty( $meta_cache['_yoast_wpseo_title'][0] ) ) {
						$__title = self::get_option( 'wpseo_titles', 'title-' . $presentation->model->object_sub_type );
						if ( ! is_null($__title) ) {
							$title = $__title;
						}
					} else {
						$title = $meta_cache['_yoast_wpseo_title'][0];
					}
					
					if ( ! empty($title) ) {
						$title = wpseo_replace_vars( $title, $presentation->source );
					}
				}

			}
			
		} elseif ( 'term' == $presentation->model->object_type ) {
			
			/**
			 * Taxonomy.
			 */
			$__title = self::get_taxonomy_meta( $presentation->model->object_sub_type, $presentation->model->object_id, 'wpseo_title' );

			if ( empty( $__title ) ) {
				$__title = self::get_option( 'wpseo_titles', 'title-tax-' . $presentation->model->object_sub_type );
			}
		
			$title = wpseo_replace_vars( $__title, $presentation->source );
			
		} elseif ( 'user' == $presentation->model->object_type ) {
			
			/** 
			 * @since 2.10.1
			 */			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'user_meta' );
			
			if ( ! empty( $meta_cache['wpseo_title'][0] ) ) {
				
				$title = WPGlobus_Core::text_filter( $meta_cache['wpseo_title'][0], WPGlobus::Config()->language );
				$title = wpseo_replace_vars( $title, $presentation->source );
			
			} else {
				
				if ( empty($title) ) {
					$title = self::get_option( 'wpseo_titles', 'title-author-wpseo' );
				}
				
				if ( ! empty($title) ) {
			
					if ( WPGlobus_Core::has_translations($title) ) {
						$title = WPGlobus_Core::text_filter( $title, WPGlobus::Config()->language );	
					}
					
					$title = wpseo_replace_vars( $title, $presentation->source );
				}
			}
		}

		return $title;
	}
	
	/**
	 * Filter post meta.
	 *
	 * @since 1.9.21
	 * @since 2.1.3
	 * @see function function get_value() in wordpress-seo\inc\class-wpseo-meta.php
	 */
	public static function filter__get_post_metadata( $check, $object_id, $meta_key, $single ) {

		global $post;
	
		if ( $single ) {
			return $check;
		}
		
		if ( ! is_object($post) ) {
			return $check;
		}
		
		if ( $object_id != $post->ID ) {
			return $check;
		}
		
		/**
		 * May be called many times on one page. Let's cache.
		 */
		static $_done = null;	
		if ( ! is_null($_done) ) {
			return $check;
		}
		
		$meta_type = 'post';	
		
		$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');
		
		if ( ! empty($meta_cache['_yoast_wpseo_title'][0]) ) {
			$meta_cache['_yoast_wpseo_title'][0] = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_title'][0], WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
			wp_cache_replace( $object_id, $meta_cache, $meta_type . '_meta' );
		}
		
		/**
		 * @since 2.2.33
		 */
		if ( ! empty($meta_cache['_yoast_wpseo_focuskw'][0]) ) {
			$meta_cache['_yoast_wpseo_focuskw'][0] = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_focuskw'][0], WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
			wp_cache_replace( $object_id, $meta_cache, $meta_type . '_meta' );
		}

		$_done = true;
		
		return $check;
	}
	
	/**
	 * Filter metadata during sitemap generation.
	 *
	 * @since 2.8.7
	 * 
	 * If post has Canonical URL from Yoast, then it interferes during multilingual sitemap generation.
	 * @see `_yoast_wpseo_canonical` meta
	 * @see `canonical` variable in `get_url($post)` function in wordpress-seo\inc\sitemaps\class-post-type-sitemap-provider.php
	 * @see `init` for query_vars in wordpress-seo\inc\sitemaps\class-sitemaps-router.php
	 */
	public static function filter__sitemap_get_post_metadata( $check, $object_id, $meta_key, $single ) {
		
		$language = WPGlobus::Config()->language;
		
		if ( $language === WPGlobus::Config()->default_language ) {
			return $check;
		}
		
		global $wp_query;
		
		if ( ! isset( $wp_query->query_vars['sitemap'] ) ) {
			return $check;
		}
		
		static $_done = array();	
		if ( isset( $_done[$object_id] ) && $_done[$object_id] ) {
			return $check;
		}
		
		$meta_type = 'post';	
		
		$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');
		
		if ( ! empty( $meta_cache['_yoast_wpseo_canonical'][0] ) ) {
			
			/**
			 * Unset `_yoast_wpseo_canonical` during generating sitemap.
			 * We can use WPGlobus_Utils::localize_url, but we should be added fix for WPGlobus Plus too.
			 * Don't use `get_permalink` here.
			 */
			unset( $meta_cache['_yoast_wpseo_canonical'] );
			wp_cache_replace( $object_id, $meta_cache, $meta_type . '_meta' );
		}		
		
		unset( $meta_cache ); 

		$_done[$object_id] = true;
		
		return $check;
	}	
	
	/**
	 * Filter Yoast post meta keywords.
	 *
	 * @scope front
	 * @since 1.8.8
	 *
	 * @param string $keywords Multilingual keywords.
	 *
	 * @return string.
	 */
	public static function filter__metakeywords( $keywords ) {
		if ( WPGlobus::Config()->language != WPGlobus::Config()->default_language ) {
			return '';
		}
		return WPGlobus_Core::text_filter($keywords, WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY);
	}
	
	/**
	 * Filter wpseo meta description.
	 *
	 * @see wordpress-seo\src\presenters\meta-description-presenter.php 
	 * @see wordpress-seo\src\presenters\open-graph\description-presenter.php
	 *
	 * @since 2.4
	 * @since 2.5.1 Added support of taxonomies.
	 *
	 * @scope front
	 *
	 * @param string 				 $meta_description Value from @see `description` field in `wp_yoast_indexable` table.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__description( $meta_description, $presentation ) {

		/**
		 * $meta_description is received from `description` field in `wp_yoast_indexable` table. 
		 */
		 
		/**
		 * Init description.
		 */
		$description = '';

		/**
		 * Key to define that `wpseo_metadesc` filter was already fired earlier.
		 */		
		static $meta_description_presenter_was_fired = false;

		if ( 'post' == $presentation->model->object_type ) {
			
			/**
			 * Post.
			 */		
			if ( $meta_description_presenter_was_fired ) {
				/**
				 * Set meta description to empty value for `wpseo_opengraph_desc` filter like for empty $meta_description in `wpseo_metadesc` filter.
				 */
				$meta_description = '';
			} else {
				if ( empty($meta_description) ) {
					$meta_description_presenter_was_fired = true;
				}
			}

			/**
			 * Added $presentation->source as parameter.
			 * @since 2.7.10
			 */
			$description = self::get_meta( '_yoast_wpseo_metadesc', $meta_description, $presentation->source );
			
			/**
			 * @since 2.7.14
			 */
			if ( ! empty($description) ) { 
				$description = wpseo_replace_vars( $description, $presentation->source );
			}
			
		} elseif ( 'term' == $presentation->model->object_type ) {
			
			/**
			 * Taxonomy.
			 * @since 2.5.1
			 */
			if ( $meta_description_presenter_was_fired ) {
				
				/**
				 * This is `wpseo_opengraph_desc` filter with empty yoast description.
				 * @todo maybe need to use cache for term object.
				 */
				$term = get_term( $presentation->model->object_id );
				if ( $term  instanceof WP_Term ) {
					$description = $term->description;
				} else {
					$description = '';
				}
			
			} else {
			
				/**
				 * @since 2.5.22
				 */
				$description = self::get_taxonomy_meta( $presentation->model->object_sub_type, $presentation->model->object_id, 'wpseo_desc' );

				/**
				 * @since 2.7.0
				 */
				if ( empty($description) ) {
					$description = self::get_option( 'wpseo_titles', 'metadesc-tax-' . $presentation->model->object_sub_type );
				}
				
				if ( empty($description) ) {
					$meta_description_presenter_was_fired = true;
				} else {
					$description = wpseo_replace_vars( $description, $presentation->source );
				}
			}
			
		} elseif ( 'home-page' == $presentation->model->object_type ) {
			
			/**
			 * When homepage displays latest post.
			 * @since 2.7.3
			 * 
			 * We get description from
			 *   1. Meta Description ('metadesc-home-wpseo') option @see tab General, page wp-admin/admin.php?page=wpseo_titles
			 *   	or
			 *   2. Tagline from General Settings page, if Meta Description is empty.
			 */
			$description = $presentation->model->description;
			
			if ( WPGlobus_Core::has_translations($description) ) {
				$description = WPGlobus_Core::text_filter( $description, WPGlobus::Config()->language );
			}

			$description = wpseo_replace_vars( $description, $presentation->source );
			
		} elseif ( 'user' == $presentation->model->object_type ) {
			
			/** 
			 * @since 2.10.1
			 */			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'user_meta' );
			
			if ( ! empty( $meta_cache['wpseo_metadesc'][0] ) ) {
				
				$description = WPGlobus_Core::text_filter( $meta_cache['wpseo_metadesc'][0], WPGlobus::Config()->language );
				$description = wpseo_replace_vars( $description, $presentation->source );
			
			} else {

				if ( empty($description) ) {
					$description = self::get_option( 'wpseo_titles', 'metadesc-author-wpseo' );
				}
				
				if ( empty($description) ) {
					$meta_description_presenter_was_fired = true;
				} else {

					if ( WPGlobus_Core::has_translations($description) ) {
						$description = WPGlobus_Core::text_filter( $description, WPGlobus::Config()->language );	
					}					
					$description = wpseo_replace_vars( $description, $presentation->source );
					
				}				
				
			}
		}
	
		/**
		 * @since 2.6.3
		 */	
		self::$description = $description;
		
		return $description;
	}

	/**
	 * Filter canonical URL and open graph URL put out by Yoast SEO.
	 *
	 * @see wordpress-seo\src\presenters\canonical-presenter.php
	 * @see wordpress-seo\src\presenters\open-graph\url-presenter.php
	 * @scope front
	 * @since 2.4
	 * @since 2.5.1 Added support of taxonomies.
	 * @since 2.5.10 Added `wpglobus_wpseo_localize_url` filter.
	 *
	 * @param string 				 $url The canonical URL or open graph URL.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__localize_url( $url, $presentation ) {
		/**
		 * URL is received from `permalink` field in `wp_yoast_indexable` table. 
		 */
		if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
			return $url;
		}		

		$tag = false;
		if ( is_singular() ) {
			$tag = 'singular';
		} elseif ( is_category() ) {
			$tag = 'category';
		} elseif ( is_tag() ) {
			$tag = 'tag';
		} elseif ( is_tax() ) {
			$tag = 'tax';
		}

		if ( $tag ) {
			
			$language =WPGlobus::Config()->language;
			
			self::$canonical_url[ WPGlobus::Config()->default_language ] = $url; 
			
			/**
			 * Filters for a localized url.
			 *
			 * @since 2.5.10
			 *
			 * @param string $url		Localized URL.
			 * @param string $language  Current language.
			 * @param string $tag  		Conditional Tag.
			 */
			self::$canonical_url[ $language ] = apply_filters( 'wpglobus_wpseo_localize_url', WPGlobus_Utils::localize_url( $url, $language ), $language, $tag );
			
			self::$canonical_url[ $language ] = urldecode( self::$canonical_url[ $language ] );
			
			return self::$canonical_url[ $language ];
		}
		
		return $url;
	}
	
	/**
	 * Filtering of the rel `prev` and `next` URL put out by Yoast SEO.
	 *
	 * @see wordpress-seo\src\presenters\rel-prev-presenter.php
	 * @see wordpress-seo\src\presenters\rel-next-presenter.php
	 * @scope front
	 *
	 * @since 2.5.11
	 *
	 * @param string 				 $url 		   Link relationship, prev or next.
	 * @param string 				 $relationship `prev` or `next`.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */
	public static function filter_front__wpseo_adjacent_rel_url( $link, $relationship, $presentation ) {
		
		if ( empty( $link ) || WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
			return $link;
		}				

		if ( empty( self::$canonical_url ) ) {
			return $link;
		}

		return str_replace( 
			self::$canonical_url[ WPGlobus::Config()->default_language ],
			self::$canonical_url[ WPGlobus::Config()->language ], 
			$link 
		);
	}
	
	/**
	 * Filter the HTML output of the Yoast SEO breadcrumbs class.
	 *
	 * @see wordpress-seo\src\presenters\breadcrumbs-presenter.php
	 * @scope front
	 * @since 2.4.2
	 *
	 * @param $output 							   The HTML output
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @api string $output The HTML output.	
	 *
	 * @return string
	 */
	public static function filter__breadcrumb_output( $output, $presentation ) {
		
		/** @global wpdb $wpdb */
		global $wpdb;
		
		$object_type 	 = null;
		$object_sub_type = null;
		$object_order 	 = null;
		
		if ( $presentation->source instanceof WP_Post ) {
			
			$object_type = 'post';	
			$object_sub_type = $presentation->source->post_type;	
		
		} elseif ( $presentation->source instanceof WP_Term ) {
		
			$object_type = 'taxonomy';	
			$object_sub_type = $presentation->source->taxonomy;	

			if ( $presentation->source->parent == 0 ) {
				$object_order = array($presentation->source->term_id);
			} else {
				$object_order = get_ancestors($presentation->source->term_id, $object_sub_type);
				if ( count($object_order) > 1 ) {
					$object_order = array_reverse( $object_order );
				}
				$object_order[] = $presentation->source->term_id;
			}

		}
		
		$ids = array();
		$breadcrumbs = array();
		$i = 0;
		
		foreach( $presentation->breadcrumbs as $order=>$piece ) {	
			
			if ( $order == 0 ) {

				$_piece = '/' . preg_quote($piece['url'], '/') . '/';
				
				if ( empty( $piece['id'] ) ) {
					/**
					 * If homepage displays as latest posts, then we should force the setting of `Home` for all languages.
					 */
					$output = preg_replace( $_piece, home_url('/'), $output, 1  );
				} else {
					$output = preg_replace( $_piece, home_url('/'), $output, 1  );
				}
				
				if ( WPGlobus_Core::has_translations($piece['text']) ) {
					$_home_text = WPGlobus_Core::text_filter( $piece['text'], WPGlobus::Config()->language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
					$output = str_replace( $piece['text'], $_home_text, $output );
				}
				
			} else {
			
				switch ($object_type) :
					case 'post' :
						if ( ! empty($piece['id']) ) {
							$ids[] = $piece['id']; 
							$breadcrumbs[ $piece['id'] ] = $piece;
							$breadcrumbs[ $piece['id'] ]['object_type'] = $object_type;
							$breadcrumbs[ $piece['id'] ]['object_sub_type'] = $object_sub_type;
						}
						break;
					case 'taxonomy' :
						$_id = $order;
						if ( ! is_null( $object_order ) ) {
							$_id = $object_order[$i];
							$ids[] = $_id; 
						}
						$breadcrumbs[ $_id ] = $piece;
						$breadcrumbs[ $_id ]['object_type'] = $object_type;
						$breadcrumbs[ $_id ]['object_sub_type'] = $object_sub_type;
						$i++;
						break;
				endswitch;
			}				
		}

		$query = null;
		
		if ( ! empty($ids) ) {
			
			$_ids = implode( ',', $ids );
			switch ($object_type) :
				case 'post' :
					$select   = $wpdb->prepare( "SELECT ID, post_title AS ml_title, post_name, post_type FROM $wpdb->posts WHERE ID IN ( %s )", $_ids );
					$select   = str_replace( "'", '', $select );
					break;
				case 'taxonomy' :
					$select   = $wpdb->prepare( "SELECT term_id, name AS ml_title, slug FROM $wpdb->terms WHERE term_id IN ( %s )", $_ids );
					$select   = str_replace( "'", '', $select );
					break;
			endswitch;		

			$query = $wpdb->get_results( $select, OBJECT_K );
			
			foreach( $breadcrumbs as $id=>$piece ) {
			
				$output = str_replace( 
					array( 
						$piece['url'],
						$piece['text'] 
					),
					array( 
						WPGlobus_Utils::localize_url( $piece['url'], WPGlobus::Config()->language ), 
						WPGlobus_Core::text_filter( $query[$id]->ml_title, WPGlobus::Config()->language ) 
					), 
					$output 
				);
			}
		}

		/**
		 * @since 2.4.2 @W.I.P
		 */
		//$output = apply_filters( 'wpglobus_wpseo_breadcrumb_output', $output, $breadcrumbs, $query );
		
		return $output;
	}
	
	/**
	 * Filter wpseo meta description.
	 *
	 * @see wordpress-seo\admin\class-meta-columns.php
	 * @scope admin
	 * @since 2.2.16
	 *
	 * @param string $metadesc_val Value in default language.
	 *
	 * @return string
	 */
	public static function filter__wpseo_metadesc( $metadesc_val ) {
		
		if ( empty($metadesc_val) ) {
			return $metadesc_val;
		}
		
		if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
			return $metadesc_val;
		}

		return self::get_meta( '_yoast_wpseo_metadesc', $metadesc_val );
	}
	
	/**
	 * To translate Yoast `column-wpseo-title`.
	 *
	 * @see wordpress-seo\admin\class-meta-columns.php
	 * @scope admin
	 * @since 2.2.16
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public static function filter__wpseo_title( $title ) {
		return WPGlobus_Core::extract_text( $title, WPGlobus::Config()->language );	
	}

	/**
	 * Get meta for extra language.
	 *
	 * @scope both
	 * @since 2.2.16
	 * @since 2.7.10 Added $presentation_source parameter.
	 */	
	protected static function get_meta( $meta_key, $meta_value = '', $presentation_source = null ) {

		// TODO: Test it!
		// if ( ! empty( $meta_value ) ) {
			// return WPGlobus_Core::extract_text( $meta_value );
		// }

		if ( is_null(self::$wpseo_meta) ) {
			self::get_wpseo_meta();
		}

		if ( empty( self::$wpseo_meta[ $meta_key ] ) ) {
			// TODO: Test it!
			// if ( ! empty( $presentation_source->ID ) ) {
				// self::$wpseo_meta[ $meta_key ][ $presentation_source->ID ] = $meta_value;
			// } else {
				return '';
			// }
		}
		
		/** @global WP_Post $post */
		global $post;
		
		if ( empty( $meta_value ) ) {

			if ( $presentation_source instanceof WP_Post ) {
				if ( empty( self::$wpseo_meta[$meta_key][$presentation_source->ID] ) ) {
					return '';
				}
				// TODO: Test it!
				// return WPGlobus_Core::extract_text( self::$wpseo_meta[$meta_key][$presentation_source->ID] );
				return WPGlobus_Core::text_filter( self::$wpseo_meta[$meta_key][$presentation_source->ID], WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
			}			
			
			/**
			 * Try get meta by post ID.
			 */
			if ( ! $post instanceof WP_Post ) {
				return '';
			}
			if ( empty( self::$wpseo_meta[$meta_key][$post->ID] ) ) {
				return '';
			}

			// TODO: Test it!
			// return WPGlobus_Core::extract_text( self::$wpseo_meta[$meta_key][$post->ID] );
			return WPGlobus_Core::text_filter( self::$wpseo_meta[$meta_key][$post->ID], WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
		}

		$_return_value = '';
		foreach( self::$wpseo_meta[ $meta_key ] as $_meta_value ) {
			
			if ( $presentation_source instanceof WP_Post ) {
				$_meta_value =  wpseo_replace_vars( $_meta_value, $presentation_source );
			} else if ( $post instanceof WP_Post ) {
				$_meta_value =  wpseo_replace_vars( $_meta_value, $post );
			}

			if ( false !== strpos( $_meta_value, $meta_value ) ) {
				// TODO: Test it!
				// $_return_value = WPGlobus_Core::extract_text( $_meta_value );
				$_return_value = WPGlobus_Core::text_filter( $_meta_value, WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
				break;
			}
		}

		return $_return_value;
	}
	
	/**
	 * Get `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` meta.
	 *
	 * @scope both
	 * @since 2.4
	 * @since 2.4.6  Separate the defining of post type for frontend and admin.
	 * @since 2.4.14 Revised code.
	 */
	protected static function get_wpseo_meta() {
		
		/** @global wpdb $wpdb */
		global $wpdb;
		
		/** @global WP_Post $post */
		global $post;

		$post_id = false;
		
		if ( is_admin() ) {
			
			/**
			 * Admin.
			 */
			if ( ! empty( $_GET['post'] ) ) { 
				$post_id = sanitize_text_field( $_GET['post'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
			}
			
		} else {

			if ( $post instanceof WP_Post ) {
				
				/**
				 * Front-end.
				 */
				$post_id = $post->ID;

			}			
		}
		
		if ( $post_id ) {
			
			$query = $wpdb->prepare( 
				"SELECT p.ID, p.post_type, pm.meta_key, pm.meta_value FROM {$wpdb->prefix}posts AS p JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.ID = %s AND (pm.meta_key = %s OR pm.meta_key = %s)",
				$post_id,
				'_yoast_wpseo_metadesc',
				'_yoast_wpseo_focuskw'
			);
			
			$metas = $wpdb->get_results( $query, ARRAY_A  );

			if ( ! empty( $metas ) ) {
				foreach( $metas as $_meta ) {
					if ( ! isset( self::$wpseo_meta[ $_meta['meta_key'] ] ) ) {
						self::$wpseo_meta[ $_meta['meta_key'] ] = array();
					}
					self::$wpseo_meta[ $_meta['meta_key'] ][ $_meta['ID'] ] = $_meta['meta_value'];
				}
			}			
		} else {
			// Here we can add code to get meta for multiple posts.
		}
	}
	
	/**
	 * Get taxonomy meta from `wpseo_taxonomy_meta` option.
	 *
	 * @since 2.5.1
	 * @since 2.5.22 Added $meta_key parameter to the `get_taxonomy_meta` function.
	 * @scope front
	 *
	 * @param string 				 $object_sub_type The Indexable Object sub type.
	 * @param string 				 $object_id	      The object ID.
	 * @param Indexable_Presentation $meta_key		  The WPSEO meta key.
	 *
	 * return string | array
	 */	
	protected static function get_taxonomy_meta( $object_sub_type, $object_id, $meta_key = '' ) {
		
		if ( is_null( self::$wpseo_taxonomy_meta ) ) {
			self::$wpseo_taxonomy_meta = get_option( 'wpseo_taxonomy_meta' );
		}

		if ( empty( self::$wpseo_taxonomy_meta[ $object_sub_type ][ $object_id ] ) ) {
			return '';
		}
		
		if ( empty( $meta_key ) ) {
			return self::$wpseo_taxonomy_meta[ $object_sub_type ][ $object_id ];
		}
		
		if ( empty( self::$wpseo_taxonomy_meta[ $object_sub_type ][ $object_id ][ $meta_key ] ) ) {
			return '';
		}
	
		$meta_value = WPGlobus_Core::text_filter( 
			self::$wpseo_taxonomy_meta[ $object_sub_type ][ $object_id ][ $meta_key ],
			WPGlobus::Config()->language,
			WPGlobus::RETURN_EMPTY
		);	
		
		return $meta_value;
	}

	/**
	 * Enqueue JS for YoastSEO support.
	 *
	 * @since 1.4.0
	 * @since 2.2.20
	 * @since 2.4.5  Add JS script on dashboard.
	 */
	public static function action__admin_print_scripts() {

		if ( WPGlobus_WP::is_pagenow( 'admin.php' ) ) {
			
			if ( 'wpseo_tools' == WPGlobus_Utils::safe_get('page') && 'bulk-editor' == WPGlobus_Utils::safe_get('tool') ) {

				$wrng1 = '<div>' . esc_html__( 'Bulk editing of the multilingual titles and descriptions is not supported by the current version.', 'wpglobus' ) . '</div>';
				$wrng2 = '<div>' . esc_html__( 'Therefore, to avoid any data loss, we do not recommend using this.', 'wpglobus' ) . '</div>';

				$i18n = array(
					'preWarning'        => esc_html__( 'WPGlobus warning: ', 'wpglobus' ),
					'bulkEditorWarning' => $wrng1 . $wrng2,
				);

				$src = WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-yoastseo-dashboard' . WPGlobus::SCRIPT_SUFFIX() . '.js';

				wp_register_script(
					self::$handle_script_dashboard,
					$src,
					array( 'jquery' ),
					WPGLOBUS_VERSION,
					true
				);
				
				wp_enqueue_script(self::$handle_script_dashboard);

				wp_localize_script(
					self::$handle_script_dashboard,
					'WPGlobusYoastSeoDashboard',
					array(
						'version' 		=> WPGLOBUS_VERSION,
						'wpseo_version' => WPSEO_VERSION,
						'pagenow' 		=> 'admin.php',
						'page' 			=> WPGlobus_Utils::safe_get('page'),
						'tool' 			=> WPGlobus_Utils::safe_get('tool'),
						'i18n'    		=> $i18n
					)				
				);
				
				return;
			}
		}

		if ( 'off' === WPGlobus::Config()->toggle ) {
			return;
		}

		if ( self::disabled_entity() ) {
			return;
		}

		/** @global string $pagenow */
		global $pagenow;

		$enabled_pages = array(
			'post.php',
			'post-new.php',
			'edit-tags.php',
			'term.php'
		);

		if ( WPGlobus_WP::is_pagenow( $enabled_pages ) ) {

			WPGlobus::O()->vendors_scripts['WPSEO'] = true;

			if ( defined( 'WPSEO_PREMIUM_PLUGIN_FILE' ) ) {
				/**
				 * @see wordpress-seo-premium\wp-seo-premium.php
				 */
				WPGlobus::O()->vendors_scripts['WPSEO_PREMIUM'] = true;
			}

			$yoastseo_plus_readability_access   = '';
			$yoastseo_plus_readability_inactive = '';
			
			$yoastseo_plus_page_analysis_access   = '';
			$yoastseo_plus_page_analysis_inactive = '';
			
			$yoastseo_plus_meta_keywords_access   = '';
			$yoastseo_plus_meta_keywords_inactive = '';

			$yoastseo_plus_social_access = '';
			$yoastseo_plus_social_inactive = '';
			
			if ( WPGlobus::Config()->builder->is_builder_page() ) {

				$_url = '#';
				if ( class_exists('WPGlobusPlus') ) {
					/**
					 * @see wpglobus-plus\includes\wpglobus-plus-main.php
					 */
					$_url = add_query_arg(
						array(
							'page' => 'wpglobus-plus-options'
						),
						admin_url('admin.php')
					);
				}

				$yoastseo_plus_readability_access = sprintf(
					esc_html__( 'Please see %1s to get access to Analysis results in %2s with YoastSEO.', '' ),
					'<a href="https://wpglobus.com/product/wpglobus-plus/#yoastseo" target="_blank">WPGlobus Plus</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);
				$yoastseo_plus_readability_inactive = sprintf(
					esc_html__( 'Please activate %1sYoast SEO Plus%2s module to get access to Analysis results in %3s with YoastSEO.', '' ),
					'<a href="'.$_url.'">',
					'</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);
				
				$yoastseo_plus_page_analysis_access = sprintf(
					esc_html__( 'Please see %1s to get access to Analysis results in %2s with YoastSEO.', '' ),
					'<a href="https://wpglobus.com/product/wpglobus-plus/#yoastseo" target="_blank">WPGlobus Plus</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);
				$yoastseo_plus_page_analysis_inactive = sprintf(
					esc_html__( 'Please activate %1sYoast SEO Plus%2s module to get access to Analysis results in %3s with YoastSEO.', '' ),
					'<a href="'.$_url.'">',
					'</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);

				$yoastseo_plus_meta_keywords_access = sprintf(
					esc_html__( 'Please see %1s to get access to Focus keyphrase in %2s with YoastSEO.', '' ),
					'<a href="https://wpglobus.com/product/wpglobus-plus/#yoastseo" target="_blank">WPGlobus Plus</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);
				$yoastseo_plus_meta_keywords_inactive = sprintf(
					esc_html__( 'Please activate %1sYoast SEO Plus%2s module to get access to Focus keyphrase in %1s with YoastSEO.', '' ),
					'<a href="'.$_url.'">',
					'</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);

				$yoastseo_plus_social_access = sprintf(
					esc_html__( 'Please see %1s to get access to Social Tab content in %2s with YoastSEO.', '' ),
					'<a href="https://wpglobus.com/product/wpglobus-plus/#yoastseo" target="_blank">WPGlobus Plus</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);
				$yoastseo_plus_social_inactive = sprintf(
					esc_html__( 'Please activate %1sYoast SEO Plus%2s module to get access to Social Tab content in %1s with YoastSEO.', '' ),
					'<a href="'.$_url.'">',
					'</a>',
					WPGlobus::Config()->en_language_name[ WPGlobus::Config()->builder->get_language() ]
				);					
			}
			
			$i18n = array(
				'yoastseo_plus_readability_access' 	 	=> $yoastseo_plus_readability_access,
				'yoastseo_plus_readability_inactive' 	=> $yoastseo_plus_readability_inactive,
				'yoastseo_plus_page_analysis_access' 	=> $yoastseo_plus_page_analysis_access,
				'yoastseo_plus_page_analysis_inactive'  => $yoastseo_plus_page_analysis_inactive,
				'yoastseo_plus_meta_keywords_access' 	=> $yoastseo_plus_meta_keywords_access,
				'yoastseo_plus_meta_keywords_inactive' 	=> $yoastseo_plus_meta_keywords_inactive,
				'yoastseo_plus_social_access' 			=> $yoastseo_plus_social_access,
				'yoastseo_plus_social_inactive' 		=> $yoastseo_plus_social_inactive
			);

			$src_version 		 = false;
			$src_version_premium = false;

			if ( ! WPGlobus::O()->vendors_scripts['WPSEO_PREMIUM'] ) {
				
				/** @noinspection PhpInternalEntityUsedInspection */
				$src_version = version_compare( WPSEO_VERSION, '4.0', '>=' ) ? '40' : $src_version;
				/** @noinspection PhpInternalEntityUsedInspection */
				$src_version = version_compare( WPSEO_VERSION, '4.1', '>=' ) ? '41' : $src_version;
				/** @noinspection PhpInternalEntityUsedInspection */
				$src_version = version_compare( WPSEO_VERSION, '4.4', '>=' ) ? '44' : $src_version;
				if ( 
					/** @noinspection PhpInternalEntityUsedInspection */
					version_compare( WPSEO_VERSION, '4.8', '>=' ) 
				) {
					$src_version = self::$version;
				}
				
			} else {
				/**
				 * Start with Yoast SEO Premium.
				 */

				/**
				 * Version of file yoast seo must be latest.
				 */
				/** @noinspection PhpInternalEntityUsedInspection */
				$src_version 			= version_compare( WPSEO_VERSION, '3.9', '>=' ) ? self::$version : $src_version;
				/** @noinspection PhpInternalEntityUsedInspection */
				$src_version_premium	= version_compare( WPSEO_VERSION, '3.9', '>=' ) ? '39' : $src_version_premium;
				/**
				 * @since WPGlobus 2.2.17
				 */ 
				$src_version_premium	= version_compare( WPSEO_VERSION, '12.0', '>=' ) ? $src_version : $src_version_premium;
				
			}
			
			if ( $src_version ) :

				$handle = self::$handle_script;

				$src = WPGlobus::$PLUGIN_DIR_URL . 'includes/js/' .
					   $handle . '-' . $src_version .
					   WPGlobus::SCRIPT_SUFFIX() . '.js';

				wp_register_script(
					$handle,
					$src,
					array( 'jquery', 'underscore' ),
					WPGLOBUS_VERSION,
					true
				);
				
				wp_enqueue_script($handle);

				wp_localize_script(
					$handle,
					'WPGlobusYoastSeo',
					array(
						'version' 		=> WPGLOBUS_VERSION,
						'wpseo_version' => WPSEO_VERSION,
						'pagenow'   	=> $pagenow,
						'src_version'   => $src_version,
						'builder_id'    => WPGlobus::Config()->builder->get('id'),
						'builder_page'  => WPGlobus::Config()->builder->is_builder_page() ? 'true' : 'false',
						'language'   	=> WPGlobus::Config()->builder->get_language(),
						'is_default_language' => WPGlobus::Config()->builder->is_default_language() ? true : false,
						'src_version_premium' => $src_version_premium,
						'plus_module'   	  => self::$plus_module,
						'access_extra'   	  => ( defined( 'WPGLOBUS_YOAST_SEO_ACCESS_EXTRA' ) && WPGLOBUS_YOAST_SEO_ACCESS_EXTRA ) ? 'true' : 'false'
					)
				);

				wp_localize_script(
					$handle,
					'WPGlobusVendor',
					array(
						'version' => WPGLOBUS_VERSION,
						'vendor'  => WPGlobus::O()->vendors_scripts,
						'pagenow' => $pagenow,
						'i18n'    => $i18n
					)
				);

			endif;
		}
	}

	/**
	 * Check disabled entity.
	 *
	 * @since 1.7.3
	 * @return boolean
	 */
	public static function disabled_entity() {

		if ( WPGlobus_WP::is_pagenow( array( 'edit-tags.php', 'term.php' ) ) ) :
			/**
			 * Don't check page when editing taxonomy.
			 */
			return false;
		endif;

		/** @global WP_Post $post */
		global $post;

		$result = false;
		if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) :
			if ( empty( $post ) ) {
				$result = true;
			} elseif ( WPGlobus::O()->disabled_entity( $post->post_type ) ) {
				$result = true;
			}
		endif;
		return $result;
	}

	/**
	 * Filter allows changing graph breadcrumb output.
	 *
	 * @see wordpress-seo\src\generators\schema-generator.php
	 * @see "application/ld+json" in html code on front.
	 *
	 * @since 2.4.7
	 *
	 * @scope front
	 * @param array $graph_piece		 Array of graph piece.
	 * @param Meta_Tags_Context $context A value object with context variables.
	 * @return array
	 */
	public static function filter__wpseo_schema_breadcrumb( $graph_piece, $context ) {

		if ( empty( $graph_piece['itemListElement'] ) ) {
			return $graph_piece;		
		}

		$itemListElement = $graph_piece['itemListElement'];
		
		foreach( $itemListElement as $_key=>$_item ) {
			if ( ! empty( $_item['item']['name'] ) && WPGlobus_Core::has_translations( $_item['item']['name'] ) ) {
				$graph_piece['itemListElement'][$_key]['item']['name'] = WPGlobus_Core::extract_text( $graph_piece['itemListElement'][$_key]['item']['name'], WPGlobus::Config()->language );
			}
		}
		
		return $graph_piece;		
	}
	
	/**
	 * Filter allows changing output of graph `webpage`.
	 *
	 * @see wordpress-seo\src\generators\schema-generator.php
	 * @see "application/ld+json" in html code on front.
	 *
	 * @since 2.4.14
	 * @since 2.4.15 Localize description.
	 * @since 2.5.1 Added support of taxonomies.
	 * @since 2.7.11 Added filter for breadcrumb title.
	 * @since 2.8.9 Added filter for taxonomy term name.
	 * @since 2.10.1 Added filter for user object type.
	 * 
	 * @scope front
	 * @param array $graph_piece		 Array of graph piece.
	 * @param Meta_Tags_Context $context A value object with context variables.
	 * @return array	 
	 */ 	
	public static function filter__wpseo_schema_webpage( $graph_piece, $context ) {
		
		if ( 'post' == $context->indexable->object_type ) {
		
			if ( ! empty( $graph_piece['name'] ) && WPGlobus_Core::has_translations( $graph_piece['name'] ) ) {
				$graph_piece['name'] = WPGlobus_Core::extract_text( $graph_piece['name'], WPGlobus::Config()->language );
			}

			/**
			 * @since 2.4.15
			 */
			if ( ! empty( $graph_piece['description'] ) && WPGlobus_Core::has_translations( $graph_piece['description'] ) ) {
				$graph_piece['description'] = WPGlobus_Core::extract_text( $graph_piece['description'], WPGlobus::Config()->language );
			}		

		} elseif ( 'term' == $context->indexable->object_type ) {
		
			/**
			 * Taxonomy.
			 * @since 2.5.1
			 */
			$graph_piece['description'] = self::get_taxonomy_meta( $context->indexable->object_sub_type, $context->indexable->object_id, 'wpseo_desc' );
			$graph_piece['url'] 		= WPGlobus_Utils::localize_url( $graph_piece['url'], WPGlobus::Config()->language );
			$graph_piece['@id'] 		= WPGlobus_Utils::localize_url( $graph_piece['@id'], WPGlobus::Config()->language );
			$graph_piece['breadcrumb']['@id'] = WPGlobus_Utils::localize_url( $graph_piece['breadcrumb']['@id'], WPGlobus::Config()->language );

			/**
			 * Filter breadcrumb title.
			 * @since 2.7.11
			 */	
			if ( WPGlobus_Core::has_translations($context->indexable->breadcrumb_title) ) {
				$context->indexable->breadcrumb_title = WPGlobus_Core::extract_text( $context->indexable->breadcrumb_title, WPGlobus::Config()->language );
			}

			/**
			 * Filter taxonomy term name.
			 * @since 2.8.9
			 */				
			if ( WPGlobus_Core::has_translations($graph_piece['name']) ) {
				$graph_piece['name'] = WPGlobus_Core::extract_text( $graph_piece['name'], WPGlobus::Config()->language );
			}
			
		} elseif ( 'home-page' == $context->indexable->object_type ) {
		
			/**
			 * When homepage displays latest post.
			 * @since 2.7.3
			 */
			if ( ! empty( $graph_piece['description'] ) && WPGlobus_Core::has_translations( $graph_piece['description'] ) ) {
				$graph_piece['description'] = WPGlobus_Core::extract_text( $graph_piece['description'], WPGlobus::Config()->language );
			}
			
		} elseif ( 'user' == $context->indexable->object_type ) {
		
			/**
			 * @since 2.10.1
			 */
			if ( ! empty( $graph_piece['description'] ) && WPGlobus_Core::has_translations( $graph_piece['description'] ) ) {
				$graph_piece['description'] = WPGlobus_Core::extract_text( $graph_piece['description'], WPGlobus::Config()->language );
			}
	
			if ( ! empty( $graph_piece['name'] ) && WPGlobus_Core::has_translations( $graph_piece['name'] ) ) {
				$graph_piece['name'] = WPGlobus_Core::extract_text( $graph_piece['name'], WPGlobus::Config()->language );
			}	
			
		}

		return $graph_piece;
	}
	
	/**
	 * Filter for changing the Yoast SEO generated title.
	 *
	 * @see wordpress-seo\src\presenters\title-presenter.php
	 *
	 * @since 2.5.22
	 *
	 * @scope front
	 *
	 * @param string 				 $title        The title.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 *
	 * @return string
	 */	
	public static function filter_front__title( $title, $presentation ) {
		
		if ( 'post' == $presentation->model->object_type ) {
			
			/**
			 * Post.
			 */	
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'post_meta' );
			
			if ( ! empty( $meta_cache['_yoast_wpseo_title'][0] ) ) {
				
				$__title = WPGlobus_Core::text_filter( $meta_cache['_yoast_wpseo_title'][0], WPGlobus::Config()->language );
				
				if ( $presentation->source instanceof WP_Post ) {

					if ( WPGlobus_Core::has_translations( $presentation->source->post_title ) ) {
						$presentation->source->post_title = WPGlobus_Core::text_filter( $presentation->source->post_title, WPGlobus::Config()->language );
					}

					/**
					 * @see wordpress-seo\inc\wpseo-functions.php
					 */						
					$title = wpseo_replace_vars( $__title, $presentation->source );
				}
				
			} else {
				
				/** 
				 * @since 2.5.23
				 */
				if ( WPGlobus_Core::has_translations( $title ) ) {
					$title = WPGlobus_Core::extract_text( $title, WPGlobus::Config()->language );
				}
				
			}
			
		} elseif ( 'term' == $presentation->model->object_type ) {
			
			/**
			 * Taxonomy.
			 */
			$__title = self::get_taxonomy_meta( $presentation->model->object_sub_type, $presentation->model->object_id, 'wpseo_title' );
			
			if ( empty( $__title ) ) {
				$__title = self::get_option( 'wpseo_titles', 'title-tax-' . $presentation->model->object_sub_type );
			}
		
			$title = wpseo_replace_vars( $__title, $presentation->source );
 
		} elseif ( 'user' == $presentation->model->object_type ) {
			
			/** 
			 * @since 2.10.1
			 */			
			$meta_cache = wp_cache_get( $presentation->model->object_id, 'user_meta' );
			
			if ( ! empty( $meta_cache['wpseo_title'][0] ) ) {
				
				$title = WPGlobus_Core::text_filter( $meta_cache['wpseo_title'][0], WPGlobus::Config()->language );
				$title = wpseo_replace_vars( $title, $presentation->source );
			
			} else {
				
				if ( empty($title) ) {
					$title = self::get_option( 'wpseo_titles', 'title-author-wpseo' );
				}
				
				if ( ! empty($title) ) {
			
					if ( WPGlobus_Core::has_translations($title) ) {
						$title = WPGlobus_Core::text_filter( $title, WPGlobus::Config()->language );	
					}
					
					$title = wpseo_replace_vars( $title, $presentation->source );
				}
			}		
			
		}
		
		return $title;
	}	
	
	/**
	 * Get wpseo options.
	 *
	 * @since 2.5.22
	 *
	 * @return array|string 
	 */
	public static function get_option( $option = 'wpseo_titles', $key = '' ) {
		
		/**
		 * @todo Maybe to use WPSEO_Options.
		 * @see wordpress-seo\inc\options\class-wpseo-options.php
		 * @since 2.5.22
		 */
		
		$options = get_option( $option );
		
		if ( empty( $key ) ) {
			return $options;
		}
		
		if ( ! isset( $options[$key] ) ) {
			return null;
		}
		
		return $options[$key];
	}		

} // class WPGlobus_YoastSEO.

# --- EOF
