<?php
/**
 * Widget
 * @since   1.0.7
 * @package WPGlobus
 */

/**
 * class WPGlobusWidget
 */
class WPGlobusWidget extends WP_Widget {

	/**
	 * Array types of switcher
	 * @access private
	 * @since  1.0.7
	 * @var array
	 */
	private $types = array();

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct(
			'wpglobus',
			__( 'WPGlobus widget', 'wpglobus' ),
			array(
				'description' => __( 'Add language switcher', 'wpglobus' )
			)
		);
		$this->types['flags']               = __( 'Flags', 'wpglobus' );
		$this->types['list']                = __( 'List', 'wpglobus' );
		$this->types['list_with_flags']     = __( 'List with flags', 'wpglobus' );
		$this->types['select']              = __( 'Select', 'wpglobus' );
		$this->types['select_with_code']    = __( 'Select with language code', 'wpglobus' );
		$this->types['dropdown']            = __( 'Dropdown', 'wpglobus' );
		$this->types['dropdown_with_flags'] = __( 'Dropdown with flags', 'wpglobus' );
	}

	/**
	 * Echo the widget content
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {

		if ( ! empty( $instance['type'] ) ) {
			$type = $instance['type'];
		} else {
			$type = 'flags';
		}

		$inside = '';

		$enabled_languages = WPGlobus::Config()->enabled_languages;

		switch ( $type ) :
			case 'list' :
				$code = '<div class="list">{{inside}}</div>';
				break;
			case 'list_with_flags' :
				$code = '<div class="list flags">{{inside}}</div>';
				break;
			case 'select' :
			case 'select_with_code' :
				$code =
					'<div class="select-styled"><select onchange="document.location.href = this.value;">{{inside}}</select></div>';
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
				$code              = '<div class="dropdown-styled"> <ul>
					  <li>
						{{language}}
						<ul>
							{{inside}}
						</ul>
					  </li>
					</ul></div>';
				break;
			default:
				//	This is case 'flags'. Having is as default makes $code always set.
				$code = '<div class="flags-styled">{{inside}}</div>';
				break;
		endswitch;
		
		/**
		 * Filter enabled languages.
		 *
		 * Returning array.
		 *
		 * @since 1.0.13
		 *
		 * @param array     $enabled_languages 			 An array with languages to show off in menu.
		 * @param string    WPGlobus::Config()->language The current language.
		 */
		$enabled_languages = apply_filters( 'wpglobus_extra_languages', $enabled_languages, WPGlobus::Config()->language );			
		
		/**
		 * Class for link in a and option tags. Used for adding hash.
		 * @see class wpglobus-selector-link 
		 * @since 1.2.0
	     */
		$link_classes['selector_link'] = 'wpglobus-selector-link';
		
		/**
		 * Class for flag box
		 * @since 1.4.0
		 */
		$flag_classes = array();
		
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		foreach ( $enabled_languages as $language ) :

			$selected = '';
			
			/**
			 * Init current language class
			 */
			$link_classes['current_language'] = '';
			
			/**
			 * Init current language class for flag box
			 */
			$flag_classes['current_language'] = '';			
			
			if ( $language == WPGlobus::Config()->language ) {
				$selected = ' selected';
				switch ( $type ) :
					case 'flags' :
						$flag_classes['current_language'] = 'wpglobus-current-language';
					break;
					case 'list' :
					case 'list_with_flags' :
					case 'dropdown' :	
					case 'dropdown_with_flags' :	
						$link_classes['current_language'] = 'wpglobus-current-language';
					break;
				endswitch;				
			}

			$url = WPGlobus_Utils::localize_current_url( $language );

			$flag = WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $language ];

			switch ( $type ) :
				case 'flags' :
					$inside .= '<span class="flag ' . implode( ' ', $flag_classes ) . '">';
					$inside .= 		'<a href="' . $url . '" class="' . implode( ' ', $link_classes ) . '"><img src="' . $flag . '"/></a>';
					$inside .= '</span>';
					break;
				case 'list' :
				case 'list_with_flags' :
					$inside .= '<a href="' . $url . '" class="' . implode( ' ', $link_classes ) . '">' .
					           '<img src="' . $flag . '" alt=""/>' .
					           ' ' .
					           '<span class="name">' .
					           WPGlobus::Config()->language_name[ $language ] .
					           '</span>' .
					           ' ' .
					           '<span class="code">' . strtoupper( $language ) . '</span>' .
					           '</a>';
					break;
				case 'select' :
					$inside .= '<option class="' . implode( ' ', $link_classes ) . '" ' . $selected . ' value="' . $url . '">' . WPGlobus::Config()->language_name[ $language ] . '</option>';
					break;
				case 'select_with_code' :
					$inside .= '<option class="' . implode( ' ', $link_classes ) . '" ' . $selected . ' value="' . $url . '">' . WPGlobus::Config()->language_name[ $language ] . '&nbsp;(' . strtoupper( $language ) . ')</option>';
					break;
				case 'dropdown' :
					if ( '' != $selected ) {
						$code =
							str_replace( '{{language}}', '<a class="' . implode( ' ', $link_classes ) . '" href="' . $url . '">' . WPGlobus::Config()->language_name[ $language ] . '&nbsp;(' . strtoupper( $language ) . ')</a>', $code );
					} else {
						$inside .= '<li><a class="' . implode( ' ', $link_classes ) . '" href="' . $url . '">' . WPGlobus::Config()->language_name[ $language ] . '&nbsp;(' . strtoupper( $language ) . ')</a></li>';
					}
					break;
				case 'dropdown_with_flags' :
					if ( '' != $selected ) {
						$code =
							str_replace( '{{language}}', '<a class="' . implode( ' ', $link_classes ) . '" href="' . $url . '"><img src="' . $flag . '"/>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[ $language ] . '</a>', $code );
					} else {
						$inside .= '<li><a class="' . implode( ' ', $link_classes ) . '" href="' . $url . '"><img src="' . $flag . '"/>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[ $language ] . '</a></li>';
					}
					break;
			endswitch;

		endforeach;

		echo str_replace( '{{inside}}', $inside, $code );

		echo $args['after_widget'];

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
			$selected_type = 'flags';
		}
		if ( empty( $instance['title'] ) ) {
			$instance['title'] = '';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'type' ); ?>"><?php echo __( 'Title' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"/>
		</p>
		<p><?php _e( 'Selector type', 'wpglobus' ); ?></p>
		<p><?php
			foreach ( $this->types as $type => $caption ) :
				$checked = '';
				if ( $selected_type == $type ) {
					$checked = ' checked';
				} ?>
				<input type="radio"
				       id="<?php echo $this->get_field_id( 'type' ); ?>"
				       name="<?php echo $this->get_field_name( 'type' ); ?>" <?php echo $checked; ?>
				       value="<?php echo esc_attr( $type ); ?>"/> <?php echo $caption . '<br />';
			endforeach;
			?></p>        <?php

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
		$instance          = array();
		$instance['type']  = ( ! empty( $new_instance['type'] ) ) ? $new_instance['type'] : '';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';

		return $instance;
	}
}

# --- EOF
