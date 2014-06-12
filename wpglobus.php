<?php
/*
Plugin Name: WPGlobus
Description: WPGlobus translation plugin
Text Domain: wpglobus
Version: 0.2
Author: Alex Gor
Author URI: 
*/

// Exit if accessed directly
if ( !defined('ABSPATH') ) exit;

include( dirname(__FILE__) . '/includes/wpglobus.config.php' );
global $WPGlobus_Config;
$WPGlobus_Config = new WPGlobus_Config();

include( dirname(__FILE__) . '/functions.php' );

# extract url information
$WPGlobus_Config->url_info = globus_extractURL( $_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' );
$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];

class WPGlobus {

	public static $_version = '0.2';
	
	public static $minmalReduxFramework_version = '3.2.9.4';

	/*
	 * Language edit page
	 */
	const LANGUAGE_EDIT_PAGE = 'wpglobus_language_edit';

	/*
	 * List navigation menus
	 * @var array
	 */
	var $menus = array();


	/*
	 * Constructor
	 */
	function __construct() {

		global $WPGlobus_Config;

		if ( is_admin() ) {

			require_once 'Redux-Framework/ReduxCore/framework.php';
			require_once 'includes/options/wpglobus.option.php';

			add_filter( "redux/{$WPGlobus_Config->option}/field/class/table", array( &$this, 'on_field_table' ) );

			global $WPGlobusOption;
			$WPGlobusOption = new Redux_Framework_globus_option();

			add_action( 'admin_menu',  			array( &$this, 'on_admin_menu' ), 10 );
			add_action( 'admin_print_scripts', 	array( &$this, 'on_admin_scripts' ) );
			add_action( 'admin_print_styles', 	array( &$this, 'on_admin_styles' ) );

		} else {
			
			$test_str = __( 'Test str', 'wpglobus' );
			
			$this->menus = $this->_get_nav_menus();

			add_filter( 'wp_list_pages', 		array( &$this, 'on_wp_list_pages' ), 99, 2 );
			add_filter( 'wp_nav_menu_objects', 	array( &$this, 'on_add_item' ), 99, 2 );
			add_action( 'wp_head', 				array( &$this, 'on_wp_head'), 11 );
			add_action( 'wp_print_styles', 		array( &$this, 'on_wp_styles' ) );
		}

	}

	/*
	 * Enqueue admin scripts
	 * @return void
	 */
	function on_admin_scripts() {

		global $WPGlobus_Config;
		$page = isset($_GET['page']) ? $_GET['page'] : '';

		if ( self::LANGUAGE_EDIT_PAGE ==  $page ) {

			wp_register_script(
				'select2',
				plugins_url( '/Redux-Framework/ReduxCore/assets/js/vendor/select2/select2.js', __FILE__ ),
				array('jquery'),
				self::$_version,
				true
			);
			wp_enqueue_script( 'select2' );

			wp_register_script(
				'admin-globus',
				plugins_url( '/includes/js/admin.globus.js', __FILE__ ),
				array('jquery'),
				self::$_version,
				true
			);
			wp_enqueue_script( 'admin-globus' );
			wp_localize_script(
				'admin-globus',
				'aaAdminGlobus',
				array(
					'version'			=> self::$_version,
					'ajaxurl'			=> admin_url( 'admin-ajax.php' ),
					'parentClass'		=> __CLASS__,
					'process_ajax' 		=> __CLASS__ . '_process_ajax',
					'flag_url'			=> $WPGlobus_Config->flags_url,
					'i18n'				=> '$i18n'
				)
			);

		}
	}

	/*
	 * Enqueue admin styles
	 * @return void
	 */
	function on_admin_styles() {

		$page = isset($_GET['page']) ? $_GET['page'] : '';

		if ( self::LANGUAGE_EDIT_PAGE ==  $page ) {
			wp_register_style(
				'select2',
				plugins_url( '/Redux-Framework/ReduxCore/assets/js/vendor/select2/select2.css', __FILE__ ),
				array(),
				self::$_version,
				'all'
			);
			wp_enqueue_style( 'select2' );
		}
	}

