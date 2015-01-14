<?php
/**
 * @package   WPGlobus
 * @copyright Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 */

/**
 * Class WPGlobus
 */
class WPGlobus {

	/**
	 * @var string
	 */
	public static $_version = '0.1.0';

	/**
	 * @var string
	 */
	public static $minimalReduxFramework_version = '3.2.9.4';

	/**
	 * Options page slug needed to get access to settings page
	 */
	const OPTIONS_PAGE_SLUG = 'wpglobus_options';

	/**
	 * Language edit page
	 */
	const LANGUAGE_EDIT_PAGE = 'wpglobus_language_edit';

	/**
	 * List navigation menus
	 * @var array
	 */
	public $menus = array();

	/**
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_PATH = '';

	/**
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_URL = '';

	/**
	 * Are we using our version of Redux or someone else's?
	 * @var string
	 */
	public $redux_framework_origin = 'external';
	
	/**
	 * Support third party plugin vendors
	 */
	public $vendors_scripts = array();

	const RETURN_IN_DEFAULT_LANGUAGE = 'in_default_language';
	const RETURN_EMPTY = 'empty';
	
	const TAG_REGEXP = '/(\{:|\[:|<!--:)[a-z]{2}/';

	/**
	 * Don't make some updates at post screen and don't load scripts for this post types 
	 */
	public $disabled_post_types = array();
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		global $WPGlobus_Config, $WPGlobus_Options;

		global $pagenow;
		
		$this->disabled_post_types[] = 'attachment';
		
		if ( defined( 'WPSEO_VERSION' ) ) {
			$this->vendors_scripts['WPSEO'] = true;
		}
		
		add_filter( 'wp_redirect', array(
			$this,
			'on_wp_redirect'
		));

		
		/**
		 * NOTE: do not check for !DOING_AJAX here. Redux uses AJAX, for example, for disabling tracking.
		 * So, we need to load Redux on AJAX requests, too
		 */
		if ( is_admin() ) {

			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array( $this, 'on_process_ajax' ) );		
		
			if ( ! class_exists( 'ReduxFramework' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once self::$PLUGIN_DIR_PATH . 'vendor/ReduxCore/framework.php';

				/** Set a flag to know that we are using the embedded Redux */
				$this->redux_framework_origin = 'embedded';
			}

			require_once 'options/class-wpglobus-options.php';
			$WPGlobus_Options = new WPGlobus_Options();

			if ( 'edit-tags.php' == $pagenow ) {
				/**
				 * Need to get taxonomy for using correct filter
				 */
				if ( !empty($_GET['taxonomy']) ) {
					
					add_action( "{$_GET['taxonomy']}_pre_edit_form", array(
						$this,
						'on_add_language_tabs_edit_taxonomy'
					), 10, 2 );	
					
					add_action( "{$_GET['taxonomy']}_edit_form", array(
						$this,
						'on_add_taxonomy_form_wrapper'
					), 10, 2 );		
					
				}
			}

			if ( ! isset($_GET['devmode']) || 'off' == $_GET['devmode'] ) {

				/**
				 * Join post content and post title for enabled languages in func wp_insert_post
				 *
				 * @see action in wp-includes\post.php:3326
				 */
				add_action( 'wp_insert_post_data' , array(
					$this,
					'on_save_post_data'
				), 10, 2 );
				
				add_action( 'edit_form_after_editor', array(
					$this,
					'on_add_wp_editors'
				), 10 );
				
				add_action( 'edit_form_after_editor', array(
					$this,
					'on_add_language_tabs'
				));
				
				add_action( 'edit_form_after_title', array(
					$this,
					'on_add_title_fields' 
				));		

				add_action( 'admin_print_scripts', array(
					$this,
					'on_admin_scripts'
				) );
			
			}	// endif $devmode 

			add_action( 'admin_print_styles', array(
				$this,
				'on_admin_styles'
			) );			

			add_filter( "redux/{$WPGlobus_Config->option}/field/class/table", array(
				$this,
				'on_field_table'
			) );

			add_action( 'admin_menu', array(
				$this,
				'on_admin_menu'
			), 10 );

			add_action('post_submitbox_misc_actions', array(
				$this,
				'on_add_devmode_switcher'
			) );
			
		}
		else {
			$WPGlobus_Config->url_info = WPGlobus_Utils::extract_url(
													   $_SERVER['REQUEST_URI'],
														   $_SERVER['HTTP_HOST'],
														   isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ''
			);

			$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];

