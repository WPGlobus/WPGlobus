<?php
/**
 * File: wpglobus-aioseop.php
 * Support Standard/Classic mode.
 *
 * @since 2.3.9
 * @since 2.6.1 Support All in One SEO v.3 only.
 *
 * @package WPGlobus\All-in-One-SEO
 */

if ( defined( 'AIOSEOP_VERSION' ) ) {
	
	if ( version_compare( AIOSEOP_VERSION, '3.4.2', '>=' ) ) {
		
		/**
		 * Load `WPGlobus_aioseop` class after loading of vendor class.
		 * @see `plugins_loaded` action in all-in-one-seo-pack\class-aioseop-core.php
		 */
		function wpglobus_vendor__aioseop_load() {
			require_once 'class-wpglobus-aioseop.php';
			if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$WPGlobus_aioseop = new WPGlobus_aioseop();
			}		
		}		 
		add_action( 'plugins_loaded', 'wpglobus_vendor__aioseop_load', 6 );
		
	} else {
		
		require_once 'class-wpglobus-aioseop.php';
		if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$WPGlobus_aioseop = new WPGlobus_aioseop();
		}
	}

}

# --- EOF