	/*
	 * Add hidden submenu for Language edit page
	 *
	 * @return void
	 */
	function on_admin_menu(){
		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			self::LANGUAGE_EDIT_PAGE,
			array( &$this, 'on_language_edit' )
		);
	}

	/*
	 * Include file for language edit page
	 *
	 * @return void
	 */
	function on_language_edit(){
		include dirname(__FILE__) . '/includes/admin/class.language-edit.php';
		new WPGlobus_language_edit();
	}

	/*
	 * Include file for new field 'table'
	 *
	 * @return string
	 */
	function on_field_table($field){
		return dirname(__FILE__) . '/includes/options/fields/table/field_table.php';
	}

	/*
	 * Enqueue styles
	 *
	 * @return void
	 */
	function on_wp_styles(){
		wp_register_style(
			'flags',
			plugins_url( '/includes/css/globus.flags.css', __FILE__ ),
			array(),
			self::$_version,
			'all'
		);
		wp_enqueue_style( 'flags' );
	}

	/*
	 * Add css styles to head section
	 *
	 * @return string
	 */
	function on_wp_head() {

		global $WPGlobus_Config;

		$css  = "<style type=\"text/css\" media=\"screen\">\n";
		foreach( $WPGlobus_Config->enabled_languages as $language) {
			$css .= ".globus-flag-" . $language . " { background:url(" . $WPGlobus_Config->flags_url . $WPGlobus_Config->flag[$language] . ") no-repeat }\n";
		}
		$css  .= $WPGlobus_Config->css_editor . "\n";
		$css  .= "</style>\n";

		echo $css;

	}


	/*
	 * Add item to navigation menu which was created with wp_list_pages
	 *
	 * @return string
	 */
	function on_wp_list_pages( $output, $r ) {

		global $WPGlobus_Config;

		$extra_languages = array();
		foreach( $WPGlobus_Config->enabled_languages as $languages ) {
			if ( $languages != $WPGlobus_Config->language ) {
				$extra_languages[] = $languages;
			}
		}

		$span_classes 	= array( 'globus-flag', 'globus-language-name' );

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'globus-flag-' . $WPGlobus_Config->language;

		$output .= '<li class="page_item page-item-globus-menu-switch page_item_has_children">
						<a href="' . globus_getUrl($WPGlobus_Config->language) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $WPGlobus_Config->language ) . '</span></a>
						<ul class="children">';
		foreach( $extra_languages as $language ) {
			$span_classes_lang	 = $span_classes;
			$span_classes_lang[] = 'globus-flag-' . $language;
			$output .= 		'<li id="" class="page_item">
								<a href="' . globus_getUrl($language) . '"><span class="' . implode( ' ', $span_classes_lang ) .'">' . $this->_get_flag_name( $language ) . '</span></a>
							</li>';
		}	// end foreach
		$output .= 	'	</ul>
					</li>';

		return $output;
	}

	/*
	 * Add item to navigation menu
	 * @return array
	 */
	function on_add_item( $sorted_menu_items, $args ) {

		global $WPGlobus_Config;

		if ( ! empty( $WPGlobus_Config->nav_menu ) ) {
			if ( ! isset($args->menu->slug) ) {
				if ( $this->menus[0]->slug != $WPGlobus_Config->nav_menu ) {
					return $sorted_menu_items;
				}
			} elseif ( $args->menu->slug != $WPGlobus_Config->nav_menu ) {
				return $sorted_menu_items;
			}
		}

		$extra_languages = array();
		foreach( $WPGlobus_Config->enabled_languages as $languages ) {
			if ( $languages != $WPGlobus_Config->language ) {
				$extra_languages[] = $languages;
			}
		}

		/** main menu item classes */
		$menu_item_classes    = array( '', 'menu-item-globus-menu-switch' );
		
		/** submenu item classes */
		$submenu_item_classes = array( '', 'sub-menu-item-globus-menu-switch' );

		$span_classes 	= array( 'globus-flag', 'globus-language-name' );

		$span_classes_lang	 = $span_classes;
		$span_classes_lang[] = 'globus-flag-' . $WPGlobus_Config->language;

		$item = new stdClass();
		$item->ID				= 9999999999; # 9 999 999 999
		$item->db_id			= 9999999999;
		$item->menu_item_parent = 0;
		$item->title 			= '<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $WPGlobus_Config->language ) . '</span>';
		$item->url 				= globus_getUrl( $WPGlobus_Config->language );
		$item->classes  		= $menu_item_classes;

		$sorted_menu_items[]  	= $item;

		foreach( $extra_languages as $language ) {
			$span_classes_lang 		= $span_classes;
			$span_classes_lang[]	= 'globus-flag-' . $language;

			$item = new stdClass();
			$item->ID				= 'globus-menu-switch-' . $language;
			$item->db_id			= 'globus-menu-switch-' . $language;
			$item->menu_item_parent = 9999999999;
			$item->title 			= '<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span>';
			$item->url 				= globus_getUrl( $language );
			$item->classes  		= $submenu_item_classes;

			$sorted_menu_items[]  	= $item;
		}

		return $sorted_menu_items;
	}

	/*
	 * Get flag name for navigation menu
	 * @return string
	 */
	function _get_flag_name( $language ) {

		global $WPGlobus_Config;

		switch ( $WPGlobus_Config->show_flag_name ) {
			case 'name' :
				$flag_name = $WPGlobus_Config->language_name[$language] ;
				break;
			case 'code' :
				$flag_name = $language;
				break;
			default:
				$flag_name = '';
		}
		return $flag_name;

	}

	/*
	 * Get navigation menus
	 *
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

$WPGlobus = new WPGlobus();
