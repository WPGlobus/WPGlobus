<?php
/*
Plugin Name: Globus
Description: Globus translation plugin
Text Domain: wpglobus
Version: 0.1
Author: Alex Gor
Author URI: 
*/

// Exit if accessed directly
if ( !defined('ABSPATH') ) exit;

include( dirname(__FILE__) . '/globus.config.php' );
global $WPGlobus_Config;
$WPGlobus_Config = new WPGlobus_Config();

include( dirname(__FILE__) . '/functions.php' );

# extract url information
$WPGlobus_Config->url_info = globus_extractURL( $_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' );
$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];


class WPGlobus {

	public static $_version = '0.1';

	/*
	 * List navigation menus
	 * @var array
	 */
	var $menus = array();

	/*
	 * WPGlobus option key
	 * @var string
	 */	
	private $option = 'wpglobus_option';

	/*
	 * Constructor
	 */
	function __construct() {

		if ( is_admin() ) {

			require_once 'Redux-Framework/ReduxCore/framework.php';
			require_once 'includes/options/globus-option.php';

			// add_filter( "redux/{$this->option}/field/class/radio_sorter", array( &$this, 'on_radio_sorter' ) );
			// add_filter( "redux/{$this->option}/field/class/select_with_flag", array( &$this, 'on_select_with_flag' ) );
			add_filter( "redux/{$this->option}/field/class/select", array( &$this, 'on_select' ) );

			global $WPGlobusOption;
			$WPGlobusOption = new Redux_Framework_globus_option();

			$wpglobus_option = get_option( $this->option );

		} else {

			$this->menus = $this->_get_nav_menus();

			add_filter( 'wp_list_pages', 		array( &$this, 'on_wp_list_pages' ), 99, 2 );
			add_filter( 'wp_nav_menu_objects', 	array( &$this, 'on_add_item' ), 99, 2 );
			add_action( 'wp_head', 				array( &$this, 'on_wp_head'), 11 );
			add_action( 'wp_print_styles', 		array( &$this, 'on_wp_styles' ) );
		}

	}

	function on_select_with_flag($field){
		return dirname(__FILE__) . '/includes/options/fields/select_with_flag/field_select_with_flag.php';
	}
	function on_select($field){
		return dirname(__FILE__) . '/includes/options/fields/select/field_select.php';
	}
	/*
	 * Enqueue styles
	 * @return void
	 */
	function on_wp_styles(){
		wp_register_style(
			'flags',
			plugins_url( '/globus.flags.css', __FILE__ ),
			array(),
			'',
			'all'
		);
		wp_enqueue_style( 'flags' );
	}

	/*
	 * Add css styles to head section
	 * @return string
	 */
	function on_wp_head() {

		global $WPGlobus_Config;

		$css  = "<style type=\"text/css\" media=\"screen\">\n";
		foreach( $WPGlobus_Config->enabled_languages as $language) {
			$css .= ".globus-flag-" . $language . " { background:url(" . $WPGlobus_Config->flags_url . $WPGlobus_Config->flag[$language] . ") no-repeat }\n";
		}
		$css  .= $WPGlobus_Config->custom_css . "\n";
		$css  .= "</style>\n";

		echo $css;

	}


	/*
	 * Add item to navigation menu which was created with wp_list_pages
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

		#$menu_item_classes = array( '', 'menu-item', 'menu-item-type-post_type', 'menu-item-globus-menu-switch' );
		
		/** main menu item classes */
		$menu_item_classes    = array( '', 'menu-item-globus-menu-switch' );
		
		/** submenu item classes */
		$submenu_item_classes = array( '', 'sub-menu-item-globus-menu-switch' );

		$span_classes 	= array( 'globus-flag', 'globus-language-name' );

		$span_classes_lang	 = $span_classes;
		$span_classes_lang[] = 'globus-flag-' . $WPGlobus_Config->language;

		$item = new stdClass();
		$item->ID				= 9999999999; // 9 999 999 999
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



// globus_extractURL();

//error_log( print_r( $menus, true ) );


//add_filter( 'wp_nav_menu_items', 'on_add_item', 10, 2);  // 4 items from menu
//add_filter( 'wp_nav_menu_args', 'on_add_item1', 10 );
function on_add_item1( $args ) {
	error_log( print_r( $args, true ) );
	return $args;
}

//add_filter( 'wp_nav_menu', 'on_add_item', 10, 2);  // 6 items ( without switcher )
function on_add_item( $items, $args ) {
	global $globus_config;
	
	//error_log( print_r( $_SERVER, true ) );
	
	$url1 = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
	
	$item = '<ul class="globus-top-switch-submenu sub-menu">'; 	// position: relative;	
		
	foreach( $globus_config['enabled_languages'] as $language ) {
		if ( $globus_config['language'] != $language ) {
			$item .= '<li class="menu-item"><a href="' . $url1 . $language . $_SERVER['REQUEST_URI'] . '">' . $language . '</a></li>';
		}
	}
	
	$item .= '</ul>';

	$items = str_replace('<span class="globus-switch-extend"></span></a>', '</a>' . $item, $items );
	//error_log( print_r( $items, true ) );
	//error_log( print_r( $args, true ) );
	
	return $items;
}
