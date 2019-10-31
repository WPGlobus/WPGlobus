<?php
/**
 * 1. Add multilingual Caption, Alternative Text, Description and post Title to media files.
 * 2. Handling the inserting media in post.
 *
 * @since 2.2.22
 * @package WPGlobus
 */
if ( version_compare( $GLOBALS['wp_version'], '4.6.999', '>' ) ) :
	
	$http_post_action = false;
	
	if ( WPGlobus_WP::is_doing_ajax() ) {
		if ( WPGlobus_WP::is_http_post_action('send-attachment-to-editor') ) {
			$http_post_action = 'send-attachment-to-editor';
		} else if ( WPGlobus_WP::is_http_post_action('query-attachments') ) {
			/**
			 * Action for gutenberg.
			 */
			$http_post_action = 'query-attachments';
		}
	}

	/**
	 * Array of actions to handle the inserting media on post.php page.
	 */
	$http_post_actions = array(
		'send-attachment-to-editor', // standard and builder mode (except gutenberg).
		'query-attachments'			 // gutenberg.
	);
	
	if ( WPGlobus_WP::is_pagenow( 'post.php' ) || $http_post_action ) {
		if ( ! class_exists('WPGlobus_Media') ) {
			require_once 'class-wpglobus-media.php';
		}
		WPGlobus_Media::get_instance($http_post_action, $http_post_actions);
	}
endif; 