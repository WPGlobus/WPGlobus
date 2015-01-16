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
	 * Name for WPGlobus WC translations page
	 *
	 * @access private
	 * @since 1.0.0
	 * @var string	 
	 */		
	private $page_menu	 = 'wpglobus_wc_translations';
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		global $pagenow;
		
		$this->enabled_entities[] = 'product';		
		
		if ( is_admin() ) {
		
			if ( 'edit-tags.php' == $pagenow || 'edit.php' == $pagenow || 'post.php' == $pagenow ) {
				add_filter( 'wpg_disabled_entities', array(
					$this,
					'on_enable_product'
				) );	
			}

			add_action( 'admin_menu', array( 
				$this, 
				'on_admin_menu' 
			) );
			
			add_action( 'admin_print_scripts', array(
				$this,
				'on_admin_scripts'
			) );
				
			 // is_admin()
		} else {
			/**
			 * @see woocommerce\templates\single-product\short-description.php 
			 */
			add_filter( 'woocommerce_short_description', 'wpg_text_filter' );
		} 
	}
	
	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function on_admin_scripts() {

		wp_register_script(
			'wpglobus.wc',
			#plugins_url( '/includes/js/wpglobus.wc.js', __FILE__ ), in separate plugin
			plugins_url( '/js/wpglobus.wc.js', __FILE__ ),
			array( 'jquery' ),
			WPGLOBUS_WC_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus.wc' );
		wp_localize_script(
			'wpglobus.wc',
			'WPGlobusWC',
			array(
				'version' => WPGLOBUS_WC_VERSION,
				#'vendor' => $this->vendors_scripts
			)
		);
		
	}
	
	/**
	 * Make translatable product at WC pages
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function on_enable_product($entities) {
		if ( ! $this->enabled_entity('product') ) {
			return $entities;
		}
		foreach( $entities as $key=>$entity ) {
			if ( false !== strpos($entity, 'product') ) {
				unset($entities[$key]);
			}
		}
		return $entities;
	}		
	
	/**
	 * Add mainmenu or submenu
	 *
	 * @since 1.0.0
	 *
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
				array( $this, 'wc_translation_table' )
			);

			//add_action( 'admin_print_styles-' . $this->admin_submenu, array( $this, 'on_admin_styles' ) );
			//add_action( 'admin_print_scripts-' . $this->admin_submenu, array( $this, 'on_admin_scripts' ) );

			//add_action( 'admin_print_styles-' . $this->admin_submenu_cat, array( $this, 'on_admin_cat_styles' ) );
			//add_action( 'admin_print_scripts-' . $this->admin_submenu_cat, array( $this, 'on_admin_cat_scripts' ) );

			//add_action( 'admin_print_styles-' . $this->admin_submenu_control, array( $this, 'on_admin_control_styles' ) );
			
		}

	}

	/**
	 * Check for enabled post_types, taxonomies
	 *
	 * @since 1.0.0	 
	 *
	 * @param $entity String
	 * @return boolean
	 */
	function enabled_entity( $entity = '' ) {
		if ( empty($entity) ) {
			/**
			 * Try get entity from url. Ex. edit-tags.php?taxonomy=product_cat&post_type=product
			 */
			if ( isset($_GET['post_type']) ) {
				$entity = $_GET['post_type'];
			}	
			if ( empty($entity) && isset($_GET['taxonomy']) ) { 
				$entity = $_GET['taxonomy'];
			}
		}	
		if ( in_array($entity, $this->enabled_entities) ) {
			return true;	
		}
		return false;	
	}	
	
	/**
	 *
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wc_translation_table() {
		?>
		<div class="wrap">
			<h2><?php _e('Taxonomy translations', ''); ?></h2>
		</div> <!-- .wrap -->
		<?php
	}
	
}