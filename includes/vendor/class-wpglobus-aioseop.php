<?php
/**
 * @package   WPGlobus
 */

require_once( WP_PLUGIN_DIR . '/all-in-one-seo-pack/aioseop_class.php' );

/**
 * Class WPGlobus_All_in_One_SEO
 */
class WPGlobus_All_in_One_SEO extends All_in_One_SEO_Pack {
	
	
	// const DISPLAY_HELP_START	= '<a class="aioseop_help_text_link" style="cursor:pointer;" title="%s" onclick="toggleVisibility(\'%s_tip\');"><label class="aioseop_label textinput">%s</label></a>';
	// const DISPLAY_HELP_END		= '<div class="aioseop_help_text_div" style="display:none" id="%s_tip"><label class="aioseop_help_text">%s</label></div>';
	// const DISPLAY_LABEL_FORMAT  = '<span class="aioseop_option_label" style="text-align:%s;vertical-align:top;">%s</span>';
	// const DISPLAY_TOP_LABEL		= "</div>\n<div class='aioseop_input aioseop_top_label'>\n";
	// const DISPLAY_ROW_TEMPLATE	= '<div class="aioseop_wrapper%s" id="%s_wrapper"><div class="aioseop_input">%s<span class="aioseop_option_input"><div class="aioseop_option_div" %s>%s</div>%s</span><p style="clear:left"></p></div></div>';
				
	
	private $wpg_language = '';
	
	function __construct() {
	}
	
	public static function filter__text( $text ) {

		$title_arr = explode('|', $text);
		$title  = WPGlobus_Core::text_filter( $title_arr[0], WPGlobus::Config()->language, null);
		$title .= ' |';
		$title .= WPGlobus_Core::text_filter( $title_arr[1], WPGlobus::Config()->language, null);
		
		return $title;
	}
	
	function wpg_get_option_row( $name, $opts, $args, $language ) {

		$this->wpg_language = $language;
		
		$r =  $this->get_option_row( $name, $opts, $args );

		return $r;
	
	}
	
	/**
	 * Format a row for an option on a settings page.
	 */
	function get_option_row( $name, $opts, $args ) { 

		$language = '_'.$this->wpg_language;

		$label_text = $input_attr = $help_text_2 = $id_attr = '';
		if ( $opts['label'] == 'top' )
			$align	= 'left';
		else
			$align = 'right';
		if ( isset( $opts['id'] ) ) $id_attr .= " id=\"{$opts['id']}_div\" ";
		if ( $opts['label'] != 'none' ) { 
			if ( isset( $opts['help_text'] ) ) {
				$help_text = sprintf(	All_in_One_SEO_Pack_Module::DISPLAY_HELP_START, __( 'Click for Help!', 'all_in_one_seo_pack' ), $name.$language, $opts['name'] );
				$help_text_2 = sprintf(	All_in_One_SEO_Pack_Module::DISPLAY_HELP_END, $name.$language, $opts['help_text'] );
			} else $help_text = $opts['name'];
			$label_text = sprintf( All_in_One_SEO_Pack_Module::DISPLAY_LABEL_FORMAT, $align, $help_text );
		} else $input_attr .= ' aioseop_no_label ';
		if ( $opts['label'] == 'top' ) $label_text .= All_in_One_SEO_Pack_Module::DISPLAY_TOP_LABEL;
		$input_attr .= " aioseop_{$opts['type']}_type";
		if ( $name == 'aiosp_title' && $language == '_en' ) {
			//error_log( sprintf( All_in_One_SEO_Pack_Module::DISPLAY_ROW_TEMPLATE, $input_attr, $name.$language, $label_text, $id_attr, $this->get_option_html( $args ), $help_text_2 ) );
			//error_log( $this->get_option_html( $args ) );
		}	
		return sprintf( All_in_One_SEO_Pack_Module::DISPLAY_ROW_TEMPLATE, $input_attr, $name.$language, $label_text, $id_attr, $this->get_option_html( $args ), $help_text_2 );
	}
	
