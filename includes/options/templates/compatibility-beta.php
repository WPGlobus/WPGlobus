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

$compatibility_beta  = '(*)' . '&nbsp;';
$compatibility_beta .= esc_html__( 'Предоставляется с целью тестирования и выявления возможных ошибок.', 'wpglobus' );
$compatibility_beta .= '<br />';
$compatibility_beta .= esc_html__( 'Не рекомендуется использовать на рабочих сайтах.', 'wpglobus' );
$compatibility_beta .= '<br />';

$_link = add_query_arg( array( 'page' => 'wpglobus-helpdesk' ), admin_url( 'admin.php' ) );
$_support_link_1 = '<a href="'.$_link.'">';
$_support_link_2 = '</a>';
$_support_message  = esc_html__( 'О возникших проблемах сообщайте в %1$sслужбу поддержки%2$s.', 'wpglobus' );
$_support_link  = sprintf( $_support_message, $_support_link_1, $_support_link_2 );
$compatibility_beta .= $_support_link;

return $compatibility_beta;
