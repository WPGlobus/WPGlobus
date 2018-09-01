<?php
/**
 * File: class-wpglobus-gutenberg.php
 *
 * @package WPGlobus\Builders\Gutenberg
 * @author Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Gutenberg.
 */
class WPGlobus_Gutenberg extends WPGlobus_Builder {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		
		parent::__construct('gutenberg');

		if ( is_admin() ) {
			
			/**
			 * Filter the post for Gutenberg editor.
			 * @see wp-includes\class-wp-query.php
			 */
			add_action( 'the_post', array( $this, 'translate_post' ), 5 );
			
			/**
			 * Add 'wpglobus-language' hidden field.
			 */
			add_action( 'add_meta_boxes', array( $this, 'on__add_meta_box' ) );

			add_action( 'admin_enqueue_scripts', array(
				$this,
				'on__enqueue_scripts'
			), 1000 );	
			
			/**
			 * @see wpglobus-seo\includes\class-wpglobus-seo.php
			 */
			add_filter( 'wpglobus_seo_meta_box_title', array($this, 'filter__seo_meta_box_title') );

		}

	}
	
	/**
	 * Translate post.
	 */
	public function translate_post($object) {
		if ( $object instanceof WP_Post ) {
			$object = WPGlobus_Core::translate_wp_post($object, $this->language, WPGlobus::RETURN_EMPTY);
		}
	}
	
	/**
	 * Path to languages folder.
	 *
	 * @return string
	 */
	public function languages_path() {
		return self::$PLUGIN_DIR_PATH . 'languages';
	}
	
	/**
	 * Generate box with language switcher.
	 * 
	 * @since 2.0
	 * @return string
	 */
	private function get_switcher_box($page) {
		
		$query_string = explode('&', $_SERVER['QUERY_STRING']);
		
		foreach( $query_string as $key=>$_q ) {
			if ( false !== strpos($_q, 'language=') ) {
				unset( $query_string[$key] );
			}
		}
		$query = implode('&', $query_string );
		$url = admin_url( 
			add_query_arg( array(
					'language' => '{{language}}'
				),
				'post.php?'.$query 
			)
		);

		$_box_style = 'position:absolute;top:15px;left:10px;z-index:100;';
		if ( file_exists( WPGlobus::Config()->flag_path['big'].WPGlobus::Config()->flag[$this->language] ) ) {
			$_flag_img = WPGlobus::Config()->flag_urls['big'].WPGlobus::Config()->flag[$this->language];
			$_height = 'height="25px"';
			$_width  = 'width="25px"';
			$_flag_style  = 'style="border: 1px solid #bfbfbf;border-radius: 25px;"';
		} else {
			$_flag_img = WPGlobus::Config()->flags_url.WPGlobus::Config()->flag[$this->language];
			$_height = '';
			$_width = '';
			$_flag_style = 'style="margin-top:5px;"';
			$_box_style .= 'margin-top:3px;';
		}
		
		if ( 'post-new.php' == $page ) {
		
			ob_start();
			?>
			<div style="<?php echo $_box_style; ?>" class="wpglobus-gutenberg-selector-box">
				<div style="display:grid;grid-template-columns:50% 50%;place-items:center;grid-gap:0;">
					<a style="text-decoration:none;cursor:text;" onclick="return false; 
						href="#" style="" class="wpglobus-gutenberg-selector" data-language="<?php echo $this->language; ?>">
						<img <?php echo $_height . $_width; ?>  
							<?php echo $_flag_style; ?>  
							src="<?php echo $_flag_img ?>" />
					</a>
					<a style="text-decoration:none;cursor:text;" onclick="return false;"
						href="#" style="" class="wpglobus-gutenberg-selector" data-language="<?php echo $this->language; ?>">
					&nbsp;<span><?php echo WPGlobus::Config()->en_language_name[$this->language]; ?></span>
					</a>
				</div>
				<ul class="wpglobus-gutenberg-selector-dropdown" style="display:none;position:fixed;width:300px;border-left:1px solid #ddd;border-right:1px solid #ddd;background-color:#eee;margin:5px 0 0;list-style-type:none;">
					<li class="item" style="text-align:left;border-bottom:1px solid #ddd;margin:0 auto;height:25px;padding:3px 0 0 10px;">
						<?php _e('Save draft or publish post and reload page.', 'wpglobus'); ?>
					</li>
				</ul>
			</div>			
			<?php
			$out = ob_get_contents();
			ob_end_clean();			
			
		} else if ( 'post.php' == $page ) {

			ob_start();
			?>
			<div style="<?php echo $_box_style; ?>" class="wpglobus-gutenberg-selector-box">
				<div style="display:grid;grid-template-columns:50% 50%;place-items:center;grid-gap:0;">
					<a style="text-decoration: none;" 
						href="<?php echo str_replace('{{language}}', $this->language, $url); ?>" style="" class="wpglobus-gutenberg-selector" data-language="<?php echo $this->language; ?>">
						<img <?php echo $_height . $_width; ?>  
							<?php echo $_flag_style; ?>  
							src="<?php echo $_flag_img ?>" />
					</a>
					<a style="text-decoration: none;" 
						href="<?php echo str_replace('{{language}}', $this->language, $url); ?>" style="" class="wpglobus-gutenberg-selector" data-language="<?php echo $this->language; ?>">
					&nbsp;<span><?php echo WPGlobus::Config()->en_language_name[$this->language]; ?></span>
					</a>
				</div>
				<ul class="wpglobus-gutenberg-selector-dropdown" style="display:none;position:fixed;width:80px;border-left:1px solid #ddd;border-right:1px solid #ddd;background-color:#eee;margin:5px 0 0;list-style-type:none;">
					<?php foreach( WPGlobus::Config()->enabled_languages as $lang ) :	?>
						<?php if ( $lang == $this->language ) { continue; }	?>
						<li class="item" style="text-align:left;cursor:pointer;border-bottom:1px solid #ddd;margin:0;height:25px;padding:5px 0 5px 5px;" data-language="<?php echo $lang; ?>">
							<a href="<?php echo str_replace('{{language}}', $lang, $url); ?>">
								<img src="<?php echo WPGlobus::Config()->flags_url.WPGlobus::Config()->flag[$lang]; ?>" />&nbsp;<?php echo WPGlobus::Config()->en_language_name[$lang]; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>			
			<?php
			$out = ob_get_contents();
			ob_end_clean();
			
		}
		
		return $out;

	}

	/**
	 * Callback for 'wpglobus_seo_meta_box_title'.
	 */
	public function filter__seo_meta_box_title($meta_box_title) {
		return $meta_box_title . ' for ' . WPGlobus::Config()->en_language_name[$this->get_current_language()];
	}	

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function on__enqueue_scripts() {
		
		global $pagenow;
		
		if ( ! in_array( $pagenow, array('post.php', 'post-new.php') ) ) {
			return;
		} 		

		if ( isset( $_GET['classic-editor'] ) ) {
			return;
		}
		
		$tabs = $this->get_switcher_box($pagenow);
	
		wp_register_script(
			'wpglobus-gutenberg',
			WPGlobus::plugin_dir_url() . "includes/builders/gutenberg/assets/js/wpglobus-gutenberg" . WPGlobus::SCRIPT_SUFFIX() . ".js",
			array('jquery'),
			WPGLOBUS_VERSION,
			true
		);		
		wp_enqueue_script('wpglobus-gutenberg');
		wp_localize_script(
			'wpglobus-gutenberg',
			'WPGlobusGutenberg',
			array(
				'version' 			=> WPGLOBUS_VERSION,
				'versionGutenberg' 	=> GUTENBERG_VERSION,
				'tabs'  			=> $tabs,
				'language'  		=> $this->language,
				'pagenow'  			=> $pagenow,
				'defaultLanguage' 	=> WPGlobus::Config()->default_language
			)
		);
	}

	/**
	 * Callback for 'add_meta_boxes'.
	 */
	public function on__add_meta_box() {
		add_meta_box( 'wpglobus', __( 'WPGlobus' ), array( $this, 'callback__meta_box' ), 'post.php', 'side', 'core' );
	}
	
	/**
	 * Callback for 'add_meta_box' function.
	 */
	public function callback__meta_box() {
		echo $this->get_language_field();
		do_action('wpglobus_gutenberg_metabox');
	}		
	
}

# --- EOF