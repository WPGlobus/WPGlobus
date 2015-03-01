<?php
/**
 * Widget
 * 
 * @since 1.0.7
 *
 * @package WPGlobus
 */
 
/**
 * class WPGlobusWidget
 */
class WPGlobusWidget extends WP_Widget {
	
	/**
	 * Array types of switcher
	 *
	 * @access private
	 * @since 1.0.7
	 * @var array	 
	 */
	private $types = array();
	
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct( 
			'wpglobus_widget',
			__( 'WPGlobus widget', 'wpglobus' ),
			array( 
				'description' => __( 'Add language switcher', 'wpglobus' ) 
			) 
		);
		$this->types['flags'] 			 	= __('Flags', 'wpglobus');
		$this->types['select'] 			 	= __('Select', 'wpglobus');
		$this->types['select_with_code'] 	= __('Select with language code', 'wpglobus');
		$this->types['dropdown'] 		 	= __('Dropdown', 'wpglobus');
		$this->types['dropdown_with_flags'] = __('Dropdown with flags', 'wpglobus');
	}

	/** 
	 * Echo the widget content
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {
		
		if ( !empty( $instance['type'] ) ) {
			$type = $instance['type'];
		} else {
			$type = 'flags';
		}
		
		$inside = '';
		
		$enabled_languages = WPGlobus::Config()->enabled_languages;
		
		switch ($type) :
		case 'flags' :
			$code = '<div class="flags-styled">{{inside}}</div>';
			break;
		case 'select' :
		case 'select_with_code' :
			$code = '<div class="select-styled"><select onchange="document.location.href = this.value;">{{inside}}</select></div>';
			break;
		case 'dropdown' :
		case 'dropdown_with_flags' :		
			$sorted[] = WPGlobus::Config()->language;
			foreach ( $enabled_languages as $language ) {
				if ( $language != WPGlobus::Config()->language ) {
					$sorted[] = $language;
				}	
			}
			$enabled_languages = $sorted;
			$code = '<div class="dropdown-styled"> <ul>
					  <li>
						{{language}}
						<ul>
							{{inside}}
						</ul>
					  </li>
					</ul></div>';			
			break;
		endswitch;		?>
		
		<aside class="widget wpglobus-widget">			<?php 
			foreach ( $enabled_languages as $language ) :
				
				if ( $language == WPGlobus::Config()->default_language && WPGlobus::Config()->hide_default_language ) {
					$l = '';	
				} else {
					$l = '/' . $language;
				}
				
				$selected = '';
				if ( $language == WPGlobus::Config()->language ) {
					$selected = ' selected';	
				}	
				
				$url  = WPGlobus::Config()->url_info['schema'] . WPGlobus::Config()->url_info['host'] . $l . WPGlobus::Config()->url_info['url'];	
				$flag = WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[$language];
				
				switch ($type) :
				case 'flags' :
					$inside .= '<span class="flag"><a href="' . $url .'"><img src="' . $flag . '"/></a></span>';
					break;
				case 'select' :
					$inside .= '<option ' . $selected . ' value="' . $url .'">' . WPGlobus::Config()->language_name[$language] . '</option>';
					break;
				case 'select_with_code' :
					$inside .= '<option ' . $selected . ' value="' . $url .'">' . WPGlobus::Config()->language_name[$language] . '&nbsp;(' . strtoupper($language) . ')</option>';
					break;
				case 'dropdown' :
					if ( '' != $selected ) {
						$code = str_replace( '{{language}}', '<a href="' . $url .'">' . WPGlobus::Config()->language_name[$language] . '&nbsp;(' . strtoupper($language) . ')</a>', $code );
					} else {
						$inside .= '<li><a href="' . $url .'">' . WPGlobus::Config()->language_name[$language] . '&nbsp;(' . strtoupper($language) . ')</a></li>';
					}				
					break;
				case 'dropdown_with_flags' :
					if ( '' != $selected ) {
						$code = str_replace( '{{language}}', '<a href="' . $url .'"><img src="' . $flag . '"/>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[$language] . '</a>', $code );
					} else {
						$inside .= '<li><a href="' . $url .'"><img src="' . $flag . '"/>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[$language] . '</a></li>';
					}
					break;
				endswitch;
				
			endforeach; 	

			echo str_replace( '{{inside}}', $inside, $code );	
					?>
		</aside>
		<?php
	}

	/**
	 * Echo the settings update form
	 *
	 * @param array $instance Current settings
	 *
	 * @return string
	 */
	public function form( $instance ) {
		
		if ( isset( $instance['type'] ) ) {
			$selected_type = $instance['type'];
		} else {
			$selected_type = '';
		}

		?>
		<p><?php _e( 'Selector type', 'wpglobus' ); ?></p>
		<p><?php
			foreach ( $this->types as $type=>$caption ) :		
				$checked = 	'';
				if ( $selected_type == $type ) {
					$checked = ' checked';
				}	?>
				<input type="radio"	
					id="<?php echo $this->get_field_id('type'); ?>" 
					name="<?php echo $this->get_field_name('type'); ?>" <?php echo $checked; ?> 
					value="<?php echo esc_attr( $type ); ?>" /> <?php echo $caption . '<br />'; 
			endforeach;
		?></p>		<?php

		return '';

	}

	/**
	 * Update a particular instance.
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) {
		$instance         = array();
		$instance['type'] = ( ! empty( $new_instance['type'] ) ) ? $new_instance['type'] : '';
		return $instance;
	}
}

# --- EOF