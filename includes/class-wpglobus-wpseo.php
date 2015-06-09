<?php
/**
 * Support of WP-SEO by Yoast
 * @package WPGlobus
 * @since   1.1.1
 */

/**  */
class WPGlobus_WPSEO {

	public static function controller() {
		if ( is_admin() ) {

			if ( ! WPGlobus_WP::is_doing_ajax() ) {

				/** @see \WPGlobus::__construct */
				WPGlobus::O()->vendors_scripts['WPSEO'] = true;

				if ( WPGlobus_WP::is_pagenow( 'edit.php' ) ) {
					/**
					 * To translate Yoast columns on edit.php page
					 */
					add_filter( 'esc_html', array(
						'WPGlobus_WPSEO',
						'filter__wpseo_columns'
					), 0 );
				}

				add_action( 'admin_print_scripts', array(
					'WPGlobus_WPSEO',
					'action__admin_print_scripts'
				) );

				add_action( 'wpseo_tab_content', array(
					'WPGlobus_WPSEO',
					'action__wpseo_tab_content'
				), 11 );
			}


		} else {
			/**
			 * Filter SEO title and meta description on front only, when the page header HTML tags are generated.
			 * AJAX is probably not required (waiting for a case).
			 */
			//add_filter( 'wpseo_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
			//add_filter( 'wpseo_metadesc', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
			
			/**
			 * Filter for @see wpseo_title
			 * @scope front
			 * @since 1.1.1		 
			 */			
			add_filter( 'wpseo_title', array( 'WPGlobus_WPSEO', 'filter__title' ), 0 );
			
			/**
			 * Filter for @see wpseo_description
			 * @scope front
			 * @since 1.1.1		 
			 */			
			add_filter( 'wpseo_metadesc', array( 'WPGlobus_WPSEO', 'wpseo_metadesc' ), 0 );					
		
		}

	}
	
	/**
	 * Filter SEO meta description
	 *
	 * @scope front
	 * @since 1.1.1		 
	 *
	 * @param string $text
	 *
	 * @return string
	 */		
	public static function wpseo_metadesc( $text ) {
		
		if ( empty( $text ) ) {
			return $text;
		}	
		
		return WPGlobus_Core::text_filter( $text, WPGlobus::Config()->language );
	
	}
	
	/**
	 * Generate title
	 *
	 * @see get_title_from_options()
	 * @scope front
	 * @since 1.1.1		 
	 *
	 * @param string $text
	 *
	 * @return string
	 */	
	public static function filter__title( $text ) {

		$text = WPGlobus_Core::text_filter( $text, WPGlobus::Config()->language );

		$wpseo_f = WPSEO_Frontend::get_instance();
		
		if ( empty($text) ) {
			global $post;
			$text = $post->post_title . ' ' . $wpseo_f->get_title_from_options( 'wpseo_titles' );
		} else {
			$extra = $wpseo_f->get_title_from_options( 'wpseo_titles' );
			if ( ! empty( $extra ) && false === strpos( $text, $extra ) ) {
				$text .= ' ' . $extra;
			}	
		}
		
		return $text;
		
	}
	
	/**
	 * To translate Yoast columns
	 * @see   WPSEO_Metabox::column_content
	 * @scope admin
	 *
	 * @param string $text
	 *
	 * @return string
	 * @todo  Yoast said things might change in the next version. See the pull request
	 * @link  https://github.com/Yoast/wordpress-seo/pull/1946
	 */
	public static function filter__wpseo_columns( $text ) {

		if ( WPGlobus_WP::is_filter_called_by( 'column_content', 'WPSEO_Metabox' ) ) {

			$text = WPGlobus_Core::text_filter(
				$text,
				WPGlobus::Config()->language,
				null,
				WPGlobus::Config()->default_language
			);
		}

		return $text;
	}