	/**
	 * Outputs a setting item for settings pages and metaboxes.
	 */
	function get_option_html( $args ) {
		static $n = 0;
		extract( $args );
		if ( $options['type'] == 'custom' )
			return apply_filters( "{$prefix}output_option", '', $args );				
		if ( in_array( $options['type'], Array( 'multiselect', 'select', 'multicheckbox', 'radio', 'checkbox', 'textarea', 'text', 'submit', 'hidden' ) ) && ( is_string( $value ) ) )
			$value = esc_attr( $value );
		$buf = '';
		$onload = '';
		if ( !empty( $options['count'] ) ) {
			$n++;
			$attr .= ''; // " onKeyDown='if (typeof countChars == \"function\") countChars(document.{$this->form}.$name,document.{$this->form}.{$prefix}length$n)' onKeyUp='if (typeof countChars == \"function\") countChars(document.{$this->form}.$name,document.{$this->form}.{$prefix}length$n)'";
			$onload = ''; // "if (typeof countChars == \"function\") countChars(document.{$this->form}.$name,document.{$this->form}.{$prefix}length$n);";
		}
		if ( isset( $opts['id'] ) ) $attr .= " id=\"{$opts['id']}\" ";
		switch ( $options['type'] ) {
			case 'multiselect':   $attr .= ' MULTIPLE';
								  $args['attr'] = $attr;
								  $args['name'] = $name = "{$name}[]";
			case 'select':		  $buf .= $this->do_multi_input( $args ); break;
			case 'multicheckbox': $args['name'] = $name = "{$name}[]";
								  $args['options']['type'] = $options['type'] = 'checkbox';
			case 'radio':		  $buf .= $this->do_multi_input( $args ); break;
			case 'checkbox':	  if ( $value ) $attr .= ' CHECKED';
								  $buf .= "<input name='$name' type='{$options['type']}' $attr>\n"; break;
			case 'textarea':	  $buf .= "<textarea name='$name' $attr $data $classes>$value</textarea>"; break;
			case 'image':		  $buf .= "<input class='aioseop_upload_image_button button-primary' type='button' value='Upload Image' style='float:left;' />" .
										  "<input class='aioseop_upload_image_label' name='$name' type='text' $attr value='$value' size=57 style='float:left;clear:left;'>\n";
								  break;
			case 'html':		  $buf .= $value; break;
			default:			  $buf .= "<input name='$name' type='{$options['type']}' $attr $data $classes value='$value'>\n";
		}
		if ( !empty( $options['count'] ) ) {
			$size = 60;
			if ( isset( $options['size'] ) ) $size = $options['size'];
			elseif ( isset( $options['rows'] ) && isset( $options['cols'] ) ) $size = $options['rows'] * $options['cols'];
			if ( isset( $options['count_desc'] ) )
				$count_desc = $options['count_desc'];
			else
				$count_desc = __( ' characters. Most search engines use a maximum of %s chars for the %s.', 'all_in_one_seo_pack' );
			$buf .= "<br /><input readonly type='text' name='{$prefix}length{$suffix}' size='3' maxlength='3' style='width:53px;height:23px;margin:0px;padding:0px 0px 0px 10px;' value='" . $this->strlen($value) . "' />"
				 . sprintf( $count_desc, $size, trim( $this->strtolower( $options['name'] ), ':' ) );
			if ( !empty( $onload ) ) $buf .= "<script>jQuery( document ).ready(function() { {$onload} });</script>";
		}
		return $buf;
	}	
}	 
 
/**
 * Class WPGlobus_aioseop
 */
class WPGlobus_aioseop {
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		add_action( 'admin_print_scripts', array(
			$this,
			'on_admin_scripts'
		) );		
		
