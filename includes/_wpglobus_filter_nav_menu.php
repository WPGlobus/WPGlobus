<?php
/**
 * Filter for admin nav-menus.php screen
 */
add_filter( 'wp_nav_menu_objects', 'wpglobus_filter_nav_menu', 0 );
add_filter( 'wp_setup_nav_menu_item', 'wpglobus_filter_nav_menu', 0 );

/**
 * Filter for i18n before displaying a navigation menu.
 * @todo revising this filter because it now using for $post->attr_title and maybe $post->title translation only
 *
 * @param array
 *
 * @return array
 */
function wpglobus_filter_nav_menu( $object ) {

	global $pagenow;
	if ( 'nav-menus.php' == $pagenow && 'wp_setup_nav_menu_item' == current_filter() ) {
		/**
		 * Prevent reset i18n Navigation Labels and Title Attributes in navigation menu at nav-menus.php screen
		 */
		return $object;
	}

	if ( is_array( $object ) ) {
		foreach ( $object as &$post ) {

			if ( is_object( $post ) && 'WP_Post' == get_class( $post ) ) {

				$post->post_title = __wpg_text_filter( $post->post_title );

				$post->post_content = __wpg_text_filter( $post->post_content );

				$post->post_excerpt = __wpg_text_filter( $post->post_excerpt );

				if ( ! empty( $post->title ) ) {
					$post->title = __wpg_text_filter( $post->title );
				}

				if ( ! empty( $post->attr_title ) ) {
					$post->attr_title = __wpg_text_filter( $post->attr_title );
				}

			}

		}
	} else if ( is_object( $object ) && 'WP_Post' == get_class( $object ) ) {

		$object->post_title = __wpg_text_filter( $object->post_title );

		$object->post_content = __wpg_text_filter( $object->post_content );

		$object->post_excerpt = __wpg_text_filter( $object->post_excerpt );

		if ( ! empty( $object->title ) ) {
			$object->title = __wpg_text_filter( $object->title );
		}

		if ( ! empty( $object->attr_title ) ) {
			$object->attr_title = __wpg_text_filter( $object->attr_title );
		}

	}


	reset( $object );

	return $object;
}
