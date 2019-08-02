<?php
/**
 * @package   WPGlobus\All-in-One-SEO
 */

/**
 * @see aioseop_mrt_pccolumn() in original plugin
 */
function aioseop_mrt_pccolumn( $aioseopcn, $aioseoppi ) {
	$id     = $aioseoppi;
	$target = null;
	if ( $aioseopcn === 'seotitle' ) {
		$target = 'title';
	}
	if ( $aioseopcn === 'seokeywords' ) {
		$target = 'keywords';
	}
	if ( $aioseopcn === 'seodesc' ) {
		$target = 'description';
	}
	if ( ! $target ) {
		return;
	}
	if ( current_user_can( 'edit_post', $id ) ) { ?>
		<div class="aioseop_mpc_admin_meta_container">
			<div class="aioseop_mpc_admin_meta_options"
					id="aioseop_<?php print esc_attr( $target ); ?>_<?php echo esc_attr( $id ); ?>"
					style="float:left;">
				<?php
				$content = strip_tags( stripslashes( get_post_meta( $id, '_aioseop_' . $target, true ) ) );
				if ( ! empty( $content ) ) :
					$content = WPGlobus_Core::text_filter( $content, WPGlobus::Config()->language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
					$label   = "<label id='aioseop_label_{$target}_{$id}'>" . $content . '</label>';
				else :
					$label = "<label id='aioseop_label_{$target}_{$id}'></label><strong><i>No " . $target . '</i></strong>';
				endif;
				$nonce = wp_create_nonce( "aioseop_meta_{$target}_{$id}" );
				print $label; // WPCS: XSS ok.
				echo '<a id="' . esc_attr( $target ) . 'editlink' . esc_attr( $id )
					 . '" href="javascript:void(0);" onclick=\'aioseop_ajax_edit_meta_form(' .
					 esc_js( $id ) . ', "' . esc_js( $target ) . '", "' . esc_js( $nonce ) . '");return false;\' title="'
					 . esc_attr__( 'Edit' ) . '">';
				print "<img class='aioseop_edit_button' 
					id='aioseop_edit_id' 
					src='" . esc_url( AIOSEOP_PLUGIN_IMAGES_URL ) . "cog_edit.png' /></a>";
				?>
			</div>
		</div>
	<?php
	}
}

/* @noinspection PhpIncludeInspection */
require_once AIOSEOP_PLUGIN_DIR . 'aioseop_class.php';

/**
 * Class WPGlobus_All_in_One_SEO.
 */
if ( ! class_exists('WPGlobus_All_in_One_SEO') ) :

	class WPGlobus_All_in_One_SEO extends All_in_One_SEO_Pack {

		private $wpg_language = '';
		
		/**
		 * @since 2.2.9
		 */ 
		public $version = '';

		public function __construct() {
			
			/**
			 * @since 2.2.9
			 */ 			
			$this->version = AIOSEOP_VERSION;
		}

		/**
		 * Filter for post title.
		 *
		 * @since 1.0.8
		 *
		 * @param string $text
		 *
		 * @return string
		 */
		public static function filter__title( $text ) {

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return $text;
			}

			global $post;

			$title = $text;

			if ( is_singular() ) {

				$title_source = get_post_meta( $post->ID, "_aioseop_title", true );

				if ( empty( $title_source ) ) {
					$default_title = null;
				} else {
					$default_title = WPGlobus_Core::text_filter( $title_source, WPGlobus::Config()->default_language );
				}

				if ( $default_title != null && false !== strpos( mb_strtolower( $text ), mb_strtolower( $default_title ) ) ) {

					/**
					 * Because we have not translation of SEO title for current language need to autogenerate it
					 */
					if ( false === strpos( $text, '|' ) ) {
						$title = $post->post_title;

					} else {

						$title_arr = explode( '|', $text );
						$title     = $post->post_title;
						$title     .= ' |';
						$title     .= WPGlobus_Core::text_filter( $title_arr[1], WPGlobus::Config()->language, null );

					}

				} else {
					$title = $text;
				}

			}

			return $title;

		}

		/**
		 * Filter text.
		 * We need own filter to avoid duplicate keyword from language by default in extra languages.
		 *
		 * @since 1.6.6
		 *
		 * @param string $text
		 *
		 * @return string
		 */
		public static function filter__text( $text ) {

			if ( ! WPGlobus_Core::has_translations( $text ) ) {
				return $text;
			}

			return WPGlobus_Core::text_filter( $text, WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );

		}

		/**
		 * Filter for post description.
		 *
		 * @since 1.0.8
		 *
		 * @param string $text
		 *
		 * @return string
		 */
		public static function filter__description( $text ) {

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return $text;
			}

			global $post;

			$description_source  = get_post_meta( $post->ID, "_aioseop_description", true );
			$default_description = WPGlobus_Core::text_filter( $description_source, WPGlobus::Config()->default_language );

			if ( $default_description == $text ) {

				/**
				 * Because we have not translation of SEO description for current language need to autogenerate it
				 *
				 * @see get_post_description() in original plugin
				 */
				$aio = new All_in_One_SEO_Pack();

				$aioseop_options = get_option( 'aioseop_options' );

				if ( empty( $aioseop_options["aiosp_skip_excerpt"] ) ) {
					if ( method_exists($aio, 'trim_excerpt_without_filters_full_length') ) {
						$description = $aio->trim_excerpt_without_filters_full_length( $post->post_excerpt );
					} else if ( method_exists($aio, 'trim_text_without_filters_full_length') ) {
						/**
						 * @since All In One SEO Pack 2.4.4
						 */
						$description = $aio->trim_text_without_filters_full_length( $post->post_excerpt );
					} else {
						$description = WPGlobus_Core::text_filter( $post->post_excerpt );
					}				
				}
				if ( ! $description && $aioseop_options["aiosp_generate_descriptions"] ) {
					$content = $post->post_content;
					if ( ! empty( $aioseop_options["aiosp_run_shortcodes"] ) ) {
						$content = do_shortcode( $content );
					}
					$content     = wp_strip_all_tags( $content );
					$description = $aio->trim_excerpt_without_filters( $content );
				}

				// "internal whitespace trim"
				$description = preg_replace( "/\s\s+/u", " ", $description );

				$description = WPGlobus_Core::text_filter( $description, WPGlobus::Config()->language );

			} else {
				$description = $text;
			}

			return $description;

		}

		public function wpg_get_option_row( $name, $opts, $args, $language ) {

			$this->wpg_language = $language;

			if ( version_compare( $this->version, '2.99.99', '>' ) ) {
				$r = $this->get_option_row_3( $name, $opts, $args );
			} else {
				$r = $this->get_option_row( $name, $opts, $args );
			}

			return $r;

		}

		/**
		 * Format a row for an option on a settings page.
		 */
		public function get_option_row( $name, $opts, $args ) {

			$language = '_' . $this->wpg_language;

			$label_text = $input_attr = $help_text_2 = $id_attr = '';
			if ( $opts['label'] == 'top' ) {
				$align = 'left';
			} else {
				$align = 'right';
			}
			if ( isset( $opts['id'] ) ) {
				$id_attr .= " id=\"{$opts['id']}_div\" ";
			}
			if ( $opts['label'] != 'none' ) {
				if ( isset( $opts['help_text'] ) ) {
					$help_text   = sprintf( All_in_One_SEO_Pack_Module::DISPLAY_HELP_START, __( 'Click for Help!', 'all_in_one_seo_pack' ), $name . $language, $opts['name'] );
					$help_text_2 = sprintf( All_in_One_SEO_Pack_Module::DISPLAY_HELP_END, $name . $language, $opts['help_text'] );
				} else {
					$help_text = $opts['name'];
				}
				$label_text = sprintf( All_in_One_SEO_Pack_Module::DISPLAY_LABEL_FORMAT, $align, $help_text );
			} else {
				$input_attr .= ' aioseop_no_label ';
			}
			if ( $opts['label'] == 'top' ) {
				$label_text .= All_in_One_SEO_Pack_Module::DISPLAY_TOP_LABEL;
			}
			$input_attr .= " aioseop_{$opts['type']}_type";

			return sprintf( All_in_One_SEO_Pack_Module::DISPLAY_ROW_TEMPLATE, $input_attr, $name . $language, $label_text, $id_attr, $this->get_option_html( $args ), $help_text_2 );
		}

		/**
		 * Format a row for an option on a settings page.
		 *
		 * @since 2.2.9
		 * @see all-in-one-seo-pack\admin\aioseop_module_class.php
		 */
		public function get_option_row_3( $name, $opts, $args ) {

			$language = '_' . $this->wpg_language;

			$label_text = $input_attr = $id_attr = '';

			if ( ! class_exists('AIOSEOP_Helper') ) {
				require_once( AIOSEOP_PLUGIN_DIR . 'admin/class-aioseop-helper.php' );
			}
			$info = new AIOSEOP_Helper( get_class( $this ) );

			$align = 'right';
			if ( $opts['label'] == 'top' ) {
				$align = 'left';
			}
			if ( isset( $opts['id'] ) ) {
				$id_attr .= " id=\"{$opts['id']}_div\" ";
			}
			if ( $opts['label'] != 'none' ) {
				$tmp_help_text = $info->get_help_text( $name );
				if ( isset( $tmp_help_text ) && ! empty( $tmp_help_text ) ) {
					$display_help = '<a class="aioseop_help_text_link" style="cursor: help;" title="%s"></a><label class="aioseop_label textinput">%s</label>';

					$__help_text = $info->get_help_text( $name );
					if ( false !== strpos( $__help_text, 'DEV: Missing Help Text:' ) ) {
						$help_text    = sprintf( $display_help, esc_html( $opts['help_text'] ), $opts['name'] );
					} else {
						$help_text    = sprintf( $display_help, $__help_text, $opts['name'] );
					}
				} else {
					$help_text = $opts['name'];
				}

				// TODO Possible remove text align.
				// Currently aligns to the right when everything is being aligned to the left; which is usually a workaround.
				$display_label_format = '<span class="aioseop_option_label" style="text-align:%s;vertical-align:top;">%s</span>';
				$label_text           = sprintf( $display_label_format, $align, $help_text );
			} else {
				$input_attr .= ' aioseop_no_label ';
			}
			if ( $opts['label'] == 'top' ) {
				$label_text .= "</div><div class='aioseop_input aioseop_top_label'>";
			}
			$input_attr .= " aioseop_{$opts['type']}_type";

			$display_row_template = '<div class="aioseop_wrapper%s" id="%s_wrapper"><div class="aioseop_input">%s<span class="aioseop_option_input"><div class="aioseop_option_div" %s>%s</div></span><p style="clear:left"></p></div></div>';
			return sprintf( $display_row_template, $input_attr, $name . $language, $label_text, $id_attr, $this->get_option_html( $args ) );
		}

		/**
		 * Outputs a setting item for settings pages and metaboxes.
		 */
		function get_option_html( $args ) {
			static $n = 0;
			extract( $args );
			if ( $options['type'] == 'custom' ) {
				return apply_filters( "{$prefix}output_option", '', $args );
			}
			if ( in_array( $options['type'], Array(
					'multiselect',
					'select',
					'multicheckbox',
					'radio',
					'checkbox',
					'textarea',
					'text',
					'submit',
					'hidden',
				) ) && ( is_string( $value ) ) ) {
				$value = esc_attr( $value );
			}
			$buf    = '';
			$onload = '';
			if ( ! empty( $options['count'] ) ) {
				$n ++;
				$attr   .= ''; // " onKeyDown='if (typeof countChars == \"function\") countChars(document.{$this->form}.$name,document.{$this->form}.{$prefix}length$n)' onKeyUp='if (typeof countChars == \"function\") countChars(document.{$this->form}.$name,document.{$this->form}.{$prefix}length$n)'";
				$onload = ''; // "if (typeof countChars == \"function\") countChars(document.{$this->form}.$name,document.{$this->form}.{$prefix}length$n);";
			}
			if ( isset( $opts['id'] ) ) {
				$attr .= " id=\"{$opts['id']}\" ";
			}
			switch ( $options['type'] ) {
				case 'multiselect':
					$attr         .= ' MULTIPLE';
					$args['attr'] = $attr;
					$args['name'] = $name = "{$name}[]";
				case 'select':
					$buf .= $this->do_multi_input( $args );
					break;
				case 'multicheckbox':
					$args['name']            = $name = "{$name}[]";
					$args['options']['type'] = $options['type'] = 'checkbox';
				case 'radio':
					$buf .= $this->do_multi_input( $args );
					break;
				case 'checkbox':
					if ( $value ) {
						$attr .= ' CHECKED';
					}
					$buf .= "<input name='$name' type='{$options['type']}' $attr>\n";
					break;
				case 'textarea':
					$buf .= "<textarea name='$name' $attr $data $classes>$value</textarea>";
					break;
				case 'image':
					$buf .= "<input class='aioseop_upload_image_button button-primary' type='button' value='Upload Image' style='float:left;' />" .
							"<input class='aioseop_upload_image_label' name='$name' type='text' $attr value='$value' size=57 style='float:left;clear:left;'>\n";
					break;
				case 'html':
					$buf .= $value;
					break;
				default:
					$buf .= "<input name='$name' type='{$options['type']}' $attr $data $classes value='$value'>\n";
			}
			if ( ! empty( $options['count'] ) ) {
				$size = 60;
				if ( isset( $options['size'] ) ) {
					$size = $options['size'];
				} elseif ( isset( $options['rows'] ) && isset( $options['cols'] ) ) {
					$size = $options['rows'] * $options['cols'];
				}
				if ( isset( $options['count_desc'] ) ) {
					$count_desc = $options['count_desc'];
				} else {
					$count_desc = __( ' characters. Most search engines use a maximum of %s chars for the %s.', 'all_in_one_seo_pack' );
				}
				$buf .= "<br /><input readonly type='text' name='{$prefix}length{$suffix}' size='3' maxlength='3' style='width:53px;height:23px;margin:0px;padding:0px 0px 0px 10px;' value='" . $this->strlen( $value ) . "' />"
						. sprintf( $count_desc, $size, trim( $this->strtolower( $options['name'] ), ':' ) );
				if ( ! empty( $onload ) ) {
					$buf .= "<script>jQuery( document ).ready(function() { {$onload} });</script>";
				}
			}

			return $buf;
		}
	}
	
