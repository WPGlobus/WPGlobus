<?php

/**
 * Class WPGlobus
 */
class WPGlobus {

	public static $_version = '0.1.0';

	public static $minimalReduxFramework_version = '3.2.9.4';

	/*
	 * Options page slug needed to get access to settings page
	 */
	const OPTIONS_PAGE_SLUG = 'wpglobus_options';

	/*
	 * Language edit page
	 */
	const LANGUAGE_EDIT_PAGE = 'wpglobus_language_edit';

	/*
	 * List navigation menus
	 * @var array
	 */
	var $menus = array();


	/**
	 * Constructor
	 */
	function __construct() {

		global $WPGlobus_Config;

		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

			if ( ! class_exists( 'ReduxFramework' ) ) {
				/** @todo Here we can set a flag to know that we are using the embedded Redux */
				require_once 'Redux-Framework/ReduxCore/framework.php';
			}

			require_once 'includes/options/wpglobus.option.php';

			add_filter( "redux/{$WPGlobus_Config->option}/field/class/table", array(
				$this,
				'on_field_table'
			) );

			/**
			 * @todo Let's follow the same format. Other vars had underscore.
			 * @todo We should not make globals in the middle of classes. Need to review this.
			 */
			global $WPGlobusOption;
			$WPGlobusOption = new Redux_Framework_globus_option();

			add_action( 'admin_menu', array(
				$this,
				'on_admin_menu'
			), 10 );

			add_action( 'admin_print_scripts', array(
				$this,
				'on_admin_scripts'
			) );

			add_action( 'admin_print_styles', array(
				$this,
				'on_admin_styles'
			) );

		}
		else {

			#$test_str = __( 'Test str', 'wpglobus' );

			$this->menus = $this->_get_nav_menus();

			add_filter( 'wp_list_pages', array(
				$this,
				'on_wp_list_pages'
			), 99, 2 );

			add_filter( 'wp_nav_menu_objects', array(
				$this,
				'on_add_item'
			), 99, 2 );

			add_action( 'wp_head', array(
				$this,
				'on_wp_head'
			), 11 );

			add_action( 'wp_print_styles', array(
				$this,
				'on_wp_styles'
			) );
		}

	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	function on_admin_scripts() {

		global $WPGlobus_Config;
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		if ( self::LANGUAGE_EDIT_PAGE === $page ) {

			/** @todo Why needed? What if redux is loaded not from here? */
			wp_register_script(
				'select2',
				plugins_url( '/Redux-Framework/ReduxCore/assets/js/vendor/select2/select2.js', __FILE__ ),
				array( 'jquery' ),
				self::$_version,
				true
			);
			wp_enqueue_script( 'select2' );

			wp_register_script(
				'admin-globus',
				plugins_url( '/includes/js/admin.globus.js', __FILE__ ),
				array( 'jquery' ),
				self::$_version,
				true
			);
			wp_enqueue_script( 'admin-globus' );
			wp_localize_script(
				'admin-globus',
				'aaAdminGlobus',
				array(
					'version'      => self::$_version,
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'parentClass'  => __CLASS__,
					'process_ajax' => __CLASS__ . '_process_ajax',
					'flag_url'     => $WPGlobus_Config->flags_url,
					'i18n'         => '$i18n'
				)
			);

		}
	}

	/**
	 * Enqueue admin styles
	 * @return void
	 */
	function on_admin_styles() {

		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		if ( self::LANGUAGE_EDIT_PAGE === $page ) {
			/** @todo Why needed? What if redux is loaded not from here? */
			wp_register_style(
				'select2',
				plugins_url( '/Redux-Framework/ReduxCore/assets/js/vendor/select2/select2.css', __FILE__ ),
				array(),
				self::$_version,
				'all'
			);
			wp_enqueue_style( 'select2' );
		}

		wp_register_style(
			'globus.admin',
			plugins_url( '/includes/css/globus.admin.css', __FILE__ ),
			array(),
			self::$_version,
			'all'
		);
		wp_enqueue_style( 'globus.admin' );

	}

