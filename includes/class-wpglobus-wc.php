<?php
/**
 * @package WPGlobus WC
 */

/**
 * Class WPGlobus WC
 */
class WPGlobus_WC {

	/**
	 * Menu items position by default. It may has 2 values 'submenu' or 'mainmenu'
	 *
	 * @access private
	 * @since 1.0.0
	 * @var string
	 */
	private $admin_menu_position = 'submenu';
	
	/**
	 * Parent slug for adding submenu
	 *
	 * @access private
	 * @since 1.0.0
	 * @var string	 
	 */
	private $menu_parent_slug = 'edit.php?post_type=product';
		
	/**
	 * Name for WPGlobus Woo page
	 *
	 * @access private
	 * @since 1.0.0
	 * @var string	 
	 */		
	private $page_menu	 = 'wpglobus_woo_translations';
	
	/**
	 * Constructor
	 */
	function __construct() {
	
		if ( is_admin() ) {
		
			add_action( 'admin_menu', array( 
				$this, 
				'on_admin_menu' 
			) );
		
		}
		
	}
	
	/**
	 * Add mainmenu or submenu
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_admin_menu() {

		if ( 'submenu' == $this->admin_menu_position ) {

			$this->admin_submenu = add_submenu_page(
				$this->menu_parent_slug,
				'Translations',
				'Translations',
				'administrator',
				$this->page_menu,
				array( $this, 'woo_translation_table' )
			);

			//add_action( 'admin_print_styles-' . $this->admin_submenu, array( $this, 'on_admin_styles' ) );
			//add_action( 'admin_print_scripts-' . $this->admin_submenu, array( $this, 'on_admin_scripts' ) );

			//add_action( 'admin_print_styles-' . $this->admin_submenu_cat, array( $this, 'on_admin_cat_styles' ) );
			//add_action( 'admin_print_scripts-' . $this->admin_submenu_cat, array( $this, 'on_admin_cat_scripts' ) );

			//add_action( 'admin_print_styles-' . $this->admin_submenu_control, array( $this, 'on_admin_control_styles' ) );
			
		}

	}

	/**
	 *
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function woo_translation_table() {
		?>
		<div class="wrap">
			<h2><?php _e('Taxonomy translations', ''); ?></h2>
		</div> <!-- .wrap -->
		<?php
	}
	
}