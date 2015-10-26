<?php
/**
 * Multilingual Customizer
 * @package    WPGlobus
 * @subpackage WPGlobus/Admin
 * @since      1.2.1
 */

/**
 * Class WPGlobus_Customize
 */
class WPGlobus_Customize {

	public static function controller() {
		/**
		 * @see \WP_Customize_Manager::wp_loaded
		 * It calls the `customize_register` action first,
		 * and then - the `customize_preview_init` action
		 */
		add_action( 'customize_register', array(
			'WPGlobus_Customize',
			'action__customize_register'
		) );
		add_action( 'customize_preview_init', array(
			'WPGlobus_Customize',
			'action__customize_preview_init'
		) );

		/**
		 * This is called by wp-admin/customize.php
		 */
		add_action( 'customize_controls_enqueue_scripts', array(
			'WPGlobus_Customize',
			'action__customize_controls_enqueue_scripts'
		), 1000 );
		
		if ( WPGlobus_WP::is_admin_doing_ajax() ) {
			add_filter( 'clean_url', array(
				'WPGlobus_Customize',
				'filter__clean_url'
			), 10, 2 );
		
		}	
	}

	/**
	 * Filter a string to check translations for URL.
	 * We build multilingual URLs in customizer using the ':::' delimiter.
	 * See wpglobus-customize-control.js
	 *
	 * @note  To work correctly, value of $url should begin with URL for default language.
	 * @see   esc_url() - the 'clean_url' filter
	 * @since 1.3.0
	 *
	 * @param string $url          The cleaned URL.
	 * @param string $original_url The URL prior to cleaning.
	 *
	 * @return string
	 */
	public static function filter__clean_url( $url, $original_url ) {

		if ( false !== strpos( $original_url, ':::' ) ) {
			$arr1 = array();
			$arr  = explode( ':::', $original_url );
			foreach ( $arr as $k => $val ) {
				// Note: 'null' is a string, not real `null`.
				if ( 'null' !== $val ) {
					$arr1[ WPGlobus::Config()->enabled_languages[ $k ] ] = $val;
				}
			}

			return WPGlobus_Utils::build_multilingual_string( $arr1 );
		}

		return $url;
	}
	
	/**
	 * Add multilingual controls.
	 * The original controls will be hidden.
	 * @param WP_Customize_Manager $wp_customize
	 */
	public static function action__customize_register( WP_Customize_Manager $wp_customize ) {

		/**
		 * Blog Name
		 */
		$wp_customize->add_setting( 'wpglobus_blogname', array(
			'default' => WPGlobus_Core::text_filter( get_bloginfo( 'name' ), WPGlobus::Config()->language )
		) );
		$wp_customize->get_setting( 'wpglobus_blogname' )->transport = 'postMessage';
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'wpglobus_blogname', array(
				'label'    => __( 'Site Title' ),
				'type'     => 'text',
				'section'  => 'title_tagline',
				'settings' => 'wpglobus_blogname',
				'values'   => WPGlobus_Core::text_filter( get_bloginfo( 'name' ), WPGlobus::Config()->language ),
				'input_attrs' => array(
					'class' => 'wpglobus-customize-control wpglobus-not-trigger-change'
				)
			)
		) );

		/**
		 * Blog Description
		 */
		$wp_customize->add_setting( 'wpglobus_blogdescription', array(
			'default' => WPGlobus_Core::text_filter( get_bloginfo( 'description' ), WPGlobus::Config()->language )
		) );
		$wp_customize->get_setting( 'wpglobus_blogdescription' )->transport = 'postMessage';
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'wpglobus_blogdescription', array(
				'label'    => __( 'Tagline' ),
				'type'     => 'text',
				'section'  => 'title_tagline',
				'settings' => 'wpglobus_blogdescription',
				'input_attrs' => array(
					'class' => 'wpglobus-customize-control wpglobus-not-trigger-change'
				)
			)
		) );
		
		/**
		 * Add elements from wpglobus-config.json
		 */
		if ( empty( WPGlobus::Config()->WPGlobus_WP_Theme ) ) {
			return;
		}

		foreach( WPGlobus::Config()->WPGlobus_WP_Theme->elements as $key=>$value ) {

			/**
			 * $value['type']
			 * @see https://codex.wordpress.org/Class_Reference/WP_Customize_Control  for Input Types 
			 */ 
			$wp_customize->add_setting( $key, array(
				'default' => ''
			) );			
			$wp_customize->get_setting( $key )->transport = 'postMessage';
			$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
				$key, array(
					'label'    => '{{title}}',
					'description'    => '{{description}}',
					'type'     => $value['type'],
					'section'  => $value['section'],
					'settings' => $key,
					'input_attrs' => array(
						'class' => 'wpglobus-customize-control wpglobus-control-' . $value['type'],
						'data-type' => $value['type'],
						'data-source' => ''
					)
				)
			) );
			
		}	

	}
	
	/**
	 * Load Customize Preview JS
	 * Used by hook: 'customize_preview_init'
	 * @see 'customize_preview_init'
	 */
	public static function action__customize_preview_init() {
		wp_enqueue_script(
			'wpglobus-customize-preview',
			WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-customize-preview' .
			WPGlobus::SCRIPT_SUFFIX() . '.js',
			array( 'jquery', 'customize-preview' ),
			WPGLOBUS_VERSION,
			true
		);
		wp_localize_script(
			'wpglobus-customize-preview',
			'WPGlobusCustomize',
			array(
				'version'         => WPGLOBUS_VERSION,
				'blogname'        => WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language ),
				'blogdescription' => WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language )
			)
		);
	}

	/**
	 * Load Customize Control JS
	 */
	public static function action__customize_controls_enqueue_scripts() {
		wp_enqueue_script(
			'wpglobus-customize-control',
			WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-customize-control' .
			WPGlobus::SCRIPT_SUFFIX() . '.js',
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);
	}

} // class

# --- EOF