	/**
	 * Add hidden submenu for Language edit page
	 * @return void
	 */
	function on_admin_menu() {
		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			self::LANGUAGE_EDIT_PAGE,
			array(
				$this,
				'on_language_edit'
			)
		);
	}

	/**
	 * Include file for language edit page
	 * @return void
	 */
	function on_language_edit() {
		require_once dirname( __FILE__ ) . '/includes/admin/class.language.edit.php';
		new WPGlobus_language_edit();
	}

	/**
	 * Include file for new field 'table'
	 * @return string
	 */
	function on_field_table() {
		return dirname( __FILE__ ) . '/includes/options/fields/table/field_table.php';
	}

	/**
	 * Enqueue styles
	 * @return void
	 */
	function on_wp_styles() {
		wp_register_style(
			'flags',
			plugins_url( '/includes/css/globus.flags.css', __FILE__ ),
			array(),
			self::$_version,
			'all'
		);
		wp_enqueue_style( 'flags' );
	}

	/**
	 * Add css styles to head section
	 * @return string
	 */
	function on_wp_head() {

		global $WPGlobus_Config;

		$css = '';
		foreach ( $WPGlobus_Config->enabled_languages as $language ) {
			$css .= '.globus-flag-' . $language .
				' { background:url(' .
				$WPGlobus_Config->flags_url . $WPGlobus_Config->flag[$language] . ') no-repeat }';
		}
		$css .= strip_tags( $WPGlobus_Config->css_editor );

		if ( ! empty( $css ) ) {
			echo '<style>' . $css . '</style>';
		}

	}


	/**
	 * Add item to navigation menu which was created with wp_list_pages
	 * @param string $output
	 * @return string
	 */
	function on_wp_list_pages( $output ) {

		global $WPGlobus_Config;

		$extra_languages = array();
		foreach ( $WPGlobus_Config->enabled_languages as $languages ) {
			if ( $languages != $WPGlobus_Config->language ) {
				$extra_languages[] = $languages;
			}
		}
		/** @todo All CSS classes should start with wpglobus */
		$span_classes = array(
			'globus-flag',
			'globus-language-name'
		);

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'globus-flag-' . $WPGlobus_Config->language;

		$output .= '<li class="page_item page-item-globus-menu-switch page_item_has_children">
						<a href="' . WPGlobus_Utils::get_url( $WPGlobus_Config->language ) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $WPGlobus_Config->language ) . '</span></a>
						<ul class="children">';
		foreach ( $extra_languages as $language ) {
			$span_classes_lang   = $span_classes;
			$span_classes_lang[] = 'globus-flag-' . $language;
			$output .= '<li id="" class="page_item">
								<a href="' . WPGlobus_Utils::get_url( $language ) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span></a>
							</li>';
		} // end foreach
		$output .= '	</ul>
					</li>';

		return $output;
	}

	/**
	 * Add item to navigation menu
	 * @param array  $sorted_menu_items
	 * @param object $args An object containing wp_nav_menu() arguments.
	 * @return array
	 * @see wp_nav_menu()
	 */
	function on_add_item( $sorted_menu_items, $args ) {

		global $WPGlobus_Config;

		if ( ! empty( $WPGlobus_Config->nav_menu ) ) {
			if ( ! isset( $args->menu->slug ) ) {
				if ( $this->menus[0]->slug != $WPGlobus_Config->nav_menu ) {
					return $sorted_menu_items;
				}
			}
			elseif ( $args->menu->slug != $WPGlobus_Config->nav_menu ) {
				return $sorted_menu_items;
			}
		}

		$extra_languages = array();
		foreach ( $WPGlobus_Config->enabled_languages as $languages ) {
			if ( $languages != $WPGlobus_Config->language ) {
				$extra_languages[] = $languages;
			}
		}

		/** main menu item classes */
		$menu_item_classes = array(
			'',
			'menu-item-globus-menu-switch'
		);

		/** submenu item classes */
		$submenu_item_classes = array(
			'',
			'sub-menu-item-globus-menu-switch'
		);

		$span_classes = array(
			'globus-flag',
			'globus-language-name'
		);

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'globus-flag-' . $WPGlobus_Config->language;

		$item                   = new stdClass();
		$item->ID               = 9999999999; # 9 999 999 999
		$item->db_id            = 9999999999;
		$item->menu_item_parent = 0;
		$item->title            =
			'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $WPGlobus_Config->language ) . '</span>';
		$item->url              = globus_getUrl( $WPGlobus_Config->language );
		$item->classes          = $menu_item_classes;

		$sorted_menu_items[] = $item;

		foreach ( $extra_languages as $language ) {
			$span_classes_lang   = $span_classes;
			$span_classes_lang[] = 'globus-flag-' . $language;

			$item                   = new stdClass();
			$item->ID               = 'globus-menu-switch-' . $language;
			$item->db_id            = 'globus-menu-switch-' . $language;
			$item->menu_item_parent = 9999999999;
			$item->title            =
				'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span>';
			$item->url              = globus_getUrl( $language );
			$item->classes          = $submenu_item_classes;

			$sorted_menu_items[] = $item;
		}

		return $sorted_menu_items;
	}

	/**
	 * Get flag name for navigation menu
	 * @param string $language
	 * @return string
	 */
	function _get_flag_name( $language ) {

		global $WPGlobus_Config;

		switch ( $WPGlobus_Config->show_flag_name ) {
			case 'name' :
				$flag_name = $WPGlobus_Config->language_name[$language];
				break;
			case 'code' :
				$flag_name = $language;
				break;
			default:
				$flag_name = '';
		}
		return $flag_name;

	}

	/**
	 * Get navigation menus
	 * @return array
	 */
	public static function _get_nav_menus() {
		/** @global wpdb $wpdb */
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}terms AS t
					  LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id
					  WHERE tt.taxonomy = 'nav_menu'";

		$menus = $wpdb->get_results( $query );

		return $menus;

	}

}

# --- EOF