			$this->menus = $this->_get_nav_menus();

			/** @todo */
			0 && add_filter( 'wp_list_pages', array(
				$this,
				'on_wp_list_pages'
			), 99, 2 );

			/** @todo */
			1 && add_filter( 'wp_page_menu', array(
				$this,
				'on_wp_page_menu'
			), 99, 2 );

			/**
			 * Add language switcher to navigation menu
			 * @see on_add_item
			 */
			1 && add_filter( 'wp_nav_menu_objects', array(
				$this,
				'on_add_item'
			), 99, 2 );
			
			/**
			 * Convert url for menu items
			 */
			1 && add_filter( 'wp_nav_menu_objects', array(
				$this,
				'on_get_convert_url_menu_items'
			), 10, 2 );			

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
	 * Handle ajax process
 	 */
	public function on_process_ajax() {
		$order = $_POST['order'];
		
		$result = '';
		switch( $order['action'] ) :
		case 'get_titles':
			global $WPGlobus_Config;
			$result = array();
			foreach( $order['title'] as $id=>$title ) {
				$result[$id]['source'] = $title['source']; 
				foreach ( $WPGlobus_Config->enabled_languages as $language ) {
					$result[$id][$language] = __wpg_text_filter($title['source'], $language, WPGlobus::RETURN_EMPTY);
				}
			}
			break;
		endswitch;

		echo json_encode($result);
		die();	
	}
	
	/**
	 * Ugly hack.
	 * @see wp_page_menu
	 * @param string $html
	 * @return string
	 */
	public function on_wp_page_menu( $html ) {
		$switcher_html = $this->on_wp_list_pages( '' );
		$html          = str_replace( '</ul></div>', $switcher_html . '</ul></div>', $html );
		return $html;
	}

	/**
	 * Start WPGlobus on "init" hook, so if there is another ReduxFramework, it will be loaded first. Hopefully :-)
	 * Note: "init" hook is not guaranteed to stay in the future versions.
	 */
	public static function init() {
		/** @global WPGlobus WPGlobus */
		global $WPGlobus;
		$WPGlobus = new self;
	}

	/**
	 * WP redirect hook
	 */
	function on_wp_redirect($location) {
		if ( is_admin() ) { 
			if ( isset($_POST['_wp_http_referer']) && false !== strpos($_POST['_wp_http_referer'], 'devmode=on') ) {
				$location .= '&devmode=on';
			}
		}
		return $location;
	}	
		
	/**
	 * Add switcher to publish metabox
	 */
	function on_add_devmode_switcher() {
		global $post;
		$mode = 'on';
		if ( isset($_GET['devmode']) && 'on' == $_GET['devmode'] ) {
			$mode = 'off';
		}
		?>
		<div class="misc-pub-section wpglobus-switch">
			<span id="wpglobus-raw">&nbsp;&nbsp;Dev mode </span>
			<a href="post.php?post=<?php echo $post->ID; ?>&action=edit&devmode=<?php echo $mode; ?>"><?php echo $mode; ?></a>
		</div>	
		<?php	
	}
	
