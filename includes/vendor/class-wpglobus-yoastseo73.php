<?php
/**
 * Support of Yoast SEO 7.3
 * @package WPGlobus\Yoast
 * @since   1.9.14
 */

/**
 * Class WPGlobus_YoastSEO
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
	 * Static "controller"
	 */
	public static function controller($version) {
			
		self::$version = $version;

		if ( is_admin() ) {
			
			/**
			 * @since 1.9.17 to prevent start scripts on edit page.
			 */
			if ( version_compare( WPSEO_VERSION, '7.6.99', '>=' ) ) {
				return;
			}
			
			if ( ! WPGlobus_WP::is_doing_ajax() ) {

				/** @see WPGlobus::__construct */
				WPGlobus::O()->vendors_scripts['WPSEO'] = true;

				if ( defined( 'WPSEO_PREMIUM_PLUGIN_FILE' ) ) {
					/**
					 * @see wordpress-seo-premium\wp-seo-premium.php
					 */
					WPGlobus::O()->vendors_scripts['WPSEO_PREMIUM'] = true;
				}

				if ( WPGlobus_WP::is_pagenow( 'edit.php' ) ) {

					/**
					 * @since 1.5.3
					 */
					add_filter( 'wpseo_replacements_filter_sep', array( __CLASS__, 'filter__get_separator' ), 999 );

					/**
					 * To translate Yoast columns on edit.php page
					 */
					add_filter( 'esc_html', array(
						__CLASS__,
						'filter__wpseo_columns'
					), 0 );

				}

				/**
				 * @since 1.6.3
				 */
				add_action( 'wpseo_save_compare_data', array(
					__CLASS__,
					'action__save_compare_data'
				) );

				add_action( 'admin_print_scripts', array(
					__CLASS__,
					'action__admin_print_scripts'
				) );

				add_action( 'wpseo_tab_content', array(
					__CLASS__,
					'action__wpseo_tab_content'
				), 11 );

				if ( WPGlobus_WP::is_pagenow( array( 'edit-tags.php', 'term.php' ) ) ) {
					add_filter( 'wp_default_editor', array(
						__CLASS__,
						'set_default_editor'
					) );
				}

			}

		} else {
			/**
			 * Filter SEO title and meta description on front only, when the page header HTML tags are generated.
			 * AJAX is probably not required (waiting for a case).
			 */
			add_filter( 'wpseo_title', array( 'WPGlobus_Filters', 'filter__text' ), PHP_INT_MAX );
			add_filter( 'wpseo_metadesc', array( 'WPGlobus_Filters', 'filter__text' ), PHP_INT_MAX );
			
			/**
			 * @todo check for '_yoast_wpseo_title' meta
			 * @see <title> view-source:http://www.dev-wpg.com/test-post-seo/
			 * @see <title> view-source:http://www.dev-wpg.com/ru/test-post-seo/
			 */
			//add_filter( 'get_post_metadata', array( __CLASS__, 'filter__get_post_metadata' ), 0, 4 );
			
			/**
			 * Filter meta keywords.
			 * @from 1.8.8
			 */
			add_filter( 'wpseo_metakeywords', array( __CLASS__, 'filter__metakeywords' ), 0 );

		}

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
	 * Fix empty yoast_wpseo_focuskw while saving/updating post with active extra language.
	 *
	 * @since 1.6.3
	 * @since 1.7.7
	 * @see filter 'wpseo_save_compare_data' wordpress-seo\admin\metabox\class-metabox.php
	 *
	 * @param WP_Post Object $post Current post.
	 */
	public static function action__save_compare_data( $post ) {
		/**
		 * Don't need @since 1.7.7
		 * It is helpful for testing purposes, use: error_log( print_r( $_POST, true ) );
		 */
		return;
		/*
		if ( empty( $_POST[ 'yoast_wpseo_focuskw' ] ) ) {
			if ( ! empty( $_POST[ 'yoast_wpseo_focuskw_text_input' ] ) ) {
				$_POST[ 'yoast_wpseo_focuskw' ] = $_POST[ 'yoast_wpseo_focuskw_text_input' ];
			}
		} */
	}

	/**
	 * Filter Yoast post metadata.
	 *
	 * When Yoast builds HTML title and meta description, it looks in tree places:
	 * - Actual post_title,
	 * - Title and Description from Yoast Snippet (fancy metabox for each post),
	 * - Rules (%%title%% %%sep%% %%page%%) in the SEO Settings.
	 * Yoast gets confused when not all languages are filled in consistently
	 * (one language has post_title, another one - only Snippet, others - should work
	 * from the Rules).
	 * We are trying to hook into the `get_post_metadata` and return filtered values
	 * to Yoast, so when it should be empty - it's empty and not
	 * {:xx}Something from another language{:}
	 *
	 * @scope         front
	 * @since         1.4.0 (original)
	 *                1.5.5 (restored and rewritten)
	 *
	 * @param null|array $metadata Comes as NULL. Return something to short-circuit.
	 * @param int        $post_id  Post ID.
	 * @param string     $meta_key Empty because the array of all metas is returned.
	 * @param bool       $single   False in this case.
	 *
	 * @return null|array Return metadata array if we are "in business".
	 */
	public static function filter__get_post_metadata(
		$metadata, $post_id, $meta_key, $single
	) {
		// Yoast does not pass any `meta_key`, and does not ask for `single`.
		// Checking it here is faster than going to backtrace directly.
		if ( $meta_key || $single ) {
			return $metadata;
		}

		// We only need to deal with these two callers:
		if ( WPGlobus_WP::is_functions_in_backtrace( array(
			array( 'WPSEO_Frontend', 'get_content_title' ),
			array( 'WPSEO_Frontend', 'generate_metadesc' ),
		) )
		) {
			/**
			 * The part of getting meta / updating cache is copied from
			 * @see get_metadata
			 * (except for doing serialize - we believe it's not necessary for Yoast).
			 */

			/** @var array $post_meta */
			$post_meta = wp_cache_get( $post_id, 'post_meta' );

			if ( ! $post_meta ) {
				$meta_cache = update_meta_cache( 'post', array( $post_id ) );
				$post_meta  = $meta_cache[ $post_id ];
			}

			/**
			 * Filter both title and meta description to the current language.
			 *
			 * @important Return empty if no current language text exists,
			 * do not use the default. Yoast must receive empty string to realize
			 * that meta is not set for that language.
			 */
			foreach ( array( '_yoast_wpseo_title', '_yoast_wpseo_metadesc' ) as $_ ) {
				if ( ! empty( $post_meta[ $_ ][0] ) ) {
					$post_meta[ $_ ][0] = WPGlobus_Core::text_filter(
						$post_meta[ $_ ][0],
						WPGlobus::Config()->language,
						WPGlobus::RETURN_EMPTY
					);
				}
			}
			// ... and return it.
			$metadata = $post_meta;
		}

		return $metadata;
	}

	/**
	 * Filter to get yoast seo separator.
	 *
	 * @since 1.5.3
	 *
	 * @param array $sep Contains separator.
	 *
	 * @return string
	 */
	public static function filter__get_separator( $sep ) {
		self::$yoastseo_separator = $sep;

		return $sep;
	}

	/**
	 * Filter which editor should be displayed by default.
	 *
	 * @since 1.4.8
	 *
	 * @param array $editors An array of editors. Accepts 'tinymce', 'html', 'test'.
	 *
	 * @return string
	 */
	public static function set_default_editor(
		/** @noinspection PhpUnusedParameterInspection */
		$editors
	) {
		return 'tinymce';
	}

	/**
	 * To translate Yoast columns
	 * @see   WPSEO_Meta_Columns::column_content
	 * @scope admin
	 *
	 * @param string $text
	 *
	 * @return string
	 * @todo  Yoast said things might change in the next version. See the pull request
	 * @link  https://github.com/Yoast/wordpress-seo/pull/1946
	 */
	public static function filter__wpseo_columns( $text ) {

		if ( WPGlobus_WP::is_filter_called_by( 'column_content', 'WPSEO_Meta_Columns' ) ) {

			if ( self::$yoastseo_separator && false !== strpos( $text, self::$yoastseo_separator ) ) {

				$title_arr = explode( self::$yoastseo_separator, $text );

				foreach ( $title_arr as $key => $piece ) {
					if ( (int) $key === 0 ) {
						$title_arr[ $key ] = WPGlobus_Core::text_filter( $piece, WPGlobus::Config()->language ) . ' ';
					} else {
						$title_arr[ $key ] = ' ' . WPGlobus_Core::text_filter( $piece, WPGlobus::Config()->language );
					}
				}

				$text = implode( self::$yoastseo_separator, $title_arr );

			} else {

				$text = WPGlobus_Core::text_filter(
					$text,
					WPGlobus::Config()->language,
					null,
					WPGlobus::Config()->default_language
				);

			}
		}

		return $text;
	}

	/**
	 * Enqueue js for YoastSEO support
	 * @since 1.4.0
	 */
	public static function action__admin_print_scripts() {

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

			$yoastseo_plus_page_analysis_access = sprintf(
				__( 'Please see %s to get access to page analysis with YoastSEO.', '' ),
				'<a href="https://wpglobus.com/product/wpglobus-plus/#yoastseo" target="_blank">WPGlobus Plus</a>'
			);

			$yoastseo_plus_meta_keywords_access = sprintf(
				__( 'Please see %s to get access to meta keywords with YoastSEO.', '' ),
				'<a href="https://wpglobus.com/product/wpglobus-plus/#yoastseo" target="_blank">WPGlobus Plus</a>'
			);			
			
			$i18n = array(
				'yoastseo_plus_page_analysis_access' => $yoastseo_plus_page_analysis_access,
				'yoastseo_plus_meta_keywords_access' => $yoastseo_plus_meta_keywords_access
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
				
			}

			if ( $src_version_premium ) :

				$handle = self::$handle_script_premium;

				$src = WPGlobus::$PLUGIN_DIR_URL . 'includes/js/' .
					   $handle . '-' . $src_version_premium .
					   WPGlobus::SCRIPT_SUFFIX() . '.js';

				wp_enqueue_script(
					$handle,
					$src,
					array( 'jquery', 'underscore' ),
					WPGLOBUS_VERSION,
					true
				);

			endif;

			if ( $src_version ) :

				$handle = self::$handle_script;

				$src = WPGlobus::$PLUGIN_DIR_URL . 'includes/js/' .
					   $handle . '-' . $src_version .
					   WPGlobus::SCRIPT_SUFFIX() . '.js';

				wp_enqueue_script(
					$handle,
					$src,
					array( 'jquery', 'underscore' ),
					WPGLOBUS_VERSION,
					true
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
	 * Add language tabs to wpseo metabox ( .wpseo-metabox-tabs-div )
	 */
	public static function action__wpseo_tab_content() {

		/** @global WP_Post $post */
		global $post;

		if ( self::disabled_entity() ) {
			return;
		}

		$permalink = array();
		if ( 'publish' === $post->post_status ) {
			$permalink['url']    = get_permalink( $post->ID );
			$permalink['action'] = 'complete';
		} else {
			$permalink['url']    = trailingslashit( home_url() );
			$permalink['action'] = '';
		}

		/**
		 * #wpseo-metabox-tabs
		 */
		/**
		 * Array of IDs to make multilingual.
		 */
		$ids = array(
			'wpseo-add-keyword-popup',
			'wpseosnippet',
			#'wpseosnippet_title',
			'snippet_preview',
			'title_container',
			'snippet_title',
			'snippet_sitename',
			'url_container',
			'snippet_citeBase',
			'snippet_cite',
			'meta_container',
			'snippet_meta',
			'yoast_wpseo_focuskw_text_input',
			'yoast_wpseo_focuskw',
			'yoast_wpseo_focuskeywords',
			'focuskwresults',
			'yoast_wpseo_title',
			#'yoast_wpseo_title-length-warning',
			'yoast_wpseo_metadesc',
			#'yoast_wpseo_metadesc-length',
			#'yoast_wpseo_metadesc_notice',
			'yoast_wpseo_linkdex',
			'wpseo-pageanalysis',
			'yoast-seo-content-analysis',
			'YoastSEO-plugin-loading',
			#from Yoast 3.1
			'snippet-editor-title',
			'snippet-editor-slug',
			'snippet-editor-meta-description',
			#from Yoast 4.4
			'wpseo-focuskeyword-section',
			'wpseo-pageanalysis-section',
			'pageanalysis',
			#'_yst_is_cornerstone',
			/* @since 1.8.8 */
			'wpseometakeywords',
			'yoast_wpseo_metakeywords'
		);

		/**
		 * Array of special IDs for yoast premium.
		 * @since 1.7.12
		 */
		$ids_premium_special = array(
			'_yst_is_cornerstone',
			/* @since 1.8.8 */
			'wpseometakeywords'
		);

		$names = array(
			'yoast_wpseo_focuskw_text_input',
			'yoast_wpseo_focuskw',
			'yoast_wpseo_focuskeywords',
			'yoast_wpseo_title',
			'yoast_wpseo_metadesc',
			'yoast_wpseo_linkdex',
			/* @since 1.8.8 */
			'yoast_wpseo_metakeywords'
		);

		$qtip = array(
			'snippetpreviewhelp',
			'focuskw_text_inputhelp',
			'pageanalysishelp',
			#'focuskwhelp',
			#'titlehelp',
			#'metadeschelp',
			#since yoast seo 3.2
			'snippetpreview-help',
			'focuskw_text_input-help',
			'pageanalysis-help',
			'snippetpreview-help-toggle',
			'focuskw_text_input-help-toggle',
			'pageanalysis-help-toggle'
		);

		?>

		<div id="wpglobus-wpseo-tabs" style="width:90%;float:right;">    <?php
			/**
			 * Use span with attributes 'data' for send to js script ids, names elements for which needs to be set new ids, names with language code.
			 */ ?>
			<span id="wpglobus-wpseo-attr"
			      data-ids="<?php echo esc_attr( implode( ',', $ids ) ); ?>" 
			      data-names="<?php echo esc_attr( implode( ',', $names ) ); ?>" 
			      data-qtip="<?php echo esc_attr( implode( ',', $qtip ) ); ?>" 
			      data-ids-premium-special="<?php echo esc_attr( implode( ',', $ids_premium_special ) ); ?>">
			</span>
			<ul class="wpglobus-wpseo-tabs-list">    <?php
				$order = 0;
				foreach ( WPGlobus::Config()->open_languages as $language ) { ?>
					<li id="wpseo-link-tab-<?php echo esc_attr( $language ); ?>"
					    data-language="<?php echo esc_attr( $language ); ?>"
					    data-order="<?php echo esc_attr( $order ); ?>"
					    class="wpglobus-wpseo-tab"><a
							href="#wpseo-tab-<?php echo esc_attr($language); ?>"><?php echo esc_attr( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul> <?php

			/**
			 * Get meta description
			 */
			$metadesc = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );

			/**
			 * Get title
			 */
			$wpseotitle = get_post_meta( $post->ID, '_yoast_wpseo_title', true );

			/**
			 * From Yoast3 focus keyword key is '_yoast_wpseo_focuskw_text_input'.
			 */
			$focuskw = get_post_meta( $post->ID, '_yoast_wpseo_focuskw_text_input', true );

			/**
			 * Make yoast cite base.
			 */
			list( $yoast_permalink ) = get_sample_permalink( $post->ID );
			$yoast_permalink = str_replace( array( '%pagename%', '%postname%' ), '', urldecode( $yoast_permalink ) );

			/**
			 *  Set cite does not editable by default.
			 */
			$cite_contenteditable = 'false';

			foreach ( WPGlobus::Config()->open_languages as $language ) {

				$focuskeywords = '';
				if ( $language == WPGlobus::Config()->default_language ) {
					/**
					 * Get meta _yoast_wpseo_focuskeywords from yoast premium.
					 * @since 1.7.2 are processing default language only.
					 */
					$focuskeywords = get_post_meta( $post->ID, '_yoast_wpseo_focuskeywords', true );
				}

				$yoast_cite_base = WPGlobus_Utils::localize_url( $yoast_permalink, $language );
				$yoast_cite_base = str_replace( array( 'http://', 'https://' ), '', $yoast_cite_base );
				$yoast_cite_base = str_replace( '//', '/', $yoast_cite_base );

				$permalink['url'] = WPGlobus_Utils::localize_url( $permalink['url'], $language );
				$url              = apply_filters( 'wpglobus_wpseo_permalink', $permalink['url'], $language );

				if ( $url !== $permalink['url'] ) {
					/* We accept that user's filter make complete permalink for draft */
					/* @todo maybe need more investigation */
					$permalink['action'] = 'complete';
				} else {
					if ( 'publish' !== $post->post_status ) {
						/**
						 * We cannot get post-name-full to make correct url here ( for draft & auto-draft ). We do it in JS
						 * @see var wpseosnippet_url in wpglobus-wpseo-**.js
						 */
						$permalink['action'] = '';
					}
				} ?>
				<div id="wpseo-tab-<?php echo esc_attr( $language ); ?>" class="wpglobus-wpseo-general"
				     data-language="<?php echo esc_attr( $language ); ?>"
				     data-url-<?php echo esc_attr( $language ); ?>="<?php echo esc_attr( $url ); ?>"
				     data-yoast-cite-base="<?php echo esc_attr( $yoast_cite_base ); ?>"
				     data-cite-contenteditable="<?php echo esc_attr( $cite_contenteditable ); ?>"
				     data-permalink="<?php echo esc_attr( $permalink['action'] ); ?>"
				     data-metadesc="<?php echo esc_attr( WPGlobus_Core::text_filter( $metadesc, $language, WPGlobus::RETURN_EMPTY ) ); ?>"
				     data-wpseotitle="<?php echo esc_attr( WPGlobus_Core::text_filter( $wpseotitle, $language, WPGlobus::RETURN_EMPTY ) ); ?>"
				     data-focuskw="<?php echo esc_attr( WPGlobus_Core::text_filter( $focuskw, $language, WPGlobus::RETURN_EMPTY ) ); ?>"
				     data-focuskeywords='<?php echo esc_attr( $focuskeywords ); ?>'>
				</div> <?php
			} ?>
		</div>
		<?php
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
			} else if ( WPGlobus::O()->disabled_entity( $post->post_type ) ) {
				$result = true;
			}
		endif;
		return $result;
	}

} // class

# --- EOF
