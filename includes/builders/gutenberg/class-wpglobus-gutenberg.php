<?php
/**
 * File: class-wpglobus-gutenberg.php
 *
 * @package WPGlobus\Builders\Gutenberg
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Gutenberg.
 */
class WPGlobus_Gutenberg extends WPGlobus_Builder {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( 'gutenberg' );

		if ( is_admin() ) {

			/**
			 * Filter the post for Gutenberg editor.
			 *
			 * @see wp-includes\class-wp-query.php
			 */
			add_action( 'the_post', array( $this, 'translate_post' ), 5 );

			/**
			 * Add 'wpglobus-language' hidden field.
			 */
			add_action( 'add_meta_boxes', array( $this, 'on__add_meta_box' ) );

			add_action( 'admin_enqueue_scripts', array(
				$this,
				'on__enqueue_scripts',
			), 1000 );

			/**
			 * @since 1.9.29
			 */
			add_action( 'admin_print_styles', array(
				$this,
				'on__enqueue_styles',
			) );

			/**
			 * @see wpglobus-seo\includes\class-wpglobus-seo.php
			 */
			add_filter( 'wpglobus_seo_meta_box_title', array( $this, 'filter__seo_meta_box_title' ) );

		}

	}

	/**
	 * Translate post.
	 *
	 * @param WP_Post $object
	 */
	public function translate_post( $object ) {
		if ( $object instanceof WP_Post ) {
			WPGlobus_Core::translate_wp_post( $object, $this->language, WPGlobus::RETURN_EMPTY );
		}
	}

	/**
	 * Generate box with language switcher.
	 *
	 * @param string $page
	 *
	 * @return string
	 */
	private function get_switcher_box( $page ) {

		global $post;

		$query_string = explode( '&', $_SERVER['QUERY_STRING'] );

		foreach ( $query_string as $key => $_q ) {
			if ( false !== strpos( $_q, 'language=' ) ) {
				unset( $query_string[ $key ] );
			}
		}
		$query = implode( '&', $query_string );
		$url   = admin_url(
			add_query_arg(
				array(
					'language' => '{{language}}',
				),
				'post.php?' . $query
			)
		);

		$_box_style = 'position:absolute;top:15px;left:10px;z-index:100;';
		if ( file_exists( WPGlobus::Config()->flag_path['big'] . WPGlobus::Config()->flag[ $this->language ] ) ) {
			$_flag_img   = WPGlobus::Config()->flag_urls['big'] . WPGlobus::Config()->flag[ $this->language ];
			$_height     = 'height="25px"';
			$_width      = 'width="25px"';
			$_flag_style = 'style="border: 1px solid #bfbfbf;border-radius: 25px;"';
		} else {
			$_flag_img   = WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $this->language ];
			$_height     = '';
			$_width      = '';
			$_flag_style = 'style="margin-top:5px;"';

			$_box_style .= 'margin-top:3px;';
		}

		$out = '';

		if ( 'post-new.php' === $page ) {

			ob_start();
			?>
			<div style="<?php echo $_box_style; // WPCS: XSS ok. ?>" class="wpglobus-gutenberg-selector-box">
				<!--suppress CssInvalidPropertyValue -->
				<div class="wpglobus-selector-grid"
						style="">
					<a style="text-decoration:none;cursor:text;" onclick="return false;"
							href="#" class="wpglobus-gutenberg-selector wpglobus-gutenberg-selector-column-1"
							data-language="<?php echo esc_attr( $this->language ); ?>">
						<img <?php echo $_height . $_width; // WPCS: XSS ok. ?>
							<?php echo $_flag_style; // WPCS: XSS ok. ?>
								src="<?php echo esc_url( $_flag_img ); ?>" alt=""/>
					</a>
					<a style="text-decoration:none;cursor:text;" onclick="return false;"
							href="#" class="wpglobus-gutenberg-selector wpglobus-gutenberg-selector-column-2"
							data-language="<?php echo esc_attr( $this->language ); ?>">
						&nbsp;<span
								class="wpglobus-gutenberg-selector-text"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $this->language ] ); ?></span>
					</a>
				</div>
				<ul class="wpglobus-gutenberg-selector-dropdown"
						style="display:none;position:fixed;margin:5px;list-style-type:none;">
					<li class="item" style="border:1px solid #ddd;background-color:#eee;padding:4px;">
						<?php esc_html_e( 'Before switching the language, please save draft or publish.', 'wpglobus' ); ?>
					</li>
				</ul>
			</div>
			<?php
			$out = ob_get_clean();

		} elseif ( 'post.php' === $page ) {

			ob_start();
			?>
			<div style="<?php echo $_box_style; // WPCS: XSS ok. ?>" class="wpglobus-gutenberg-selector-box">
				<!--suppress CssInvalidPropertyValue -->
				<div class="wpglobus-selector-grid"
						style="">
					<a style="text-decoration: none;"
							href="<?php echo esc_url( str_replace( '{{language}}', $this->language, $url ) ); ?>"
							class="wpglobus-gutenberg-selector wpglobus-gutenberg-selector-column-1"
							data-language="<?php echo esc_attr( $this->language ); ?>">
						<img <?php echo $_height . $_width; // WPCS: XSS ok. ?>
							<?php echo $_flag_style; // WPCS: XSS ok. ?>
								src="<?php echo $_flag_img; // WPCS: XSS ok. ?>" alt=""/>
					</a>
					<a style="text-decoration: none;"
							href="<?php echo esc_url( str_replace( '{{language}}', $this->language, $url ) ); ?>"
							class="wpglobus-gutenberg-selector wpglobus-gutenberg-selector-column-2"
							data-language="<?php echo esc_attr( $this->language ); ?>">
						&nbsp;<span class="wpglobus-gutenberg-selector-text">
							<?php
							/**
							 * Filter the current language name.
							 * Returning string.
							 *
							 * @since 2.1.0
							 *
							 * @param string  English language name.
							 * @param string  $language Current language.
							 * @param WP_Post $post     The current post.
							 */
							echo esc_html(
							// phpcs:ignore WordPress.NamingConventions
								apply_filters(
									'wpglobus_gutenberg_selector_text',
									WPGlobus::Config()->en_language_name[ $this->language ],
									$this->language,
									$post
								)
							);
							?>
						</span>
					</a>
				</div>
				<ul class="wpglobus-gutenberg-selector-dropdown"
						style="display:none;position:fixed;border-left:1px solid #ddd;border-right:1px solid #ddd;background-color:#eee;margin:5px 0 0;padding:0 5px 5px 0;list-style-type:none;">
					<?php foreach ( WPGlobus::Config()->enabled_languages as $lang ) : ?>
						<?php
						if ( $lang === $this->language ) {
							continue;
						}
						?>
						<li class="item"
								style="text-align:left;cursor:pointer;border-bottom:1px solid #ddd;margin:0;height:25px;padding:5px 0 5px 5px;"
								data-language="<?php echo esc_attr( $lang ); ?>">
							<a href="<?php echo esc_url( str_replace( '{{language}}', $lang, $url ) ); ?>">
								<img src="<?php echo esc_url( WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $lang ] ); ?>" alt=""/>&nbsp;<?php echo esc_html( WPGlobus::Config()->en_language_name[ $lang ] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
			$out = ob_get_clean();

		}

		return $out;

	}

	/**
	 * Callback for 'wpglobus_seo_meta_box_title'.
	 *
	 * @param string $meta_box_title
	 *
	 * @return string
	 */
	public function filter__seo_meta_box_title( $meta_box_title ) {
		return $meta_box_title . ' ' .
			   // Translators: Metabox title FOR language.
			   _x( 'for', 'filter__seo_meta_box_title', 'wpglobus' )
			   . ' ' . WPGlobus::Config()->en_language_name[ $this->get_current_language() ];
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 1.9.29
	 * @return void
	 */
	public function on__enqueue_styles() {
		/** @global string $pagenow */
		global $pagenow;

		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		// phpcs:ignore WordPress.CSRF.NonceVerification
		if ( isset( $_GET['classic-editor'] ) ) {
			return;
		}

		/**
		 * While testing process is run, don't add .scss and .map files.
		 */
		wp_register_style(
			'wpglobus-gutenberg',
			WPGlobus::plugin_dir_url() . 'includes/builders/gutenberg/assets/css/wpglobus-gutenberg' . WPGlobus::SCRIPT_SUFFIX() . '.css',
			array(),
			WPGLOBUS_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus-gutenberg' );

	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function on__enqueue_scripts() {

		/** @global string $pagenow */
		global $pagenow, $wp_version;

		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		// phpcs:ignore WordPress.CSRF.NonceVerification
		if ( isset( $_GET['classic-editor'] ) ) {
			return;
		}

		$tabs = $this->get_switcher_box( $pagenow );

		$i18n           = array();
		$i18n['reload'] = esc_html__( 'Page is being reloaded. Please wait...', 'wpglobus' );

		/**
		 * We have Gutenberg in core since WP 5.0.
		 *
		 * @since 2.0
		 */
		$version_gutenberg = '';
		if ( version_compare( $wp_version, '4.9.99', '>' ) ) {
			$version_gutenberg = $wp_version;
		} elseif ( defined( 'GUTENBERG_VERSION' ) ) {
			$version_gutenberg = GUTENBERG_VERSION;
		}

		/**
		 * Check for Yoast SEO.
		 */
		$yoast_seo = false;
		if ( defined( 'WPSEO_VERSION' ) ) {
			$yoast_seo = true;
		}

		wp_register_script(
			'wpglobus-gutenberg',
			WPGlobus::plugin_dir_url() . 'includes/builders/gutenberg/assets/js/wpglobus-gutenberg' . WPGlobus::SCRIPT_SUFFIX() . '.js',
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus-gutenberg' );
		wp_localize_script(
			'wpglobus-gutenberg',
			'WPGlobusGutenberg',
			array(
				'version'          => WPGLOBUS_VERSION,
				'versionGutenberg' => $version_gutenberg,
				'context'          => WPGlobus::Config()->builder->get( 'context' ),
				'tabs'             => $tabs,
				'language'         => $this->language,
				'pagenow'          => $pagenow,
				'postEditPage'     => 'post.php',
				'postNewPage'      => 'post-new.php',
				'defaultLanguage'  => WPGlobus::Config()->default_language,
				'i18n'             => $i18n,
				'yoastSeo'         => $yoast_seo,
			)
		);
	}

	/**
	 * Callback for 'add_meta_boxes'.
	 */
	public function on__add_meta_box() {

		global $post;

		if ( in_array( $post->post_type, WPGlobus::Config()->disabled_entities, true ) ) {
			return;
		}

		add_meta_box( 'wpglobus', __( 'WPGlobus', 'wpglobus' ), array(
			$this,
			'callback__meta_box',
		), null, 'side', 'core' );
	}

	/**
	 * Callback for 'add_meta_box' function.
	 */
	public function callback__meta_box() {
		echo $this->get_language_field(); // WPCS: XSS ok.
		do_action( 'wpglobus_gutenberg_metabox' );
	}

}