	/**
	 * Enqueue admin scripts
	 * @return void
	 */
	function on_admin_scripts() {
	
		/**
		 * Dequeue autosave for prevent alert from wp.autosave.server.postChanged() after run post_edit in wpglobus.admin.js
		 *
		 * @see wp-includes\js\autosave.js
		 */
		wp_dequeue_script('autosave');
		
		/** @global $pagenow */
		global $pagenow;

		/** @global $post */
		global $post;
		
		if ( is_object($post) && 'WP_Post' == get_class($post) ) {
			if ( in_array($post->post_type, $this->disabled_post_types) ) {
				return;	
			}
		}		
		
		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;
	
		/**
		 * Set array of enabled pages for loading js
		 */
		$enabled_pages = array();
		$enabled_pages[] = self::LANGUAGE_EDIT_PAGE;
		$enabled_pages[] = self::OPTIONS_PAGE_SLUG;
		$enabled_pages[] = 'post.php';
		$enabled_pages[] = 'post-new.php';
		$enabled_pages[] = 'nav-menus.php';
		$enabled_pages[] = 'edit-tags.php';
		$enabled_pages[] = 'edit.php';
		
		/**
		 * Init $post_content 
		 */
		$post_content = '';
		
		/**
		 * Init $post_title
		 */
		$post_title = ''; 
		
		/**
		 * Init $post_title
		 */
		$post_excerpt = ''; 		
		
		/**
		 * Init array data depending on the context for localize script
		 */
		$data = array(
			'default_language' => $WPGlobus_Config->default_language,
			'language' => $WPGlobus_Config->language,
			'enabled_languages' => $WPGlobus_Config->enabled_languages,
			'en_language_name' => $WPGlobus_Config->en_language_name,
			'locale_tag_start' => self::LOCALE_TAG_START,
			'locale_tag_end' => self::LOCALE_TAG_END	
		);
		
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
		
		if ( '' == $page ) {
			/**
			 * Now get $pagenow
			 */
			$page = isset( $pagenow ) ? $pagenow : '';
			
			if ( 'post.php' == $page || 'post-new.php' == $page ) {

				$page_action = 'post-edit';			
				
				/**
				 * We use $post_content, $post_title at edit post page 
				 */			
			
				/**
				 * Set $post_content for default language
				 * because we have text with all languages and delimiters in $post->post_content
				 * next we send $post_content to js with localize script 
				 * @see post_edit() in admin.globus.js 
				 */
				$post_content = __wpg_text_filter($post->post_content, $WPGlobus_Config->default_language, WPGlobus::RETURN_EMPTY); 

				/**
				 * Set $post_title for default language
				 */	
				$post_title = __wpg_text_filter($post->post_title, $WPGlobus_Config->default_language, WPGlobus::RETURN_EMPTY);
				
			}
			
		}
		
		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( self::LANGUAGE_EDIT_PAGE === $page ) {

			/**
			 * Using the same 'select2-js' ID as Redux Plugin does, to avoid duplicate enqueueing
			 * @todo Check if we should do it only if redux origin is 'embedded'
			 */
			wp_register_script(
				'select2-js',
				self::$PLUGIN_DIR_URL . "vendor/ReduxCore/assets/js/vendor/select2/select2$suffix.js",
				array( 'jquery' ),
				self::$_version,
				true
			);
			wp_enqueue_script( 'select2-js' );

		}

		if ( in_array($page, $enabled_pages) ) {

			/**
			 * Init $tabs_suffix
			 */
			$tabs_suffix = array();
			
			if ( in_array($page, array('post.php', 'post-new.php', 'edit-tags.php')) ) {				
				/**
				 * Enqueue jQueryUI tabs
				 */
				wp_enqueue_script( 'jquery-ui-tabs' );
		
				/**
				 * Make suffixes for tabs
				 */
				foreach ( $WPGlobus_Config->enabled_languages as $language ) {
					if ( $language == $WPGlobus_Config->default_language ) {
						$tabs_suffix[] = 'default';
					} else {
						$tabs_suffix[] = $language;
					}
				}
				
			}	
			$i18n = array();
			$i18n['cannot_disable_language'] = __( 'You cannot disable first enabled language.', 'wpglobus' );

			if ( 'post.php' == $page ) {
			
				$data['template'] = '';
				foreach( $WPGlobus_Config->enabled_languages as $language ) {
					$data['template'] .= '<textarea data-language="' . $language . '" placeholder="' . $WPGlobus_Config->en_language_name[$language] .'" class="wpglobus-excerpt" rows="1" cols="40" name="excerpt-' . $language . '" id="excerpt-' . $language . '">';
					$data['template'] .= __wpg_text_filter($post->post_excerpt, $language, WPGlobus::RETURN_EMPTY);
					$data['template'] .= '</textarea>';
				}
				
			} else if ( 'nav-menus.php' == $page ) {
				
				$page_action = 'menu-edit';
				$menu_items  = array();
 			
				global $wpdb;
				$items = $wpdb->get_results( "SELECT ID, post_title, post_excerpt, post_name FROM {$wpdb->prefix}posts WHERE post_type = 'nav_menu_item'", OBJECT );
			
				foreach( $items as $item ) {

					if ( ! WPGlobus_Utils::has_translations($item->post_title) ) :
						/**
						 * Check for menu item has post type page
						 * autocomplete Navigation Label input field
						 */
						$page = $wpdb->get_row( "SELECT ID, post_title, post_name, post_type FROM {$wpdb->prefix}posts WHERE post_type = 'page' AND post_name='{$item->post_name}'" );

						if ( !empty($page)) {
							$new_title = trim($page->post_title);
							if ( !empty($new_title) ) {
								$item->post_title = $new_title;
								/**
								 * Update translation of title for menu item
								 */
								$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_title = '%s' WHERE ID = %d", $new_title, $item->ID) );		
							}							
						}

					endif;				
				
					$menu_items[$item->ID]['item-title'] = __wpg_text_filter( $item->post_title, $WPGlobus_Config->default_language );
					
					foreach( $WPGlobus_Config->enabled_languages as $language ) {
						
						$menu_items[$item->ID][$language]['input.edit-menu-item-title']['caption']   = __wpg_text_filter( $item->post_title, $language, WPGlobus::RETURN_EMPTY );
						$menu_items[$item->ID][$language]['input.edit-menu-item-attr-title']['caption'] = __wpg_text_filter( $item->post_excerpt, $language, WPGlobus::RETURN_EMPTY ); 

						$menu_items[$item->ID][$language]['input.edit-menu-item-title']['class']   = 'widefat wpglobus-menu-item wpglobus-item-title';
						$menu_items[$item->ID][$language]['input.edit-menu-item-attr-title']['class'] = 'widefat wpglobus-menu-item wpglobus-item-attr'; 
					}
				}
				
				$data['items'] = $menu_items;
				
				$i18n['save_nav_menu'] = __( 'You must save navigation menu before edit.', 'wpglobus' );
			
			} else if ( 'edit-tags.php' == $page ) {
				
				global $tag;
				
				$data['tag_id']    = empty($_GET['tag_ID']) ? false : $_GET['tag_ID'];
				$data['has_items'] = true;
				
				if ( $data['tag_id'] ) {
					/**
					 * For example url: edit-tags.php?action=edit&taxonomy=category&tag_ID=4&post_type=post
					 */
					$page_action = 'taxonomy-edit';
				} else {
					/**
					 * For example url: edit-tags.php?taxonomy=category
					 * edit-tags.php?taxonomy=product_cat&post_type=product
					 */		
					if ( isset($_GET['taxonomy']) && empty( get_terms($_GET['taxonomy'], array('hide_empty'=>false)) ) ) {
						$data['has_items'] = false;
					}
					$page_action = 'taxonomy-quick-edit';
				}
				
				if ( $data['tag_id'] ) {
					foreach( $WPGlobus_Config->enabled_languages as $language ) {
						$lang = $language == $WPGlobus_Config->default_language ? 'default' : $language;		
						$data['i18n'][$lang]['name'] = __wpg_text_filter($tag->name, $language, WPGlobus::RETURN_EMPTY ); 
						$data['i18n'][$lang]['description'] = __wpg_text_filter($tag->description, $language, WPGlobus::RETURN_EMPTY );
					}
				} else {
					$data['template'] = $this->_get_quickedit_template(); 
				}				
				
			} else if ( 'edit.php' == $page ) {
				$page_action = 'edit.php';
				
				global $posts;
				$data['has_items'] = empty($posts) ? false : true;
				$data['template'] = $this->_get_quickedit_template();

			}	
			
			if ( ! empty($this->vendors_scripts) ) {
				wp_register_script(
					'wpglobus.vendor',
					self::$PLUGIN_DIR_URL . 'includes/js/wpglobus.vendor.js',
					array( 'jquery' ),
					self::$_version,
					true
				);
				wp_enqueue_script( 'wpglobus.vendor' );
				wp_localize_script(
					'wpglobus.vendor',
					'aaWPGlobusVendor',
					array(
						'version' => self::$_version,
						'vendor' => $this->vendors_scripts
					)
				);
			}			
			
			wp_register_script(
				'wpglobus.admin',
				self::$PLUGIN_DIR_URL . 'includes/js/wpglobus.admin.js',
				array( 'jquery' ),
				self::$_version,
				true
			);
			wp_enqueue_script( 'wpglobus.admin' );
			wp_localize_script(
				'wpglobus.admin',
				'aaAdminGlobus',
				array(
					'version'      => self::$_version,
					'page'		   => $page_action,
					'content'	   => $post_content,
					'title'	   	   => $post_title,
					'excerpt'	   => $post_excerpt,
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'parentClass'  => __CLASS__,
					'process_ajax' => __CLASS__ . '_process_ajax',
					'flag_url'     => $WPGlobus_Config->flags_url,
					'tabs'		   => $tabs_suffix,
					'i18n'         => $i18n,
					'data'		   => $data
				)
			);
			
		}
		wp_enqueue_script('autosave');
	}

