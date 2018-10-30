<?php
/**
 * File: compatibility-beta.php
 *
 * @package WPGlobus/Options
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$compatibility_beta = '*)&nbsp;';

$compatibility_beta .= esc_html__( 'Multilingual support of this add-on is currently in Beta stage.', 'wpglobus' );
$compatibility_beta .= ' ';
$compatibility_beta .= esc_html__( 'We do not recommend using it on production sites.', 'wpglobus' );
$compatibility_beta .= '<br />';

$_link            = add_query_arg( array( 'page' => 'wpglobus-helpdesk' ), admin_url( 'admin.php' ) );
$_support_link_1  = '<a href="' . $_link . '">';
$_support_link_2  = '</a>';
$_support_message = esc_html__( 'Please report all problems to %1$sWPGlobus Technical Support%2$s.', 'wpglobus' );
$_support_link    = sprintf( $_support_message, $_support_link_1, $_support_link_2 );

$compatibility_beta .= $_support_link;

return $compatibility_beta;
