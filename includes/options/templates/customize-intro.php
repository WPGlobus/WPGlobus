<?php
/**
 * File: customize-intro
 *
 * @package WPGlobus/Options
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$disabled_themes = WPGlobus_Customize_Themes::disabled_themes();

$current_theme = WPGlobus_Customize_Themes::current_theme();

$themes_list = '';

if ( ! empty( $disabled_themes ) ) {

	$themes_list = '<ul>';
	foreach ( $disabled_themes as $_theme ) {
		$themes_list .= '<li>';
		$themes_list .= ucwords( $_theme );
		$themes_list .= '</li>';
	}
	$themes_list .= '</ul>';

}

$customizer_intro_desc = '';

$customizer_intro_desc .=
	'<h3>' .
	esc_html__( 'WPGlobus Customizer is integrated into the Customize Theme panel', 'wpglobus' )
	. '</h3>';

$customizer_intro_desc .=
	esc_html__( 'However, some themes do not follow all WordPress standards strictly.', 'wpglobus' ) .
	' ' .
	esc_html__( 'WPGlobus Customizer may behave incorrectly with those themes.', 'wpglobus' );

$customizer_intro_desc .=
	'<h4>' .
	esc_html__( 'To avoid conflicts, you can switch the WPGlobus Customizer off:', 'wpglobus' ) .
	'</h4>' .
	'{{variants}}';

if ( ! empty( $themes_list ) ) {
	$customizer_intro_desc .= '<hr />';
	$customizer_intro_desc .=
		'<h4>' .
		esc_html__( 'Below is the list of themes that do not support the WPGlobus Customizer. With those themes, WPGlobus Customizer is switched off by default', 'wpglobus' ) .
		':</h4>' .
		$themes_list;
}

$customizer_intro_desc .= '<hr />';
$customizer_intro_desc .=
	esc_html__( 'The currently active theme is', 'wpglobus' ) .
	' <strong>' . $current_theme . '</strong>';

$customizer_intro_desc .= '<hr />';
$customizer_intro_desc .=
	'<h4>' .
	esc_html__( 'In the current version, WPGlobus Customizer does not support', 'wpglobus' ) .
	':</h4>' .
	'<ul>' .
	'<li>' .
	esc_html__( 'translation of the navigation menus', 'wpglobus' ) .
	'<br/> (' .
	esc_html__( 'to translate, please go to', 'wpglobus' ) .
	' <a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Menus' ) . '</a>' .
	')' .
	'</li>' .
	'</ul>';

$customizer_intro_desc .= '<hr />';
$customizer_intro_desc .=
	'<a href="' . esc_url( admin_url( 'customize.php' ) ) . '" class="button button-primary">' .
	esc_html__( 'Go to Customizer', 'wpglobus' ) .
	'</a>';

if ( defined( 'WPGLOBUS_CUSTOMIZE' ) ) {
	$init_var = esc_html__( 'is set to', 'wpglobus' )
				. ' <strong>' . ( WPGLOBUS_CUSTOMIZE ? 'true' : 'false' ) . '</strong>';
} else {
	$init_var = esc_html__( 'is undefined', 'wpglobus' );
}

$variants = '' .
			'<ul>' .
			'<li>' .
			esc_html__( 'by adding to the file wp-config.php', 'wpglobus' ) . ' <code>define( "WPGLOBUS_CUSTOMIZE", false );</code>' .
			'<br/> (' .
			esc_html__( 'now', 'wpglobus' ) . ' <strong>WPGLOBUS_CUSTOMIZE</strong> ' . $init_var .
			')' .
			'</li>' .
			'</ul>';
/** <li>2. ' . esc_html__( '2', 'wpglobus' ) . '</li>' */

$customizer_intro_desc = str_replace( '{{variants}}', $variants, $customizer_intro_desc );

return $customizer_intro_desc;