	/**
	 * Get template for quick edit at edit-tags.php, edit.php screens
	 *
	 * @return string
	 */
	function _get_quickedit_template() {
		global $WPGlobus_Config;
		$t = '';
		foreach( $WPGlobus_Config->enabled_languages as $language ) {
			$t .= '<label>';
			$t .= '<span class="input-text-wrap">';
			$t .= '<input data-lang="' . $language. '" style="width:100%;" class="ptitle wpglobus-quick-edit-title" type="text" value="" name="post_title-' . $language . '" placeholder="' . $WPGlobus_Config->en_language_name[$language] .'">';
			$t .= '</span>';
			$t .= '</label>';
		}
		return $t;	
	}
	
	/**
	 * Enqueue admin styles
	 * @return void
	 */
	function on_admin_styles() {
		
		/** @global $pagenow */
		global $pagenow;
		
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		wp_register_style(
			'wpglobus.admin',
			self::$PLUGIN_DIR_URL . 'includes/css/wpglobus.admin.css',
			array(),
			self::$_version,
			'all'
		);
		wp_enqueue_style( 'wpglobus.admin' );
		
		if ( self::LANGUAGE_EDIT_PAGE === $page ) {
			wp_register_style(
				'select2-css',
				self::$PLUGIN_DIR_URL . 'vendor/ReduxCore/assets/js/vendor/select2/select2.css',
				array(),
				self::$_version,
				'all'
			);
			wp_enqueue_style( 'select2-css' );
		}

		if ( in_array($pagenow, array('post.php', 'post-new.php', 'edit-tags.php')) ) {
			wp_register_style(
				'wpglobus.admin.tabs',
				self::$PLUGIN_DIR_URL . 'includes/css/wpglobus.admin.tabs.css',
				array(),
				self::$_version,
				'all'
			);
			wp_enqueue_style( 'wpglobus.admin.tabs' );
			
		}
		


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
		require_once 'admin/class-wpglobus-language-edit.php';
		new WPGlobus_Language_Edit();
	}

