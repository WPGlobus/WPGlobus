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

	private $admin_submenu = '';
	
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

			if ( ! isset($_GET['devmode']) || 'off' == $_GET['devmode'] ) {			
				
				add_action( 'admin_print_scripts', array(
					$this,
					'on_admin_scripts'
				) );
					
				add_action( 'admin_print_styles', array(
					$this,
					'on_admin_styles'
				) );
			
			}	
			
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

		global $post;
		if ( empty($post) || $post->post_type != 'product' ) {
			return;
		}
		
		wp_register_script(
			'wpglobus.wc',
			#plugins_url( '/includes/js/wpglobus.wc.js', __FILE__ ), in separate plugin
			plugins_url( '/js/wpglobus.wc.js', __FILE__ ),
			array( 'jquery', 'jquery-ui-tabs' ),
			WPGLOBUS_WC_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus.wc' );
		wp_localize_script(
			'wpglobus.wc',
			'WPGlobusWC',
			array(
				'version' => WPGLOBUS_WC_VERSION,
				'excerpt_template' => $this->get_template(),
				'locale_tag_start' => WPGlobus::LOCALE_TAG_START,
				'locale_tag_end'   => WPGlobus::LOCALE_TAG_END
			)
		);
		
	}
	
	/**
	 * Enqueue admin styles
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */	
	function on_admin_styles() {
		
		global $post;
		if ( empty($post) || $post->post_type != 'product' ) {
			return;
		}
	
		wp_register_style(
			'wpglobus.wc.tabs',
			#plugins_url( '/includes/css/wpglobus.wc.tabs.css', __FILE__ ), in separate plugin
			plugins_url( '/css/wpglobus.wc.tabs.css', __FILE__ ),			
			array(),
			WPGLOBUS_WC_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus.wc.tabs' );

	}

	/**
	 * Make translatable product at WC pages
	 * @since 1.0.0
	 *
	 * @param string[] $entities
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
	 *
	 * @return void
	 */
	public function wc_translation_table() {
		?>
		<div class="wrap">
			<h2><?php _e('Taxonomy translations', ''); ?></h2>
		</div> <!-- .wrap -->
		<?php
	}
	
	/**
	 * Get template
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function get_template() {
	
		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;
		
		global $post;	
	
		$settings = array(
			'textarea_name' => 'excerpt',
			'quicktags'     => array( 'buttons' => 'em,strong,link' ),
			'tinymce'       => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			),
			'editor_css'    => '<style>#wpglobus-wc-excerpt-tabs .wp-editor-area{height:175px; width:100%;}</style>'
		);
		//$settings = apply_filters( 'woocommerce_product_short_description_editor_settings', $settings );
		
		$excerpt = htmlspecialchars_decode( $post->post_excerpt );
		
		ob_start(); ?>
		<div id="wpglobus-wc-excerpt-tabs">
			<ul>	<?php
				foreach ( $WPGlobus_Config->enabled_languages as $language ) { ?>
					<li id="wpglobus-excerpt-tab-<?php echo $language; ?>"><a href="#excerpt-tab-<?php echo $language; ?>"><?php echo $WPGlobus_Config->en_language_name[$language]; ?></a></li> <?php
				} ?>
			</ul>	<?php
			
			foreach ( $WPGlobus_Config->enabled_languages as $language ) { 
				$settings['textarea_name'] = 'excerpt' . "-$language"; ?>
				<div id="excerpt-tab-<?php echo $language; ?>" class="">
					<?php 
						wp_editor( __wpg_text_filter($excerpt, $language), 'excerpt-' . $language, $settings );
					?>
				</div>
				<?php
			} ?>
		</div> <!--  #wpglobus-wc-excerpt-tabs"	 --> <?php
		
		return ob_get_clean();
	
	}	
	
}