	/**
	 * Enqueue js for WPSEO support
	 * @since 1.0.8
	 */
	public static function action__admin_print_scripts() {

		if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) {

			$handle = 'wpglobus-wpseo';

			/**
			 * WP-SEO Version 2.2 introduces breaking changes.
			 * A new version of our script will be required.
			 */

			/** @noinspection PhpInternalEntityUsedInspection */
			$src_version = version_compare( WPSEO_VERSION, '2.2', '>=' ) ? '22' : '21';

			$src = WPGlobus::$PLUGIN_DIR_URL . 'includes/js/' .
			       $handle . '-' . $src_version .
			       WPGlobus::SCRIPT_SUFFIX() . '.js';

			wp_enqueue_script(
				$handle,
				$src,
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);

			wp_localize_script(
				$handle,
				'WPGlobusVendor',
				array(
					'version' => WPGLOBUS_VERSION,
					'vendor'  => WPGlobus::O()->vendors_scripts
				)
			);
		}

	}

	/**
	 * Add language tabs to wpseo metabox ( .wpseo-metabox-tabs-div )
	 */
	public static function action__wpseo_tab_content() {

		/** @global WP_Post $post */
		global $post;

		$type = empty( $post ) ? '' : $post->post_type;
		if ( WPGlobus::O()->disabled_entity( $type ) ) {
			return;
		}

		$permalink = array();
		if ( 'publish' == $post->post_status ) {
			$permalink['url']    = get_permalink( $post->ID );
			$permalink['action'] = 'complete';
		} else {
			$permalink['url']    = trailingslashit( home_url() );
			$permalink['action'] = '';
		}
		?>

		<div id="wpglobus-wpseo-tabs">    <?php
			/**
			 * Use span with attributes 'data' for send to js script ids, names elements for which needs to be set new ids, names with language code.
			 */ ?>
			<span id="wpglobus-wpseo-attr"
			      data-ids="wpseosnippet,wpseosnippet_title,yoast_wpseo_focuskw,focuskwresults,yoast_wpseo_title,yoast_wpseo_title-length-warning,yoast_wpseo_metadesc,yoast_wpseo_metadesc-length,yoast_wpseo_metadesc_notice"
			      data-names="yoast_wpseo_focuskw,yoast_wpseo_title,yoast_wpseo_metadesc"
			      data-qtip="snippetpreviewhelp,focuskwhelp,titlehelp,metadeschelp">
			</span>
			<ul class="wpglobus-wpseo-tabs-list">    <?php
				$order = 0;
				foreach ( WPGlobus::Config()->open_languages as $language ) { ?>
					<li id="wpseo-link-tab-<?php echo $language; ?>"
					    data-language="<?php echo $language; ?>"
					    data-order="<?php echo $order; ?>"
					    class="wpglobus-wpseo-tab"><a
							href="#wpseo-tab-<?php echo $language; ?>"><?php echo WPGlobus::Config()->en_language_name[ $language ]; ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul>    <?php

			foreach ( WPGlobus::Config()->open_languages as $language ) {
				$url        = WPGlobus_Utils::localize_url( $permalink['url'], $language );
				$metadesc   = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
				$wpseotitle = get_post_meta( $post->ID, '_yoast_wpseo_title', true );
				$focuskw    = get_post_meta( $post->ID, '_yoast_wpseo_focuskw', true ); ?>
				<div id="wpseo-tab-<?php echo $language; ?>" class="wpglobus-wpseo-general"
				     data-language="<?php echo $language; ?>"
				     data-url-<?php echo $language; ?>="<?php echo $url; ?>"
				     data-permalink="<?php echo $permalink['action']; ?>"
				     data-metadesc="<?php echo WPGlobus_Core::text_filter( $metadesc, $language, WPGlobus::RETURN_EMPTY ); ?>"
				     data-wpseotitle="<?php echo WPGlobus_Core::text_filter( $wpseotitle, $language, WPGlobus::RETURN_EMPTY ); ?>"
				     data-focuskw="<?php echo WPGlobus_Core::text_filter( $focuskw, $language, WPGlobus::RETURN_EMPTY ); ?>">
				</div> <?php
			} ?>
		</div>
	<?php
	}


} // class

# --- EOF