	/**
	 * We must convert url for nav_menu_item with type == custom
	 * For other types url has language shortcode already
	 *
	 * @param $sorted_menu_items
	 * @internal param $args
	 * @return array
	 */
	function on_get_convert_url_menu_items( $sorted_menu_items ) {

		foreach( $sorted_menu_items as $key=>$item ) {
			if ( 'custom' == $item->type ) {
				$sorted_menu_items[$key]->url = WPGlobus_Utils::get_convert_url($sorted_menu_items[$key]->url);
			}
		}

		return $sorted_menu_items;

	}

	/**
	 * Include file for new field 'table'
	 * @return string
	 */
	function on_field_table() {
		return dirname( __FILE__ ) . '/options/fields/table/field_table.php';
	}

	/**
	 * Enqueue styles
	 * @return void
	 */
	function on_wp_styles() {
		wp_register_style(
			'flags',
			self::$PLUGIN_DIR_URL . 'includes/css/wpglobus.flags.css',
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
			$css .= '.wpglobus_flag_' . $language .
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

		$span_classes = array(
			'wpglobus_flag',
			'wpglobus_language_name'
		);

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'wpglobus_flag_' . $WPGlobus_Config->language;

		$output .= '<li class="page_item page_item_wpglobus_menu_switch page_item_has_children">
						<a href="' . WPGlobus_Utils::get_url( $WPGlobus_Config->language ) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $WPGlobus_Config->language ) . '</span></a>
						<ul class="children">';
		foreach ( $extra_languages as $language ) {
			$span_classes_lang   = $span_classes;
			$span_classes_lang[] = 'wpglobus_flag_' . $language;
			$output .= '<li class="page_item">
								<a href="' . WPGlobus_Utils::get_url( $language ) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span></a>
							</li>';
		} // end foreach
		$output .= '	</ul>
					</li>';

		return $output;
	}

	/**
	 * Add language switcher to navigation menu
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
			'menu_item_wpglobus_menu_switch'
		);

		/** submenu item classes */
		$submenu_item_classes = array(
			'',
			'sub_menu_item_wpglobus_menu_switch'
		);

		$span_classes = array(
			'wpglobus_flag',
			'wpglobus_language_name'
		);

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'wpglobus_flag_' . $WPGlobus_Config->language;

		$item                   = new stdClass();
		$item->ID               = 9999999999; # 9 999 999 999
		$item->db_id            = 9999999999;
		$item->menu_item_parent = 0;
		$item->title            =
			'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $WPGlobus_Config->language ) . '</span>';
		$item->url              = WPGlobus_Utils::get_url( $WPGlobus_Config->language );
		$item->classes          = $menu_item_classes;
		$item->description      = '';
		
		$sorted_menu_items[] = $item;

		foreach ( $extra_languages as $language ) {
			$span_classes_lang   = $span_classes;
			$span_classes_lang[] = 'wpglobus_flag_' . $language;

			$item                   = new stdClass();
			$item->ID               = 'wpglobus_menu_switch_' . $language;
			$item->db_id            = 'wpglobus_menu_switch_' . $language;
			$item->menu_item_parent = 9999999999;
			$item->title            =
				'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span>';
			$item->url              = WPGlobus_Utils::get_url( $language );
			$item->classes          = $submenu_item_classes;
			$item->description      = '';

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

	/**
	 * Added wp_editor for enabled languages at post.php page
	 *
	 * @see action edit_form_after_editor in wp-admin\edit-form-advanced.php:542
	 * @param WP_Post $post
	 * @return void
	 */
	function on_add_wp_editors($post) {

		if ( ! post_type_supports($post->post_type, 'editor') ) {
			return;
		}

		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;

		foreach( $WPGlobus_Config->enabled_languages as $language ) :
			if ( $language == $WPGlobus_Config->default_language ) {

				continue;

			} else {	?>

				<div id="postdivrich-<?php echo $language; ?>" class="postarea postdivrich-wpglobus">	<?php
					wp_editor( __wpg_text_filter($post->post_content, $language, WPGlobus::RETURN_EMPTY), 'content-' . $language, array(
						'_content_editor_dfw' => true,
						#'dfw' => true,
						'drag_drop_upload' => true,
						'tabfocus_elements' => 'insert-media-button,save-post',
						'editor_height' => 300,
						'tinymce' => array(
							'resize' => true,
							'wp_autoresize_on' => true,
							'add_unload_trigger' => false,
						),
					) ); ?>
				</div> <?php

			}
		endforeach;
	}

	const LOCALE_TAG = '{:%s}%s{:}';
	const LOCALE_TAG_START = '{:%s}';
	const LOCALE_TAG_END = '{:}';

	/**
	 * Surround text with language tags
	 * @param string $text
	 * @param string $language
	 *
	 * @return string
	 */
	public static function tag_text( $text, $language ) {
		return sprintf( WPGlobus::LOCALE_TAG, $language, $text );
	}

	/**
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	function on_save_post_data($data, $postarr) {
		
		if ( 'revision' == $postarr['post_type'] ) {
			
			/**
			 * Don't working with revision
			 * note: revision there are 2 types, its have some differences
			 * 		- [post_name] => {post_id}-autosave-v1	and [post_name] => {post_id}-revision-v1
			 * 		- when [post_name] == {post_id}-autosave-v1  $postarr has [post_content] and [post_title] in default_language
			 * 		- [post_name] == {post_id}-revision-v1 $postarr has [post_content] and [post_title] in all enabled languages with delimiters
			 * 
			 * see $postarr for more info	
			 */
			return $data;
			
		}

		global $pagenow;

		/**
		 * Now we save post content and post title for all enabled languages for post.php, post-new.php
		 *
		 * @todo Let's don't forget about other pages, like 'admin-ajax.php', 'nav-menus.php' and more
		 */
		$enabled_pages[] = 'post.php';
		$enabled_pages[] = 'post-new.php';

		if ( ! in_array($pagenow, $enabled_pages) ) {
			return $data;
		}

		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;		
		
		$devmode = true;	
		foreach( $WPGlobus_Config->enabled_languages as $language ) {
			if ( $language != $WPGlobus_Config->default_language ) {
				if ( isset($postarr['content-' . $language]) ) {
					$devmode = false;	
					break;
				}
			}	
		}

		$data['post_content'] = trim($data['post_content']);
		if ( !empty($data['post_content']) ) {
			if ( ! $devmode ) {
				$data['post_content'] = WPGlobus::tag_text( $data['post_content'], $WPGlobus_Config->default_language );
			}	
		}

		$data['post_title'] = trim($data['post_title']);
		if ( !empty($data['post_title']) ) {
			if ( ! $devmode ) {
				$data['post_title'] = WPGlobus::tag_text( $data['post_title'], $WPGlobus_Config->default_language );
			}	
		}


		foreach( $WPGlobus_Config->enabled_languages as $language ) :
			if ( $language == $WPGlobus_Config->default_language ) {

				continue;

			} else {

				/**
				 * Join post content for enabled languages
				 */
				$content = isset($postarr['content-' . $language]) ? trim($postarr['content-' . $language]) : '';
				if ( !empty($content) ) {
					$data['post_content'] .= WPGlobus::tag_text( $postarr['content-' . $language], $language );
				}

				/**
				 * Join post title for enabled languages
				 */
				$title = isset($postarr['post_title_' . $language]) ? trim($postarr['post_title_' . $language]) : '';
				if ( !empty($title) ) {
					$data['post_title'] .= WPGlobus::tag_text( $postarr['post_title_' . $language], $language );
				}

			}
		endforeach;

		return $data;

	}
	