		add_action( 'admin_footer', array(
			$this,
			'on_admin_footer'
		) );
		
	}
	
	/**
	 * Enqueue admin scripts
	 * @return void
	 */
	function on_admin_scripts() {
			//global $WPGlobus;
			wp_register_script(
				'wpglobus-aioseop',
				WPGLobus::$PLUGIN_DIR_URL . "includes/js/wpglobus-aioseop" . WPGLobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-aioseop' );
			wp_localize_script(
				'wpglobus-aioseop',
				'WPGlobusAioseop',
				array(
					'version' => WPGLOBUS_VERSION
				)
			);		
	
	}	
	
	function on_admin_footer() {
		global $post;
		//error_log( print_r($post, true) );

		$permalink = array();
		$permalink['url']    = get_permalink( $post->ID );
		$permalink['action'] = 'complete';
		
		$fields = array();
	
		/**
		 * Keywords
		 */
		$fields['aiosp_keywords']['opts'] = array(
			'name' => __( 'Keywords (comma separated)', 'all_in_one_seo_pack' ),
			'type' => 'text',
			'label' => '',
			'help_text' => __( 'A comma separated list of your most important keywords for this page that will be written as META keywords.', 'all_in_one_seo_pack' ),
		);
		$fields['aiosp_keywords']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_keywords']['args'] = array(
			'name' => 'aiosp_keywords',
			'attr' =>  'placeholder="{{placeholder}}"',
			'data'	=> '',
			'classes' => '',
			'value' => '',
			'prefix' => '',
			'options' => $fields['aiosp_keywords']['opts']
		);		

	
		/**
		 * Description
		 */		
		$fields['aiosp_description']['opts'] = array(
			'name' => __( 'Description', 'all_in_one_seo_pack' ),
			'type' => 'textarea',
			'count' => true,
			'cols' => 80,
			'rows' => 2,
			'label' => '',
			'help_text' => __( 'The META description for this page. This will override any autogenerated descriptions.', 'all_in_one_seo_pack' ),
			'placeholder' => ''
		);
		$fields['aiosp_description']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_description']['args'] = array(
			'name' => 'aiosp_description',
			'attr' =>  'placeholder="{{placeholder}}"',
			'data' => '',
			'classes' => 'class="wpglobus_countable wpglobus-aioseop_description"',
			'value' => '',
			'prefix' => 'wpglobus_',
			'suffix' => '',
			'options' => $fields['aiosp_description']['opts']
		);
		
		/**
		 * Title
		 */	
		$fields['aiosp_title']['opts'] = array(
			'name' => __( 'Title', 'all_in_one_seo_pack' ),
			'type' => 'text',
			'count' => true,
			'size' => 60,
			'help_text' => __( 'A custom title that shows up in the title tag for this page.', 'all_in_one_seo_pack' ),
			'default' => '',
			'initial_options' => '', 
			'nowrap' => '',
			'label' => '',
			'save' => true,
			#'prefix' => 'wpglobus_',
			'placeholder' => ''
		);
		$fields['aiosp_title']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_title']['args'] = array(
			'name' => 'aiosp_title_{{language}}',
			'attr' =>  'size="60"  placeholder="{{placeholder}}"',
			'data'	=> '',
			'classes' => 'class="wpglobus_countable wpglobus-aioseop_title"',
			'value' => '',
			'prefix' => 'wpglobus_',
			'suffix' => '',
			'options' => $fields['aiosp_title']['opts']
		);		
		
		/**
		 * Snippet must be last in array
		 */
		$fields['aiosp_snippet']['opts'] = array(
			'name' => __( 'Preview Snippet', 'all_in_one_seo_pack' ),
			'type' => 'html',
			'label' => 'top',
			#'default' => '<div class="preview_snippet"><div id="aioseop_snippet"><h3><a>%s</a></h3><div><div><cite id="aioseop_snippet_link">%s</cite></div><span id="aioseop_snippet_description">%s</span></div></div></div>',
			'help_text' => __( 'A preview of what this page might look like in search engine results.', 'all_in_one_seo_pack' ),
			#'initial_options' => '<div class="preview_snippet"><div id="aioseop_snippet"><h3><a>%s</a></h3><div><div><cite id="aioseop_snippet_link">%s</cite></div><span id="aioseop_snippet_description">%s</span></div></div></div>',
			'nowrap' => 1,
			'save' => true,
			'prefix' => ''
		);
		$fields['aiosp_snippet']['opts']['help_text'] .= '<br /><a target="_blank" href="http://semperplugins.com/sections/postpage-settings/">Click here for documentation on this setting</a>';
		$fields['aiosp_snippet']['args'] = array(
			'name' => 'aiosp_snippet',
			'attr' => '',
			'classes' => 'wpglobus-aioseop_snippet',
			'data'	=> '',			
			'value' => '<div class="preview_snippet">
							<div id="aioseop_snippet_{{language}}" data-extra-length="{{extra_length}}">
								<h3><a><span id="aioseop_snippet_title_{{language}}">%s</span>%s</a></h3>
								<div>
									<div>
										<cite id="aioseop_snippet_link_{{language}}">%s</cite>
									</div>
									<span id="aioseop_snippet_description_{{language}}">%s</span>
								</div>
							</div>
						</div>	',
			'prefix' => '',
			'options' => $fields['aiosp_snippet']['opts']
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
		// */
		
		/**
		 * get keywords for current post
		 * use original function for compatibility
		 */
		$keywords = $aio->get_all_keywords();
		$keywords = explode( ',', $keywords );
		
		global $wpdb;
		
		//$keywords_source = array();
		foreach( $keywords as $keyword ) {
			$keyword = trim( $keyword );
			if ( empty($keyword) ) {
				$keywords_source[$keyword] = '';
			} else {
				$keywords_source[$keyword] = $wpdb->get_var( "SELECT name FROM $wpdb->terms WHERE name LIKE '%$keyword%'" );
			}
		}
		//error_log(print_r($keywords_source, true));
		
		//error_log('here');
		$aioseop_options =  aioseop_get_options(); 
		//error_log(print_r($aioseop_options, true));			
		//error_log( $post->post_type );
		
		switch ( $post->post_type ) :
			case 'post' :	
				$title_format = $aioseop_options['aiosp_post_title_format'];
				$title_format = explode( ' ', preg_replace('/\s+/', ' ', $title_format) );
			break;
			default:
				$title_format = '';
		endswitch;
		
		//error_log(print_r($title_format, true));
		//foreach( $title_format as $d ) {
			//error_log(strlen($d));	
		//}	
		// %post_title% | %blog_title%

		//$title_format = '%post_title%';
	
		$aiosp_meta_title 		= $aio->get_aioseop_title($post);
		$aiosp_meta_description = $aio->get_aioseop_description($post);

		//error_log($aiosp_meta_description);
		
		?>
		
		<div id="wpglobus-aioseop-tabs">    <?php
			/**
			 * Use span with attributes 'data' for send to js script ids, names elements for which needs to be set new ids, names with language code.
			 */ ?>
			<span id="wpglobus-aioseop-attr"
			      data-elements="aiosp_snippet_wrapper,aiosp_title_wrapper,aiosp_description_wrapper,aiosp_keywords_wrapper">
			</span>
			<ul class="wpglobus-aioseop-tabs-ul">    <?php
				$order = 0;
				foreach ( WPGlobus::Config()->open_languages as $language ) { ?>
					<li id="aioseop-link-tab-<?php echo $language; ?>"
					    data-language="<?php echo $language; ?>"
					    data-order="<?php echo $order; ?>"
					    class="wpglobus-aioseop-tab"><a
							href="#aioseop-tab-<?php echo $language; ?>"><?php echo WPGlobus::Config()->en_language_name[ $language ]; ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul>    		<div style="clear:both;margin-bottom:20px;"></div><?php


			foreach ( WPGlobus::Config()->open_languages as $language ) {
				
				$return = $language == WPGlobus::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;
				
				$url        = WPGlobus_Utils::get_convert_url( $permalink['url'], $language );  
				
				$aiosp_title 		= trim( WPGlobus_Core::text_filter($aiosp_meta_title, $language, $return) );
				$aiosp_description 	= trim( WPGlobus_Core::text_filter($aiosp_meta_description, $language, $return) );			
				
				$aiosp_placeholder_title = WPGlobus_Core::text_filter($post->post_title, $language, $return);
				$aiosp_snippet_title 	 = empty( $aiosp_title ) ? $aiosp_placeholder_title : $aiosp_title;
				
				$aiosp_snippet_description 	 = $aiosp_description;
				?>

				<div id="aioseop-tab-<?php echo $language; ?>" class="wpglobus-aioseop-general" data-language="<?php echo $language; ?>"
				     data-url-<?php echo $language; ?>="<?php echo $url; ?>">			<?php 
						$r = '';
						foreach( $fields as $name=>$data ) :
							
							if ( 'aiosp_snippet' == $name ) {

								$snippet_title_2 = '';
								if ( false !== strpos($title_format[2], '%blog_title%') ) {
								
									$snippet_title_2 = ' ' . $title_format[1] . ' ' . WPGlobus_Core::text_filter(get_option('blogname'),  $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE) ;	
									
								}
								
								$data['args']['value'] 	= str_replace( '{{language}}', $language, $data['args']['value'] );
								
								$data['args']['value'] 	= sprintf( $data['args']['value'], $aiosp_snippet_title, $snippet_title_2, WPGlobus_Utils::get_convert_url($permalink['url'], $language), $aiosp_snippet_description );
								
								$data['args']['value'] 	= str_replace( '{{extra_length}}',  mb_strlen($snippet_title_2), $data['args']['value'] );
								//$data['args']['data']	= ' data-extra-length="' . strlen($snippet_title_2) . '" ';
							
							} else if ( 'aiosp_title' == $name ) {
								

								$data['args']['name']  	 	= str_replace( '{{language}}', $language, $data['args']['name'] );
								$data['args']['attr']   	= str_replace( '{{placeholder}}', $aiosp_placeholder_title, $data['args']['attr'] );
								$data['args']['prefix']   	= 'wpglobus_title_';
								$data['args']['suffix']   	= '_' . $language;
								$data['args']['data']   	= ' data-field-count="wpglobus_title_length_' . $language . '" data-extra-element="aioseop_snippet_' . $language . '" data-language="' . $language . '"';
								$data['args']['value']   	= $aiosp_title;
							
							} else if ( 'aiosp_description' == $name ) {
						
								$data['args']['attr']  		= str_replace( '{{placeholder}}', $aiosp_description, $data['args']['attr'] );
								$data['args']['prefix']   	= 'wpglobus_description_';
								$data['args']['suffix']   	= '_' . $language;
								$data['args']['name']   	= $data['args']['name'] . '_' . $language;
								$data['args']['data']   	= ' data-field-count="wpglobus_description_length_' . $language . '" data-language="' . $language . '"';
								
							} else if ( 'aiosp_keywords' == $name ) {
								
								$placeholders = array();
								foreach( $keywords as $keyword ) {
									/**
									 * @todo maybe better use WPGlobus::RETURN_EMPTY, in this case we will be have tags in native language only
									 */
									$placeholders[] = WPGlobus_Core::text_filter( $keywords_source[$keyword], $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
								}
								$placeholder = implode(',', $placeholders);

								$data['args']['attr']  = str_replace( '{{placeholder}}', $placeholder, $data['args']['attr'] );
								$data['args']['data']  = ' data-language="' . $language . '" ';
							}
							
							$r = $aio->wpg_get_option_row( $name, $data['opts'], $data['args'], $language ) . $r; 
							
						endforeach;
						echo $r;	
					?> 
				</div> <!-- .wpglobus-aioseop-general -->	<?php
				
			} ?>
			<hr />
		</div> <!-- #wpglobus-aioseop-tabs -->

		<?php		
		
	}	
}