<?php
/**
 * File: class-wpglobus-admin-central.php
 *
 * @since 1.6.6
 * @package WPGlobus\Admin\Central
 */

/**
 * Class WPGlobus_Admin_Central.
 */
if ( ! class_exists( 'WPGlobus_Admin_Central' ) ) :

	class WPGlobus_Admin_Central {

		/**
		 * Link template.
		 * @var string
		 */
		public static $link_template;

		/**
		 * Constructor.
		 */
		public static function construct() {

			self::set_vars();

			add_action( 'admin_menu', array( __CLASS__, 'add_menu' ), PHP_INT_MAX );

		}

		/**
		 * Set class variables.
		 */
		public static function set_vars() {

			self::$link_template  = '<a href="{{href}}" class="{{link_class}}" data-tab-id="{{tab_id}}">';
			self::$link_template .= 	'<span class="{{span_class}}" style="vertical-align: sub;"></span>';
			self::$link_template .= 	'{{title}}';
			self::$link_template .= '</a>';

		}

		/**
		 * Add a hidden admin menu item.
		 * It serves as a base for several admin tabs, but currently do not have the "root" content.
		 */
		public static function add_menu() {
			add_submenu_page(
				null,
				'',
				'',
				'administrator',
				WPGlobus::PAGE_WPGLOBUS_ADMIN_CENTRAL,
				array( __CLASS__, 'central_page' )
			);
		}

		/**
		 * The admin central page.
		 */
		public static function central_page() {

			/**
			 * Filter tabs.
			 * Returning array.
			 * @since 1.6.6
			 *
			 * @param array $tabs Array of tabs.
			 * @param string $link_template The link template.
			 */
			$tabs = apply_filters( 'wpglobus_admin_central_tabs', self::set_tabs(), self::$link_template );

			WPGlobus_Admin_Page::print_header();

			?>
			<h2 class="nav-tab-wrapper">	<?php
				foreach ( $tabs as $type=>$tab ) :
					$html = str_replace( '{{link_class}}', 	implode( ' ', $tab['link_class'] ), $tab['link'] );
					$html = str_replace( '{{span_class}}', 	implode( ' ', $tab['span_class'] ), $html );
					$html = str_replace( '{{title}}', 		$tab['title'], 		$html );
					if ( ! empty( $tab['tab_id'] ) ) {
						$html = str_replace( '{{href}}', '#' . $tab['tab_id'], $html );
						$html = str_replace( '{{tab_id}}', $tab['tab_id'], $html );
					} elseif ( ! empty( $tab['href'] ) ) {
						$html = str_replace( '{{href}}', $tab['href'], $html );
						$html = str_replace( '{{tab_id}}', '', $html );
					} else {
						$html = str_replace( '{{href}}', '#', $html );
						$html = str_replace( '{{tab_id}}', '', $html );
					}
					echo $html; // WPCS: XSS ok.
				endforeach;	?>
			</h2>	<?php

			/**
			 * Fires to render a specific tab panel.
			 *
			 * @since 1.6.6
			 *
			 * @param array $tabs Array of tabs.
			 */
			do_action( 'wpglobus_admin_central_panel', $tabs );

			WPGlobus_Admin_Page::print_footer();

		}

		/**
		 * Add standard tabs.
		 * @return array
		 */
		protected static function set_tabs() {

			$tabs = array();

			/**
			 * WPGlobus Guide tab.
			 */
			$tab = array(
				'title' 	 => __( 'Guide', 'wpglobus' ),
				'link_class' => array( 'nav-tab' ),
				'span_class' => array( 'dashicons', 'dashicons-book-alt' ),
				'link' 		 => self::$link_template,
				'href' 		 => WPGlobus::URL_WPGLOBUS_SITE . 'quick-start/',
				'tab_id'	 => ''
			);
			$tabs[ 'guide' ] = $tab;

			/**
			 * WPGlobus Help Desk tab.
			 */
			$href = add_query_arg(
						array(
							'page' => WPGlobus::PAGE_WPGLOBUS_HELPDESK
						),
						admin_url( 'admin.php' )
					);

			$tab = array(
				'title' 	 => __( 'WPGlobus Help Desk', 'wpglobus' ),
				'link_class' => array( 'nav-tab' ),
				'span_class' => array( 'dashicons', 'dashicons-format-chat' ),
				'link' 		 => self::$link_template,
				'href' 		 => $href,
				'tab_id'	 => ''
			);
			$tabs[ 'helpdesk' ] = $tab;

			/**
			 * WPGlobus Add-ons tab.
			 */
			$href = WPGlobus_Admin_Page::url_addons();

			$tab = array(
				'title' 	 => __( 'Add-ons', 'wpglobus' ),
				'link_class' => array( 'nav-tab' ),
				'span_class' => array( 'dashicons', 'dashicons-admin-plugins' ),
				'link' 		 => self::$link_template,
				'href' 		 => $href,
				'tab_id'	 => ''
			);
			$tabs[ 'add_ons' ] = $tab;

			return $tabs;

		}
	}

endif;
/* EOF */