	/**
	 *
	 */
	function on_add_taxonomy_form_wrapper() {
		
		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;		
		
		foreach ( $WPGlobus_Config->enabled_languages as $language ) {
			$tab_suffix = $language == $WPGlobus_Config->default_language ? 'default' : $language; ?>
			<div id="tab-<?php echo $tab_suffix; ?>">
			</div>	
			<?php
		}
		
	}
	
	/**
	 *
	 */
	function on_add_language_tabs_edit_taxonomy() {
		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;	?>

		<ul class="wpglobus-post-tabs-ul">	<?php
			foreach ( $WPGlobus_Config->enabled_languages as $language ) {
				$tab_suffix = $language == $WPGlobus_Config->default_language ? 'default' : $language; ?>
				<li id="link-tab-<?php echo $tab_suffix; ?>"><a href="#tab-<?php echo $tab_suffix; ?>"><?php echo $WPGlobus_Config->en_language_name[$language]; ?></a></li> <?php
			} ?>
		</ul>	<?php		
	}
	
	/**
	 * Add language tabs for jQueryUI
	 */
	function on_add_language_tabs() {
		
		global $post;
		
		if ( in_array($post->post_type, $this->disabled_post_types) ) {
			return;	
		}
		
		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;	?>

		<ul class="wpglobus-post-tabs-ul">	<?php
			foreach ( $WPGlobus_Config->enabled_languages as $language ) {
				$tab_suffix = $language == $WPGlobus_Config->default_language ? 'default' : $language; ?>
				<li id="link-tab-<?php echo $tab_suffix; ?>"><a href="#tab-<?php echo $tab_suffix; ?>"><?php echo $WPGlobus_Config->en_language_name[$language]; ?></a></li> <?php
			} ?>
		</ul>	<?php

	}