endif;

/**
 * Class WPGlobus_aioseop
 */
class WPGlobus_aioseop {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_print_scripts', array(
			$this,
			'on_admin_scripts',
		) );

		add_action( 'admin_footer', array(
			$this,
			'on_admin_footer',
		) );

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	function on_admin_scripts() {
		//global $WPGlobus;
		
		$add_css_rules = false;
		if ( version_compare( AIOSEOP_VERSION, '2.9.99', '>' ) ) {
			$add_css_rules = true;
		}
		
		wp_register_script(
			'wpglobus-aioseop',
			WPGlobus::$PLUGIN_DIR_URL . "includes/js/wpglobus-vendor-aioseop" . WPGlobus::SCRIPT_SUFFIX() . ".js",
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus-aioseop' );
		wp_localize_script(
			'wpglobus-aioseop',
			'WPGlobusAioseop',
			array(
				'version' => WPGLOBUS_VERSION,
				'vendor_version' => AIOSEOP_VERSION,
				'add_css_rules'  => $add_css_rules,
			)
		);

	}

	function on_admin_footer() {

		global $post;

		$permalink           = array();
		$permalink['url']    = get_permalink( $post->ID );
		$permalink['action'] = 'complete';

		$fields = array();
		
		/**
		 * @since 2.2.11
		 */
		$aioseop_options = aioseop_get_options();		
		
		if ( ! empty( $aioseop_options['aiosp_togglekeywords'] ) || (int) $aioseop_options['aiosp_togglekeywords'] === 1 ) {
			/**
			 * Do not set the keywords field in according to the option.
			 * @since 2.2.11
			 */ 
		} else {
			/**
			 * Keywords.
			 */
			$fields['aiosp_keywords']['opts']              = array(
				'name'      => __( 'Keywords (comma separated)', 'all_in_one_seo_pack' ),
				'type'      => 'text',
				'label'     => '',
				'help_text' => __( 'A comma separated list of your most important keywords for this page that will be written as META keywords.', 'all_in_one_seo_pack' ),
			);
			$fields['aiosp_keywords']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
			$fields['aiosp_keywords']['args']              = array(
				'name'    => 'aiosp_keywords',
				'attr'    => 'placeholder="{{placeholder}}"',
				'data'    => '',
				'classes' => 'class="wpglobus-aioseop_keywords wpglobus-translatable"',
				'value'   => '',
				'prefix'  => '',
				'options' => $fields['aiosp_keywords']['opts'],
			);
		}
		
		/**
		 * Description
		 */
		$fields['aiosp_description']['opts']              = array(
			'name'        => __( 'Description', 'all_in_one_seo_pack' ),
			'type'        => 'textarea',
			'count'       => true,
			'cols'        => 80,
			'rows'        => 2,
			'label'       => '',
			'help_text'   => __( 'The META description for this page. This will override any autogenerated descriptions.', 'all_in_one_seo_pack' ),
			'placeholder' => '',
		);
		$fields['aiosp_description']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_description']['args']              = array(
			'name'    => 'aiosp_description',
			'attr'    => 'placeholder="{{placeholder}}"',
			'data'    => ' data-max-size="160" ',
			'classes' => 'class="wpglobus_countable wpglobus-aioseop_description wpglobus-translatable"',
			'value'   => '',
			'prefix'  => 'wpglobus_',
			'suffix'  => '',
			'options' => $fields['aiosp_description']['opts'],
		);

		/**
		 * Title
		 */
		$fields['aiosp_title']['opts']              = array(
			'name'            => __( 'Title', 'all_in_one_seo_pack' ),
			'type'            => 'text',
			'count'           => true,
			'size'            => 60,
			'help_text'       => __( 'A custom title that shows up in the title tag for this page.', 'all_in_one_seo_pack' ),
			'default'         => '',
			'initial_options' => '',
			'nowrap'          => '',
			'label'           => '',
			'save'            => true,
			#'prefix' => 'wpglobus_',
			'placeholder'     => '',
		);
		$fields['aiosp_title']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_title']['args']              = array(
			'name'    => 'aiosp_title_{{language}}',
			'attr'    => 'size="60"  placeholder="{{placeholder}}"',
			'data'    => ' data-max-size="60" ',
			'classes' => 'class="wpglobus_countable wpglobus-aioseop_title wpglobus-translatable"',
			'value'   => '',
			'prefix'  => 'wpglobus_',
			'suffix'  => '',
			'options' => $fields['aiosp_title']['opts'],
		);

		/**
		 * Snippet must be last in array
		 */
		$fields['aiosp_snippet']['opts']              = array(
			'name'      => __( 'Preview Snippet', 'all_in_one_seo_pack' ),
			'type'      => 'html',
			'label'     => 'top',
			#'default' => '<div class="preview_snippet"><div id="aioseop_snippet"><h3><a>%s</a></h3><div><div><cite id="aioseop_snippet_link">%s</cite></div><span id="aioseop_snippet_description">%s</span></div></div></div>',
			'help_text' => __( 'A preview of what this page might look like in search engine results.', 'all_in_one_seo_pack' ),
			#'initial_options' => '<div class="preview_snippet"><div id="aioseop_snippet"><h3><a>%s</a></h3><div><div><cite id="aioseop_snippet_link">%s</cite></div><span id="aioseop_snippet_description">%s</span></div></div></div>',
			'nowrap'    => 1,
			'save'      => true,
			'prefix'    => '',
		);
		$fields['aiosp_snippet']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_snippet']['args']              = array(
			'name'    => 'aiosp_snippet',
			'attr'    => '',
			'classes' => 'wpglobus-aioseop_snippet',
			'data'    => '',
			'value'   => '<div class="preview_snippet">
							<div id="aioseop_snippet_{{language}}" data-extra-length="{{extra_length}}">
								<h3{{header_style}}><a{{link_style}}><span id="aioseop_snippet_title_{{language}}">%s</span>%s</a></h3>
								<div>
									<div>
										<cite{{cite_style}} id="aioseop_snippet_link_{{language}}">%s</cite>
									</div>
									<span id="aioseop_snippet_description_{{language}}">%s</span>
								</div>
							</div>
						</div>	',
			'prefix'  => '',
			'options' => $fields['aiosp_snippet']['opts'],
		);

		$aio = new WPGlobus_All_in_One_SEO();

		/**
		 * @todo check url
		 */
		$permalink = array();
		if ( 'publish' == $post->post_status ) {
			$permalink['url']    = get_permalink( $post->ID );
			$permalink['action'] = 'complete';
		} else {
			$permalink['url']    = trailingslashit( home_url() );
			$permalink['action'] = '';
		}

		/**
		 * get keywords for current post
		 * use original function for compatibility
		 *
		 * "All In One SEO Pack" may to add keywords in addition to any keywords you specify on the Edit Post screen.
		 *
		 * @see options in Keyword Settings metabox on General Settings page.
		 */
		$keywords = $aio->get_all_keywords();
		$keywords = explode( ',', $keywords );

		global $wpdb;

		//$keywords_source = array();
		foreach ( $keywords as $keyword ) {
			$keyword = trim( $keyword );
			if ( empty( $keyword ) ) {
				$keywords_source[ $keyword ] = '';
			} else {
				$name = $wpdb->get_var( "SELECT name FROM $wpdb->terms WHERE name LIKE '%$keyword%'" );
				if ( empty( $name ) ) {
					$keywords_source[ $keyword ] = '';
				} else {
					$keywords_source[ $keyword ] = $name;
				}
			}
		}

		switch ( $post->post_type ) :
			case 'post' :
				$title_format = $aioseop_options['aiosp_post_title_format'];
				$title_format = explode( ' ', preg_replace( '/\s+/', ' ', $title_format ) );
				break;
			default:
				$title_format = '';
		endswitch;

		/**
		 * Get meta title in current language ( WPGlobus::Config()->language )
		 * in $aioseop_options['aiosp_post_title_format'] format, usual as %post_title% | %blog_title%
		 * Title will be like to "New Post for All in one Seo Pack | WPGlobus" without language marks
		 */
		$aiosp_meta_title = $aio->get_aioseop_title( $post );

		if ( isset(
			/** separator **/
			$title_format[1]
		) ) {
			$aiosp_meta_title = explode( $title_format[1], $aiosp_meta_title );
			$aiosp_meta_title = $aiosp_meta_title[0];
		}

		$aiosp_meta_title_source = get_post_meta( $post->ID, "_aioseop_title", true );
		$aiosp_meta_title_source = trim( $aiosp_meta_title_source );
		if ( ! WPGlobus_Core::text_filter( $aiosp_meta_title_source, WPGlobus::Config()->default_language ) ) {
			/**
			 * Reset meta title for default language
			 */
			$aiosp_meta_title = '';
		}

		/**
		 * Get meta description in current language ( WPGlobus::Config()->language ) with $aio->get_post_description($post)
		 *
		 * @see 'localization' filter in wpglobus-controller.php
		 */
		// $aiosp_post_description 		= $aio->get_post_description($post);

		/**
		 * but we need description with language marks
		 */
		$aiosp_meta_description_source = $aiosp_post_description = get_post_meta( $post->ID, "_aioseop_description", true );
		$aiosp_meta_description_source = trim( $aiosp_meta_description_source );

		/**
		 * Get keywords /// title in current language ( WPGlobus::Config()->language )
		 */
		$aiosp_keywords_source = get_post_meta( $post->ID, "_aioseop_keywords", true );


		$header_style = ' style="padding:8px 0;"';
		$link_style   = ' style="color:#12c;cursor: pointer;text-decoration: -moz-use-text-color none solid;font-size:16px;"';
		$cite_style   = ' style="color:#093;font-style:normal;"';
		?>

		<div id="wpglobus-aioseop-tabs" class="hidden wpglobus-hidden">
			<ul class="wpglobus-aioseop-tabs-list">    <?php
				$order = 0;
				foreach ( WPGlobus::Config()->enabled_languages as $language ) { ?>
					<li id="aioseop-link-tab-<?php echo esc_attr( $language ); ?>"
							data-language="<?php echo esc_attr( $language ); ?>"
							data-order="<?php echo esc_attr( $order ); ?>"
							class="wpglobus-aioseop-tab"><a
								href="#aioseop-tab-<?php echo esc_attr( $language ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul>
			<div style="clear:both;margin-bottom:20px;"></div><?php


			foreach ( WPGlobus::Config()->enabled_languages as $language ) {

				if ( $language == WPGlobus::Config()->default_language ) {

					$return = WPGlobus::RETURN_IN_DEFAULT_LANGUAGE;
					if ( $language == WPGlobus::Config()->language ) {
						$aiosp_title = trim( WPGlobus_Core::text_filter( $aiosp_meta_title, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE ) );
					} else {
						/**
						 * Get title from source ( post meta key '_aioseop_title' )
						 */
						$aiosp_title = trim( WPGlobus_Core::text_filter( $aiosp_meta_title_source, $language, WPGlobus::RETURN_EMPTY ) );
					}

				} else {

					$return      = WPGlobus::RETURN_EMPTY;
					$aiosp_title = trim( WPGlobus_Core::text_filter( $aiosp_meta_title, $language, WPGlobus::RETURN_EMPTY ) );
					if ( empty( $aiosp_title ) ) {
						/**
						 * Get title from source ( post meta key '_aioseop_title' )
						 */
						$aiosp_title = trim( WPGlobus_Core::text_filter( $aiosp_meta_title_source, $language, WPGlobus::RETURN_EMPTY ) );
					}

				}

				$url = WPGlobus_Utils::localize_url( $permalink['url'], $language );

				/**
				 * Set snippet title
				 */
				$aiosp_placeholder_title = WPGlobus_Core::text_filter( $post->post_title, $language, $return );
				$aiosp_snippet_title     = empty( $aiosp_title ) ? $aiosp_placeholder_title : $aiosp_title;

				/**
				 * Set meta description
				 */
				$aiosp_meta_description = WPGlobus_Core::text_filter( $aiosp_meta_description_source, $language, $return );

				if ( empty( $aiosp_meta_description ) ) {

					$description = '';

					if ( empty( $aioseop_options["aiosp_skip_excerpt"] ) ) {

						if ( method_exists($aio, 'trim_excerpt_without_filters_full_length') ) {
							$description = $aio->trim_excerpt_without_filters_full_length( WPGlobus_Core::text_filter( $post->post_excerpt, $language, WPGlobus::RETURN_EMPTY ) );
						} else if ( method_exists($aio, 'trim_text_without_filters_full_length') ) {
							/**
							 * @since All In One SEO Pack 2.4.4
							 */
							$description = $aio->trim_text_without_filters_full_length( WPGlobus_Core::text_filter( $post->post_excerpt, $language, WPGlobus::RETURN_EMPTY ) );
						} else {
							$description = WPGlobus_Core::text_filter( $post->post_excerpt, $language, WPGlobus::RETURN_EMPTY );
						}

					}	

					if ( ! $description && $aioseop_options["aiosp_generate_descriptions"] ) {
						$content = WPGlobus_Core::text_filter( $post->post_content, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
						if ( ! empty( $aioseop_options["aiosp_run_shortcodes"] ) ) {
							$content = do_shortcode( $content );
						}
						$content     = wp_strip_all_tags( $content );
						$description = $aio->trim_excerpt_without_filters( $content );
					}

					$aiosp_description             = '';
					$aiosp_placeholder_description = $description;
					$aiosp_snippet_description     = $aiosp_placeholder_description;

				} else {

					$aiosp_description             = WPGlobus_Core::text_filter( $aiosp_post_description, $language, $return );
					$aiosp_placeholder_description = $aiosp_description;
					$aiosp_snippet_description     = $aiosp_description;

				}
				/**
				 * Make All in one Seo Pack tabs
				 */
				?>
				<div id="aioseop-tab-<?php echo esc_attr( $language ); ?>" class="wpglobus-aioseop-general"
						data-language="<?php echo esc_attr( $language ); ?>"
						data-url-<?php echo esc_attr( $language ); ?>="<?php echo esc_url( $url ); ?>">
					<?php
					$r = '';
					foreach ( $fields as $name => $data ) :

						if ( 'aiosp_snippet' == $name ) {

							$snippet_title_2 = '';
							if ( isset( $title_format[2] ) && false !== strpos( $title_format[2], '%blog_title%' ) ) {

								$snippet_title_2 = ' ' . $title_format[1] . ' ' . WPGlobus_Core::text_filter( get_option( 'blogname' ), $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );

							}

							$data['args']['value'] = str_replace( '{{language}}', $language, $data['args']['value'] );

							$data['args']['value'] = str_replace( '{{header_style}}', $header_style, $data['args']['value'] );
							$data['args']['value'] = str_replace( '{{link_style}}', $link_style, $data['args']['value'] );
							$data['args']['value'] = str_replace( '{{cite_style}}', $cite_style, $data['args']['value'] );

							$data['args']['value'] = sprintf( $data['args']['value'], $aiosp_snippet_title, $snippet_title_2, WPGlobus_Utils::localize_url( $permalink['url'], $language ), $aiosp_snippet_description );

							$data['args']['value'] = str_replace( '{{extra_length}}', mb_strlen( $snippet_title_2 ), $data['args']['value'] );

						} elseif ( 'aiosp_title' == $name ) {


							$data['args']['name']   = str_replace( '{{language}}', $language, $data['args']['name'] );
							$data['args']['attr']   = str_replace( '{{placeholder}}', $aiosp_placeholder_title, $data['args']['attr'] );
							$data['args']['prefix'] = 'wpglobus_title_';
							$data['args']['suffix'] = '_' . $language;
							$data['args']['data']   = $data['args']['data'] . ' data-field-count="wpglobus_title_length_' . $language . '" data-extra-element="aioseop_snippet_' . $language . '" data-language="' . $language . '"';
							$data['args']['value']  = $aiosp_title;

						} elseif ( 'aiosp_description' == $name ) {

							$data['args']['attr']   = str_replace( '{{placeholder}}', $aiosp_placeholder_description, $data['args']['attr'] );
							$data['args']['prefix'] = 'wpglobus_description_';
							$data['args']['suffix'] = '_' . $language;
							$data['args']['name']   = $data['args']['name'] . '_' . $language;
							$data['args']['data']   = $data['args']['data'] . ' data-field-count="wpglobus_description_length_' . $language . '" data-language="' . $language . '"';
							$data['args']['value']  = $aiosp_description;

						} elseif ( 'aiosp_keywords' == $name ) {

							$placeholders = array();
							foreach ( $keywords as $keyword ) {
								if ( empty( $keywords_source[ $keyword ] ) ) {
									continue;
								}
								/**
								 * @todo maybe better use WPGlobus::RETURN_EMPTY, in this case we will be have tags in native language only
								 */
								$placeholders[] = WPGlobus_Core::text_filter( $keywords_source[ $keyword ], $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
							}
							$placeholder = implode( ',', $placeholders );

							$data['args']['attr']  = str_replace( '{{placeholder}}', $placeholder, $data['args']['attr'] );
							$data['args']['data']  = ' data-language="' . $language . '" ';
							$data['args']['name']  = $data['args']['name'] . '_' . $language;
							$data['args']['data']  = ' data-language="' . $language . '" ';
							$data['args']['value'] = WPGlobus_Core::text_filter( $aiosp_keywords_source, $language, WPGlobus::RETURN_EMPTY );

						}

						$r = $aio->wpg_get_option_row( $name, $data['opts'], $data['args'], $language ) . $r;

					endforeach;
					echo $r; // WPCS: XSS ok.
					?>
				</div> <!-- .wpglobus-aioseop-general -->    <?php

			}    // end foreach ?>
			<!-- <hr /> -->
		</div> <!-- #wpglobus-aioseop-tabs -->

		<?php

	}
}