	/**
	 * Add title fields for enabled languages at post.php, post-new.php page
	 *
	 * @param $post
	 * @return void
	 */
	function on_add_title_fields( $post ) {
		
		if ( in_array($post->post_type, $this->disabled_post_types) ) {
			return;	
		}		
		
		if ( ! post_type_supports($post->post_type, 'title') ) {
			return;
		}

		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;

		foreach( $WPGlobus_Config->enabled_languages as $language ) :
		
			if ( $language == $WPGlobus_Config->default_language ) { 
				
				continue; 
			
			} else {	?>	
			
				<div id="titlediv-<?php echo $language;?>" class="titlediv-wpglobus">
					<div id="titlewrap-<?php echo $language;?>" class="titlewrap-wpglobus">
						<label class="screen-reader-text" id="title-prompt-text-<?php echo $language; ?>" for="title_<?php echo $language; ?>"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
						<input type="text" name="post_title_<?php echo $language; ?>" size="30" value="<?php echo esc_attr( htmlspecialchars( __wpg_text_filter($post->post_title, $language, WPGlobus::RETURN_EMPTY) ) ); ?>" id="title_<?php echo $language;?>" class="title_wpglobus" autocomplete="off" />
					</div> <!-- #titlewrap -->
					<div class="inside">
						<div id="edit-slug-box-<?php echo $language; ?>" class="hide-if-no-js">
							<b>Slug will be here</b>
						</div>
					</div> <!-- .inside -->
				</div>	<!-- #titlediv -->	<?php					

			}
			
		endforeach;
	}	
}

# --- EOF